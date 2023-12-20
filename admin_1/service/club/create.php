<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    header('Content-Type: application/json');
    require_once '../connect.php';

    $maxClubStmt = $conn->prepare("SELECT MAX(CAST(SUBSTRING(club_id, 7) AS SIGNED)) FROM club");
    $maxClubStmt->execute();
    $maxClubNumber = $maxClubStmt->fetchColumn();
    $nextClubNumber = $maxClubNumber + 1;
    $club_id = 'CB-01-' . sprintf('%04d', $nextClubNumber);

    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM club WHERE club_id = :club_id");
    $checkStmt->bindParam(":club_id", $club_id, PDO::PARAM_STR);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    $club_name = $_POST['club_name'];
    $details = $_POST['details'];
    $club_bureau = $_POST['club_bureau'];

    $club_image = basename($_FILES['txt_file']['name']);
    $type = $_FILES['txt_file']['type'];
    $size = $_FILES['txt_file']['size'];
    $temp = $_FILES['txt_file']['tmp_name'];

    $uploadPath = 'upload/' . $club_image;

    $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
    if (in_array($type, $allowedTypes)) {
        if (!file_exists($uploadPath)) {
            if ($size < 5000000) {
                move_uploaded_file($temp, $uploadPath);
            } else {
                $errorMsg = "ไฟล์ของคุณมีขนาดใหญ่เกินไป กรุณาอัพโหลดไฟล์ที่มีขนาดน้อยกว่า 5MB";
            }
        } else {
            $errorMsg = "ไฟล์นี้มีอยู่แล้ว กรุณาตรวจสอบโฟลเดอร์ที่อัพโหลด";
        }
    } else {
        $errorMsg = "รองรับเฉพาะไฟล์รูปภาพในรูปแบบ JPG, JPEG, PNG & GIF เท่านั้น";
    }

    if (!isset($errorMsg)) {
        $sql = "INSERT INTO club (club_id, club_name, details, club_bureau, club_image) 
                VALUES (:club_id, :club_name, :details, :club_bureau, :club_image)";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(":club_id", $club_id, PDO::PARAM_STR);
        $stmt->bindParam(":club_name", $club_name, PDO::PARAM_STR);
        $stmt->bindParam(":details", $details, PDO::PARAM_STR);
        $stmt->bindParam(":club_bureau", $club_bureau, PDO::PARAM_STR);
        $stmt->bindParam(":club_image", $club_image, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $response = [
                'status' => true,
                'message' => 'เพิ่มข้อมูลสำเร็จ'
            ];
            http_response_code(200);
            echo json_encode($response);
        } else {
            $response = [
                'status' => false,
                'message' => 'เพิ่มข้อมูลล้มเหลว'
            ];
            http_response_code(500);
            echo json_encode($response);
        }
    } else {
        $response = [
            'status' => false,
            'message' => $errorMsg
        ];
        http_response_code(500);
        echo json_encode($response);
    }
?>
