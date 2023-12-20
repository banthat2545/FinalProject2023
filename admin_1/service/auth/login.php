<?php

header('Content-Type: application/json');
require_once '../connect.php';

// print_r($_POST);

/**
 * $_POST['mb_username']
 * $_POST['mb_password']
 */

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $stmt = $conn->prepare("SELECT * FROM member WHERE mb_id = :mb_id");
    $stmt->execute(array(":mb_id" => $_POST['mb_id']));
    $row = $stmt->fetch(PDO::FETCH_OBJ);

    //      T
    if (!empty($row) && password_verify($_POST['mb_password'], $row->mb_password)) {
        $_SESSION['MB_ID'] = $row->mb_id;
        $_SESSION['MB_NAME'] = $row->mb_name;
        $_SESSION['MB_EMAIL'] = $row->mb_email;
        $_SESSION['MB_TEL'] = $row->mb_tel;
        $_SESSION['MB_ROLE'] = $row->mb_role;
        $_SESSION['MB_LOGIN'] = $row->updated_at;

        $updateStmt = $conn->prepare("UPDATE member SET updated_at = :updated_at WHERE mb_id = :mb_id");
        $updateStmt->execute(array(":updated_at" => date("Y-m-d H:i:s"), ":mb_id" => $row->mb_id));

        if ($updateStmt->rowCount() > 0) {
            http_response_code(200);
            // Redirect based on user role
            if ($row->mb_role == 'ประธานชมรม') {
                echo json_encode(array('status' => true, 'message' => 'Login Success!', 'role' => $row->mb_role));
            } elseif ($row->mb_role == 'คณะกรรมการชมรม') {
                echo json_encode(array('status' => true, 'message' => 'Login Success!', 'role' => $row->mb_role));
            } else {
                // Handle other roles or situations if needed
                echo json_encode(array('status' => false, 'message' => 'Invalid user role!'));
            }
        } else {
            http_response_code(500); // Changed to 500 for server error
            echo json_encode(array('status' => false, 'message' => 'Failed to update login timestamp!'));
        }
    } else {
        http_response_code(401);
        echo json_encode(array('status' => false, 'massage' => 'Unauthorized!'));
    }
} else {
    http_response_code(405);
    echo json_encode(array('status' => false, 'massage' => 'Method Not Allowed'));
}
?>