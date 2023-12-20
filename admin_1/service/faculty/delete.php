<?php
    header('Content-Type: application/json');
    require_once '../connect.php';
    ?>

    <?php
    // $mb_id = $_POST['mb_id'];
    $data = json_decode(file_get_contents("php://input"), true);
    $faculty_id = $data['faculty_id'];

    $sql = "DELETE FROM faculty WHERE faculty_id = :faculty_id";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(":faculty_id", $faculty_id, PDO::PARAM_STR);

    // Execute the statement to perform the deletion
    try {
        $stmt->execute();

        $response = [
            'status' => true,
            'message' => 'Delete Success'
        ];
        http_response_code(204);
        echo json_encode($response);
    } catch (PDOException $e) {
        // Handle errors if the deletion fails
        $response = [
            'status' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
        http_response_code(500);
        echo json_encode($response);
        echo 'Error: ' . $e->getMessage();
    }
?>