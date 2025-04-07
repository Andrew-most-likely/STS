<?php
session_start(); // Start the session

// Database connection parameters
include('../db_connection.php');

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get today's date
    $today = date('Y-m-d');

    // Query to count rooms checked today
    $stmt = $conn->prepare("SELECT COUNT(*) AS checked_rooms FROM total_room_check WHERE `Date Checked` = :today");
    $stmt->bindParam(':today', $today);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $checkedRooms = $result['checked_rooms'];

    // Expected number of room checks per day
    $expectedRooms = 280;

    // Calculate unchecked rooms based on expectation
    $uncheckedRooms = max($expectedRooms - $checkedRooms, 0); // Avoid negatives

    // Prepare JSON data for Chart.js
    $chartData = [
        "labels" => ["Checked Rooms", "Unchecked Rooms"],
        "values" => [$checkedRooms, $uncheckedRooms]
    ];

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($chartData);

} catch (PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
}
?>
