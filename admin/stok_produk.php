<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include('../db.php'); 
$page = "statistik";


if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit;
}

// Statistik produk
// Corrected: Joining pesanan_detail to sum 'jumlah'
$sqlStats = "
    SELECT 
        COUNT(p.id_produk) AS total_produk,
        SUM(CASE WHEN p.stok = 0 THEN 1 ELSE 0 END) AS unavailable_produk,
        (SELECT SUM(pd.jumlah) FROM pesanan_detail pd JOIN pesanan ps ON pd.id_pesanan = ps.id_pesanan WHERE ps.status_pesanan != 'Dibatalkan') AS total_terjual
    FROM produk p
";
$resultStats = mysqli_query($kon, $sqlStats);
if (!$resultStats) {
    die("Query gagal: " . mysqli_error($kon));
}
$stats = mysqli_fetch_assoc($resultStats);

// Data stok produk
// Corrected: Joining pesanan_detail to sum 'jumlah' for individual products
$sqlProducts = "
    SELECT 
        p.id_produk, 
        p.nama_produk, 
        p.stok, 
        (SELECT SUM(pd.jumlah) FROM pesanan_detail pd JOIN pesanan ps ON pd.id_pesanan = ps.id_pesanan WHERE pd.id_produk = p.id_produk AND ps.status_pesanan != 'Dibatalkan') AS total_terjual, 
        (SELECT AVG(rating_produk) FROM review_produk WHERE id_produk = p.id_produk) AS rata_rating 
    FROM produk p
";
$resultProducts = mysqli_query($kon, $sqlProducts);
if (!$resultProducts) {
    die("Query gagal: " . mysqli_error($kon));
}
$products = mysqli_fetch_all($resultProducts, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <?php 
        include 'aset.php';
    ?>
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

        h4 {
            font-size: 20px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .text-success {
            font-weight: bold;
        }

        .text-danger {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- ======= Header ======= -->
    <?php require "atas.php"; ?>
    <!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <?php require "menu.php"; ?>
    <!-- End Sidebar-->
    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-clipboard-data"></i>&nbsp; STOK & STATISTIK</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                    <li class="breadcrumb-item active"> STOK & STATISTIK</li>
                </ol>
            </nav>
        </div>
        <!-- Product Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light text-black">
                        <h4>Statistika Produk</h4>
                    </div>
                    <br>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <h5>Total Produk</h5>
                                <p class="h3"><?= $stats['total_produk'] ?></p>
                            </div>
                            <div class="col-md-4">
                                <h5>Produk Tidak Tersedia</h5>
                                <p class="h3 text-danger"><?= $stats['unavailable_produk'] ?></p>
                            </div>
                            <div class="col-md-4">
                                <h5>Total Terjual</h5>
                                <p class="h3"><?= $stats['total_terjual'] ?? 0 ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light text-black">
                        <h4>Stok Produk</h4>
                    </div>
                    <br>
                    <div class="card-body">
                        <table class="table datatable">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Produk</th>
                                    <th>Performa</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['nama_produk']) ?></td>
                                        <td>
                                            <strong>Total Terjual:</strong> <?= $product['total_terjual'] ?? 0 ?> <br>
                                            <strong>Rata-rata Rating:</strong> <?= number_format($product['rata_rating'] ?? 0, 1) ?>/5
                                        </td>
                                        <td><?= $product['stok'] ?></td>
                                        <td>
                                            <?php if ($product['stok'] > 0): ?>
                                                <span class="text-success">Tersedia</span>
                                            <?php else: ?>
                                                <span class="text-danger">Tak Tersedia</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (empty($products)): ?>
                            <div class="alert alert-warning text-center">
                                Tidak ada produk ditemukan.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

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
