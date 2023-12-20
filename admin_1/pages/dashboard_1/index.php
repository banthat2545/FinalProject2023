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
  <title>Home-Chairman</title>
  <!-- <link rel="shortcut icon" type="image/x-icon" href="../../assets/images/favicon.ico"> -->
  <!-- stylesheet -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
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
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-info shadow">
                            <div class="inner text-center">
                                <h1 class="py-3">&nbsp;ข้อมูลสมาชิก&nbsp;</h1>
                            </div>
                            <a href="../member/" class="small-box-footer py-3"> คลิกจัดการระบบ <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-primary shadow">
                            <div class="inner text-center">
                                <h1 class="py-3">ข้อมูลชมรม</h1>
                            </div>
                            <a href="../club/" class="small-box-footer py-3"> คลิกจัดการระบบ <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-warning shadow">
                            <div class="inner text-center">
                                <h1 class="py-3">ข้อมูลคณะ</h1>
                            </div>
                            <a href="../faculty/" class="small-box-footer py-3"> คลิกจัดการระบบ <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-success shadow">
                            <div class="inner text-center">
                                <h1 class="py-3">ข้อมูลสาขาวิชา</h1>
                            </div>
                            <a href="../branch/" class="small-box-footer py-3"> คลิกจัดการระบบ <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-6">
                        <div class="small-box bg-danger shadow">
                            <div class="inner text-center">
                                <h1 class="py-3">ข้อมูลประเภทกิจกรรม</h1>
                            </div>
                            <a href="../activity_type/" class="small-box-footer py-3"> คลิกจัดการระบบ <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- <div class="row">
                    <div class="col-lg-3">
                        <div class="small-box py-3 bg-white shadow">
                            <div class="inner">
                                <h3>3,500 บาท</h3>
                                <p class="text-danger">ยอดขายประจำวัน</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="small-box py-3 bg-white shadow">
                            <div class="inner">
                                <h3>25,000 บาท</h3>
                                <p class="text-danger">ยอดขาย 7 วันที่ผ่านมา</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-area"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="small-box py-3 bg-white shadow">
                            <div class="inner">
                                <h3>5 รายการ</h3>
                                <p class="text-danger">ยอดคำสั่งซื้อประจำวัน</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-cart-arrow-down"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="small-box py-3 bg-white shadow">
                            <div class="inner">
                                <h3>10 คน</h3>
                                <p class="text-danger">ลูกค้าหน้าใหม่ในเดือนนี้</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow">
                            <div class="card-body">
                                <div class="d-flex">
                                    <p class="d-flex flex-column">
                                        <span class="text-bold text-xl" id="salesReport"></span>
                                        <span class="text-danger" id="salesTextReport"></span>
                                    </p>
                                    <p class="ml-auto flex-row" id="salesbtn">
                                        <button class="btn btn-secondary m-1 d-block d-md-inline ml-auto" onclick="selectReport('report-month.php', this, 'line')">ยอดขายเดือนนี้</button>
                                        <button class="btn btn-outline-secondary m-1 d-block d-md-inline ml-auto" onclick="selectReport('report-sixmonths.php', this, 'bar')">6 เดือน</button>
                                        <button class="btn btn-outline-secondary m-1 d-block d-md-inline ml-auto" onclick="selectReport('report-twelvemonths.php', this, 'bar')">12 เดือน</button>
                                        <button class="btn btn-outline-secondary m-1 d-block d-md-inline ml-auto" onclick="selectReport('report-year.php', this, 'bar')">2021</button>
                                    </p>
                                </div>
                                <div class="position-relative">
                                    <canvas id="visitors-chart" height="350"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</div>


<!-- SCRIPTS -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/adminlte.min.js"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="../../plugins/chart.js/Chart.min.js"></script>
<script src="../../plugins/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>
<script src="../../assets/js/pages/dashboard.js"></script>
</body>
</html>
