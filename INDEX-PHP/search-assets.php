<?php
include('../db_connection.php');

header('Content-Type: application/json');

try {
    // Get the search query from the request
    $searchQuery = isset($_GET['q']) ? $_GET['q'] : '';
    
    // Check if search query is empty
    if (empty($searchQuery)) {
        echo json_encode([]);
        exit;
    }
    
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Define the tables to search in
    $tables = [
        'Total_printer_check' => 'total_printer_check',
        'total_room_check' => 'Room Check',
        'total_ewaste' => 'E-waste',
        'total_equipment_return' => 'Equipment Return',
        'toner_delivery_view' => 'Toner Delivery',
        'other_delivery_view' => 'Other Delivery',
        'individual_delivery_view' => 'Individual Delivery',
        'bulk_delivery_view' => 'Bulk Delivery'
    ];
    
    $results = [];
    
    // Search across all tables
    foreach ($tables as $table => $tag) {
        // Dynamically build query based on table structure
        // First, get the columns in this table
        $columnsQuery = "SHOW COLUMNS FROM $table";
        $columnsStmt = $pdo->prepare($columnsQuery);
        $columnsStmt->execute();
        $columns = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Prepare search conditions for each table based on its columns
        $searchConditions = [];
        $searchParams = [];
        
        foreach ($columns as $column) {
            // Skip binary columns and other non-searchable types
            $typeQuery = "SELECT DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
                         WHERE TABLE_SCHEMA = :dbname AND TABLE_NAME = :table AND COLUMN_NAME = :column";
            $typeStmt = $pdo->prepare($typeQuery);
            $typeStmt->bindParam(':dbname', $dbname);
            $typeStmt->bindParam(':table', $table);
            $typeStmt->bindParam(':column', $column);
            $typeStmt->execute();
            $dataType = $typeStmt->fetchColumn();
            
            if (!in_array(strtolower($dataType), ['blob', 'binary', 'varbinary', 'tinyblob', 'mediumblob', 'longblob'])) {
                $searchConditions[] = "`$column` LIKE :search" . count($searchParams);
                $searchParams[] = "%$searchQuery%";
            }
        }
        
        if (empty($searchConditions)) {
            continue; // Skip if no searchable columns
        }
        
        $query = "SELECT * FROM `$table` WHERE " . implode(" OR ", $searchConditions) . " LIMIT 2";
                 
        $stmt = $pdo->prepare($query);
        
        // Bind all search parameters
        for ($i = 0; $i < count($searchParams); $i++) {
            $stmt->bindValue(':search' . $i, $searchParams[$i]);
        }
        
        // Try to execute the statement - if it fails, likely due to missing columns, just continue
        try {
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format the results
            foreach ($data as $row) {
                // Determine the best title field (prefer Asset Tag or Serial)
                $assetTitle = '';
                foreach (['Asset Tag', 'Serial', 'ID'] as $possibleField) {
                    if (isset($row[$possibleField]) && !empty($row[$possibleField])) {
                        $assetTitle = $row[$possibleField];
                        break;
                    }
                }
                
                if (empty($assetTitle)) {
                    $assetTitle = 'Unknown Asset';
                }
                
                // Build primary details (more important fields)
                $primaryDetails = '';
                $primaryFields = ['Model', 'Make', 'Type', 'Status', 'Location', 'Department', 'Room Number'];
                foreach ($primaryFields as $field) {
                    if (isset($row[$field]) && !empty($row[$field])) {
                        $primaryDetails .= "<p><b>$field</b>: $row[$field]</p>";
                    }
                }
                $primaryDetails = rtrim($primaryDetails, ' • ');
                
                // Build secondary details (other useful information)
                $secondaryDetails = '';
                $secondaryFields = ['Description', 'Notes', 'Date', 'Submitted By', 'Contact Person', 'Quantity'];
                foreach ($secondaryFields as $field) {
                    if (isset($row[$field]) && !empty($row[$field])) {
                        $secondaryDetails .= "<p><b>$field</b>: $row[$field]</p> ";
                    }
                }
                $secondaryDetails = rtrim($secondaryDetails, ' • ');
                
                // Combine the details
                $description = '';
                if (!empty($primaryDetails)) {
                    $description .= $primaryDetails;
                }
                if (!empty($secondaryDetails)) {
                    if (!empty($description)) {
                        $description .= '<br>';
                    }
                    $description .= $secondaryDetails;
                }
                
                // If we still don't have any description, check for any other non-empty fields
                if (empty($description)) {
                    foreach ($row as $key => $value) {
                        if (!in_array($key, ['ID', 'Asset Tag', 'Serial']) && !empty($value)) {
                            $description .= "<p><b>$key</b>: $value</p>";
                        }
                    }
                    $description = rtrim($description, ' • ');
                }
                
                // Include the date if available - useful for sorting by recency
                $date = '';
                foreach (['Date', 'Timestamp', 'Created At', 'Submitted Date', 'Date Of Check', 'Completion_time', 'date_checked'] as $dateField) {
                    if (isset($row[$dateField]) && !empty($row[$dateField])) {
                        $date = $row[$dateField];
                        break;
                    }
                }
                
                $results[] = [
                    'id' => isset($row['ID']) ? $row['ID'] : '',
                    'asset' => $assetTitle,
                    'description' => $description,
                    'tag' => $tag,
                    'table' => $table,
                    'date' => $date
                ];
            }
        } catch (PDOException $e) {
            // Skip this table if query execution fails
            continue;
        }
    }
    
    // Sort results by date if available
    usort($results, function($a, $b) {
        if (!empty($a['date']) && !empty($b['date'])) {
            return strtotime($b['date']) - strtotime($a['date']); // Newest first
        }
        return 0;
    });
    
    echo json_encode($results);

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