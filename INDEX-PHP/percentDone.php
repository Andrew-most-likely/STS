<?php
session_start(); // Start the session

// Database connection parameters
include('../db_connection.php');

try {
    // Create a PDO connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch the count of checked printers
    $stmt = $conn->prepare("SELECT DISTINCT IP_Address FROM current_day_printer_checks");
    $stmt->execute();
    $checkedPrinters = $stmt->rowCount();

    // Fetch the total printers count from downcity and harborside
    $stmt2 = $conn->prepare("SELECT DISTINCT IP_Address FROM downcity_printers");
    $stmt2->execute();
    
    $stmt3 = $conn->prepare("SELECT DISTINCT IP_Address FROM harborside_printers");
    $stmt3->execute();
    
    $totalPrinters = $stmt2->rowCount() + $stmt3->rowCount();
    $uncheckedPrinters = $totalPrinters - $checkedPrinters;

    // Prepare data for Chart.js (JSON format)
    $chartData = [
        "labels" => ["Checked Printers", "Unchecked Printers"],
        "values" => [$checkedPrinters, $uncheckedPrinters]
    ];

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($chartData);

} catch (PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
}
?>
