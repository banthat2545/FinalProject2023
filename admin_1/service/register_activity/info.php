<?php
    header('Content-Type: application/json');
    require_once '../connect.php';
    
    $act_id = $_GET['act_id'];
    
    $sql = "SELECT register_activity.*, member.mb_name, branch.branch_name FROM register_activity 
            LEFT JOIN member ON register_activity.mb_id = member.mb_id
            LEFT JOIN branch ON member.branch_id = branch.branch_id
            WHERE act_id = :act_id AND register_status = 'เช็คชื่อแล้ว' ";
            
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':act_id', $act_id, PDO::PARAM_STR);
    $stmt->execute();
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($result) {
        $response = [
            'status' => true,
            'message' => 'Get Data Manager Success',
            'data' => $result
        ];
    } else {
        $response = [
            'status' => false,
            'message' => 'No data found for the specified act_id'
        ];
    }
    
    http_response_code(200);
    echo json_encode($response);
?>