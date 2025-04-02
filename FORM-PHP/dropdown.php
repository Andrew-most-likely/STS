<?php
// Database connection parameters
include('../db_connection.php');
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch active student names from the database
$sql = "SELECT student_name FROM campus_student_workers WHERE active = 1";
$result = $conn->query($sql);

// Create the HTML dropdown menu with a placeholder option
echo '<select id="Full-Name" name="full-name" name="student_name" required>';
echo '<option value="" disabled selected>Select a name</option>'; // Placeholder option
while ($row = $result->fetch_assoc()) {
    echo '<option value="' . $row['student_name'] . '">' . $row['student_name'] . '</option>';
}
echo '</select>';


// Close the database connection
$conn->close();
?>
