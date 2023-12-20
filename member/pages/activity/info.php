<?php
require_once('../authen.php');
if (!isset($_SESSION['MB_ROLE']) || $_SESSION['MB_ROLE'] !== 'สมาชิก') {
    header('Location: ../../login.php');
    exit();
}

if (!isset($_GET['act_id'])) {
    echo "act_id is missing in the URL.";
    exit();
}

$act_id = $_GET['act_id'];
$stmt = $conn->prepare("SELECT * FROM activity 
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
    <title>ข้อมูลผู้ลงทะเบียน</title>
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
                                        <i class="fas fa-user-cog"></i>
                                        รายชื่อผู้ลงทะเบียน : กิจกรรม <?= $activityInfo['act_name']; ?>
                                    </h4>
                                </div>
                                <div class="card-header d-flex justify-content-end">
                                    <button class="btn btn-info my-3" onclick="goBack()">
                                        <i class="fas fa-arrow-left"></i>
                                        กลับหน้าหลัก
                                    </button>
                                </div>
                                <div class="card-body">
                                    <table id="logs" class="table table-hover" width="100%">
                                    </table>
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
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        $(function() {
            var actId = getUrlParameter('act_id');
            if (!actId) {
                console.error("act_id is missing in the URL.");
                return;
            }

            $.ajax({
                type: "GET",
                url: "../../service/activity/info.php?act_id=" + actId
            }).done(function(data) {
                if (data.status) {
                    let tableData = data.data.map(function(item, index) {
                        return [
                            ++index,
                            item.mb_id,
                            item.mb_name,
                            item.branch_name,
                            item.register_date,
                        ];
                    });
                    initDataTables(tableData);
                } else {
                    Swal.fire({
                        text: 'ไม่พบข้อมูลผู้ลงทะเบียน',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                    }).then(function() {
                        location.assign('index.php');
                    });
                }
            });

            function initDataTables(tableData) {
                if (tableData.length > 0) {
                    $('#logs').DataTable({
                        data: tableData,
                        columns: [{
                                title: "ลำดับ",
                                className: "align-middle"
                            },
                            {
                                title: "รหัสนักศึกษา",
                                className: "align-middle"
                            },
                            {
                                title: "ชื่อนักศึกษา",
                                className: "align-middle"
                            },
                            {
                                title: "สาขา",
                                className: "align-middle"
                            },
                            {
                                title: "วันที่ลงทะเบียน",
                                className: "align-middle"
                            },
                        ],
                        responsive: {
                            details: {
                                display: $.fn.dataTable.Responsive.display.modal({
                                    header: function(row) {
                                        var data = row.data();
                                        return 'ชมรม: ' + data[1];
                                    },
                                }),
                                renderer: $.fn.dataTable.Responsive.renderer.tableAll({
                                    tableClass: 'table',
                                }),
                            },
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
                                "next": "หน้าต่อไป",
                            },
                        },
                    });
                } else {
                    console.error("No data available for DataTable");
                }
            }
        });
    </script>
</body>

</html>