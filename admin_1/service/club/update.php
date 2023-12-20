<?php
header('Content-Type: application/json');
require_once '../connect.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method Not Allowed']);
    exit();
}

if (empty($_POST)) {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'NO Data sent']);
    exit();
}

$club_id = $_POST['club_id'];
$club_name = $_POST['club_name'];
$details = $_POST['details'];
$club_bureau = $_POST['club_bureau'];

if (isset($_FILES['club_image'])) {
    $club_image = basename($_FILES['club_image']['name']);
    $type = $_FILES['club_image']['type'];
    $size = $_FILES['club_image']['size'];
    $temp = $_FILES['club_image']['tmp_name'];

    $directory = "upload/";
    $path = $directory . $club_image;

    $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];

    if (in_array($type, $allowedTypes)) {
        if (!file_exists($path)) {
            if ($size < 5000000) {
                // unlink("upload/" . $data['club_image']);
                move_uploaded_file($temp, $path);
            } else {
                $errorMsg = "ไฟล์ของคุณมีขนาดใหญ่เกินไป กรุณาอัพโหลดไฟล์ที่มีขนาดน้อยกว่า 5MB";
            }
        } else {
            $errorMsg = "ไฟล์นี้มีอยู่แล้ว กรุณาตรวจสอบโฟลเดอร์ที่อัพโหลด";
        }
    } else {
        $errorMsg = "รองรับเฉพาะไฟล์รูปภาพในรูปแบบ JPG, JPEG, PNG & GIF เท่านั้น";
    }
}

if (!isset($errorMsg)) {

    if (isset($club_image)) {
        $club_image_param = $club_image;
    } else {
        $club_image_param = null;
    }

    if (isset($_FILES['club_image'])) {
        $stmt = $conn->prepare("UPDATE club SET club_name = :club_name, details = :details, 
                                            club_bureau = :club_bureau, club_image = :club_image 
                                        WHERE club_id = :club_id");

        $stmt->bindParam(":club_name", $club_name, PDO::PARAM_STR);
        $stmt->bindParam(":details", $details, PDO::PARAM_STR);
        $stmt->bindParam(":club_bureau", $club_bureau, PDO::PARAM_STR);
        $stmt->bindParam(':club_image', $club_image, PDO::PARAM_STR);
        $stmt->bindParam(":club_id", $club_id, PDO::PARAM_STR);
    } else {
        $stmt = $conn->prepare("UPDATE club SET club_name = :club_name, details = :details, 
                                        club_bureau = :club_bureau 
                                    WHERE club_id = :club_id");

        $stmt->bindParam(":club_name", $club_name, PDO::PARAM_STR);
        $stmt->bindParam(":details", $details, PDO::PARAM_STR);
        $stmt->bindParam(":club_bureau", $club_bureau, PDO::PARAM_STR);
        $stmt->bindParam(":club_id", $club_id, PDO::PARAM_STR);
    }

    if ($stmt->execute()) {
        $response = [
            'status' => true,
            'message' => 'Update Success',
        ];

        http_response_code(200);
        echo json_encode($response);
    } else {
        $response = [
            'status' => false,
            'message' => 'Update Failed',
        ];
        http_response_code(500); // Internal Server Error
        echo json_encode($response);
    }
} else {
    $response = [
        'status' => false,
        'message' => $errorMsg,
    ];
    http_response_code(400); // Bad Request
    echo json_encode($response);
}
