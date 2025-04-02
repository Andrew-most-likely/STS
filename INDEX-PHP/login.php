<?php
ini_set('display_errors', 0); // Disable error display in the response
ini_set('log_errors', 1);    // Enable error logging
error_log('Error in login.php'); // Test error logging

// Start the session
session_start();

// Database connection parameters
include('../db_connection.php');

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Function to log debug information
function debugLog($message, $data = null) {
    error_log("Login Debug - " . $message . ": " . print_r($data, true));
}

// Get the form data
if (isset($_POST['username']) && isset($_POST['password'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    debugLog("Attempting login for user", $user);

    // Modified query to include agreement status (ENUM column)
    $sql = "
        SELECT 
            u.password_hash,
            u.agreement,
            u.id,
            COALESCE(c.campus, 'student') as campus,
            CASE 
                WHEN c.campus = 'staff' THEN 1
                ELSE 0
            END as is_staff
        FROM users u 
        LEFT JOIN campus_student_workers c ON u.username = c.student_name 
        WHERE u.username = ?
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        debugLog("Prepare statement failed", $conn->error);
        die(json_encode(["success" => false, "message" => "Failed to prepare statement: " . $conn->error]));
    }

    $stmt->bind_param("s", $user);
    
    if (!$stmt->execute()) {
        debugLog("Execute failed", $stmt->error);
        die(json_encode(["success" => false, "message" => "Failed to execute query: " . $stmt->error]));
    }

    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        debugLog("Query result", $row);

        // Verify the password
        if (password_verify($pass, $row['password_hash'])) {
            // Set session variables
            $_SESSION['username'] = $user;
            $_SESSION['is_staff'] = (bool)$row['is_staff'];
            $_SESSION['campus'] = $row['campus'];
            $_SESSION['user_id'] = $row['id'];

            debugLog("Session variables set", [
                'username' => $_SESSION['username'],
                'is_staff' => $_SESSION['is_staff'],
                'campus' => $_SESSION['campus'],
                'user_id' => $_SESSION['user_id']
            ]);

            // Check agreement status
            if ($row['agreement'] === 'no') {
                debugLog("User needs to accept agreement", $user);
                echo json_encode([
                    "success" => true,
                    "needs_agreement" => true,
                    "user_id" => $row['id'],
                    "name" => $user,
                    "is_staff" => $_SESSION['is_staff'],
                    "campus" => $_SESSION['campus']
                ]);
            } else {
                echo json_encode([
                    "success" => true,
                    "needs_agreement" => false,
                    "name" => $user,
                    "is_staff" => $_SESSION['is_staff'],
                    "campus" => $_SESSION['campus']
                ]);
            }
        } else {
            debugLog("Password verification failed for user", $user);
            echo json_encode(["success" => false, "message" => "Invalid password."]);
        }
    } else {
        debugLog("No user found", $user);
        echo json_encode(["success" => false, "message" => "Username not found."]);
    }

    // Close statement
    $stmt->close();
} else {
    debugLog("Invalid request - missing username or password");
    echo json_encode(["success" => false, "message" => "Invalid request. Username or password not set."]);
}

// Close the connection
$conn->close();