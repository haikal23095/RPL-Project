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

// Total Produk
$total_products_query = mysqli_query($kon, "SELECT COUNT(id_produk) as total_produk FROM produk");
$total_products = mysqli_fetch_assoc($total_products_query)['total_produk'];

// Produk Terjual
$total_sold_query = mysqli_query($kon, "
    SELECT SUM(pd.jumlah) as total_terjual 
    FROM pesanan_detail pd
    JOIN pesanan p ON pd.id_pesanan = p.id_pesanan
    WHERE p.status_pesanan != 'Dibatalkan'
");
$total_sold = mysqli_fetch_assoc($total_sold_query)['total_terjual'];

// Grafik Data (X: Semua Produk, Y: Total Penjualan per Produk)
$chart_data_query = mysqli_query($kon, "
    SELECT 
        produk.nama_produk, 
        SUM(pesanan_detail.jumlah) as jumlah_terjual 
    FROM produk 
    LEFT JOIN pesanan_detail ON produk.id_produk = pesanan_detail.id_produk
    LEFT JOIN pesanan ON pesanan_detail.id_pesanan = pesanan.id_pesanan AND pesanan.status_pesanan != 'Dibatalkan'
    GROUP BY produk.id_produk, produk.nama_produk
    ORDER BY jumlah_terjual DESC
");

$chart_labels = [];
$chart_values = [];
if ($chart_data_query) { // Pastikan query berhasil sebelum fetch
    while ($row = mysqli_fetch_assoc($chart_data_query)) {
        $chart_labels[] = $row['nama_produk'];
        $chart_values[] = $row['jumlah_terjual'];
    }
} else {
    // Log error jika query gagal
    error_log("Error fetching chart data: " . mysqli_error($kon));
}

// Produk Paling Laris
$top_products_query = mysqli_query($kon, "
    SELECT 
        p.nama_produk, 
        p.harga, 
        SUM(pd.jumlah) as jumlah_terjual
    FROM produk p
    JOIN pesanan_detail pd ON p.id_produk = pd.id_produk
    JOIN pesanan ps ON pd.id_pesanan = ps.id_pesanan
    WHERE ps.status_pesanan != 'Dibatalkan'
    GROUP BY p.id_produk, p.nama_produk, p.harga
    ORDER BY jumlah_terjual DESC
    LIMIT 5
");
$top_products = mysqli_fetch_all($top_products_query, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Grafik Penjualan</title>
    <meta name="robots" content="noindex, nofollow" />
    <meta content="" name="description" />
    <meta content="" name="keywords" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
        h5{
            color: #2D3A3A !important;
            font-weight: bold !important;
            font-size: 18px !important;
        }
         /* Styling untuk tombol kembali (baru) */
        .standalone-back-button-container {
            margin-bottom: 15px; /* Jarak bawah dari tombol kembali */
            padding-left: 0px; /* Sesuaikan padding agar sejajar dengan konten */
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
    </style>
    <!-- HEADER -->
    <?php require "atas.php"; ?>

    <!-- SIDEBAR -->
    <?php require "menu.php"; ?>

    <main id="main" class="main">
            <div class="container">
                <div class="standalone-back-button-container">
                <a href="penjualan.php" class="standalone-back-button">
                    <i class="bi bi-arrow-left"></i>
                    Kembali
                </a>
            </div>
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

            <!-- Total Produk & Produk Terjual -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Produk</h5>
                            <h3 class="text-primary"><?= $total_products ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Produk Terjual</h5>
                            <h3 class="text-success"><?= $total_sold ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grafik Penjualan -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Grafik Penjualan</h5>
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Produk Paling Laris -->
            <div class="row mb-4">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Produk Paling Laris</h5>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nama Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah Terjual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($top_products)) : ?>
                                        <?php foreach ($top_products as $index => $product) : ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($product['nama_produk']) ?></td>
                                                <td>Rp <?= number_format($product['harga'], 0, ',', '.') ?></td>
                                                <td><?= $product['jumlah_terjual'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="4">Tidak ada data produk.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Chart.js Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
