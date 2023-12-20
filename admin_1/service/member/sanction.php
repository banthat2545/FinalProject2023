<?php
header('Content-Type: application/json');
require_once '../connect.php';
?>

<?php
#process
$sql = "SELECT member.*, branch.branch_name, faculty.faculty_name FROM member
        LEFT JOIN branch ON member.branch_id = branch.branch_id
        LEFT JOIN faculty ON branch.faculty_id = faculty.faculty_id
        WHERE member.mb_role = 'รอการอนุมัติ'";

$stmt = $conn->prepare($sql);
$stmt->execute();

$response = [
    'status' => true,
    'message' => 'Get Data Manager Success'
];

// ดึงข้อมูลจาก $response ไปแสดงผล
while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
    $response['response'][] = $row;
}

http_response_code(200);
echo json_encode($response);
?>