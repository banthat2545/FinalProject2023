<?php
header('Content-Type: application/json');
require_once '../connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method Not Allowed']);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

$faculty_name = $data['faculty_name'];
$faculty_id = $data['faculty_id'];

$stmt = $conn->prepare("UPDATE faculty SET faculty_name = :faculty_name
            WHERE faculty_id = :faculty_id");

$stmt->bindParam(":faculty_name", $faculty_name, PDO::PARAM_STR);
$stmt->bindParam(":faculty_id", $faculty_id, PDO::PARAM_STR);

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
