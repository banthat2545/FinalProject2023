<?php
require_once('../authen.php');
if (!isset($_SESSION['MB_ROLE']) || $_SESSION['MB_ROLE'] !== 'คณะกรรมการชมรม') {
    header('Location: ../../login.php');
    exit();
}

function getUrlParameter($name)
{
    if (isset($_GET[$name])) {
        return urldecode($_GET[$name]);
    } else {
        return '';
    }
}
$act_id = getUrlParameter('act_id'); // Define $act_id using the getUrlParameter function

$stmt = $conn->prepare("SELECT register_activity.*, member.mb_id, member.mb_name
                        FROM register_activity 
                        JOIN member ON register_activity.mb_id = member.mb_id 
                        WHERE register_activity.act_id = :act_id");
$stmt->bindParam(":act_id", $act_id, PDO::PARAM_STR);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    echo "No attendance data found.";
    exit();
}

$attendanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_GET['act_id'])) {
    echo "act_id is missing in the URL.";
    exit();
}

$act_id = $_GET['act_id'];

// Use prepared statements to prevent SQL injection
$sql = "SELECT * FROM activity WHERE act_id = :act_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':act_id', $act_id, PDO::PARAM_STR);
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
    <title>เช็คชื่อกิจกรรม</title>
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
        <div class="content-wrapper pt-3">
            <!-- Main content -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-header border-0 pt-4">
                                    <h4>
                                        <i class="nav-icon fas fa-edit"></i>
                                        แก้ไขเช็คชื่อผู้เข้าร่วมกิจกรรมกิจกรรม : <?= $activityInfo['act_id']; ?>
                                    </h4>
                                    <div class="card-header d-flex justify-content-end">
                                        <button class="btn btn-info my-3" onclick="goBack()">
                                            <i class="fas fa-arrow-left"></i>
                                            กลับหน้าหลัก
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table id="logs" class="table table-hover" width="100%">
                                    </table>
                                </div>
                                <div class="card-body">
                                    <p class="text-right" style="margin-right: 20px;">
                                        <br>
                                        <button type="submit" class="btn btn-warning" style="width: 200px;" id="updateAttendance">บันทึกการแก้ไข</button>
                                    </p>
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
            var actId = getUrlParameter('act_id'); // Use var actId instead of actId
            if (!actId) {
                console.error("act_id is missing in the URL.");
                return;
            }
            $.ajax({
                type: "GET",
                url: "../../service/register_activity/index2.php?act_id=" + actId
            }).done(function(data) {
                if (data.status) {
                    // Data retrieval successful
                    let tableData = [];
                    data.data.forEach(function(item, index) {
                        tableData.push([
                            ++index,
                            item.register_id,
                            item.mb_id,
                            item.mb_name,
                            item.branch_name,
                            `<div class="btn-group" role="group">
                                <input type="radio" name="register_id[${item.register_id}]" value="1" ${item.check_status == 1 ? 'checked' : ''} required>&nbsp; เข้าร่วม 
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <input type="radio" name="register_id[${item.register_id}]" value="2" ${item.check_status == 2 ? 'checked' : ''} required>&nbsp; ไม่เข้าร่วม
                            </div>`
                        ]);
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
                $('#logs').DataTable({
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
                            title: "เช็คชื่อ",
                            className: "align-middle"
                        }
                    ],

                });
            }

            $('#updateAttendance').on('click', function() {
                var checkData = {};

                $("input[name^='register_id']").each(function() {
                    var registerId = $(this).attr('name').match(/\[(.*?)\]/)[1];
                    var checkStatus = $(this).is(':checked') ? 2 : 1;
                    checkData[registerId] = checkStatus;
                });

                $.ajax({
                    type: "POST",
                    url: "../../service/register_activity/updateCheckIn.php",
                    data: {
                        act_id: actId,
                        check_data: JSON.stringify(checkData) // Make sure this matches the backend
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.status) {
                            Swal.fire({
                                text: 'บันทึกการแก้ไขเรียบร้อย',
                                icon: 'success',
                                confirmButtonText: 'ตกลง',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    goBack(); // Call the goBack function to navigate back
                                }
                            });
                        } else {
                            Swal.fire({
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'ตกลง',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error updating check-in data:", error);
                    }
                });
            });
        });
    </script>
</body>

</html>