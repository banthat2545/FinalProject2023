<?php
header('Content-Type: application/json');
require_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['act_id']) || !isset($_POST['check_data'])) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Invalid parameters.'));
        exit;
    }

    $act_id = $_POST['act_id'];
    $checkData = json_decode($_POST['check_data'], true);

    try {
        foreach ($checkData as $registerId => $checkStatus) {
            $stmt = $conn->prepare("UPDATE register_activity SET check_status = :check_status WHERE act_id = :act_id AND register_id = :register_id");
            $stmt->bindParam(":check_status", $checkStatus, PDO::PARAM_INT);
            $stmt->bindParam(":act_id", $act_id, PDO::PARAM_STR);
            $stmt->bindParam(":register_id", $registerId, PDO::PARAM_STR);
            $stmt->execute();
        }

        // Update register_status to 'เช็คชื่อแล้ว'
        $updateStatusStmt = $conn->prepare("UPDATE register_activity SET register_status = 'เช็คชื่อแล้ว' WHERE act_id = :act_id");
        $updateStatusStmt->bindParam(":act_id", $act_id, PDO::PARAM_STR);
        $updateStatusStmt->execute();

        // Update act_status to 'จัดกิจกรรมแล้ว'
        $updateActStatusStmt = $conn->prepare("UPDATE activity SET act_status = 'จัดกิจกรรมแล้ว' WHERE act_id = :act_id");
        $updateActStatusStmt->bindParam(":act_id", $act_id, PDO::PARAM_STR);
        $updateActStatusStmt->execute();

        echo json_encode(array('status' => true, 'message' => 'Check-in data saved successfully.'));
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array('status' => false, 'message' => 'Error saving check-in data.'));
    }
} else {
    http_response_code(405);
    echo json_encode(array('status' => false, 'message' => 'Method Not Allowed.'));
}
?>
