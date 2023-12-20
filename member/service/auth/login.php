<?php
header('Content-Type: application/json');
require_once '../connect.php';

function getUserData($mb_id, $conn)
{
    $stmt = $conn->prepare("SELECT * FROM member WHERE mb_id = :mb_id");
    $stmt->execute(array(":mb_id" => $mb_id));
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user_data;
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $mb_id = $_POST['mb_id'];
    $mb_password = $_POST['mb_password'];

    $stmt = $conn->prepare("SELECT * FROM member WHERE mb_id = :mb_id");
    $stmt->execute(array(":mb_id" => $mb_id));
    $row = $stmt->fetch(PDO::FETCH_OBJ);

    if ($row) {
        if (password_verify($mb_password, $row->mb_password)) {
            $_SESSION['MB_ID'] = $row->mb_id;
            $_SESSION['MB_NAME'] = $row->mb_name;
            $_SESSION['MB_EMAIL'] = $row->mb_email;
            $_SESSION['MB_TEL'] = $row->mb_tel;
            $_SESSION['MB_ROLE'] = $row->mb_role;
            $_SESSION['MB_LOGIN'] = $row->updated_at;

            $updateStmt = $conn->prepare("UPDATE member SET updated_at = :updated_at WHERE mb_id = :mb_id");
            $updateStmt->execute(array(":updated_at" => date("Y-m-d H:i:s"), ":mb_id" => $row->mb_id));

            if ($updateStmt->rowCount() > 0) {
                $user_data = getUserData($mb_id, $conn);

                if ($user_data) {
                    // Check if the user is approved
                    if ($user_data['mb_role'] == 'รอการอนุมัติ') {
                        // User is not approved
                        $response = [
                            'role' => 'รอการอนุมัติ',
                            'status' => true,
                            'message' => 'บัญชีของคุณยังไม่ได้รับการอนุมัติ'
                        ];
                    } elseif ($user_data['mb_role'] == 'สมาชิก') {
                        // Login is successful
                        $response = [
                            'role' => 'สมาชิก',
                            'status' => true,
                            'message' => 'Login Success!'
                        ];
                    } else {
                        http_response_code(402);
                        $response = ['message' => 'ไม่ผ่านการอนุมัติ สมัครใหม่อีกครั้ง!'];
                    }

                    echo json_encode($response);
                } else {
                    // User data is empty
                    http_response_code(500);
                    echo json_encode(['status' => false, 'message' => 'Failed to retrieve user data']);
                }
            } else {
                // Failed to update login timestamp
                http_response_code(500);
                echo json_encode(['status' => false, 'message' => 'Failed to update login timestamp']);
            }
        } else {
            // Incorrect password
            http_response_code(401);
            echo json_encode(['status' => false, 'message' => 'Incorrect password']);
        }
    } else {
        // mb_id not found
        http_response_code(402);
        echo json_encode(['status' => false, 'message' => 'mb_id not found']);
    }
} else {
    // Method Not Allowed
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method Not Allowed']);
}
?>