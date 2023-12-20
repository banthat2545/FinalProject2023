<?php
    header('Content-Type: application/json');
    require_once '../connect.php';

    $maxActivityTypeStmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(at_id, 9) AS SIGNED)) FROM activity_type");
    $maxActivityTypeStmt->execute();
    $maxActivityTypeNumber = $maxActivityTypeStmt->fetchColumn();

    $nextActivityTypeNumber = $maxActivityTypeNumber + 1;

    $at_id = 'ACT-TY-' . sprintf('%03d', $nextActivityTypeNumber);

    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM activity_type WHERE at_id = :at_id");
    $checkStmt->bindParam(":at_id", $at_id, PDO::PARAM_STR);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    $at_name = $_POST['at_name'];
    $details = $_POST['details'];

    $sql = "INSERT INTO activity_type (at_id, at_name, details) 
    VALUES (:at_id, :at_name, :details)";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(":at_id", $at_id, PDO::PARAM_STR);
    $stmt->bindParam(":at_name", $at_name, PDO::PARAM_STR);
    $stmt->bindParam(":details", $details, PDO::PARAM_STR);
    
    try {
        if ($stmt->execute()) {
            // Successful insertion
            $response = [
                'status' => true,
                'message' => 'Create Success'
            ];
            http_response_code(200);
            echo json_encode($response);
        } else {
            // Insertion failed
            $response = [
                'status' => false,
                'message' => 'Create failed'
            ];
            http_response_code(500);
            echo json_encode($response);
        }
    } 
    catch (PDOException $e) {
        // Handle PDOException, including duplicate entry error
        $response = [
            'status' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];  
        http_response_code(500);
        echo json_encode($response);
    } 
?>
