<?php
require_once('../authen.php');
if (!isset($_SESSION['MB_ROLE']) || $_SESSION['MB_ROLE'] !== 'ประธานชมรม') {
    header('Location: ../../login.php');
    exit();
}

$mb_id = $_GET['id'];

$sql = "SELECT * FROM member WHERE mb_id = :mb_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':mb_id', $mb_id, PDO::PARAM_STR);
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
                                        <i class="fas fa-user-edit"></i>
                                        แก้ไขข้อมูล 
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
                                                    <label for="mb_id">ชื่อผู้ใช้</label>
                                                    <input type="text" class="form-control" name="mb_id" id="mb_id" placeholder="ชื่อผู้ใช้" value="<?= $result['mb_id']; ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mb_name">ชื่อจริง</label>
                                                    <input type="text" class="form-control" name="mb_name" id="mb_name" placeholder="ชื่อจริง" value="<?= $result['mb_name']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mb_email">อีเมล</label>
                                                    <input type="text" class="form-control" name="mb_email" id="mb_email" placeholder="อีเมล" value="<?= $result['mb_email']; ?>" required>
                                                </div>

                                            </div>
                                            <div class="col-md-6 px-1 px-md-5">
                                                <div class="form-group">
                                                    <label for="mb_tel">เบอร์โทร</label>
                                                    <input type="text" class="form-control" name="mb_tel" id="mb_tel" placeholder="เบอร์โทร" value="<?= $result['mb_tel']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mb_role">สิทธิ์การใช้งาน</label>
                                                    <select class="form-control" name="mb_role" id="mb_role" required>
                                                        <option value="" disabled>กำหนดสิทธิ์</option>
                                                        <option value="ประธานชมรม" <?= ($result['mb_role'] === 'ประธานชมรม') ? 'selected' : ''; ?>>ประธานชมรม</option>
                                                        <option value="คณะกรรมการชมรม" <?= ($result['mb_role'] === 'คณะกรรมการชมรม') ? 'selected' : ''; ?>>คณะกรรมการชมรม</option>
                                                        <option value="สมาชิก" <?= ($result['mb_role'] === 'สมาชิก') ? 'selected' : ''; ?>>สมาชิก</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                    <label for="branch_id">สาขา</label>
                                                    <select class="form-control" name="branch_id" id="branch_id" required>
                                                        <option value="" disabled>เลือกสาขา</option>
                                                        <?php
                                                        // Load faculties and branches for dropdowns
                                                        $facultiesQuery = $conn->query("SELECT faculty_id, faculty_name FROM faculty");
                                                        $faculties = $facultiesQuery->fetchAll(PDO::FETCH_ASSOC);

                                                        foreach ($faculties as $faculty) {
                                                            echo "<optgroup label='" . $faculty['faculty_name'] . "'>";

                                                            $branchesQuery = $conn->prepare("SELECT branch_id, branch_name FROM branch WHERE faculty_id = :faculty_id");
                                                            $branchesQuery->bindParam(":faculty_id", $faculty['faculty_id'], PDO::PARAM_STR);
                                                            $branchesQuery->execute();
                                                            $branches = $branchesQuery->fetchAll(PDO::FETCH_ASSOC);

                                                            foreach ($branches as $branch) {
                                                                echo "<option value='" . $branch['branch_id'] . "' " . ($result['branch_id'] === $branch['branch_id'] ? 'selected' : '') . ">" . $branch['branch_name'] . "</option>";
                                                            }

                                                            echo "</optgroup>";
                                                        }
                                                        ?>
                                                    </select>
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
                    url: '../../service/member/update.php',
                    contentType: 'application/json', // เพิ่ม content type เป็น JSON
                    data: JSON.stringify({
                        mb_name: $('#mb_name').val(),
                        mb_email: $('#mb_email').val(),
                        mb_tel: $('#mb_tel').val(),
                        mb_role: $('#mb_role').val(),
                        branch_id: $('#branch_id').val(),
                        mb_id: $('#mb_id').val()
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