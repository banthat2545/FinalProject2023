<?php
header('Content-Type: application/json');
require_once '../connect.php';

// Check if the request method is PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => false, 'message' => 'Method Not Allowed']);
    exit();
}

// Retrieve data from the input stream
$data = json_decode(file_get_contents("php://input"), true);

$at_name = $data['at_name'];
$details = $data['details'];
$at_id = $data['at_id'];

$stmt = $conn->prepare("UPDATE activity_type SET at_name = :at_name, details = :details
            WHERE at_id = :at_id");

$stmt->bindParam(":at_name", $at_name, PDO::PARAM_STR);
$stmt->bindParam(":details", $details, PDO::PARAM_STR);
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
