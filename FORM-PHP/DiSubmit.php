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
    $receive_date = $_POST['date-received'];
    $tracking_number = $_POST['tracking-number'];
    $delivery_company = $_POST['delivery_company'];
    
    // Get full name from session with fallback
    $full_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown User';

    // Prepare SQL statement to insert data into base_delivery_intake table
    $sql = "INSERT INTO base_delivery_intake (receive_date, tracking_number, delivery_company, name) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(1, $receive_date);
    $stmt->bindParam(2, $tracking_number);
    $stmt->bindParam(3, $delivery_company);
    $stmt->bindParam(4, $full_name); // Bind the full name parameter

    // Execute the statement
    $stmt->execute();

    // Get the last inserted ID
    $delivery_intake_id = $conn->lastInsertId();

    // Retrieve additional form data
    $delivery_type = $_POST['type-of-delivery'];
    $num_units = 1; // Default value
    $shipment_contents = null;
    $individual_package_addressee = $contact_method = $addressed_to = $toner_id = $color = $designated_printer = null;
    
    // Get location from the correct form field name or set default for Toner
    $location = isset($_POST['package-location']) ? $_POST['package-location'] : null;
    if ($delivery_type === 'Toner') {
        $location = "Toner Closet"; // Always set Toner location to "Toner Closet"
    }

    // Assign values based on delivery type and set appropriate shipment_contents
    switch ($delivery_type) {
        case 'Bulk':
            $num_units = $_POST['number-of-units'];
            $shipment_contents = $_POST['contents'];
            break;
        case 'Individual':
            $individual_package_addressee = $_POST['person-contacted'];
            $contact_method = $_POST['contact-method'];
            $addressed_to = $_POST['addressed-to'];
            // For Individual, use the addressed-to field as the shipment contents
            $shipment_contents = $_POST['addressed-to'];
            break;
        case 'Toner':
            $toner_id = $_POST['toner-id'];
            $color = $_POST['color']; 
            $designated_printer = $_POST['designated-printer'];
            // For Toner, just use "Toner" as the contents
            $shipment_contents = "Toner";
            break;
        case 'Other':
            // For Other, use the item-description as the contents
            $shipment_contents = isset($_POST['item-description']) ? $_POST['item-description'] : 'Other Item';
            break;
    }

    // Insert data into delivery_type table
    if ($delivery_type) {
        $sqlType = "INSERT INTO delivery_type (delivery_intake_id, type, num_units, shipment_contents) VALUES (?, ?, ?, ?)";
        $stmtType = $conn->prepare($sqlType);
        $stmtType->bindParam(1, $delivery_intake_id);
        $stmtType->bindParam(2, $delivery_type);
        $stmtType->bindParam(3, $num_units);
        $stmtType->bindParam(4, $shipment_contents);
        $stmtType->execute();
    }

    // Insert data into delivery_contact_method table
    if ($contact_method) {
        $sqlContactMethod = "INSERT INTO delivery_contact_method (delivery_intake_id, method, addressed_to) VALUES (?, ?, ?)";
        $stmtContactMethod = $conn->prepare($sqlContactMethod);
        $stmtContactMethod->bindParam(1, $delivery_intake_id);
        $stmtContactMethod->bindParam(2, $contact_method);
        $stmtContactMethod->bindParam(3, $addressed_to);
        $stmtContactMethod->execute();
    }

    // Insert data into delivery_package_location table
    if ($location || in_array($delivery_type, ['Individual', 'Toner'])) {
        $sqlPackageLocation = "INSERT INTO delivery_package_location (delivery_intake_id, location) VALUES (?, ?)";
        $stmtPackageLocation = $conn->prepare($sqlPackageLocation);
        $stmtPackageLocation->bindParam(1, $delivery_intake_id);
        $stmtPackageLocation->bindParam(2, $location);
        $stmtPackageLocation->execute();
    }

    // Insert data into delivery_toner table
    if ($delivery_type === 'Toner') {
        $sqlToner = "INSERT INTO delivery_toner (delivery_intake_id, toner_id, color, designated_printer) VALUES (?, ?, ?, ?)";
        $stmtToner = $conn->prepare($sqlToner);
        $stmtToner->bindParam(1, $delivery_intake_id);
        $stmtToner->bindParam(2, $toner_id);
        $stmtToner->bindParam(3, $color);
        $stmtToner->bindParam(4, $designated_printer);
        $stmtToner->execute();
    }

    // Redirect to the thank you page
    header("Location: ../INDEX-HTML/main.html");
    exit();
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
