<?php
// Create connection
include('../db_connection.php');
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT scan_run_date FROM scan_run_log ORDER BY scan_run_date DESC LIMIT 1;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $lastRunTime = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lastRunTime) {
        $lastRunDate = new DateTime($lastRunTime['scan_run_date']);
        $currentDate = new DateTime();
        $interval = $currentDate->diff($lastRunDate);

        if ($interval->y > 0) {
            $timeSinceLastRun = $interval->y . ' years ago';
        } elseif ($interval->m > 0) {
            $timeSinceLastRun = $interval->m . ' months ago';
        } elseif ($interval->d > 0) {
            $timeSinceLastRun = $interval->d . ' days ago';
        } elseif ($interval->h > 0) {
            $timeSinceLastRun = $interval->h . ' hours ago';
        } elseif ($interval->i > 0) {
            $timeSinceLastRun = $interval->i . ' minutes ago';
        } else {
            $timeSinceLastRun = 'just now';
        }

        echo "Last Ran: " . htmlspecialchars($timeSinceLastRun);
    } else {
        echo "No records found.";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>