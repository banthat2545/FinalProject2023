<?php
header('Content-Type: application/json');
require_once '../connect.php';

$act_id = $_POST['act_id'];
$checkData = json_decode($_POST['check_data'], true);

try {
    // Fetch existing check-in data
    $stmt = $conn->prepare("SELECT register_id, check_status FROM register_activity WHERE act_id = :act_id");
    $stmt->bindParam(":act_id", $act_id, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Update check-in data based on the received data
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $registerId = $row['register_id'];
            $existingCheckStatus = $row['check_status'];
            $newCheckStatus = isset($checkData[$registerId]) ? $checkData[$registerId] : $existingCheckStatus;

            // Only update if the new status is different from the existing one
            if ($newCheckStatus != $existingCheckStatus) {
                $updateStmt = $conn->prepare("UPDATE register_activity SET check_status = :check_status WHERE register_id = :register_id AND act_id = :act_id");
                $updateStmt->bindParam(":check_status", $newCheckStatus, PDO::PARAM_INT);
                $updateStmt->bindParam(":register_id", $registerId, PDO::PARAM_STR);
                $updateStmt->bindParam(":act_id", $act_id, PDO::PARAM_STR);
                $updateStmt->execute();
            }
        }

        $response = array('status' => true, 'message' => 'Check-in data updated successfully.');
    } else {
        $response = array('status' => false, 'message' => 'No attendance data found for the specified activity.');
    }
} catch (PDOException $e) {
    $response = array('status' => false, 'message' => 'Error updating check-in data: ' . $e->getMessage());
}

echo json_encode($response);
?>