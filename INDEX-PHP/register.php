<?php
session_start(); // Start the session

// Database connection parameters
include('../db_connection.php');

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Get the form data
if (isset($_POST['username']) && isset($_POST['password'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Check if the username exists in the campus_student_workers table and if they are active
    $sql = "SELECT student_name FROM campus_student_workers WHERE student_name = ? AND active = 1";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die(json_encode(["success" => false, "message" => "Statement preparation failed: " . $conn->error]));
    }

    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->store_result();  // Store the result

    // If a match is found in the campus_student_workers table and the user is active, proceed
    if ($stmt->num_rows > 0) {
        // Check if the user already exists in the users table
        $sqlCheck = "SELECT username FROM users WHERE username = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("s", $user);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            echo json_encode(["success" => false, "message" => "Username already exists."]);
        } else {
            // If not, insert the new user into the users table
            $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
            $sqlInsert = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("ss", $user, $hashedPassword);

            if ($stmtInsert->execute()) {
                echo json_encode(["success" => true, "message" => "Registration successful!", "name" => $user]);
            } else {
                echo json_encode(["success" => false, "message" => "Registration failed: " . $stmtInsert->error]);
            }

            $stmtInsert->close();
        }

        $stmtCheck->close();
    } else {
        echo json_encode(["success" => false, "message" => "Username not found in campus student workers or inactive."]);
    }

    // Close statements
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request. Username or password not set."]);
}

// Close the connection
$conn->close();
?>
