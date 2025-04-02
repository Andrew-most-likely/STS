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
    $Difficulty = $_POST['range']; 
    $name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown User'; // Get full name from session with fallback
    $Ticket_Number = $_POST['Ticket_Number'];
    $Work_Description = $_POST['Work_Description']; // Ensure this matches the input name in HTML
    $completion_time = date('Y-m-d H:i:s'); 
    $name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown User'; // Get full name from session with fallback
    // Prepare SQL statement to insert data into MySQL table
    $sql = "INSERT INTO Field_Work (Work_Description, Ticket_Number, Name, completion_time, difficulty) VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(1, $Work_Description); // Binds the correct Work_Done input
    $stmt->bindParam(2, $Ticket_Number);
    $stmt->bindParam(3, $name);
    $stmt->bindParam(4, $completion_time);
    $stmt->bindParam(5, $Difficulty);
    
    // Execute the statement
    $stmt->execute();

    // Redirect to the thank you page
    header("Location: ../INDEX-HTML/main.html");
    exit();
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
