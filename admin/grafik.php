<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require "../db.php";
$page = "penjualan";
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit;
}

// Total Produk (Tidak diubah)
$total_products_query = mysqli_query($kon, "SELECT COUNT(id_produk) as total_produk FROM produk");
$total_products = mysqli_fetch_assoc($total_products_query)['total_produk'];

// Produk Terjual (Tidak diubah)
$total_sold_query = mysqli_query($kon, "
    SELECT SUM(pd.jumlah) as total_terjual 
    FROM pesanan_detail pd
    JOIN pesanan p ON pd.id_pesanan = p.id_pesanan
    WHERE p.status_pesanan != 'Dibatalkan'
");
$total_sold = mysqli_fetch_assoc($total_sold_query)['total_terjual'];

// Grafik Data (Tidak diubah)
$chart_data_query = mysqli_query($kon, "
    SELECT 
        produk.nama_produk, 
        COALESCE(SUM(pesanan_detail.jumlah), 0) as jumlah_terjual 
    FROM produk 
    LEFT JOIN pesanan_detail ON produk.id_produk = pesanan_detail.id_produk
    LEFT JOIN pesanan ON pesanan_detail.id_pesanan = pesanan.id_pesanan AND pesanan.status_pesanan != 'Dibatalkan'
    GROUP BY produk.id_produk, produk.nama_produk
    ORDER BY jumlah_terjual DESC
");

$chart_labels = [];
$chart_values = [];
if ($chart_data_query) {
    while ($row = mysqli_fetch_assoc($chart_data_query)) {
        $chart_labels[] = $row['nama_produk'];
        $chart_values[] = $row['jumlah_terjual'];
    }
} else {
    error_log("Error fetching chart data: " . mysqli_error($kon));
}

// Query Produk Paling Laris (Tidak diubah)
$top_products_query = mysqli_query($kon, "
    SELECT 
        p.id_produk,
        p.nama_produk, 
        p.harga, 
        p.gambar,
        SUM(pd.jumlah) as jumlah_terjual
    FROM produk p
    JOIN pesanan_detail pd ON p.id_produk = pd.id_produk
    JOIN pesanan ps ON pd.id_pesanan = ps.id_pesanan
    WHERE ps.status_pesanan != 'Dibatalkan'
    GROUP BY p.id_produk, p.nama_produk, p.harga, p.gambar
    ORDER BY jumlah_terjual DESC
    LIMIT 3
");
$top_products = [];
if ($top_products_query) {
    $top_products = mysqli_fetch_all($top_products_query, MYSQLI_ASSOC);
} else {
    error_log("Error fetching top products: " . mysqli_error($kon));
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Grafik Penjualan</title>
    <?php include "aset.php"; ?>
</head>
<body>
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
        h5 {
            color: #2D3A3A !important;
            font-weight: bold !important;
            font-size: 18px !important;
        }
        h3.text-primary, h3.text-success {
            color: #FF8C12 !important;
        }
        .standalone-back-button {
            display: inline-flex;
            align-items: center;
            text-decoration: none; 
            color: #6c757d;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background-color 0.2s ease-in-out; 
        }
        .standalone-back-button:hover {
            background-color: #e9ecef; 
            color: #495057;
        }
        .standalone-back-button .bi {
            font-size: 1.1em;
            margin-right: 8px; 
        }
        .top-product-title {
            font-weight: 600;
            color: #6c757d;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .top-product-card {
            background-color: #fff;
            border-radius: 0.75rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        .top-product-card:hover {
             transform: translateY(-3px);
             box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }
        .top-product-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
        .top-product-price {
            background-color: #FF8C12;
            color: white;
            padding: 0.5em 0.9em;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.9rem;
            /* Menjadikannya inline-block agar text-align di parentnya bekerja */
            display: inline-block; 
        }
    </style>
    <?php require "atas.php"; ?>

    <?php require "menu.php"; ?>

    <main id="main" class="main">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div class="pagetitle">
                    <h1><i class="bi bi-bar-chart"></i>&nbsp; GRAFIK TOTAL PENJUALAN</h1>
                    <nav>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                            <li class="breadcrumb-item"><a href="penjualan.php">PENJUALAN</a></li>
                            <li class="breadcrumb-item active">GRAFIK TOTAL PENJUALAN</li>
                        </ol>
                    </nav>
                </div>
                <a href="penjualan.php" class="standalone-back-button">
                    <i class="bi bi-arrow-left"></i>
                    Kembali
                </a>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-center"><div class="card-body"><h5 class="card-title">Total Produk</h5><h3 class="text-primary"><?= $total_products ?></h3></div></div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center"><div class="card-body"><h5 class="card-title">Produk Terjual</h5><h3 class="text-success"><?= $total_sold ?? 0 ?></h3></div></div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card"><div class="card-body"><h5 class="card-title">Grafik Penjualan</h5><canvas id="salesChart"></canvas></div></div>
                </div>
            </div>

            <div class="card" style="background-color: #f8f9fa;">
                <div class="card-body p-4">
                    <h5 class="card-title top-product-title mb-4">Produk Paling Laris</h5>
                    
                    <?php if (!empty($top_products)) : ?>
                        <?php foreach ($top_products as $product) : ?>
                        <div class="top-product-card mb-3">
                            <div class="card-body p-3">
                                <div class="row align-items-center">
                                    
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <img src="../uploads/<?= htmlspecialchars($product['gambar']) ?>" alt="<?= htmlspecialchars($product['nama_produk']) ?>" class="top-product-img me-3">
                                            <div>
                                                <h6 class="mb-0 fw-bold"><?= htmlspecialchars($product['nama_produk']) ?></h6>
                                                <small class="text-muted">Kode Produk: #<?= htmlspecialchars($product['id_produk']) ?></small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 text-center">
                                        <span class="top-product-price">
                                            Rp <?= number_format($product['harga'], 0, ',', '.') ?>
                                        </span>
                                    </div>
                                    
                                    <div class="col-md-3 text-end">
                                        <span class="fw-bold fs-5 me-1"><?= $product['jumlah_terjual'] ?></span>
                                        <span class="text-muted">Sold</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <div class="alert alert-light text-center">Tidak ada data penjualan produk.</div>
                    <?php endif; ?>
                </div>
            </div>
            </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (document.getElementById('salesChart')) {
                const ctx = document.getElementById('salesChart').getContext('2d');
                const salesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($chart_labels) ?>,
                        datasets: [{
                            label: 'Jumlah Terjual',
                            data: <?= json_encode($chart_values) ?>,
                            backgroundColor: 'rgba(255, 159, 64, 0.7)',
                            borderColor: 'rgba(255, 159, 64, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }
        });
    </script>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>