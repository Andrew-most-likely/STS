<?php
// Create connection
include('../db_connection.php');
try{
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM campus_student_workers WHERE active = '1';";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $student_workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $students_as_json = json_encode($student_workers);

    echo "let activeStudents = $students_as_json";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
