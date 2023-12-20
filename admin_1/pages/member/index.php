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
                                        <i class="fas fa-user-cog"></i>
                                        สมาชิกชมรม
                                    </h4>
                                    <div class="button-container">
                                        <a href="form-create.php" class="btn btn-primary mt-3">
                                            <i class="fas fa-plus"></i>
                                            เพิ่มข้อมูล
                                        </a>
                                        <a href="form-sanction.php" class="btn btn-success mt-3">
                                            <i class="fas fa-check"></i>
                                            อนุมัติการสมัครสมาชิก
                                        </a>
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
        <?php include_once('../includes/footer.php') ?>
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

    <!-- Modal -->
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
                    <p><strong>ชื่อ-นามสกุล:</strong> <span id="mb_name_modal"></span></p>
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
                url: "../../service/member/index.php"
            }).done(function(data) {
                let tableData = []
                data.response.forEach(function(item, index) {
                    tableData.push([
                        ++index,
                        item.mb_id,
                        item.mb_name,
                        item.mb_email,
                        item.mb_tel,
                        item.updated_at,
                        `<span class="badge badge-info">${item.mb_role}</span>`,
                        `<div class="btn-group" role="group">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#my-modal" 
                            onclick="showUserDetails('${item.mb_id}', '${item.mb_name}', '${item.mb_email}', '${item.mb_tel}', '${item.updated_at}', '${item.mb_role}', '${item.branch_name}', '${item.faculty_name}')">
                            รายละเอียด
                        </button>
                        <a href="form-edit.php?id=${item.mb_id}" type="button" class="btn btn-warning text-white">
                            <i class="far fa-edit"></i> แก้ไข
                        </a>
                        <button type="button" class="btn btn-danger" id="delete" data-id="${item.mb_id}" data-index="${index}">
                            <i class="far fa-trash-alt"></i> ลบ
                        </button>
                    </div>`
                    ])
                })
                initDataTables(tableData)
            }).fail(function() {
                Swal.fire({
                    text: 'ไม่สามารถเรียกดูข้อมูลได้',
                    icon: 'error',
                    confirmButtonText: 'ตกลง',
                }).then(function() {
                    location.assign('../dashboard')
                })
            })
            $(document).on('click', '.btn-details', function() {
                // Get data from the row
                var rowData = $('#logs').DataTable().row($(this).parents('tr')).data();
                showUserDetails(rowData[1], rowData[2], rowData[3], rowData[4], rowData[5], rowData[6], rowData[7]);
            });

            function initDataTables(tableData) {
                $('#logs').DataTable({
                    data: tableData,
                    columns: [{title: "ลำดับ", className: "align-middle"},
                        {title: "ชื่อผู้ใช้งาน", className: "align-middle"},
                        {title: "ชื่อ-นามสกุล", className: "align-middle"},
                        {title: "อีเมล", className: "align-middle"},
                        {title: "เบอร์โทร", className: "align-middle"},
                        {title: "ใช้งานล่าสุด", className: "align-middle"},
                        {title: "ตำแหน่ง", className: "align-middle"},
                        {title: "จัดการ", className: "align-middle"}
                    ],
                    initComplete: function() {
                        $(document).on('click', '#delete', function() {
                            let mb_id = $(this).data('id');
                            let index = $(this).data('index');
                            Swal.fire({
                                text: "คุณแน่ใจหรือไม่...ที่จะลบรายการนี้?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'ใช่! ลบเลย',
                                cancelButtonText: 'ยกเลิก'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        type: "DELETE",
                                        url: "../../service/member/delete.php",
                                        data: JSON.stringify({
                                            mb_id: mb_id
                                        }),
                                        contentType: "application/json; charset=utf-8",
                                        dataType: "json"
                                    }).done(function(data) {
                                        Swal.fire({
                                            text: 'รายการของคุณถูกลบเรียบร้อย',
                                            icon: 'success',
                                            confirmButtonText: 'ตกลง',
                                        }).then((result) => {
                                            location.reload();
                                        })
                                    }).fail(function(jqXHR, textStatus, errorThrown) {
                                        Swal.fire({
                                            text: 'ไม่สามารถลบข้อมูลรายการนี้ได้',
                                            icon: 'info',
                                            confirmButtonText: 'ตกลง',
                                        })
                                        console.log("AJAX Error: " +
                                            textStatus + ' - ' + errorThrown
                                        );
                                    })
                                }
                            })
                        })
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
                })
            }
        })
    </script>
</body>

</html>