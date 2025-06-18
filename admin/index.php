<?php
session_start();
require "../db.php";
$page = "dashboard";

if (!isset($_SESSION["admin"]))
{
  header("Location: ../login.php");
}

// MENGHITUNG SEMUA DATA
$sql_user = mysqli_query($kon, "SELECT * FROM user");
$row_user = mysqli_num_rows($sql_user);

$sql_kategori = mysqli_query($kon, "SELECT * FROM kategori");
$row_kategori = mysqli_num_rows($sql_kategori);

$sql_produk = mysqli_query($kon, "SELECT * FROM produk");
$row_produk = mysqli_num_rows($sql_produk);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>DASHBOARD</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="../assets/img/LOGOCASALUXE2.png" rel="icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="../assets/css/style.css" rel="stylesheet">

</head>

<body>

  <!-- ======= Header ======= -->
  <?php require "atas.php"; ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php require "menu.php"; ?>
  <!-- End Sidebar-->
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif;
            color: #2D3A3A;
        }
        .sidebar {
            background-color: #F8F7F1 !important;
        }
    header{
            background-color: #F8F7F1 !important;
        }
  </style>
  <main id="main" class="main">

    <div class="pagetitle">
      <h1><i class="bi bi-grid"></i>&nbsp; BERANDA</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
          <li class="breadcrumb-item active">BERANDA</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <div class="col-lg-12">
          <div class="row">

            <!-- ROW CARDS -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
              <a href="order.php">
                <div class="card-body">
                  <h5 class="card-title">PESANAN MASUK</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?= number_format($row_user, 0, "", ".") ?></h6>
                    </div>
                  </div>
                </div>
              </div>
            </a>
            </div>

            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">

              <a href="kategori.php">
                <div class="card-body">
                  <h5 class="card-title">KATEGORI</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-list-nested"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?= number_format($row_kategori, 0, "", ".") ?></h6>
                    </div>
                  </div>
                </div>

              </div>
             </a>
            </div>

            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">

              <a href="stok_produk.php">
                <div class="card-body">
                  <h5 class="card-title"> STOK PRODUK</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-clipboard-data"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?= number_format($row_produk, 0, "", ".") ?></h6>
                    </div>
                  </div>
                </div>

              </div>
             </a>
            </div>

            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
              <a href="user.php">
                <div class="card-body">
                  <h5 class="card-title"> DAFTAR USER</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-person"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?= number_format($row_user, 0, "", ".") ?></h6>
                    </div>
                  </div>
                </div>
              </div>
            </a>
            </div>

            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
              <a href="pesanan_dibatalkan.php">
                <div class="card-body">
                  <h5 class="card-title">PESANAN DIBATALKAN</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-clipboard2-x"></i>
                    </div>
                    <div class="ps-3">
                      <h6><?= number_format($row_user, 0, "", ".") ?></h6>
                    </div>
                  </div>
                </div>
              </div>
            </a>
            </div>

          </div>
        </div>

      </div>
    </section>

  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.umd.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>

  <!-- Core Bootstrap JS -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>

</body>

</html>