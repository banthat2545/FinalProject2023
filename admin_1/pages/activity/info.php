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
$stmt = $conn->prepare("SELECT activity.*, activity_type.at_name 
                                    FROM activity 
                                    JOIN activity_type ON activity.at_id = activity_type.at_id 
                                    WHERE activity.act_id = :act_id");
$stmt->bindParam(":act_id", $act_id, PDO::PARAM_STR);
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
    <title>ข้อมูลกิจกรรม</title>
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
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <?php include_once('../includes/sidebar_2.php') ?>
        <div class="content-wrapper pt-4">
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow">
                                <div class="card-header border-0 pt-4">
                                    <h4>
                                        <i class="nav-icon fas fa-puzzle-piece"></i>
                                        ข้อมูลกิจกรรม : <?= $activityInfo['act_id']; ?>
                                    </h4>
                                    <div class="card-header d-flex justify-content-end">
                                        <button class="btn btn-info my-3" onclick="goBack()">
                                            <i class="fas fa-arrow-left"></i>
                                            กลับหน้าหลัก
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body px-5">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card shadow-sm">
                                                <div class="card-header pt-4">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-bookmark"></i>
                                                        รายละเอียด
                                                    </h3>
                                                </div>
                                                <div class="card-body px-5">
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">ชื่อกิจกรรม :</p>
                                                        <div class="col-xl-9">
                                                            <?= $activityInfo['act_name']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">สถานที่ :</p>
                                                        <div class="col-xl-9">
                                                            <?= $activityInfo['act_location']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">วันที่จัด :</p>
                                                        <div class="col-xl-9">
                                                            <?php
                                                            $formattedDate = DateTime::createFromFormat('Y-m-d', $activityInfo['act_date']);
                                                            $thaiMonthNames = [
                                                                1 => 'มกราคม',
                                                                2 => 'กุมภาพันธ์',
                                                                3 => 'มีนาคม',
                                                                4 => 'เมษายน',
                                                                5 => 'พฤษภาคม',
                                                                6 => 'มิถุนายน',
                                                                7 => 'กรกฎาคม',
                                                                8 => 'สิงหาคม',
                                                                9 => 'กันยายน',
                                                                10 => 'ตุลาคม',
                                                                11 => 'พฤศจิกายน',
                                                                12 => 'ธันวาคม',
                                                            ];
                                                            echo $formattedDate->format('d') . ' ' . $thaiMonthNames[(int)$formattedDate->format('m')] . ' ' . $formattedDate->format('Y');
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">หน่วยกิต :</p>
                                                        <div class="col-xl-9">
                                                            <?= $activityInfo['act_credit']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">จำนวนผู้ลงทะเบียนเข้าร่วมสูงสุด :</p>
                                                        <div class="col-xl-9">
                                                            <?= $activityInfo['act_total_max']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">สถานะ :</p>
                                                        <div class="col-xl-9">
                                                            <?= $activityInfo['act_status']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <p class="col-xl-3 text-muted">ประเภทกิจกรรม :</p>
                                                        <div class="col-xl-9">
                                                            <?= $activityInfo['at_name']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="card-header border-0 d-flex justify-content-end">
                                                        <div class="btn-group" role="group">
                                                            <a href="form-edit.php?act_id=<?= $activityInfo['act_id']; ?>" type="button" class="btn btn-warning text-white">
                                                                <i class="far fa-edit"></i> แก้ไข
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body px-5">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="card shadow-sm">
                                                <div class="card-header pt-4">
                                                    <h3 class="card-title">
                                                        <i class="fas fa-users"></i>
                                                        รายชื่อผู้เข้าร่วม/ไม่ได้เข้าร่วมกิจกรรม
                                                    </h3>
                                                </div>
                                                <div class="card-body">
                                                    <table id="logs2" class="table table-hover" width="100%">
                                                    </table>
                                                </div>
                                                <div class="card-header border-0 d-flex justify-content-end px-5">
                                                    <div class="btn-group" role="group">
                                                        <a href="form-editCheckName.php?act_id=<?= $activityInfo['act_id']; ?>" type="button" class="btn btn-warning text-white">
                                                            <i class="far fa-edit"></i> แก้ไข
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
            function goBack() {
                window.history.back();
            }
            $(function() {
                $.ajax({
                    type: "GET",
                    url: "../../service/activity/info.php"
                }).done(function(data) {
                    let tableData = []
                    data.response.forEach(function(item, index) {
                        tableData.push([
                            item['act_id'],
                            item['at_id'],
                            item['act_name'],
                            item['act_date'],
                            item['act_location'],
                            item['act_credit'],
                            item['act_total_max'],
                            item['act_status']
                        ])
                    })
                    initDataTables(tableData)
                }).fail(function() {
                    Swal.fire({
                        text: 'ไม่สามารถเรียกดูข้อมูลได้',
                        icon: 'error',
                        confirmButtonText: 'ตกลง',
                    }).then(function() {
                        location.assign('../dashboard_2')
                    })
                })

                function initDataTables(tableData) {
                    $('#logs').DataTable({
                        paging: false,
                        ordering: false,
                        info: false,
                        searching: false,
                        data: tableData,
                        responsive: {
                            details: {
                                display: $.fn.dataTable.Responsive.display.modal({
                                    header: function(row) {
                                        return 'กิจกรรม'
                                    }
                                }),
                                renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                                    tableClass: 'table'
                                })
                            }
                        },
                        language: {
                            "lengthMenu": "แสดงข้อมูล _MENU_ แถว",
                            "zeroRecords": "ไม่พบข้อมูลที่ต้องการ",
                            "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
                            "infoEmpty": "ไม่พบข้อมูลที่ต้องการ",
                            "infoFiltered": "(filtered from _MAX_ total records)",
                            "search": 'ค้นหา',
                            "paginate": {
                                "previous": "ก่อนหน้านี้",
                                "next": "หน้าต่อไป"
                            }
                        },
                    });
                }
            });
        </script>

        <script>
            // Function to get URL parameter
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }

            $(function() {
                var actId = getUrlParameter('act_id'); // Get act_id from URL
                if (!actId) {
                    console.error("act_id is missing in the URL.");
                    return;
                }

                // AJAX request to fetch data
                $.ajax({
                    type: "GET",
                    url: "../../service/register_activity/info.php?act_id=" + actId
                }).done(function(data) {
                    if (data.status) {
                        let tableData = data.data.map(function(item, index) {
                            // แปลงค่า check_status เป็นข้อความที่เหมาะสม
                            let statusColor = (item.check_status === '1') ? 'text-success' : 'text-danger';
                            let statusText = (item.check_status === '1') ? 'เข้าร่วมกิจกรรม' : 'ไม่เข้าร่วมกิจกรรม';
                            return [
                                ++index,
                                item.register_id,
                                item.mb_id,
                                item.mb_name,
                                item.branch_name,
                                `<span class="${statusColor}">${statusText}</span>`
                            ];
                        });
                        initDataTables(tableData);
                    } else {
                        // location.assign('info.php');
                    }
                });

                function initDataTables(tableData) {
                    $('#logs2').DataTable({
                        data: tableData,
                        columns: [{
                                title: "ลำดับ",
                                className: "align-middle"
                            },
                            {
                                title: "รหัสลงทะเบียน",
                                className: "align-middle"
                            },
                            {
                                title: "รหัสนักศึกษา",
                                className: "align-middle"
                            },
                            {
                                title: "ชื่อ",
                                className: "align-middle"
                            },
                            {
                                title: "สาขาวิชา",
                                className: "align-middle"
                            },
                            {
                                title: "สถานะการเข้าร่วมกิจกรรม",
                                className: "align-middle"
                            }
                        ],
                    });
                }
            });
        </script>
</body>

</html>