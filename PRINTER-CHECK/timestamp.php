<?php
// Create connection
include('../db_connection.php');
try{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO scan_run_log (scan_run_date) VALUES (NOW());";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $downcity_printers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $printers_as_json = json_encode($downcity_printers);

    echo "var studentPrinters = $printers_as_json";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
