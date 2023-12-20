<?php
    require_once('../authen.php');
    if (!isset($_SESSION['MB_ROLE']) || $_SESSION['MB_ROLE'] !== 'ประธานชมรม') {
        header('Location: ../../login.php');
        exit();
    }
    
    $faculty_id = $_GET['faculty_id'];

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT * FROM faculty WHERE faculty_id = :faculty_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':faculty_id', $faculty_id, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    // print_r($result);
    // return;
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
    <link rel="stylesheet" href="../../assets/css/adminlte.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include_once('../includes/sidebar_1.php') ?>
        <div class="content-wrapper pt-3">
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-header border-0 pt-4">
                                    <h4>
                                        <i class="nav-icon fas fa-book"></i>
                                        แก้ไขข้อมูลผู้ดูแล
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
                                                    <label for="faculty_id">รหัสคณะ</label>
                                                    <input type="text" class="form-control" name="faculty_id" id="faculty_id" placeholder="รหัสคณะ" value="<?= $result['faculty_id']; ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="faculty_name">ชื่อคณะ</label>
                                                    <input type="text" class="form-control" name="faculty_name" id="faculty_name" placeholder="ชื่อคณะ" value="<?= $result['faculty_name']; ?>" required>
                                                </div>
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
    <script src="../../assets/js/adminlte.min.js"></script>

    <script>
        function goBack() {
            window.history.back();
        }
        $(function() {
            $('#formData').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'PUT',
                    url: '../../service/faculty/update.php',
                    contentType: 'application/json', // เพิ่ม content type เป็น JSON
                    data: JSON.stringify({
                        faculty_name: $('#faculty_name').val(),
                        faculty_id: $('#faculty_id').val(),
                    })
                }).done(function(resp) {
                    Swal.fire({
                        text: 'อัพเดทข้อมูลเรียบร้อย',
                        icon: 'success',
                        confirmButtonText: 'ตกลง',
                    }).then((result) => {
                        location.assign('./');
                    });
                });
            });
        });
    </script>

    </body>

</html>