<?php
session_start();
require "../db.php";
$page = "dashboard";

if (!isset($_SESSION["user"])) {
  header("Location: ../login.php");
}

// GET ID FROM USER
$user = $_SESSION["user"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>DASHBOARD</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="../assets/img/LOGOCASALUXE2.png" rel="icon">
  <link href="../assets/img/LOGOCASALUXE2.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

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
            color: #2D3A3A !important;
        }
        main{
          margin-left: 20px;
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

          <!-- Carousel Section -->
          <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <img src="../assets/img/SLIDE 1.png" class="d-block w-100" alt="Image 1">
              </div>
              <div class="carousel-item">
                <img src="../assets/img/SLIDE 2.png" class="d-block w-100" alt="Image 2">
              </div>
              <div class="carousel-item">
                <img src="../assets/img/SLIDE 3.png" class="d-block w-100" alt="Image 3">
              </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
              <span class="carousel-control-prev-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
              <span class="carousel-control-next-icon" aria-hidden="true"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
          <!-- End of Carousel Section -->

          <!-- Space -->
          <div class="my-4"></div>

          <!-- Products Section -->
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Produk Kami</h2>
            <a href="produk.php" class="btn btn-clear">Produk Lainnya  ></a>
          </div>
          <div class="row">
            <?php
            // Fetch products from database
            $query = "SELECT p.nama_produk, p.harga, k.nama_kategori, p.gambar 
                      FROM produk p
                      JOIN kategori k ON p.id_kategori = k.id_kategori
                      LIMIT 4";
            $result = mysqli_query($kon, $query);

            while ($product = mysqli_fetch_assoc($result)) {
              echo '<div class="col-lg-3 col-md-6 col-sm-12 mb-4">';
              echo '  <div class="card">';
              echo '    <img src="../uploads/' . $product['gambar'] . '" class="card-img-top" alt="' . $product['nama_produk'] . '">';
              echo '    <div class="card-body">';
              echo '      <h5 class="card-title">' . $product['nama_produk'] . '</h5>';
              echo '      <p class="card-text">Kategori: ' . $product['nama_kategori'] . '</p>';
              echo '      <p class="card-text">Harga: Rp ' . number_format($product['harga'], 0, ',', '.') . '</p>';
              echo '    </div>';
              echo '  </div>';
              echo '</div>';
            }
            ?>
          </div>
          <!-- End of Products Section -->

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.umd.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="../assets/js/main.js"></script>

</body>

</html>
