<?php
require_once('../authen.php');

if (!isset($_SESSION['MB_ROLE']) || $_SESSION['MB_ROLE'] !== 'ประธานชมรม') {
    header('Location: ../../login.php');
    exit();
}

$club_id = $_GET['club_id'];

$sql = "SELECT * FROM club WHERE club_id = :club_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':club_id', $club_id, PDO::PARAM_STR);
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
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>จัดการข้อมูลประเภทกิจกรรม</title>
    <!-- <link rel="shortcut icon" type="image/x-icon" href="../../assets/images/favicon.ico"> -->
    <!-- stylesheet -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="../../plugins/summernote/summernote-bs4.css">
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
                            <div class="card">
                                <div class="card-header border-0 pt-4">
                                    <h4>
                                        <i class="nav-icon fas fa-cube"></i>
                                        จัดการชมรม
                                    </h4>
                                    <div class="card-header d-flex justify-content-end">
                                        <button class="btn btn-info my-3" onclick="goBack()">
                                            <i class="fas fa-arrow-left"></i>
                                            กลับหน้าหลัก
                                        </button>
                                    </div>
                                </div>
                                <form id="formData" enctype="multipart/form-data">
                                    <div class="card-body">
                                        <div class="form-row">
                                            <div class="form-group col-md-12">
                                                <label for="club_id">รหัสชมรม</label>
                                                <input type="text" class="form-control" name="club_id" id="club_id" placeholder="รหัสชมรม" value="<?= $result['club_id']; ?>" readonly>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="club_name">ชื่อชมรม</label>
                                                <input type="text" class="form-control" name="club_name" id="club_name" placeholder="ชื่อชมรม" value="<?= $result['club_name']; ?>">
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label for="details">รายละเอียด</label>
                                                <textarea id="details" class="textarea" name="details" placeholder="Place some text here"><?= $result['details']; ?></textarea>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="club_bureau">หน่วยงาน</label>
                                                <input type="text" class="form-control" name="club_bureau" id="club_bureau" placeholder="หน่วยงาน" value="<?= $result['club_bureau']; ?>">
                                            </div>
                                            <div class="form-group col-sm-6">
                                                <label for="club_image">รูปโลโก้</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" id="club_image" name="club_image" onchange="updateFileName()">
                                                    <label class="custom-file-label" for="club_image" id="fileLabel"><?= $result['club_image']; ?></label>
                                                </div>
                                                <?php if ($result['club_image']) : ?>
                                                    <img src="../../service/club/upload/<?= $result['club_image']; ?>" id="img_output_01" alt="Uploadedt Image" class="mt-2" style="max-width: 100%; height: auto;">
                                                <?php endif; ?>
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

    <script>
        function goBack() {
            window.history.back();
        }
        function updateFileName() {
            var input = document.getElementById('club_image');
            var label = document.getElementById('fileLabel');
            var image = document.getElementById('img_output_01');

            if (input.files.length > 0) {
                var fileName = input.files[0].name;
                label.innerHTML = fileName;
                var imageURL = URL.createObjectURL(input.files[0]);
                image.src = imageURL;
            } else {
                label.innerHTML = "เลือกไฟล์";
            }
        }
        $(function() {
            $('#details').summernote({
                height: 500,
            });
            $('#formData').submit(function(e) {
                e.preventDefault();

                var formData = new FormData();

                formData.append('club_name', $('#club_name').val());
                formData.append('details', $('#details').val());
                formData.append('club_bureau', $('#club_bureau').val());
                formData.append('club_id', $('#club_id').val());
                formData.append('club_image', $('#club_image')[0].files[0]);

                $.ajax({
                    type: 'POST',
                    url: '../../service/club/update.php',
                    processData: false,
                    contentType: false,
                    data: formData
                }).done(function(resp) {
                    if (resp.status) {
                        Swal.fire({
                            text: 'อัพเดทข้อมูลเรียบร้อย',
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
                }).fail(function(xhr, status, error) {
                    Swal.fire({
                        text: 'เกิดข้อผิดพลาดในการส่งข้อมูล: ' + error,
                        icon: 'error',
                        confirmButtonText: 'ตกลง',
                    });
                });
            });
        });
    </script>
</body>

</html>