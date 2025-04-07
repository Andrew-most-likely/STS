<?php
header('Content-Type: application/json');

// Include DB connection
include('../db_connection.php');

// Check for required POST data
if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$id = intval($_POST['id']);
$status = $_POST['status'];

// Validate status
$validStatuses = ['pending', 'approved', 'denied'];
if (!in_array($status, $validStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Prepare and run update
$stmt = $conn->prepare("UPDATE Field_Work SET status = ? WHERE id = ?");
if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Prepare failed',
        'error' => $conn->error
    ]);
    exit;
}

$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Execution failed',
        'error' => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
