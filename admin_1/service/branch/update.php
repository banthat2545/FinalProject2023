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

    if (!isset($data['faculty_id'])) {
        $response = [
            'status' => false,
            'message' => 'Missing faculty_id in the request'
        ];
        http_response_code(400);
        echo json_encode($response);
        exit();
    }
    
    $faculty_id = $data['faculty_id'];
    $branch_name = $data['branch_name'];
    $branch_id = $data['branch_id'];

    $checkFacultyStmt = $conn->prepare("SELECT COUNT(*) FROM faculty WHERE faculty_id = :faculty_id");
    $checkFacultyStmt->bindParam(":faculty_id", $faculty_id, PDO::PARAM_STR);
    $checkFacultyStmt->execute();
    $countFaculty = $checkFacultyStmt->fetchColumn();

    if ($countFaculty == 0) {
        $response = [
            'status' => false,
            'message' => 'Invalid faculty_id'
        ];
        http_response_code(400);
        echo json_encode($response);
        exit();
    }

    $stmt = $conn->prepare("UPDATE branch 
                            SET branch_name = :branch_name, faculty_id = :faculty_id
                            WHERE branch_id = :branch_id");

    $stmt->bindParam(":branch_name", $branch_name, PDO::PARAM_STR);
    $stmt->bindParam(":faculty_id", $faculty_id, PDO::PARAM_STR);
    $stmt->bindParam(":branch_id", $branch_id, PDO::PARAM_STR);

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