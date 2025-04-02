<?php
// Database connection parameters
include('../db_connection.php');
// Start the session to access session variables
session_start();

try {
    // Create a PDO instance
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve form data
    $Campus = $_POST['Campus'];
    $deviceType = $_POST['Device-Type'];
    $assetTag = $_POST['Asset-Tag'];
    $modelNumber = $_POST['Model-Number'];
    $Serial_Number_Service_Tag = $_POST['Serial_Number'];
    $start_time = $_POST['form_open_time'];
    $completion_time = date('Y-m-d H:i:s'); 
    $Name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown User'; // Get full name from session with fallback

    // Prepare SQL statement to insert data into MySQL table
    $sql = "INSERT INTO ewaste (Start_time, Completion_time, name, Campus, device_type, asset_tag, model_number, Serial_Number_Service_Tag) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(1, $start_time);
    $stmt->bindParam(2, $completion_time);
    $stmt->bindParam(3, $Name); // Use the session username here
    $stmt->bindParam(4, $Campus);
    $stmt->bindParam(5, $deviceType);
    $stmt->bindParam(6, $assetTag);
    $stmt->bindParam(7, $modelNumber);
    $stmt->bindParam(8, $Serial_Number_Service_Tag);

    // Execute the statement
    $stmt->execute();

    // Redirect to the thank you page
    header("Location: ../INDEX-HTML/main.html");
    exit();
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
