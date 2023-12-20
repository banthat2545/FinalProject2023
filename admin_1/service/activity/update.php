<?php
    header('Content-Type: application/json');
    require_once '../connect.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['status' => false, 'message' => 'Method Not Allowed']);
        exit();
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['at_id'])) {
        $response = [
            'status' => false,
            'message' => 'Missing at_id in the request'
        ];
        http_response_code(400);
        echo json_encode($response);
        exit();
    }

    $at_id = $data['at_id'];
    $act_name = $data['act_name'];
    $act_date = $data['act_date'];
    $act_credit = $data['act_credit'];
    $act_location = $data['act_location'];
    $act_total_max = $data['act_total_max'];
    $act_id = $data['act_id'];

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
        exit();
    }

    $stmt = $conn->prepare("UPDATE activity 
                        SET act_name = :act_name, act_date = :act_date,
                        act_credit = :act_credit, act_location = :act_location, act_total_max = :act_total_max, at_id = :at_id
                        WHERE act_id = :act_id");

    $stmt->bindParam(":act_id", $act_id, PDO::PARAM_STR);
    $stmt->bindParam(":act_name", $act_name, PDO::PARAM_STR);
    $stmt->bindParam(":act_date", $act_date, PDO::PARAM_STR); // Assuming act_date is a string in 'YYYY-MM-DD' format
    $stmt->bindParam(":act_location", $act_location, PDO::PARAM_STR);
    $stmt->bindParam(":act_credit", $act_credit, PDO::PARAM_INT); // Assuming act_credit is an integer
    $stmt->bindParam(":act_total_max", $act_total_max, PDO::PARAM_INT); 
    $stmt->bindParam(":at_id", $at_id, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $response = [
            'status' => true,
            'message' => 'Update Success'
        ];
        http_response_code(200);
        echo json_encode($response);
    } else {
        $response = [
            'status' => false,
            'message' => 'Update Failed'
        ];
        http_response_code(500); // Internal Server Error
        echo json_encode($response);
    }
?>
