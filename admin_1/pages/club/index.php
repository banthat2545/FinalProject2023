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
                                        <i class="nav-icon fas fa-cube"></i>
                                        ข้อมูลชมรม
                                    </h4>
                                    <!-- <div class="card-header d-flex justify-content-end">
                                        <a href="form-create.php" class="btn btn-primary mt-3">
                                            <i class="fas fa-plus"></i>
                                            เพิ่มชมรม
                                        </a>
                                    </div> -->
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
    <!-- <button type="button" class="btn btn-danger" id="delete" data-id="${item.club_id}" data-index="${index}">
                            <i class="far fa-trash-alt"></i> ลบ
                        </button> -->

    <script>
        $(function() {
        $.ajax({
            type: "GET",
            url: "../../service/club/index.php"
        }).done(function(data) {
            let tableData = []
            data.response.forEach(function (item, index){
                tableData.push([    
                    ++index,
                    `<img src="../../service/club/upload/${item.club_image}" alt="club_image" style="max-width: 100px; max-height: 100px;">`,
                    item.club_id,
                    item.club_name,
                    item.details,
                    item.club_bureau,
                    `<div class="btn-group" role="group">
                        <a href="form-edit.php?club_id=${item.club_id}" type="button" class="btn btn-warning text-white">
                            <i class="far fa-edit"></i> แก้ไข
                        </a>
                        
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
                location.assign('../dashboard_1')
            })
        })

        function initDataTables(tableData) {
            $('#logs').DataTable( {
                data: tableData,
                columns: [
                    { title: "ลำดับ" , className: "align-middle"},
                    { title: "รูปภาพ", className: "align-middle"},
                    { title: "รหัสชมรม" , className: "align-middle"},
                    { title: "ชื่อชมรม", className: "align-middle"},
                    { title: "รายละเอียด", className: "align-middle"},
                    { title: "หน่วยงาน", className: "align-middle"},
                    { title: "จัดการ", className: "align-middle"}
                ],
                initComplete: function() {
                        $(document).on('click', '#delete', function() {
                            let club_id = $(this).data('id');
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
                                        url: "../../service/club/delete.php",
                                        data: JSON.stringify({
                                            club_id: club_id
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
                                        console.log("AJAX Error: " + textStatus + ' - ' + errorThrown);
                                    })
                                }
                            })
                        })
                    },
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.modal( {
                            header: function ( row ) {
                                var data = row.data()
                                return 'ชมรม: ' + data[1]
                            }
                        }),
                        renderer: $.fn.dataTable.Responsive.renderer.tableAll( {
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