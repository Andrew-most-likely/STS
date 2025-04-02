<?php
// Create connection
include('../db_connection.php');
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM downcity_printers";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $downcity_printers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($downcity_printers) > 0) {
        // If records are found, output the JSON as a JavaScript variable
        $printers_as_json = json_encode($downcity_printers);
        echo "var studentPrinters = $printers_as_json;";
    } else {
        // If no records are found, output an empty array and log the message
        echo "var studentPrinters = [];";  // Empty array
        echo "console.log('No records found.');";  // Log a message to the console
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
