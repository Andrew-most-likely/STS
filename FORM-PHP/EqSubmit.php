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
    $full_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown User'; // Get full name from session with fallback
    $dropper_name = $_POST['deliverer'];
    $asset_tag = $_POST['asset_tag'];
    $Staff_Member_Assigned = $_POST['Staff_Member_Assigned']; 
    $additional_info = $_POST['additional_info'];
    $start_time = $_POST['form_open_time'];
    $completion_time = date('Y-m-d H:i:s'); 

    // Prepare SQL statement to insert data into MySQL table
    $sql = "INSERT INTO equipment_return (Start_time, Completion_time, Name, Dropper_Name, JWU_Asset_Tag, Staff_Member_Assigned, Additional_Info) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(1, $start_time);
    $stmt->bindParam(2, $completion_time);
    $stmt->bindParam(3, $full_name);
    $stmt->bindParam(4, $dropper_name);
    $stmt->bindParam(5, $asset_tag);
    $stmt->bindParam(6, $Staff_Member_Assigned);
    $stmt->bindParam(7, $additional_info);

    // Execute the statement
    $stmt->execute();

    // Redirect to the thank you page
    header("Location: ../INDEX-HTML/main.html");
    exit();
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
