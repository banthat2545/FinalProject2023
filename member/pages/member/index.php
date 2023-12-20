<?php
    require_once('../authen.php');
    if (!isset($_SESSION['MB_ROLE']) || $_SESSION['MB_ROLE'] !== 'สมาชิก') {
        header('Location: ../../login.php');
        exit();
    }

    $mb_id = $_GET['id'];

    $sql = "SELECT member.*, branch.branch_name, faculty.faculty_name FROM member
            LEFT JOIN branch ON member.branch_id = branch.branch_id
            LEFT JOIN faculty ON branch.faculty_id = faculty.faculty_id
            WHERE mb_id = :mb_id";
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
        <?php include_once('../includes/sidebar.php') ?>
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
                                        ข้อมูลส่วนตัว : <?= $result['mb_name']; ?>
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
                                                    <label for="mb_id">ชื่อผู้ใช้</label>
                                                    <input type="text" class="form-control" name="mb_id" id="mb_id" placeholder="ชื่อผู้ใช้" value="<?= $result['mb_id']; ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mb_name">ชื่อจริง</label>
                                                    <input type="text" class="form-control" name="mb_name" id="mb_name" placeholder="ชื่อจริง" value="<?= $result['mb_name']; ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mb_email">อีเมล</label>
                                                    <input type="text" class="form-control" name="mb_email" id="mb_email" placeholder="อีเมล" value="<?= $result['mb_email']; ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mb_tel">เบอร์โทร</label>
                                                    <input type="text" class="form-control" name="mb_tel" id="mb_tel" placeholder="เบอร์โทร" value="<?= $result['mb_tel']; ?>" readonly>
                                                </div>
                                                <div class="form-group">
                                                    <label for="mb_role">สิทธิ์การใช้งาน</label>
                                                    <input type="text" class="form-control" name="mb_role" id="mb_role" placeholder="ตำแหน่ง" value="<?= $result['mb_role']; ?>" readonly>
                                                </div>

                                                <div class="form-group">
                                                    <label for="branch_name">สาขา</label>
                                                    <input type="text" class="form-control" name="branch_name" id="branch_name" placeholder="สาขา" value="<?= $result['branch_name']; ?>" readonly>
                                                </div>

                                                <div class="form-group">
                                                    <label for="faculty_name">คณะ</label>
                                                    <input type="text" class="form-control" name="faculty_name" id="faculty_name" placeholder="คณะ" value="<?= $result['faculty_name']; ?>" readonly>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="card-header border-0 d-flex justify-content-end px-5">
                                            <div class="btn-group" role="group">
                                                <a href="form-edit.php?mb_id=<?= $result['mb_id']; ?>" type="button" class="btn btn-warning text-white">
                                                    <i class="far fa-edit"></i> แก้ไข
                                                </a>
                                            </div>
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
    </script>

</body>

</html>