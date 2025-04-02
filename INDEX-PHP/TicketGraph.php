<?php
session_start(); // Start the session

// Database connection parameters
include('../db_connection.php');

try {
    // Create a PDO instance
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch data from the view and order by date_created
    $sql = "SELECT type, SUM(day_1_total) AS day_1_total,
                   SUM(day_2_total) AS day_2_total,
                   SUM(day_3_total) AS day_3_total,
                   SUM(day_4_total) AS day_4_total,
                   SUM(day_5_total) AS day_5_total,
                   SUM(day_6_total) AS day_6_total,
                   SUM(day_7_total) AS day_7_total
            FROM ids_created_last_7_days 
            GROUP BY type";

    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Fetch all rows
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Close the connection
    $conn = null;

    // Prepare data for Chart.js
    $datasets = [];
    foreach ($data as $row) {
        $type = str_replace('_', ' ', $row['type']); // Replace underscores with spaces
        $day_totals = array_slice($row, 1); // Exclude the 'type' column
        $datasets[] = [
            'label' => $type,
            'data' => array_values($day_totals)
        ];
    }

    // Generate labels for the last 7 days
    $today = strtotime('today');
    $labels = [];
    for ($i = 6; $i >= 0; $i--) {
        $labels[] = date('m/d', strtotime("-$i days", $today));
    }

    // Encode data to JSON format for Chart.js
    $chart_data = [
        'labels' => $labels,
        'datasets' => $datasets
    ];

    // Output JSON
    echo json_encode($chart_data);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
