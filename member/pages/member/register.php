<?php
require_once '../../service/connect.php';

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
    <title>สมัครสมาชิก</title>
    <!-- stylesheet -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">

    <link rel="stylesheet" href="../../assets/css/adminlte.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <section class="d-flex align-items-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <section class="col-lg-10">
                    <div class="card shadow p-3 p-md-4">
                        <h1 class="text-center text-success font-weight-bold">ชมรมอนุรักษ์ธรรมชาติและสิ่งแวดล้อม</h1>
                        <h4 class="text-center">สมัครสมาชิก</h4>
                        <div class="card-body">
                            <form id="formData">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 px-1 px-md-9">
                                            <div class="form-group">
                                                <label for="mb_id">ชื่อผู้ใช้ (ตัวอย่าง 65342310264-8)</label>
                                                <input type="text" class="form-control" name="mb_id" id="mb_id" placeholder="รหัสนักศึกษา" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="mb_password">รหัสผ่าน</label>
                                                <input type="password" class="form-control" name="mb_password" id="mb_password" placeholder="รหัสผ่าน" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="mb_password_confirm">ยืนยันรหัสผ่าน</label>
                                                <input type="password" class="form-control" name="mb_password_confirm" id="mb_password_confirm" placeholder="ยืนยันรหัสผ่าน" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="mb_name">ชื่อจริง</label>
                                                <input type="text" class="form-control" name="mb_name" id="mb_name" placeholder="ชื่อจริง" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 px-1 px-md-5">
                                            <div class="form-group">
                                                <label for="mb_email">อีเมล (ตัวอย่าง banthat.up@rmuti.ac.th)</label>
                                                <input type="text" class="form-control" name="mb_email" id="mb_email" placeholder="อีเมล" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="mb_tel">เบอร์โทร (ตัวอย่าง 0987654321)</label>
                                                <input type="text" class="form-control" name="mb_tel" id="mb_tel" placeholder="เบอร์โทร" required>
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
                                        <button type="submit" class="btn btn-primary btn-block mx-auto w-50" name="submit">สมัครสมาชิก</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>

    <!-- SCRIPTS -->
    <script src="../../plugins/jquery/jquery.min.js"></script>
    <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="../../assets/js/adminlte.min.js"></script>

    <script>
        $(function() {
            loadFaculties();
            $('#faculty_id').on('change', function() {
                var selectedFacultyId = $(this).val();
                loadBranches(selectedFacultyId);
            });
            $('#formData').on('submit', function(e) {
                e.preventDefault();
                var formData = $('#formData').serialize();

                var password = $('#mb_password').val();
                var confirmPassword = $('#mb_password_confirm').val();
                if (password !== confirmPassword) {
                    Swal.fire({
                        text: 'รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน',
                        icon: 'error',
                        confirmButtonText: 'ตกลง',
                    });
                    return; // Stop form submission
                }

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
                    url: '../../service/member/register.php',
                    data: formData,
                    dataType: 'json',
                    success: function(resp) {
                        if (resp.status) {
                            Swal.fire({
                                text: 'สมัครสมาชิกเรียบร้อย',
                                text: 'รอผลยืนยันการเป็นสมาชิก',
                                icon: 'success',
                                confirmButtonText: 'ตกลง',
                            }).then((result) => {
                                location.assign('../../login.php');
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
                                text: 'รหัสนักศึกษาซ้ำกับที่มีอยู่ กรุณากรอกข้อมูลรหัสนักศึกษาใหม่อีกครั้ง',
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