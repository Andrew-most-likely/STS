<?php
session_start(); // Start the session

// Database connection parameters
include('../db_connection.php');

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute the SQL query
    $stmt = $conn->prepare("SELECT DISTINCT IP_Address FROM current_day_printer_checks");
    $stmt->execute();

    // Create a PDO connection
    $conn2 = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute the SQL query
    $stmt2 = $conn2->prepare("SELECT DISTINCT IP_Address FROM downcity_printers");
    $stmt2->execute();

    // Create a PDO connection
    $conn3 = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn3->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare and execute the SQL query
    $stmt3 = $conn3->prepare("SELECT DISTINCT IP_Address FROM harborside_printers");
    $stmt3->execute();

    $checkedPrinters = $stmt->rowCount();
    $totalPrinters = $stmt2->rowCount() + $stmt3->rowCount();

    $data =  round((($checkedPrinters / $totalPrinters) * 100), 2);

    echo $data;
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>