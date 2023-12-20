<?php
    session_start();
    header('Content-Type: application/json');
    require_once '../connect.php';

    if (!isset($_SESSION['MB_ID'])) {
        // Handle unauthenticated user
        $response = [
            'status' => false,
            'message' => 'User not authenticated. MB_ID not found in the session.'
        ];
        http_response_code(401);
        echo json_encode($response);
        exit();
    }

    $mb_id = $_SESSION['MB_ID'];

    $maxActivityStmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(act_id, 9) AS SIGNED)) FROM activity");
    $maxActivityStmt->execute();
    $maxActivityNumber = $maxActivityStmt->fetchColumn();

    $nextActivityNumber = $maxActivityNumber + 1;

    $act_id = 'ACT-01-' . sprintf('%03d', $nextActivityNumber);

    if (!isset($_POST['at_id'])) {
        $response = [
            'status' => false,
            'message' => 'Missing at_id in the request'
        ];
        http_response_code(400);
        echo json_encode($response);
        exit();
    }

    $at_id = $_POST['at_id'];
    $act_name = $_POST['act_name'];
    $act_date = $_POST['act_date'];
    $act_location = $_POST['act_location'];
    $act_credit = $_POST['act_credit'];
    $act_total_max = $_POST['act_total_max'];
    $act_status = "ยังไม่จัดกิจกรรม";

    $checkActivityTypeStmt = $conn->prepare("SELECT COUNT(*) FROM activity_type WHERE at_id = :at_id");
    $checkActivityTypeStmt->bindParam(":at_id", $at_id, PDO::PARAM_STR);
    $checkActivityTypeStmt->execute();
    $countActivityType = $checkActivityTypeStmt->fetchColumn();

    if ($countActivityType == 0) {
        $response = [
            'status' => false,
            'message' => 'Invalid at_id'
        ];
        http_response_code(400);
        echo json_encode($response);
        exit(); // End execution
    }


    // Check if act_id already exists in the activity table
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM activity WHERE act_id = :act_id");
    $checkStmt->bindParam(":act_id", $act_id, PDO::PARAM_STR);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    if ($count > 0) {
        $response = [
            'status' => false,
            'message' => 'Duplicate act_id. Use a different act_id.'
        ];
        http_response_code(400);
        echo json_encode($response);
        exit(); // End execution
    }

    // Use the correct SQL query with unique parameter names
    $sql = "INSERT INTO activity (act_id, act_name, act_date, act_location, act_credit, act_status, at_id, act_total_max, mb_id) 
            VALUES (:act_id, :act_name, :act_date, :act_location, :act_credit, :act_status, :at_id, :act_total_max, :mb_id)";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(":act_id", $act_id, PDO::PARAM_STR);
    $stmt->bindParam(":act_name", $act_name, PDO::PARAM_STR);
    $stmt->bindParam(":act_date", $act_date, PDO::PARAM_STR); // Assuming act_date is a string in 'YYYY-MM-DD' format
    $stmt->bindParam(":act_location", $act_location, PDO::PARAM_STR);
    $stmt->bindParam(":act_credit", $act_credit, PDO::PARAM_INT); // Assuming act_credit is an integer
    $stmt->bindParam(":act_total_max", $act_total_max, PDO::PARAM_INT); 
    $stmt->bindParam(":act_status", $act_status, PDO::PARAM_STR);
    $stmt->bindParam(":at_id", $at_id, PDO::PARAM_STR);
    $stmt->bindParam(":mb_id", $mb_id, PDO::PARAM_STR);

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
    } catch (PDOException $e) {
        // Handle PDOException, including duplicate entry error
        $response = [
            'status' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
        http_response_code(500);
        echo json_encode($response);
    }
?>
