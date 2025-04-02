<?php
// Database connection parameters
include('../db_connection.php'); // This includes your existing mysqli connection setup

// Check if the connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query the database using mysqli
$sql = "SELECT * FROM current_day_printer_checks";
$result = $conn->query($sql);

// Check if there are any rows returned
if ($result->num_rows > 0) {
    // Fetch data as an associative array
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    // Return the data in JavaScript-compatible format
    echo "var currentDayChecks = " . json_encode($data) . ";";
} else {
     // If no records are found, you can output a message or handle it differently
     echo "var studentPrinters = [];";  // An empty array for the JavaScript variable
     echo "console.log('No records found.');";  // Log a message to the console for debugging
}

// Close the connection
$conn->close();
?>
