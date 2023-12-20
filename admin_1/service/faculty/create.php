<?php
header('Content-Type: application/json');
require_once '../connect.php';

$maxFacultyStmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(faculty_id, 9) AS SIGNED)) FROM faculty");
$maxFacultyStmt->execute();
$maxFacultyNumber = $maxFacultyStmt->fetchColumn();

$nextFacultyNumber = $maxFacultyNumber + 1;

$faculty_id = 'FAC-01-' . sprintf('%03d', $nextFacultyNumber);

$faculty_name = $_POST['faculty_name'];

$checkFacultyStmt = $conn->prepare("SELECT COUNT(*) FROM faculty WHERE faculty_id = :faculty_id");
$checkFacultyStmt->bindParam(":faculty_id", $faculty_id, PDO::PARAM_STR);
$checkFacultyStmt->execute();
$countFaculty = $checkFacultyStmt->fetchColumn();

if ($countFaculty > 0) {
    $response = [
        'status' => false,
        'message' => 'Faculty ID already exists'
    ];
    http_response_code(400);
    echo json_encode($response);
    exit();
}

$sql = "INSERT INTO faculty (faculty_id, faculty_name) 
VALUES (:faculty_id, :faculty_name)";

$stmt = $conn->prepare($sql);

$stmt->bindParam(":faculty_id", $faculty_id, PDO::PARAM_STR);
$stmt->bindParam(":faculty_name", $faculty_name, PDO::PARAM_STR);

try {
    if ($stmt->execute()) {
        $response = [
            'status' => true,
            'message' => 'Create Success'
        ];
        http_response_code(200);
        echo json_encode($response);
    } else {
        $response = [
            'status' => false,
            'message' => 'Create failed'
        ];
        http_response_code(500);
        echo json_encode($response);
    }
} catch (PDOException $e) {
    $response = [
        'status' => false,
        'message' => 'Error: ' . $e->getMessage()
    ];
    http_response_code(500);
    echo json_encode($response);
}
?>
