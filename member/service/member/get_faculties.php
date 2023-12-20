<?php
header('Content-Type: application/json');
require_once '../connect.php';

// ดึงข้อมูลคณะ
$query = $conn->query("SELECT faculty_id, faculty_name FROM faculty");
$data['faculties'] = $query->fetchAll(PDO::FETCH_ASSOC);
$data['status'] = true;

echo json_encode($data);