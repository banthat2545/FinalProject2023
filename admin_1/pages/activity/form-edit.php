<?php
require_once('../authen.php');
if (!isset($_SESSION['MB_ROLE']) || $_SESSION['MB_ROLE'] !== 'คณะกรรมการชมรม') {
    header('Location: ../../login.php');
    exit();
}

if (!isset($_GET['act_id'])) {
    echo "act_id is missing in the URL.";
    exit();
}

$act_id = $_GET['act_id'];

// Use prepared statements to prevent SQL injection
$sql = "SELECT * FROM activity WHERE act_id = :act_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':act_id', $act_id, PDO::PARAM_STR);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    echo "Activity not found.";
    exit();
}

$activityInfo = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการข้อมูลสมาชิก</title>
    <!-- <link rel="shortcut icon" type="image/x-icon" href="../../assets/images/favicon.ico"> -->
    <!-- stylesheet -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="../../plugins/summernote/summernote-bs4.css">
    <link rel="stylesheet" href="../../assets/css/adminlte.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include_once('../includes/sidebar_2.php') ?>
        <div class="content-wrapper pt-3">
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-header border-0 pt-4">
                                    <h4>
                                        <i class="nav-icon fas fa-puzzle-piece"></i>
                                        แก้ไขข้อมูลกิจกรรม
                                    </h4>
                                    <div class="card-header d-flex justify-content-end">
                                        <button class="btn btn-info my-3" onclick="goBack()">
                                            <i class="fas fa-arrow-left"></i>
                                            กลับหน้าหลัก
                                        </button>
                                    </div>
                                </div>
                                <form id="formData">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12 px-1 px-md-5">
                                                <div class="form-group">
                                                    <label for="act_id">รหัสกิจกรรม</label>
                                                    <input type="text" class="form-control" name="act_id" id="act_id" placeholder="รหัสกิจกรรม" value="<?= $activityInfo['act_id']; ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="act_name">ชื่อกิจกรรม</label>
                                                    <input type="text" class="form-control" name="act_name" id="act_name" placeholder="ชื่อกิจกรรม" value="<?= $activityInfo['act_name']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6 px-1 px-md-5">
                                                <div class="form-group">
                                                    <label for="act_date">วันที่จัด</label>
                                                    <input type="text" class="form-control datepicker" name="act_date" id="act_date" placeholder="วันที่" value="<?= $activityInfo['act_date']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="act_total_max">จำนวนผู้ลงทะเบียนเข้าร่วมสูงสุด</label>
                                                    <input type="number" class="form-control" name="act_total_max" id="act_total_max" placeholder="สถานที่" value="<?= $activityInfo['act_total_max']; ?>" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 px-1 px-md-5">
                                                <div class="form-group">
                                                    <label for="act_credit">หน่วยกิต</label>
                                                    <input type="number" class="form-control" name="act_credit" id="act_credit" placeholder="สถานที่" value="<?= $activityInfo['act_credit']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="at_id">ประเภทกิจกรรม</label>
                                                    <select class="form-control" name="at_id" id="at_id" required>
                                                        <option value="" disabled>เลือกประเภทกิจกรรม</option>
                                                        <?php
                                                        $activityTypeQuery = $conn->query("SELECT at_id, at_name FROM activity_type");
                                                        $activityTypes = $activityTypeQuery->fetchAll(PDO::FETCH_ASSOC);
                                                        foreach ($activityTypes as $activity_type) {
                                                            echo "<option value='" . $activity_type['at_id'] . "' " . ($activityInfo['at_id'] === $activity_type['at_id'] ? 'selected' : '') . ">" . $activity_type['at_name'] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                            </div>

                                            <div class="form-group col-md-12 px-1 px-md-5">
                                                <label for="act_location">สถานที่</label>
                                                <textarea id="act_location" class="textarea" name="act_location" placeholder="Place some text here">
                                                    <?= $activityInfo['act_location']; ?>
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary btn-block mx-auto w-50" name="submit">บันทึกข้อมูล</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- SCRIPTS -->
    <script src="../../plugins/jquery/jquery.min.js"></script>
    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="../../plugins/summernote/summernote-bs4.min.js"></script>
    <script src="../../assets/js/adminlte.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>


    <script>
        function goBack() {
            window.history.back();
        }
        $(function() {
            $('#act_location').summernote({
                height: 500,
            });
            $('#formData').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'PUT',
                    url: '../../service/activity/update.php',
                    contentType: 'application/json', // เพิ่ม content type เป็น JSON
                    data: JSON.stringify({
                        act_name: $('#act_name').val(),
                        act_date: $('#act_date').val(),
                        act_credit: $('#act_credit').val(),
                        act_year: $('#act_year').val(),
                        act_total_max: $('#act_total_max').val(),
                        act_location: $('#act_location').val(),
                        at_id: $('#at_id').val(),
                        act_id: $('#act_id').val()
                    }),
                    success: function(resp) {
                        console.log('Success:', resp);
                        Swal.fire({
                            text: 'อัพเดทข้อมูลเรียบร้อย',
                            icon: 'success',
                            confirmButtonText: 'ตกลง',
                        }).then((result) => {
                            location.assign('./');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', xhr.responseText);
                        Swal.fire({
                            text: 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล',
                            icon: 'error',
                            confirmButtonText: 'ตกลง',
                        });
                    },
                    complete: function() {
                        console.log('Request complete');
                    }
                }).done(function(resp) {
                    Swal.fire({
                        text: 'อัพเดทข้อมูลเรียบร้อย',
                        icon: 'success',
                        confirmButtonText: 'ตกลง',
                    }).then((result) => {
                        location.assign('./');
                    });
                });
                $('.datepicker').datepicker({
                    format: 'yyyy-mm-dd', // Set the desired date format
                    autoclose: true,
                    todayHighlight: true
                });
            });
        });
    </script>

</body>

</html>