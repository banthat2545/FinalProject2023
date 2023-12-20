
<?php
    header('Content-Type: application/json');
    require_once '../connect.php';
?>

<?php
    #process
    $sql = "SELECT 
                activity.*,
                activity_type.at_name,
                COUNT(register_activity.register_id) AS act_current_registrations
            FROM activity
            LEFT JOIN activity_type ON activity.at_id = activity_type.at_id
            LEFT JOIN register_activity ON activity.act_id = register_activity.act_id
            WHERE activity.act_status = 'ยังไม่จัดกิจกรรม'
            GROUP BY activity.act_id";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $response = [
        'status' => true,
        'message' => 'Get Data Manager Success',
        'response' => []
    ];

    // ดึงข้อมูลจาก $response ไปแสดงผล
    while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
        $response['response'][] = $row;
    }

    http_response_code(200);
    echo json_encode($response);
?>