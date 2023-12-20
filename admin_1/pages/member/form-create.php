<?php
require_once('../authen.php');
if (!isset($_SESSION['MB_ROLE']) || $_SESSION['MB_ROLE'] !== 'ประธานชมรม') {
    header('Location: ../../login.php');
    exit();
}

$query = $conn->query("
        SELECT branch.branch_id, branch.branch_name, faculty.faculty_id, faculty.faculty_name
        FROM member
        JOIN branch ON member.branch_id = branch.branch_id
        JOIN faculty ON branch.faculty_id = faculty.faculty_id
    ");
$data = $query->fetchAll(PDO::FETCH_ASSOC);
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
                                        <i class="fas fa-user-cog"></i>
                                        เพิ่มข้อมูลสมาชิก
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
                                            <div class="col-md-6 px-1 px-md-5">
                                                <div class="form-group">
                                                    <label for="mb_id">ชื่อผู้ใช้ (ตัวอย่าง 65342310264-8)</label>
                                                    <input type="text" class="form-control" name="mb_id" id="mb_id" placeholder="รหัสนักศึกษา" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mb_password">รหัสผ่าน</label>
                                                    <input type="password" class="form-control" name="mb_password" id="mb_password" placeholder="รหัสผ่าน" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mb_name">ชื่อจริง</label>
                                                    <input type="text" class="form-control" name="mb_name" id="mb_name" placeholder="ชื่อจริง" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mb_email">อีเมล (ตัวอย่าง banthat.up@rmuti.ac.th)</label>
                                                    <input type="text" class="form-control" name="mb_email" id="mb_email" placeholder="อีเมล" required>
                                                </div>

                                            </div>
                                            <div class="col-md-6 px-1 px-md-5">
                                                <div class="form-group">
                                                    <label for="mb_tel">เบอร์โทร (ตัวอย่าง 0987654321)</label>
                                                    <input type="text" class="form-control" name="mb_tel" id="mb_tel" placeholder="เบอร์โทร" required>
                                                </div>

                                                <div class="form-group">
                                                    <label for="mb_role">สิทธิ์การใช้งาน</label>
                                                    <select class="form-control" name="mb_role" id="mb_role" required>
                                                        <option value disabled selected>ตำแหน่ง</option>
                                                        <option value="คณะกรรมการชมรม">คณะกรรมการชมรม</option>
                                                        <option value="สมาชิก">สมาชิก</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="faculty_id">คณะ</label>
                                                    <select class="form-control" name="faculty_id" id="faculty_id" required>
                                                        <!-- ตัวเลือกคณะจะถูกโหลดทาง JavaScript -->
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="branch_id">สาขา</label>
                                                    <select class="form-control" name="branch_id" id="branch_id" required>
                                                        <!-- ตัวเลือกสาขาจะถูกโหลดทาง JavaScript -->
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <button type="submit" class="btn btn-primary btn-block mx-auto w-50" name="submit">บันทึกข้อมูล</button>
                                        </div>
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
            loadFaculties();
            $('#faculty_id').on('change', function() {
                var selectedFacultyId = $(this).val();
                loadBranches(selectedFacultyId);
            });
            $('#formData').on('submit', function(e) {
                e.preventDefault();
                var formData = $('#formData').serialize();

                var email = $('#mb_email').val();
                var emailPattern = /^[a-zA-Z0-9._-]+@rmuti\.ac\.th$/;
                if (!emailPattern.test(email)) {
                    Swal.fire({
                        text: 'กรุณาใส่อีเมลที่ลงท้ายด้วย @rmuti.ac.th',
                        icon: 'error',
                        confirmButtonText: 'ตกลง',
                    });
                    return;
                }

                var phoneNumber = $('#mb_tel').val();
                var phoneNumberPattern = /^\d{10}$/;
                if (!phoneNumberPattern.test(phoneNumber)) {
                    Swal.fire({
                        text: 'กรุณาใส่เบอร์โทรที่มี 10 ตัวเท่านั้น',
                        icon: 'error',
                        confirmButtonText: 'ตกลง',
                    });
                    return;
                }
                $.ajax({
                    type: 'POST',
                    url: '../../service/member/create.php',
                    data: formData,
                    dataType: 'json',
                    success: function(resp) {
                        if (resp.status) {
                            Swal.fire({
                                text: 'เพิ่มข้อมูลเรียบร้อย',
                                icon: 'success',
                                confirmButtonText: 'ตกลง',
                            }).then((result) => {
                                location.assign('./');
                            });
                        } else {
                            Swal.fire({
                                text: resp.message,
                                icon: 'error',
                                confirmButtonText: 'ตกลง',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 400) {
                            Swal.fire({
                                text: 'รหัสนักศึกษาซ้ำกับที่มีอยู่ กรุณากรอกข้อมูลรหัสนักศึกษาใหม่',
                                icon: 'error',
                                confirmButtonText: 'ตกลง',
                            });
                        } else {
                            Swal.fire({
                                text: 'เกิดข้อผิดพลาดในการส่งข้อมูล: ' + error,
                                icon: 'error',
                                confirmButtonText: 'ตกลง',
                            });
                        }
                    }
                });
            });

            function loadFaculties() {
                $.ajax({
                    type: 'GET',
                    url: '../../service/member/get_faculties.php',
                    dataType: 'json',
                    success: function(data) {
                        if (data.status) {
                            var options = '<option value="" disabled selected>เลือกคณะ</option>';
                            data.faculties.forEach(function(faculty) {
                                options += '<option value="' + faculty.faculty_id + '">' + faculty.faculty_name + '</option>';
                            });
                            $('#faculty_id').html(options);
                        } else {
                            console.error('Failed to load faculties');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading faculties:', error);
                    }
                });
            }

            function loadBranches(facultyId) {
                $.ajax({
                    type: 'GET',
                    url: '../../service/member/get_branches.php',
                    data: {
                        faculty_id: facultyId
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.status) {
                            var options = '<option value="" disabled selected>เลือกสาขา</option>';
                            data.branches.forEach(function(branch) {
                                options += '<option value="' + branch.branch_id + '">' + branch.branch_name + '</option>';
                            });
                            $('#branch_id').html(options);
                        } else {
                            console.error('Failed to load branches');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error loading branches:', error);
                    }
                });
            }
        });
    </script>

</body>

</html>