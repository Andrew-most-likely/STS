<?php
session_start(); // Start the session

// Database connection parameters
include('../db_connection.php');

try {
    // Your existing database connection code using PDO
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown User'; // Get full name from session with fallback

    // Handle image upload first
    $imagePath = null;
    if (isset($_FILES['room_image'])) {
        // Check for upload errors
        if ($_FILES['room_image']['error'] !== UPLOAD_ERR_OK) {
            echo "Error uploading file. Code: " . $_FILES['room_image']['error'];
            exit();
        }

        $uploadDir = 'Uploads'; // Ensure this directory exists and is writable

        // Capture form data (e.g., building, room number)
        $building = isset($_POST['Building-dropdown']) ? $_POST['Building-dropdown'] : 'UnknownBuilding';
        $room_number = isset($_POST['Room-Number']) ? $_POST['Room-Number'] : 'UnknownRoom';

        // Generate a new file name using building, room number, and current date/time
        $timestamp = date('Ymd'); // Current date and time formatted as YYYYMMDD_HHMMSS
        $fileName = "{$building}_{$room_number}_{$timestamp}_" . basename($_FILES['room_image']['name']);
        $uploadFile = $uploadDir . '/' . $fileName;

        // Check if directory exists and is writable
        if (!is_dir($uploadDir)) {
            echo "Upload directory does not exist!";
            exit();
        }

        if (!is_writable($uploadDir)) {
            echo "Upload directory is not writable!";
            exit();
        }

        // Try moving the uploaded file to the target directory
        if (move_uploaded_file($_FILES['room_image']['tmp_name'], $uploadFile)) {
            $imagePath = $uploadFile;
            echo "File uploaded successfully!";
        } else {
            echo "Failed to move uploaded file.";
        }
    }

    // Your existing form data collection
    $date_checked = $_POST['date_checked'];
    $podium = isset($_POST['conditionA']) ? $_POST['conditionA'] : 'No'; // Handle checkboxes
    $projector = isset($_POST['conditionB']) ? $_POST['conditionB'] : 'No';
    $tablet = isset($_POST['conditionC']) ? $_POST['conditionC'] : 'No';
    $room_notes = $_POST['room_notes'];
    $form_open_time = $_POST['form_open_time'];
    $form_submit_time = date('Y-m-d H:i:s');

    // Modify your INSERT query to include the image_path
    $sql = "INSERT INTO room_check (
        date_checked, 
        building, 
        room_number, 
        podium, 
        projector, 
        tablet, 
        room_notes, 
        imagepath,
        Start_time, 
        Completion_time,
        name
    ) VALUES (
        :date_checked, :building, :room_number, :podium, :projector, :tablet, 
        :room_notes, :image_path, :form_open_time, :form_submit_time, :username
    )";

    // Prepare and bind parameters using PDO
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':date_checked', $date_checked);
    $stmt->bindParam(':building', $building);
    $stmt->bindParam(':room_number', $room_number);
    $stmt->bindParam(':podium', $podium);
    $stmt->bindParam(':projector', $projector);
    $stmt->bindParam(':tablet', $tablet);
    $stmt->bindParam(':room_notes', $room_notes);
    $stmt->bindParam(':image_path', $imagePath); // Fixed this to match the query parameter name
    $stmt->bindParam(':form_open_time', $form_open_time);
    $stmt->bindParam(':form_submit_time', $form_submit_time);
    $stmt->bindParam(':username', $name);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect after successful form submission
        header("Location: ../INDEX-HTML/main.html");
        exit(); // Ensure no further code is executed
    } else {
        echo "Error: " . $stmt->errorInfo()[2]; // Display any errors if query execution fails
    }

} catch (PDOException $e) {
    // Catch PDO exceptions and display the error message
    echo "Error: " . $e->getMessage();
}
?>
