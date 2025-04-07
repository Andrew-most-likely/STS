<?php
include('../db_connection.php');
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$sql = "SELECT * FROM Field_Work WHERE status = 'pending' ORDER BY completion_time DESC LIMIT 10";
$result = $conn->query($sql);
$requests = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $requests[] = [
            'id' => $row['id'],
            'ticket' => $row['Ticket_Number'],
            'name' => $row['Name'],
            'description' => $row['Work_Description'],
            'difficulty' => intval($row['Difficulty']),
            'time' => $row['completion_time'],
        ];
    }
}

echo json_encode($requests);
$conn->close();
?>
