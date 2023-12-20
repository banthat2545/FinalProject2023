<?php
require_once('../authen.php');
if (!isset($_SESSION['MB_ROLE']) || $_SESSION['MB_ROLE'] !== 'คณะกรรมการชมรม') {
    header('Location: ../../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>จัดการกิจกรรม</title>
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
                            <div class="card">
                                <div class="card-header border-0 pt-4">
                                    <h4>
                                        <i class="nav-icon fas fa-puzzle-piece"></i>
                                        เพิ่มข้อมูลกิจกรรม
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
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label for="act_name">ชื่อกิจกรรม</label>
                                                <input type="text" class="form-control" name="act_name" id="act_name" placeholder="ชื่อกิจกรรม">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="act_credit">หน่วยกิต</label>
                                                    <input type="number" class="form-control" name="act_credit" id="act_credit" placeholder="หน่วยกิตที่จะได้รับ" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="act_total_max">จำนวนผู้ลงทะเบียนเข้าร่วมสูงสุด</label>
                                                    <input type="number" class="form-control" name="act_total_max" id="act_total_max" placeholder="จำนวนผู้ลงทะเบียนเข้าร่วมสูงสุด" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="act_date">วันที่</label>
                                                    <input type="text" class="form-control datepicker" name="act_date" id="act_date" placeholder="วันที่" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="at_id">ประเภทกิจกรรม</label>
                                                    <select class="form-control" name="at_id" id="at_id" required>
                                                        <option value="" disabled selected>เลือกประเภทกิจกรรม</option>
                                                        <?php
                                                        $activityTypeQuery = $conn->query("SELECT at_id, at_name FROM activity_type");
                                                        $activityType = $activityTypeQuery->fetchAll(PDO::FETCH_ASSOC);
                                                        foreach ($activityType as $activity_type) {
                                                            echo "<option value='" . $activity_type['at_id'] . "'>" . $activity_type['at_name'] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="act_location">สถานที่</label>
                                                <textarea id="act_location" class="textarea" name="act_location" placeholder="Place some text here">
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary btn-block mx-auto w-75" name="submit">บันทึกข้อมูล</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- scripts -->
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
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                language: 'th',
                autoclose: true,
                todayHighlight: true
            });
            $('#act_location').summernote({
                height: 300,
            });
            $('#formData').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '../../service/activity/create.php',
                    data: $('#formData').serialize()
                }).done(function(resp) {
                    if (resp.status) {
                        // Success: Show success message and redirect
                        Swal.fire({
                            text: 'เพิ่มข้อมูลเรียบร้อย',
                            icon: 'success',
                            confirmButtonText: 'ตกลง',
                        }).
                        then((result) => {
                            location.assign('./');
                        });
                    } else {
                        // Error: Show error message
                        Swal.fire({
                            text: resp.message,
                            icon: 'error',
                            confirmButtonText: 'ตกลง',
                        });
                    }
                }).fail(function(xhr, status, error) {
                    // AJAX request failed: Show error message
                    Swal.fire({
                        text: 'เกิดข้อผิดพลาดในการส่งข้อมูล: ' + error,
                        icon: 'error',
                        confirmButtonText: 'ตกลง',
                    });
                });
            })
        });
    </script>
</body>

</html>