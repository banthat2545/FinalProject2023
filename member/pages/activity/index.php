<?php
require_once('../authen.php');
if (!isset($_SESSION['MB_ROLE']) || $_SESSION['MB_ROLE'] !== 'สมาชิก') {
    header('Location: ../../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>จัดการข้อมูลคณะ</title>
    <!-- <link rel="shortcut icon" type="image/x-icon" href="../../assets/images/favicon.ico"> -->
    <!-- stylesheet -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../../plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="../../assets/css/adminlte.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <!-- Datatables -->
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <style>
        .activity-row {
            display: flex;
            padding: 15px;
            margin-bottom: 15px;
            border: 3px solid #FFD700;
            /* เส้นกรอบสีเขียวเข้ม */
            border-radius: 10px;
            /* ขอบมนเขียว */
        }

        .activity-row img {
            width: 33.33%;
            max-height: 200px;
            object-fit: cover;
            margin-right: 15px;
            /* ระยะห่างระหว่างรูปภาพกับข้อความ */
        }

        .activity-info {
            flex-grow: 1;
        }

        .btn-container {
            display: flex;
            justify-content: flex-end;
        }

        .custom-btn {
            margin-left: 10px;
            /* ระยะห่างขอบซ้ายของปุ่ม */
        }
    </style>

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
                                        <i class="nav-icon fas fa-puzzle-piece"></i>
                                        ข้อมูลกิจกรรม
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="activityContainer"></div>
                                </div>
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
    <script src="../../assets/js/adminlte.min.js"></script>

    <!-- datatables -->
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <script>
        $(function() {
            $.ajax({
                type: "GET",
                url: "../../service/activity/index.php"
            }).done(function(data) {
                console.log(data);
                let activityContainer = $("#activityContainer");

                data.response.forEach(function(activity) {
                    let imageUrl = "";

                    if (activity.at_id === 'ACT-TY-001') {
                        imageUrl = "../../assets/images/act_01.png";
                    } else if (activity.at_id === 'ACT-TY-002') {
                        imageUrl = "../../assets/images/act_02.png";
                    } else if (activity.at_id === 'ACT-TY-003') {
                        imageUrl = "../../assets/images/act_03.png";
                    }

                    let registrationButtonHtml = activity.act_current_registrations < activity.act_total_max ?
                        `<button type="button" class="btn btn-success custom-btn" data-act-id="${activity.act_id}">ลงทะเบียน</button>` :
                        `<span class="text-danger">ลงทะเบียนเต็มแล้ว</span>`;

                    let activityCard = `
                        <div class="col-md-6">
                            <div class="activity-row">
                                <img src="${imageUrl}" alt="Activity Image">
                                <div class="activity-info">
                                    <h4>ชื่อกิจกรรม : ${activity.act_name}</h4>
                                    <h6>วันที่ : ${activity.act_date}</h6>
                                    <h6>สถานที่ : ${activity.act_location}</h6>
                                    <h6>หน่วยกิตกิจกรรม : ${activity.act_credit} หน่วย</h6>
                                    <h6>ประเภทกิจกรรม : ${activity.at_name}</h6>
                                    <div class="btn-group justify-content-end">
                                        ${registrationButtonHtml}
                                    </div>
                                    <button type="button" class="btn btn-info" onclick="location.href='info.php?act_id=${activity.act_id}'">
                                        รายชื่อผู้เข้าร่วมกิจกรรม
                                    </button>

                                </div>
                            </div>
                        </div>
                    `;
                    activityContainer.append(activityCard);
                });

                $(document).on("click", ".custom-btn", function() {
                    let actId = $(this).data("act-id");
                    registerForActivity(actId);
                });
            }).fail(function() {
                Swal.fire({
                    text: 'ไม่สามารถเรียกดูข้อมูลได้',
                    icon: 'error',
                    confirmButtonText: 'ตกลง',
                }).then(function() {
                    location.assign('../dashboard');
                });
            });

            function registerForActivity(actId) {
                let mbId = "<?php echo $_SESSION['MB_ID']; ?>";

                let registrationData = {
                    mb_id: mbId,
                    act_id: actId // Include act_id in the data
                };

                $.ajax({
                    type: "POST",
                    url: "../../service/activity/register.php",
                    data: registrationData
                }).done(function(data) {
                    if (data.status === true) {
                        Swal.fire({
                            title: "ลงทะเบียน?",
                            text: "คุณต้องการลงทะเบียนเข้าร่วมกิจกรรมรายการนี้!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "ตกลง!"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: "ลงทะเบียนสำเร็จ",
                                    text: "คุณลงทะเบียนสำเร็จ",
                                    icon: "success"
                                });
                            }
                        });
                    } else {
                        Swal.fire({
                            text: 'เกิดข้อผิดพลาด: ' + data.message,
                            icon: 'error',
                            confirmButtonText: 'ตกลง',
                        });
                    }
                }).fail(function() {
                    Swal.fire({
                        text: 'คุณได้ลงทะเบียนกิจกรรมนี้แล้ว',
                        icon: 'error',
                        confirmButtonText: 'ตกลง',
                    });
                });
            }
        });
    </script>
</body>

</html>