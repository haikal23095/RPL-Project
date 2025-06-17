<?php
session_start();
$page = "penjualan";
if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit;
}

// Koneksi Database menggunakan PDO
$host = 'localhost';
$dbname = 'casaluxedb';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// 1. Ambil semua kategori untuk dropdown filter
$categories_stmt = $pdo->query("SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Ambil nilai filter dari URL
$filter_periode = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$filter_kategori = isset($_GET['category']) ? $_GET['category'] : 'all';

// 3. Bangun klausa WHERE dinamis untuk query SQL
$base_conditions = "p.status_pesanan != 'Dibatalkan'";
$date_filter = '';
$category_filter = '';

// Filter berdasarkan periode tanggal
if ($filter_periode === 'harian') {
    $date_filter = "AND DATE(p.tanggal_pesanan) = CURDATE()";
} elseif ($filter_periode === 'bulanan') {
    $date_filter = "AND MONTH(p.tanggal_pesanan) = MONTH(CURDATE()) AND YEAR(p.tanggal_pesanan) = YEAR(CURDATE())";
} elseif ($filter_periode === 'tahunan') {
    $date_filter = "AND YEAR(p.tanggal_pesanan) = YEAR(CURDATE())";
}

// Filter berdasarkan kategori
if ($filter_kategori !== 'all' && is_numeric($filter_kategori)) {
    // Menggunakan prepared statement untuk keamanan
    $category_filter = "AND pr.id_kategori = :id_kategori";
}

$conditions = $base_conditions . ' ' . $date_filter . ' ' . $category_filter;

// 4. Hitung total pendapatan (Query diperbaiki agar akurat saat difilter)
$total_query = "SELECT SUM(pd.jumlah * pr.harga) AS total_pendapatan 
                FROM pesanan_detail pd
                JOIN pesanan p ON pd.id_pesanan = p.id_pesanan
                JOIN produk pr ON pd.id_produk = pr.id_produk
                WHERE $conditions";

$total_stmt = $pdo->prepare($total_query);
if ($filter_kategori !== 'all' && is_numeric($filter_kategori)) {
    $total_stmt->bindParam(':id_kategori', $filter_kategori, PDO::PARAM_INT);
}
$total_stmt->execute();
$total_pendapatan = $total_stmt->fetch(PDO::FETCH_ASSOC)['total_pendapatan'] ?? 0;

// 5. Query data pesanan (Query diperbaiki untuk menampilkan subtotal per item)
$query = "SELECT pr.nama_produk, p.tanggal_pesanan, pd.jumlah, pr.harga, (pd.jumlah * pr.harga) AS subtotal
          FROM pesanan_detail pd
          JOIN pesanan p ON pd.id_pesanan = p.id_pesanan
          JOIN produk pr ON pd.id_produk = pr.id_produk
          WHERE $conditions
          ORDER BY p.tanggal_pesanan DESC";

$stmt = $pdo->prepare($query);
if ($filter_kategori !== 'all' && is_numeric($filter_kategori)) {
    $stmt->bindParam(':id_kategori', $filter_kategori, PDO::PARAM_INT);
}
$stmt->execute();
$pesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include 'aset.php'; ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif;
            color: #2D3A3A;
        }
 
        h5{
            font-size: 20px !important;
            color: #2D3A3A !important;
            font-weight: bold !important;
        }
 
        h3{
            font-size: 30px !important;
            color: #efaa31 !important;
        }
        
        .btn-grafik {
            background-color: #FF8C12;
            color: white;
            font-weight: 600;
            border-radius: 0.375rem;
            padding: 0.5rem 1rem;
            text-decoration: none;
            transition: background-color 0.2s;
        }
        .btn-grafik:hover {
            background-color: #e07b0f;
            color: white;
        }
    </style>
</head>
<body>
<?php include 'atas.php'; ?>
<?php include 'menu.php'; ?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1><i class="bi bi-graph-up"></i>&nbsp; DATA PENJUALAN</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                <li class="breadcrumb-item active"> DATA PENJUALAN</li>
            </ol>
        </nav>
    </div>
    <div class="container-fluid">

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Total Pendapatan</h5>
                        <h3>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <form method="GET" class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">Filter Periode</h5>
                        <input type="hidden" name="category" value="<?= htmlspecialchars($filter_kategori) ?>">
                        <select name="filter" id="filter" class="form-select" onchange="this.form.submit()">
                            <option value="all" <?= $filter_periode == 'all' ? 'selected' : '' ?>>Semua</option>
                            <option value="harian" <?= $filter_periode == 'harian' ? 'selected' : '' ?>>Harian</option>
                            <option value="bulanan" <?= $filter_periode == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                            <option value="tahunan" <?= $filter_periode == 'tahunan' ? 'selected' : '' ?>>Tahunan</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center pt-3">
                    <form method="GET" class="w-50">
                        <h5 class="card-title pb-0">Daftar Penjualan</h5>
                        <input type="hidden" name="filter" value="<?= htmlspecialchars($filter_periode) ?>">
                        <select name="category" class="form-select" onchange="this.form.submit()">
                            <option value="all">Semua Kategori</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id_kategori'] ?>" <?= $filter_kategori == $category['id_kategori'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <a href="grafik.php" class="btn btn-grafik mt-4">
                        <i class="bi bi-bar-chart-line"></i> Grafik Penjualan
                    </a>
                </div>

                <hr>

                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Produk</th>
                            <th>Tanggal</th>
                            <th>Jumlah</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pesanan)) : ?>
                            <?php foreach ($pesanan as $index => $order) : ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($order['nama_produk']) ?></td>
                                    <td><?= date('d M Y', strtotime($order['tanggal_pesanan'])) ?></td>
                                    <td><?= $order['jumlah'] ?></td>
                                    <td>Rp <?= number_format($order['harga'], 0, ',', '.') ?></td>
                                    <td>Rp <?= number_format($order['subtotal'], 0, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data penjualan untuk filter yang dipilih.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>
</body>
</html>