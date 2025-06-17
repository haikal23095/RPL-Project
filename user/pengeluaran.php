<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require "../db.php"; // Koneksi ke database
$page = "history"; // Sesuaikan dengan nilai $page di profil_menu.php jika ini menu histori pengeluaran

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
if (!$kue_user) {
    die("Error fetching user: " . mysqli_error($kon));
}
$row_user = mysqli_fetch_array($kue_user);
$user_id = $row_user['id_user'];


// Query untuk grafik: total pengeluaran per hari pada bulan & tahun terpilih
// Menggunakan total_harga dari tabel pesanan untuk grafik keseluruhan
$grafikQuery = "SELECT
    DATE(p.tanggal_pesanan) as tanggal,
    SUM(p.total_harga) as total
    FROM pesanan p
    WHERE p.id_user = ?";

$grafikParams = [$user_id];
$bindTypesGrafik = "i"; // Tipe untuk id_user

if (!empty($month)) {
    $grafikQuery .= " AND MONTH(p.tanggal_pesanan) = ?";
    $grafikParams[] = (int)$month; // Cast to int
    $bindTypesGrafik .= "i"; // Tipe untuk bulan (integer)
}

if (!empty($year)) {
    $grafikQuery .= " AND YEAR(p.tanggal_pesanan) = ?";
    $grafikParams[] = (int)$year; // Cast to int
    $bindTypesGrafik .= "i"; // Tipe untuk tahun (integer)
}

$grafikQuery .= " GROUP BY tanggal ORDER BY tanggal ASC";


$grafikStmt = $kon->prepare($grafikQuery);
// Check if prepare was successful
if ($grafikStmt === false) {
    die("Prepare failed for grafikQuery: " . $kon->error);
}

// Perbaikan PENTING untuk bind_param: Gunakan referensi untuk setiap parameter
// Buat array referensi
$grafikRefs = [];
foreach ($grafikParams as $key => $value) {
    $grafikRefs[$key] = &$grafikParams[$key];
}
// print_r($grafikRefs); // Debugging: Cek isi $grafikRefs
call_user_func_array([$grafikStmt, 'bind_param'], array_merge([$bindTypesGrafik], $grafikRefs));

$grafikStmt->execute();
$grafikResult = $grafikStmt->get_result();

$grafikLabels = [];
$grafikData = [];
while ($row = $grafikResult->fetch_assoc()) {
    $grafikLabels[] = $row['tanggal'];
    $grafikData[] = $row['total'];
}
$grafikStmt->close(); // Tutup statement grafik


// Query untuk menampilkan data pengeluaran berdasarkan bulan dan tahun jika dipilih
// KORRECTED: Menggunakan pesanan_detail dan produk untuk mendapatkan detail per item
$tableQuery = "SELECT
            p.id_pesanan,
            p.tanggal_pesanan,
            p.total_harga,
            p.status_pesanan,
            p.sudah_dinilai
          FROM pesanan p
          WHERE p.id_user = ?";
$tableParams = [$user_id];
$tableBindTypes = "i";

if (!empty($month)) {
    $tableQuery .= " AND MONTH(p.tanggal_pesanan) = ?";
    $tableParams[] = (int)$month; // Cast to int
    $tableBindTypes .= "i"; // Tipe untuk bulan (integer)
}
if (!empty($year)) {
    $tableQuery .= " AND YEAR(p.tanggal_pesanan) = ?";
    $tableParams[] = (int)$year; // Cast to int
    $tableBindTypes .= "i"; // Tipe untuk tahun (integer)
}

$tableQuery .= " ORDER BY p.tanggal_pesanan DESC"; // Order by tanggal pesanan

$stmt = $kon->prepare($tableQuery);
// Check if prepare was successful
if ($stmt === false) {
    die("Prepare failed for detail query: " . $kon->error);
}

// Perbaikan PENTING untuk bind_param: Gunakan referensi untuk setiap parameter
// Buat array referensi
$refs = [];
foreach ($tableParams as $key => $value) {
    $refs[$key] = &$tableParams[$key];
}
// Baris 108
call_user_func_array([$stmt, 'bind_param'], array_merge([$tableBindTypes], $refs));

$stmt->execute();
$result = $stmt->get_result();

$totalPengeluaran = 0; // Ini akan menghitung total pengeluaran dari data yang ditampilkan di tabel

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
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php include 'aset.php'; ?>
</head>
<body>
    <?php require "atas.php"; ?>
    <?php require "profil_menu.php"; ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif;
            color: #2D3A3A !important;
        }
        .btn-primary{
            background-color: #FFBB34 !important;
            border: 1px solid #FFBB34 !important;
        }
        .sidebar {
            width: auto; /* Equivalent to w-64 in Tailwind */
            background-color: #F8F7F1;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100vh;
        }
    </style>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-wallet2"></i>&nbsp; RIWAYAT PENGELUARAN</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">PROFIL</a></li>
                    <li class="breadcrumb-item active">RIWAYAT PENGELUARAN</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">

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
                                        <?php if (!empty($month) || !empty($year)): ?>
                                            <a href="pengeluaran.php" class="btn btn-secondary">Reset</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>

                            <div class="mb-4">
                                <canvas id="pengeluaranChart" height="100"></canvas>
                            </div>

                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>ID Pesanan</th>
                                        <th>Tanggal Pembelian</th>
                                        <th>Status Pesanan</th>
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
                                                    <td>" . htmlspecialchars($row['id_pesanan']) . "</td>
                                                    <td>{$row['tanggal_pesanan']}</td>
                                                    <td>" . htmlspecialchars($row['status_pesanan']) . "</td>
                                                    <td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>
                                                </tr>";
                                            $no++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center'>Tidak ada data pengeluaran</td></tr>";
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">Total Pengeluaran (Bulan/Tahun Terpilih)</th>
                                        <th>Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></th>
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
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <script src="../assets/js/main.js"></script>
    <script>
    const ctx = document.getElementById('pengeluaranChart').getContext('2d');
    const pengeluaranChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($grafikLabels) ?>,
            datasets: [{
                label: 'Total Pengeluaran',
                data: <?= json_encode($grafikData) ?>,
                borderColor: 'rgba(255, 159, 64, 1)',
                backgroundColor: 'rgba(255, 159, 64, 0.7)',
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
</body>
</html>