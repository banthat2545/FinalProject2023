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

// Fetch act_id from POST data
if (isset($_POST['act_id'])) {
    $act_id = $_POST['act_id'];
} else {
    // Handle the case when act_id is not set
}

// Check if the member has already registered for the same activity
$checkExistingRegistrationStmt = $conn->prepare("SELECT COUNT(*) FROM register_activity WHERE mb_id = :mb_id AND act_id = :act_id");
$checkExistingRegistrationStmt->bindParam(":mb_id", $mb_id, PDO::PARAM_STR);
$checkExistingRegistrationStmt->bindParam(":act_id", $act_id, PDO::PARAM_STR);
$checkExistingRegistrationStmt->execute();
$existingRegistrationsCount = $checkExistingRegistrationStmt->fetchColumn();

if ($existingRegistrationsCount > 0) {
    // Member has already registered for this activity
    $response = [
        'status' => false,
        'message' => 'You have already registered for this activity.'
    ];
    http_response_code(400); // Bad Request
    echo json_encode($response);
    exit();
}

$maxRegisterActivityStmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(register_id , 9) AS SIGNED)) FROM register_activity");
$maxRegisterActivityStmt->execute();
$maxRegisterActivityNumber = $maxRegisterActivityStmt->fetchColumn();

$nextRegisterActivityNumber = $maxRegisterActivityNumber + 1;

$register_id = 'RE-' . sprintf('%07d', $nextRegisterActivityNumber);

$check_status = 0;
$register_status = "ยังไม่เช็คชื่อ";

// Fetch act_id from POST data
if(isset($_POST['act_id'])) {
    $act_id = $_POST['act_id'];
} else {
    // Handle the case when act_id is not set
}


$sql = "INSERT INTO register_activity (register_id, register_date, check_status, register_status, mb_id, act_id) 
                VALUES (:register_id, NOW(), :check_status, :register_status, :mb_id, :act_id)";

$stmt = $conn->prepare($sql);

$stmt->bindParam(":register_id", $register_id, PDO::PARAM_STR);
$stmt->bindParam(":check_status", $check_status, PDO::PARAM_INT);
$stmt->bindParam(":register_status", $register_status, PDO::PARAM_STR);
$stmt->bindParam(":mb_id", $mb_id, PDO::PARAM_STR);
$stmt->bindParam(":act_id", $act_id, PDO::PARAM_STR);

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
