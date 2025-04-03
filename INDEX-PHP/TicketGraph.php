<?php
session_start(); // Start the session

// Database connection parameters
include('../db_connection.php');

try {
    // Create a PDO instance
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch data from the view and order by type
    $sql = "SELECT type,
                   day_1_total, day_2_total, day_3_total, day_4_total, day_5_total, day_6_total, day_7_total
            FROM ids_created_last_7_days";

    // Prepare and execute the query
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Fetch all rows
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Close the connection
    $conn = null;

    // Predefined color palette for datasets
    $color_palette = [
        '#FF5733', // Red
        '#3498DB', // Blue
        '#2ECC71', // Green
        '#FF7F50', // Orange
        '#9B59B6', // Purple
        '#F39C12', // Yellow
        '#1ABC9C'  // Teal
    ];

    // Prepare the datasets for Chart.js
    $datasets = [];
    $color_index = 0; // Start with the first color in the palette
    foreach ($data as $row) {
        $type = str_replace('_', ' ', $row['type']); // Replace underscores with spaces

        // Map the days' totals into an array and filter out zero/empty values
        $day_totals = [
            $row['day_1_total'], $row['day_2_total'], $row['day_3_total'], 
            $row['day_4_total'], $row['day_5_total'], $row['day_6_total'], $row['day_7_total']
        ];

        // Filter out zero values (optional: depending on whether you want to skip these days)
        $filtered_day_totals = array_map('intval', $day_totals);

        // Prepare the dataset for this type
        $datasets[] = [
            'label' => $type,
            'data' => $filtered_day_totals,
            'fill' => false, // Disable the area fill
            'borderColor' => $color_palette[$color_index % count($color_palette)], // Use the color from the predefined palette
            'tension' => 0.4 // Enable smooth curve interpolation
        ];

        // Move to the next color for the next dataset
        $color_index++;
    }

    // Generate labels for the last 7 days
    $today = strtotime('today');
    $labels = [];
    for ($i = 0; $i <= 6; $i++) {
        $labels[] = date('m/d', strtotime("-$i days", $today)); // Date format for X-axis (e.g., "04/03")
    }

    // Prepare the chart data
    $chart_data = [
        'labels' => array_reverse($labels), // Reverse to start from the oldest day
        'datasets' => $datasets
    ];

    // Output the chart data as JSON
    header('Content-Type: application/json');
    echo json_encode($chart_data);
} catch(PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
}
?>
