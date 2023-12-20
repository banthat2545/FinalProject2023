<?php
require_once('../authen.php');
if (!isset($_SESSION['MB_ROLE']) || $_SESSION['MB_ROLE'] !== 'ประธานชมรม') {
    header('Location: ../../login.php');
    exit();
}
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
    <!-- Datatables -->
    <link rel="stylesheet" href="../../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <style>
        .button-container {
            display: flex;
            justify-content: space-between;
        }

        .btn {
            display: inline-block;
        }
    </style>
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
                                        <i class="fas fa-user-plus"></i>
                                        อนุมัติการสมัครสมาชิกชมรม
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

    <div class="modal fade" id="my-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">รายละเอียดผู้ใช้งาน</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>ชื่อผู้ใช้งาน:</strong> <span id="mb_id_modal"></span></p>
                    <p><strong>ชื่อจริง:</strong> <span id="mb_name_modal"></span></p>
                    <p><strong>อีเมล:</strong> <span id="mb_email_modal"></span></p>
                    <p><strong>เบอร์โทร:</strong> <span id="mb_tel_modal"></span></p>
                    <p><strong>ใช้งานล่าสุด:</strong> <span id="updated_at_modal"></span></p>
                    <p><strong>ตำแหน่ง:</strong> <span id="mb_role_modal"></span></p>
                    <p><strong>สาขา:</strong> <span id="branch_name_modal"></span></p>
                    <p><strong>คณะ:</strong> <span id="faculty_name_modal"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
        function showUserDetails(mb_id, mb_name, mb_email, mb_tel, updated_at, mb_role, branch_name, faculty_name) {
            $('#mb_id_modal').text(mb_id);
            $('#mb_name_modal').text(mb_name);
            $('#mb_email_modal').text(mb_email);
            $('#mb_tel_modal').text(mb_tel);
            $('#updated_at_modal').text(updated_at);
            $('#mb_role_modal').text(mb_role);
            $('#branch_name_modal').text(branch_name);
            $('#faculty_name_modal').text(faculty_name);

            // Show the modal
            $('#my-modal').modal('show');
        }
        $(function() {
            $.ajax({
                type: "GET",
                url: "../../service/member/sanction.php"
            }).done(function(data) {
                let tableData = [];
                data.response.forEach(function(item, index) {
                    tableData.push([
                        ++index,
                        item.mb_id,
                        item.mb_name,
                        item.mb_email,
                        item.mb_tel,
                        `<div class="btn-group" role="group">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#my-modal" 
                                onclick="showUserDetails('${item.mb_id}', '${item.mb_name}', '${item.mb_email}', '${item.mb_tel}', '${item.updated_at}', '${item.mb_role}', '${item.branch_name}', '${item.faculty_name}')">
                                รายละเอียด
                            </button>
                        </div>`,
                        `<div class="btn-group" role="group">
                            <button type="button" class="btn btn-success approve" data-id="${item.mb_id}" data-index="${index}">
                                <i class="fas fa-check"></i> อนุมัติ
                            </button>
                            <button type="button" class="btn btn-danger delete" data-id="${item.mb_id}" data-index="${index}">
                                <i class="fas fa-times"></i> ไม่อนุมัติ
                            </button>
                        </div>`
                    ]);
                });
                initDataTables(tableData);
            }).fail(function() {
                Swal.fire({
                    text: 'ไม่สามารถเรียกดูข้อมูลได้',
                    icon: 'error',
                    confirmButtonText: 'ตกลง',
                }).then(function() {
                    location.assign('../dashboard_1');
                });
            });

            function initDataTables(tableData) {
                $('#logs').DataTable({
                    data: tableData,
                    columns: [{
                            title: "ลำดับ",
                            className: "align-middle"
                        },
                        {
                            title: "ชื่อผู้ใช้งาน",
                            className: "align-middle"
                        },
                        {
                            title: "ชื่อจริง",
                            className: "align-middle"
                        },
                        {
                            title: "อีเมล",
                            className: "align-middle"
                        },
                        {
                            title: "เบอร์โทร",
                            className: "align-middle"
                        },{
                            title: "รายละเอียด",
                            className: "align-middle"
                        },
                        {
                            title: "อนุมัติ",
                            className: "align-middle"
                        }
                    ],
                    initComplete: function() {
                        $(document).on('click', '.approve', function() {
                            let mb_id = $(this).data('id');
                            let index = $(this).data('index');
                            let action = 'approve';

                            Swal.fire({
                                //title: "คุณการอนุมติการสมัครสมาชิกรายการนี้ใช้หรือไม่?",
                                text: "คุณการอนุมติการสมัครสมาชิกรายการนี้ใช้หรือไม่?",
                                icon: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                cancelButtonText: "ยกเลิก",
                                confirmButtonText: "ใช่, อนุมัติ!"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Perform the approval action via AJAX
                                    $.ajax({
                                        type: "PUT",
                                        url: "../../service/member/sanction-approve.php",
                                        data: JSON.stringify({
                                            mb_id: mb_id,
                                            action: action
                                        }),
                                        dataType: "json",
                                        success: function(data) {
                                            Swal.fire({
                                                title: "เยี่ยม!",
                                                text: "รายการของคุณอนุมัติสำเร็จ",
                                                icon: "success"
                                            }).then((result) => {
                                                location
                                                    .reload(); // Reload the page or update the UI as needed
                                            });
                                        },
                                        error: function(jqXHR, textStatus,
                                            errorThrown) {
                                            console.log("AJAX Error: " +
                                                textStatus + ' - ' +
                                                errorThrown);
                                            Swal.fire({
                                                text: 'รายการของคุณอนุมัติไม่สำเร็จ',
                                                icon: 'error',
                                                confirmButtonText: 'ตกลง',
                                            });
                                        }
                                    });
                                }
                            });
                        });

                        $(document).on('click', '.delete', function() {
                            let mb_id = $(this).data('id');
                            let index = $(this).data('index');
                            Swal.fire({
                                text: "คุณต้องการที่จะไม่อนุมัติการสมัครสมาชิกายการนี้?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: "#d33",
                                confirmButtonText: 'ใช่! ไม่อนุมัติ',
                                cancelButtonText: 'ยกเลิก'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        type: "DELETE",
                                        url: "../../service/member/sanction-reject.php",
                                        data: JSON.stringify({
                                            mb_id: mb_id
                                        }),
                                        contentType: "application/json; charset=utf-8",
                                        dataType: "json"
                                    }).done(function(data) {
                                        Swal.fire({
                                            text: 'รายการของคุณไม่ถูกอนุมัติ',
                                            icon: 'success',
                                            confirmButtonText: 'ตกลง',
                                        }).then((result) => {
                                            location.reload();
                                        });
                                    }).fail(function(jqXHR, textStatus,
                                        errorThrown) {
                                        console.log("AJAX Error: " +
                                            textStatus + ' - ' + errorThrown
                                        );
                                    });
                                }
                            });
                        });
                    },
                    responsive: {
                        details: {
                            display: $.fn.dataTable.Responsive.display.modal({
                                header: function(row) {
                                    var data = row.data()
                                    return 'ผู้ใช้งาน: ' + data[1]
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
                    }
                });
            }
        });
    </script>
</body>

</html>