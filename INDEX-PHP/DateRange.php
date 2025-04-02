<?php
    session_start(); // Start the session
 if (isset($_POST['submit'])) {

    
    // Database connection parameters
    include('../db_connection.php');
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

//connection check
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date1 = $_POST['date1'];
    $date2 = $_POST['date2'];

    $stmt = $conn->prepare('CALL DateRangeProcedure3(?, ?)');
    $stmt->bind_param('ss', $date1, $date2);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
		 // Encode data to JSON
        $jsonData = json_encode($data);
		 // Return JSON data
            echo $jsonData;
		
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
	}
 }
 
?>