<?php
include('../db_connection.php');

header('Content-Type: application/json');

try {
    $viewId = isset($_GET['view']) ? $_GET['view'] : '';
    $data = [];
    $columns = [];

    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    switch($viewId) {
        case 'total_room_check':
            $query = "SELECT * FROM total_room_check ORDER BY `ID` DESC";
            break;
            
        case 'Total_printer_check':
            $query = "SELECT * FROM Total_printer_check ORDER BY `ID` DESC";
            break;
            
        case 'total_ewaste':
            $query = "SELECT * FROM total_ewaste ORDER BY `ID` DESC";
            break;

        case 'total_Toner_Closet':
            $query = "SELECT * FROM total_Toner_Closet ORDER BY `ID` DESC";
            break;
            
        case 'total_equipment_return':
            $query = "SELECT * FROM total_equipment_return ORDER BY `ID` DESC";
            break;
            
        case 'toner_delivery_view':
            $query = "SELECT * FROM toner_delivery_view ORDER BY `ID` DESC";
            break;
            
        case 'other_delivery_view':
            $query = "SELECT * FROM other_delivery_view ORDER BY `ID` DESC";
            break;
            
        case 'individual_delivery_view':
            $query = "SELECT * FROM individual_delivery_view ORDER BY `ID` DESC";
            break;
            
        case 'bulk_delivery_view':
            $query = "SELECT * FROM bulk_delivery_view ORDER BY `ID` DESC";
            break;
            
        default:
            throw new Exception("Invalid view ID");
    }

    $stmt = $pdo->query($query);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($data) > 0) {
        $columns = array_keys($data[0]);
    }

    echo json_encode([
        'success' => true,
        'columns' => $columns,
        'data' => $data
    ]);

} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>