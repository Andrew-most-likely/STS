<?php
session_start(); // Start the session

// Database connection parameters
include('../db_connection.php');

try {
    // Create a PDO instance
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Define the mapping between MySQL view names and display names
    $viewMapping = array(
        "last_five_delivery_intake" => "Latest Delivery Intake",
        "last_five_equipment_return" => "Latest Equipment Return",
        "last_five_ewaste" => "Latest E-waste",
        "last_five_room_check" => "Latest Room Check",
        "last_five_printer_check" => "Latest Printer Check",
        "last_five_Field_Work" => "Latest Field Work"
    );

    // Iterate through views to fetch and display data
    foreach ($viewMapping as $mysqlViewName => $displayName) {
        // Fetch data from the view
        $stmt = $conn->query("SELECT * FROM $mysqlViewName");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Start generating HTML markup
        echo '<div class="container">';
        echo '<a href="expand.html" style="text-decoration: none;">';
        echo "<h2>$displayName</h2>"; // Added expand button
        echo '</a>';
        // Check if there are any rows fetched
        if (count($rows) > 0) {
            // Start table
            echo '<table>';
            
            // Start table header row
            echo '<tr>';
            foreach (array_keys($rows[0]) as $columnName) {
                // Display column names in table header
                echo "<th>$columnName</th>";
            }
            echo '</tr>'; // End table header row
            
            // Loop through fetched rows
            foreach ($rows as $row) {
                // Start table data row
                echo '<tr>';
                // Loop through each column in the row
                foreach ($row as $key => $value) {
                    // Format date data if column name includes "date" or "time"
                    if (stripos($key, 'date') !== false || stripos($key, 'time') !== false) {
                        // Parse date string and format it as "m/d/y h:i A"
                        $formattedDate = date("n/j/y", strtotime($value));
                        // Display formatted date in table cell
                        echo "<td>$formattedDate</td>";
                    } else {
                        // Display other data as is
                        echo "<td>$value</td>";
                    }
                }
                // End table data row
                echo '</tr>';
            }
            
            // End table
            echo '</table>';
        } else {
            // If no data is fetched, display a message
            echo "<p>No data available</p>";
        }

        // Close the container
        echo '</div>';
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
