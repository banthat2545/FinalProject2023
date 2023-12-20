<?php
    header('Content-Type: application/json');
    require_once '../connect.php';

    if (!isset($_POST['branch_id'])) {
        $response = [
            'status' => false,
            'message' => 'Missing branch_id in the request'
        ];
        http_response_code(400);
        echo json_encode($response);
        exit();
    }

    $mb_id = $_POST['mb_id'];
    $mb_password = $_POST['mb_password'];
    $mb_password_confirm = $_POST['mb_password_confirm'];
    $mb_name = $_POST['mb_name'];
    $mb_email = $_POST['mb_email'];
    $mb_tel = $_POST['mb_tel'];
    $mb_role = 'รอการอนุมัติ';
    $branch_id = $_POST['branch_id'];
    $club_id = "CB-01-0001";

    if ($mb_password !== $mb_password_confirm) {
        $response = [
            'status' => false,
            'message' => 'รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน'
        ];
        http_response_code(400);
        echo json_encode($response);
        exit();
    }

    // ตรวจสอบว่า branch_id ที่จะ INSERT มีอยู่ในตาราง branch หรือไม่
    $checkBranchStmt = $conn->prepare("SELECT COUNT(*) FROM branch WHERE branch_id = :branch_id");
    $checkBranchStmt->bindParam(":branch_id", $branch_id, PDO::PARAM_STR);
    $checkBranchStmt->execute();
    $countBranch = $checkBranchStmt->fetchColumn();

    if ($countBranch == 0) {
        // ไม่พบ branch_id ในตาราง branch
        $response = [
            'status' => false,
            'message' => 'Invalid branch_id'
        ];
        http_response_code(400);
        echo json_encode($response);
        exit();
    }


    // ตรวจสอบว่า mb_id ซ้ำหรือไม่
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM member WHERE mb_id = :mb_id");
    $checkStmt->bindParam(":mb_id", $mb_id, PDO::PARAM_STR);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    if ($count > 0) {
        // mb_id ซ้ำ
        $response = [
            'status' => false,
            'message' => 'Duplicate mb_id'
        ];
        http_response_code(400);
        echo json_encode($response);
        exit(); // จบการทำงาน
    }

    // ทำการ INSERT ข้อมูล
    $sql = "INSERT INTO member (mb_id, mb_password, mb_name,
            mb_email, mb_tel, mb_role, branch_id, created_at, updated_at, club_id) 
            VALUES (:mb_id, :mb_password, :mb_name, :mb_email, :mb_tel, :mb_role, :branch_id, NOW(), NOW(), :club_id)";

    $stmt = $conn->prepare($sql);
    $hashPassword = password_hash($mb_password, PASSWORD_DEFAULT);

    $stmt->bindParam(":mb_id", $mb_id, PDO::PARAM_STR);
    $stmt->bindParam(":mb_password", $hashPassword, PDO::PARAM_STR);
    $stmt->bindParam(":mb_name", $mb_name, PDO::PARAM_STR);
    $stmt->bindParam(":mb_email", $mb_email, PDO::PARAM_STR);
    $stmt->bindParam(":mb_tel", $mb_tel, PDO::PARAM_STR);
    $stmt->bindParam(":mb_role", $mb_role, PDO::PARAM_STR);
    $stmt->bindParam(":branch_id", $branch_id, PDO::PARAM_STR);
    $stmt->bindParam(":club_id", $club_id, PDO::PARAM_STR);

    try {
        if ($stmt->execute()) {
            // การเพิ่มข้อมูลสำเร็จ
            $response = [
                'status' => true,
                'message' => 'Create Success'
            ];
            http_response_code(200);
            echo json_encode($response);
        } else {
            // การเพิ่มข้อมูลล้มเหลว
            $response = [
                'status' => false,
                'message' => 'Create failed'
            ];
            http_response_code(500);
            echo json_encode($response);
        }
    } catch (PDOException $e) {
        error_log('PDOException: ' . $e->getMessage());
        // จัดการ PDOException, รวมถึงข้อผิดพลาดที่เกิดจากการซ้ำ
        $response = [
            'status' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
        http_response_code(500);
        echo json_encode($response);
    }
?>