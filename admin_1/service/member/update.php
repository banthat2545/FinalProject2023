<?php
    header('Content-Type: application/json');
    require_once '../connect.php';

    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['status' => false, 'message' => 'Method Not Allowed']);
        exit();
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['branch_id'])) {
        $response = [
            'status' => false,
            'message' => 'Missing branch_id in the request'
        ];
        http_response_code(400);
        echo json_encode($response);
        exit();
    }

    $mb_name = $data['mb_name'];
    $mb_email = $data['mb_email'];
    $mb_tel = $data['mb_tel'];
    $mb_role = $data['mb_role'];
    $branch_id = $data['branch_id'];
    $mb_id = $data['mb_id'];

    $checkBranchStmt = $conn->prepare("SELECT COUNT(*) FROM branch WHERE branch_id = :branch_id");
    $checkBranchStmt->bindParam(":branch_id", $branch_id, PDO::PARAM_STR);
    $checkBranchStmt->execute();
    $countBranch = $checkBranchStmt->fetchColumn();

    if ($countBranch == 0) {
        // ไม่พบ faculty_id หรือ branch_id ในตาราง faculty หรือ branch
        $response = [
            'status' => false,
            'message' => 'Invalid faculty_id or branch_id'
        ];
        http_response_code(400);
        echo json_encode($response);
        exit();
    }

    $stmt = $conn->prepare("UPDATE member 
                            SET mb_name = :mb_name, mb_email = :mb_email, mb_tel = :mb_tel, 
                                mb_role = :mb_role, branch_id = :branch_id
                            WHERE mb_id = :mb_id");

    $stmt->bindParam(":mb_name", $mb_name, PDO::PARAM_STR);
    $stmt->bindParam(":mb_email", $mb_email, PDO::PARAM_STR);
    $stmt->bindParam(":mb_tel", $mb_tel, PDO::PARAM_STR);
    $stmt->bindParam(":mb_role", $mb_role, PDO::PARAM_STR);
    $stmt->bindParam(":branch_id", $branch_id, PDO::PARAM_STR);
    $stmt->bindParam(":mb_id", $mb_id, PDO::PARAM_STR);

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
?>