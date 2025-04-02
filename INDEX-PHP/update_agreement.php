<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_log('Error in update_agreement.php');
session_start();

include('../db_connection.php');

function debugLog($message, $data = null) {
    error_log("Agreement Update Debug - " . $message . ": " . print_r($data, true));
}

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    debugLog("Connection failed", $conn->connect_error);
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'];

// Verify that the user_id matches the session user_id for security
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $user_id) {
    debugLog("Session validation failed", ['session_id' => $_SESSION['user_id'], 'request_id' => $user_id]);
    die(json_encode(["success" => false, "message" => "Invalid session"]));
}

// Using ENUM value directly since it's strictly typed
$stmt = $conn->prepare("UPDATE users SET agreement = 'yes' WHERE id = ? AND agreement = 'no'");
if (!$stmt) {
    debugLog("Prepare statement failed", $conn->error);
    die(json_encode(["success" => false, "message" => "Failed to prepare statement"]));
}

$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        debugLog("Agreement updated successfully for user_id", $user_id);
        echo json_encode([
            "success" => true,
            "message" => "Agreement updated successfully",
            "is_staff" => $_SESSION['is_staff'],
            "name" => $_SESSION['username'],
            "campus" => $_SESSION['campus']
        ]);
    } else {
        debugLog("No update needed - agreement may already be accepted", $user_id);
        echo json_encode([
            "success" => false,
            "message" => "No update needed"
        ]);
    }
} else {
    debugLog("Failed to update agreement", $stmt->error);
    echo json_encode([
        "success" => false,
        "message" => "Failed to update agreement status"
    ]);
}

$stmt->close();
$conn->close();
?>