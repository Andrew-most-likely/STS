<?php
session_start(); // Start the session

// Database connection parameters
include('../db_connection.php');

try {
    // Create a PDO instance
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve form data
    $name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown User'; // Get full name from session with fallback
    $date_checked = $_POST['date_checked'];
    $building = $_POST['Building-dropdown'];
    $room_number = $_POST['Room-Number'];
    $room_notes = $_POST['room_notes'];
    $start_time = $_POST['form_open_time'];
    $completion_time = date('Y-m-d H:i:s');

    // Prepare SQL statement to insert data into MySQL table
    $sql = "INSERT INTO room_check (
     Start_time,
     Completion_time, 
     name, 
     date_checked, 
     building, 
     room_number, 
     room_notes, 
     podium, tablet, 
     projector) 
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(1, $start_time);
    $stmt->bindParam(2, $completion_time);
    $stmt->bindParam(3, $name);
    $stmt->bindParam(4, $date_checked);
    $stmt->bindParam(5, $building);
    $stmt->bindParam(6, $room_number);
    $stmt->bindParam(7, $room_notes);

    // Handle checkbox values
    $conditionA = isset($_POST['conditionA']) ? 'Working' : 'Broken';
    $conditionB = isset($_POST['conditionB']) ? 'Working' : 'Broken';
    $conditionC = isset($_POST['conditionC']) ? 'Working' : 'Broken';
    $stmt->bindParam(8, $conditionA);
    $stmt->bindParam(9, $conditionB);
    $stmt->bindParam(10, $conditionC);

    // Execute the statement
    $stmt->execute();

    // Redirect to the thank you page
    header("Location: /sts/index.html");
    exit();
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
