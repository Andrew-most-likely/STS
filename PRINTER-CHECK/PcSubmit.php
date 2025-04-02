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
    $Checked_By = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown User'; // Get full name from session with fallback
    $Check_Date = date('Y-m-d');
    $IP_Address = $_POST['IP_Address'];
    $Install_Location = $_POST['Install_Location'];
    $Model_Number = $_POST['Model_Number'];
    $Black_Cartridge = $_POST['Black_Cartridge'];
    $Cyan_Cartridge = $_POST["Cyan_Cartridge"];
    $Yellow_Cartridge = $_POST["Yellow_Cartridge"];
    $Magenta_Cartridge = $_POST["Magenta_Cartridge"];
    $Black_Drum = $_POST['Black_Drum'];
    $Cyan_Drum = $_POST["Cyan_Drum"];
    $Yellow_Drum = $_POST["Yellow_Drum"];
    $Magenta_Drum = $_POST["Magenta_Drum"];
    $Maintenance_Kit = $_POST["Maintenance_Kit"];
    $Transfer_Kit = $_POST["Transfer_Kit"];
    $Fuser_Kit = $_POST["Fuser_Kit"];
    $Waste_Toner = $_POST["Waste_Toner"];
    $Tray_2 = $_POST["Tray_2"];
    $Tray_3 = $_POST["Tray_3"];
    $Tray_4 = $_POST["Tray_4"];
    $Tray_5 = $_POST["Tray_5"];

    // Prepare SQL statement to insert data into MySQL table
    $sql = "INSERT INTO printer_check (Checked_By, Check_Date, IP_Address, Install_Location, Model_Number, Black_Cartridge, Cyan_Cartridge, Yellow_Cartridge, Magenta_Cartridge, Black_Drum, Cyan_Drum, Yellow_Drum, Magenta_Drum, Maintenance_Kit, Transfer_Kit, Fuser_Kit, Waste_Toner, Tray_2, Tray_3, Tray_4, Tray_5) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bindParam(1, $Checked_By);
    $stmt->bindParam(2, $Check_Date);
    $stmt->bindParam(3, $IP_Address);
    $stmt->bindParam(4, $Install_Location);
    $stmt->bindParam(5, $Model_Number);
    $stmt->bindParam(6, $Black_Cartridge);
    $stmt->bindParam(7, $Cyan_Cartridge);
    $stmt->bindParam(8, $Yellow_Cartridge);
    $stmt->bindParam(9, $Magenta_Cartridge);
    $stmt->bindParam(10, $Black_Drum);
    $stmt->bindParam(11, $Cyan_Drum);
    $stmt->bindParam(12, $Yellow_Drum);
    $stmt->bindParam(13, $Magenta_Drum);
    $stmt->bindParam(14, $Maintenance_Kit);
    $stmt->bindParam(15, $Transfer_Kit);
    $stmt->bindParam(16, $Fuser_Kit);
    $stmt->bindParam(17, $Waste_Toner);
    $stmt->bindParam(18, $Tray_2);
    $stmt->bindParam(19, $Tray_3);
    $stmt->bindParam(20, $Tray_4);
    $stmt->bindParam(21, $Tray_5);

    $stmt->execute();

    // Redirect to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>