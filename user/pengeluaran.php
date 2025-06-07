<?php
session_start();
require "../db.php";
$page = "history";

// Cek apakah pengguna sudah login
if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
    exit;
}

// Filter bulan dan tahun
$month = isset($_GET['month']) ? $_GET['month'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : '';

// Ambil data user yang sedang login
$user = $_SESSION["user"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);
$user_id = $row_user['id_user'];


// Query untuk grafik: total pengeluaran per hari pada bulan & tahun terpilih
$grafikQuery = "SELECT 
    DATE(pesanan.tanggal_pesanan) as tanggal, 
    SUM(pesanan.total_harga) as total
    FROM pesanan
    WHERE pesanan.id_user = ?";

$grafikParams = [$user_id];

if (!empty($month)) {
    $grafikQuery .= " AND MONTH(pesanan.tanggal_pesanan) = ?";
    $grafikParams[] = $month;
}

if (!empty($year)) {
    $grafikQuery .= " AND YEAR(pesanan.tanggal_pesanan) = ?";
    $grafikParams[] = $year;
}

$grafikQuery .= " GROUP BY tanggal ORDER BY tanggal ASC";


$grafikStmt = $kon->prepare($grafikQuery);
$grafikStmt->bind_param(str_repeat("i", count($grafikParams)), ...$grafikParams);
$grafikStmt->execute();
$grafikResult = $grafikStmt->get_result();

$grafikLabels = [];
$grafikData = [];
while ($row = $grafikResult->fetch_assoc()) {
    $grafikLabels[] = $row['tanggal'];
    $grafikData[] = $row['total'];
}

// Query untuk menampilkan data pengeluaran berdasarkan bulan dan tahun jika dipilih
$query = "SELECT 
            produk.nama_produk, 
            pesanan.tanggal_pesanan, 
            produk.harga, 
            pesanan.jumlah AS kuantitas, 
            pesanan.total_harga
          FROM pesanan
          JOIN produk ON pesanan.id_produk = produk.id_produk
          WHERE pesanan.id_user = ?";
$params = [$user_id];

if (!empty($month)) {
    $query .= " AND MONTH(pesanan.tanggal_pesanan) = ?";
    $params[] = $month;
}
if (!empty($year)) {
    $query .= " AND YEAR(pesanan.tanggal_pesanan) = ?";
    $params[] = $year;
}

$query .= " ORDER BY pesanan.tanggal_pesanan DESC";
$stmt = $kon->prepare($query);
$stmt->bind_param(str_repeat("i", count($params)), ...$params);
$stmt->execute();
$result = $stmt->get_result();

$totalPengeluaran = 0;

// Array nama bulan
$nama_bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riwayat Pengeluaran</title>
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <!-- ...existing code... -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- ...existing code... -->
    <?php include 'aset.php'; ?>
</head>
<body>
    <?php require "atas.php"; ?>
    <?php require "profil_menu.php"; ?>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-clock-history"></i>&nbsp; Riwayat Pengeluaran</h1>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <!-- Filter Bulan dan Tahun -->
                            <form method="GET" class="mb-3">
                                <br>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>Bulan</label>
                                        <select name="month" class="form-control">
                                            <option value="">Semua Bulan</option>
                                            <?php 
                                            foreach ($nama_bulan as $index => $nama) {
                                                $monthVal = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                                                $selected = ($monthVal == $month) ? 'selected' : '';
                                                echo "<option value='$monthVal' $selected>$nama</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Tahun</label>
                                        <select name="year" class="form-control">
                                            <option value="">Semua Tahun</option>
                                            <?php for ($y = 2020; $y <= date('Y'); $y++) {
                                                $selected = ($y == $year) ? 'selected' : '';
                                                echo "<option value='$y' $selected>$y</option>";
                                            } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 align-self-end">
                                        <button type="submit" class="btn btn-primary">Filter</button>
                                    </div>
                                </div>
                            </form>

                            <!-- Grafik Pengeluaran -->
                            <div class="mb-4">
                                <canvas id="pengeluaranChart" height="100"></canvas>
                            </div>

                            <!-- Tabel Data Pengeluaran -->
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Produk</th>
                                        <th>Tanggal Pembelian</th>
                                        <th>Harga Satuan</th>
                                        <th>Kuantitas</th>
                                        <th>Total Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        $no = 1;
                                        while ($row = $result->fetch_assoc()) {
                                            $totalPengeluaran += $row['total_harga'];
                                            echo "<tr>
                                                    <td>{$no}</td>
                                                    <td>{$row['nama_produk']}</td>
                                                    <td>{$row['tanggal_pesanan']}</td>
                                                    <td>Rp " . number_format($row['harga'], 0, '', '.') . "</td>
                                                    <td>{$row['kuantitas']}</td>
                                                    <td>Rp " . number_format($row['total_harga'], 0, '', '.') . "</td>
                                                </tr>";
                                            $no++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center'>Tidak ada data pengeluaran</td></tr>";
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-right">Total Pengeluaran</th>
                                        <th>Rp <?= number_format($totalPengeluaran, 0, '', '.') ?></th>
                                    </tr>
                                </tfoot>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core Bootstrap JS -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <!-- ...existing code... -->
    <script>
    const ctx = document.getElementById('pengeluaranChart').getContext('2d');
    const pengeluaranChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($grafikLabels) ?>,
            datasets: [{
                label: 'Total Pengeluaran',
                data: <?= json_encode($grafikData) ?>,
                borderColor: 'rgba(255, 159, 64, 1)', // Orange
                backgroundColor: 'rgba(255, 159, 64, 0.7)', // Orange transparan
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Grafik Pengeluaran per Hari' }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
    </script>
<!-- ...existing code... -->                    
</body>
</html>
