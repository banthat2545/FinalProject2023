<?php

?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico"> -->
  <!-- stylesheet -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Kanit">
  <link rel="stylesheet" href="assets/css/adminlte.min.css">
  <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body,
    html {
      height: 100%;
      font-family: 'Kanit', cursive !important;
      font-size: 0.95rem !important;
      margin: 0;
      /* Add this to remove default margin */
    }
  </style>
</head>

<body style="background-image: url('assets/images/background_2.jpg'); 
  background-size: cover; 
  background-repeat: no-repeat;
  background-attachment: fixed;
  height: 100%;">
  <!-- <header class="bg-image"></header> -->
  <section class="d-flex align-items-center min-vh-100">
    <div class="container">
      <div class="row justify-content-center">
        <section class="col-lg-7">
          <div class="card shadow p-3 p-md-4">
            <h2 class="text-center text-success font-weight-bold">ชมรมอนุรักษ์ธรรมชาติและสิ่งแวดล้อม</h2>
            <h4 class="text-center">เข้าสู่ระบบ</h4>
            <div class="card-header d-flex justify-content-end">
              <a href="../index.php" class="btn btn-info">
                <i class="fas fa-list"></i>
                กลับหน้าหลัก
              </a>
            </div>
            <div class="card-body">

              <!-- HTML Form Login -->
              <form id="formLogin" method="POST" action="service/auth/login.php">
                <div class="form-group col-sm-12">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text px-2">ชื่อผู้ใช้งาน</div>
                    </div>
                    <input type="text" class="form-control" name="mb_id" placeholder="รหัสนักศึกษา" required>
                  </div>
                </div>
                <div class="form-group col-sm-12">
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <div class="input-group-text px-3">รหัสผ่าน</div>
                    </div>
                    <input type="password" class="form-control" name="mb_password" placeholder="รหัสผ่าน" required>
                  </div>
                </div>
                <button type="submit" name="btn_login" class="btn btn-primary btn-block"> เข้าสู่ระบบ</button>
              </form>
            </div>
          </div>
        </section>
      </div>
    </div>
  </section>

  <!-- script -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="plugins/toastr/toastr.min.js"></script>
  <script>
    $(function() {
      $("#formLogin").submit(function(e) {
        e.preventDefault()
        $.ajax({
          type: "POST",
          url: "service/auth/login.php",
          data: $(this).serialize()
        }).done(function(data) {
          console.log(data.role)
          toastr.success('เข้าสู่ระบบเรียบร้อย');
          setTimeout(() => {
            if (data.role === 'ประธานชมรม') {
              location.href = 'pages/dashboard_1/';
            } else if (data.role === 'คณะกรรมการชมรม') {
              location.href = 'pages/dashboard_2/';
            } else {
              toastr.warnige('ท่านไม่มีสิทธิ์เข้าใช้งาน');
              location.href = './';
            }
          }, 800);
        }).fail(function(data) {
          window.toastr.remove();
          toastr.error('ไม่สามารถเข้าสู่ระบบได้');
        });
      });
    });
  </script>
</body>

</html>