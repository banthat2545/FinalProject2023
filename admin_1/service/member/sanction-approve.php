<?php
    header('Content-Type: application/json');
    require_once '../connect.php';

    // Check if the request method is PUT
    if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['status' => false, 'message' => 'Method Not Allowed']);
        exit();
    }

    // Retrieve data from the input stream
    $data = json_decode(file_get_contents("php://input"), true);

    // Validate and sanitize input
    $mb_id = isset($data['mb_id']) ? htmlspecialchars(trim($data['mb_id'])) : null;
    $action = isset($data['action']) ? htmlspecialchars(trim($data['action'])) : null;

    // Check if the required parameters are present
    if (!$mb_id || !$action) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => false, 'message' => 'Invalid or missing parameters']);
        exit();
    }

    // Perform the action based on the provided 'action' parameter
    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE member SET mb_role = 'สมาชิก' WHERE mb_id = :mb_id");
        $stmt->bindParam(":mb_id", $mb_id, PDO::PARAM_STR);

        try {
            $stmt->execute();
            $affectedRows = $stmt->rowCount();

            if ($affectedRows > 0) {
                $response = ['status' => true, 'message' => 'สมาชิก'];
                http_response_code(200);
            } else {
                $response = ['status' => false, 'message' => 'ไม่พบรายการหรืออนุมัติแล้ว'];
                http_response_code(404); // Not Found
            }
        } catch (PDOException $e) {
            $response = ['status' => false, 'message' => 'Database Error: ' . $e->getMessage()];
            http_response_code(500); // Internal Server Error
        }
    } else {
        http_response_code(400); // Bad Request
        $response = ['status' => false, 'message' => 'Invalid action parameter'];
    }

    echo json_encode($response);
?>
