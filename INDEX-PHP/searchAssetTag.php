<?php
session_start(); // Start the session

// Database connection parameters
include('../db_connection.php');

try {
    // Create a PDO instance
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve asset tag from GET parameter
    $assetTag = $_GET['asset_tag'];

    // Prepare SQL statement
    $sql = "SELECT * FROM ewaste WHERE asset_tag = :asset_tag";
    $stmt = $conn->prepare($sql);
    // Bind parameter
    $stmt->bindParam(':asset_tag', $assetTag);
    // Execute query
    $stmt->execute();

    // Check if there are any results
    if ($stmt->rowCount() > 0) {
        // Display table header
        echo "<br>";
        echo "<table>";
        echo "<tr><th>Device Type</th><th>Campus</th><th>Name</th><th>Model Number</th><th>Serial Number/Service Tag</th><th>Completion Time</th></tr>";
        
        // Fetch data and display in table rows
        // PHP for some studpid reasong is case sensitive so if you decide to touch the 
        // variable I so carefully put into this code DONT or atleast spell it correctly thank you
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row["device_type"] . "</td>";
            echo "<td>" . $row["Campus"] . "</td>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["model_number"] . "</td>";
            echo "<td>" . $row["Serial_Number_Service_Tag"] . "</td>";
            // Format completion time to "month/day/year" format
            $completionTime = date("m/d/Y", strtotime($row["Completion_time"]));
            echo "<td>" . $completionTime . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<br>No results found for <strong>Asset Tag: " . $assetTag . "</strong> in <strong>Ewaste</strong><br>";
    }

    // Prepare SQL statement
    $sql2 = "SELECT * FROM equipment_return WHERE JWU_Asset_Tag = :asset_tag";
    $stmt2 = $conn->prepare($sql2);
    // Bind parameter
    $stmt2->bindParam(':asset_tag', $assetTag);
    // Execute query
    $stmt2->execute();

    // Check if there are any results
    if ($stmt2->rowCount() > 0) {
        // Display table header
        echo "<br>";
        echo "<table>";
        echo "<tr><th>Dropper Name</th><th>Receiving Staff Member</th><th>Staff Assigned</th><th>Completion Time</th></tr>";
        
        // Fetch data and display in table rows
        // PHP for some studpid reasong is case sensitive so if you decide to touch the 
        // variable I so carefully put into this code DONT or atleast spell it correctly thank you
        while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row2["Dropper_Name"] . "</td>";
            echo "<td>" . $row2["Name"] . "</td>";
            echo "<td>" . $row2["Staff_Member_Assigned"] . "</td>";
            // Format completion time to "month/day/year" format
            $completionTime2 = date("m/d/Y", strtotime($row2["Completion_time"]));
            echo "<td>" . $completionTime2 . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        } else {
            echo "<br>No results found for <strong>Asset Tag: " . $assetTag . "</strong> in <strong>Equipment Dropoff</strong><br>";
        }

        // Prepare SQL statement
        $sql3 = "SELECT * FROM printer_list WHERE jwu_asset = :asset_tag";
        $stmt3 = $conn->prepare($sql3);
        // Bind parameter
        $stmt3->bindParam(':asset_tag', $assetTag);
        // Execute query
        $stmt3->execute();
    
        // Check if there are any results
        if ($stmt3->rowCount() > 0) {
            // Display table header
            echo "<br>";
            echo "<table>";
            echo "<tr><th>Checked By</th><th>Check Date</th><th>Install Location</th><th>Printer Model</th></tr>";
            
            // Fetch data and display in table rows
            // PHP for some studpid reasong is case sensitive so if you decide to touch the 
            // variable I so carefully put into this code DONT or atleast spell it correctly thank you
            while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                $sql4 = "SELECT * FROM printer_check WHERE IP_Address = :ip_address AND (CAST(`it_front_desk`.`printer_check`.`Check_Date` AS DATE) = CURDATE())";
                $stmt4 = $conn->prepare($sql4);
                $stmt4->bindParam(':ip_address', $row3["IP_Address"]);
                // Execute query
                $stmt4->execute();

                if($stmt4->rowCount() > 0) {
                    while ($row4 = $stmt4->fetch(PDO::FETCH_ASSOC)) {
                        $staffChecked = $row4["Checked_By"];
                        $checkDate = $row4["Check_Date"];
                    }
                } else {
                    $staffChecked = "No Check for Current Date";
                    $checkDate = "N/A";
                }
                echo "<tr>";
                echo "<td>" . $staffChecked . "</td>";
                echo "<td>" . $checkDate . "</td>";
                echo "<td>" . $row3["install_location"] . "</td>";
                echo "<td>" . $row3["model"] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<br>";
            } else {
                echo "<br>No results found for <strong>Asset Tag: " . $assetTag . "</strong> in <strong>Printer List</strong><br>";
            }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
$conn = null; // Close connection
?>
