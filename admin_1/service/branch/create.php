<?php
    header('Content-Type: application/json');
    require_once '../connect.php';

    if (!isset($_POST['faculty_id'])) {
        $response = [
            'status' => false,
            'message' => 'Missing faculty_id in the request'
        ];
        http_response_code(400);
        echo json_encode($response);
        exit();
    }

    $branch_name = $_POST['branch_name'];
    $faculty_id = $_POST['faculty_id'];

    $maxBranchStmt = $conn->prepare("SELECT MAX(SUBSTRING(branch_id, 7)) FROM branch");
    $maxBranchStmt->execute();
    $maxBranchNumber = $maxBranchStmt->fetchColumn();

    $nextBranchNumber = $maxBranchNumber + 1;

    if ($faculty_id == 'FAC-01-001') {
        $branchPrefix = 'BR-01-';
    } elseif ($faculty_id == 'FAC-01-002') {
        $branchPrefix = 'BR-02-';
    } elseif ($faculty_id == 'FAC-01-003') {
        $branchPrefix = 'BR-03-';
    } else {
        $branchPrefix = 'BR-00-';
    }

    $branch_id = $branchPrefix . sprintf('%04d', $nextBranchNumber);

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
        exit(); // จบการทำงาน
    }

    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM branch WHERE branch_id = :branch_id");
    $checkStmt->bindParam(":branch_id", $branch_id, PDO::PARAM_STR);
    $checkStmt->execute();
    $count = $checkStmt->fetchColumn();

    $sql = "INSERT INTO branch (branch_id, branch_name, faculty_id) 
    VALUES (:branch_id, :branch_name, :faculty_id)";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(":branch_id", $branch_id, PDO::PARAM_STR);
    $stmt->bindParam(":branch_name", $branch_name, PDO::PARAM_STR);
    $stmt->bindParam(":faculty_id", $faculty_id, PDO::PARAM_STR);
    
    try {
        if ($stmt->execute()) {
            // Successful insertion
            $response = [
                'status' => true,
                'message' => 'Create Success'
            ];
            http_response_code(200);
            echo json_encode($response);
        } else {
            // Insertion failed
            $response = [
                'status' => false,
                'message' => 'Create failed'
            ];
            http_response_code(500);
            echo json_encode($response);
        }
    } 
    catch (PDOException $e) {
        // Handle PDOException, including duplicate entry error
        $response = [
            'status' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];  
        http_response_code(500);
        echo json_encode($response);
    } 
?>
