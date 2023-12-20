<?php
header('Content-Type: application/json');
require_once '../connect.php';

if (!isset($_GET['faculty_id'])) {
    $response = [
        'status' => false,
        'message' => 'Missing faculty_id in the request'
    ];
    http_response_code(400);
    echo json_encode($response);
    exit();
}

$faculty_id = $_GET['faculty_id'];

// ดึงข้อมูลสาขาที่เกี่ยวข้องกับคณะที่กำหนด
$query = $conn->prepare("
    SELECT branch_id, branch_name
    FROM branch
    WHERE faculty_id = :faculty_id
");
$query->bindParam(":faculty_id", $faculty_id, PDO::PARAM_STR);
$query->execute();

$data['branches'] = $query->fetchAll(PDO::FETCH_ASSOC);
$data['status'] = true;

echo json_encode($data);
