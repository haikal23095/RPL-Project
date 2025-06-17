<?php
require_once '../db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$user = $_SESSION["user"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);
$userId = $row_user['id_user'];

$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($orderId <= 0) {
    die("ID pesanan tidak valid.");
}

$sql = "SELECT p.status_pesanan, pg.nomor_resi, pg.nama_kurir, pg.alamat_pengiriman, 
               pg.tanggal_kirim, pg.perkiraan_tiba, pg.tanggal_tiba, 
               pg.status_pengiriman, pg.biaya_kirim
        FROM pengiriman_pesanan pg
        JOIN pesanan p ON pg.id_pesanan = p.id_pesanan
        WHERE pg.id_pesanan = ? AND p.id_user = ?";
$stmt = mysqli_prepare($kon, $sql);

if (!$stmt) {
    error_log("Prepare statement gagal: " . mysqli_error($kon));
    die("Terjadi kesalahan sistem.");
}

mysqli_stmt_bind_param($stmt, "ii", $orderId, $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$shippingData = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$shippingData) {
    die("Data pengiriman tidak ditemukan atau Anda tidak berhak mengakses pesanan ini.");
}

// Logika untuk menentukan step pada progress bar 4-langkah
$currentStep = 1; // Step 1: Pesanan dibuat (selalu aktif)
if ($shippingData['status_pesanan'] === 'Diproses' || $shippingData['status_pesanan'] === 'Dikirim' || $shippingData['status_pesanan'] === 'Selesai') {
    $currentStep = 2; // Step 2: Pesanan diproses
}
if ($shippingData['status_pesanan'] === 'Dikirim' || $shippingData['status_pesanan'] === 'Selesai') {
    $currentStep = 3; // Step 3: Pesanan dikirim
}
if ($shippingData['status_pesanan'] === 'Selesai') {
    $currentStep = 4; // Step 4: Tiba di tujuan
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Lacak Pesanan Anda</title>
    
    <?php include 'aset.php'; ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif !important;
            color: #2D3A3A !important;
        }
        .sidebar {
            background-color: #F8F7F1 !important;
        }
        header{
            background-color: #F8F7F1 !important;
        }
        .track-section {
            background-color: #fff;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 25px rgba(0,0,0,0.1);
        }
        .track-header h2 {
            font-weight: 700;
            color: #333;
        }
        .track-header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        .shipping-info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: .5rem;
            padding: 1rem;
        }
        
        /* Progress Tracker */
        .progress-tracker {
            display: flex;
            justify-content: space-between;
            list-style: none;
            padding: 0;
            margin: 2.5rem 0;
            position: relative;
        }
        .progress-tracker::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 4px;
            background-color: #e9ecef;
            transform: translateY(-50%);
            z-index: 1;
        }
        .progress-bar-line {
            position: absolute;
            top: 50%;
            left: 0;
            height: 4px;
            background-color: #0d6efd;
            transform: translateY(-50%);
            z-index: 2;
            transition: width 0.5s ease;
        }
        .progress-step {
            position: relative;
            text-align: center;
            z-index: 3;
            width: 25%;
        }
        .progress-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            border: 4px solid #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            transition: background-color 0.5s ease, color 0.5s ease;
        }
        .progress-label {
            font-size: 0.85rem;
            color: #6c757d;
            font-weight: 600;
        }
        .progress-step.active .progress-icon {
            background-color: #0d6efd;
            color: #fff;
        }
        .progress-step.active .progress-label {
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <?php require "atas.php"; ?>
    <?php require "profil_menu.php"; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-geo-alt-fill"></i> Lacak Pesanan</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Lacak Pesanan</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="track-section">
                <div class="track-header text-center">
                    <h2>Lacak Pesanan</h2>
                    <p>Pesananmu sedang dalam perjalanan!</p>
                </div>

                <div class="shipping-info-box my-4">
                    <div class="row">
                        <div class="col-md-6 mb-2 mb-md-0">
                            <strong>Estimasi Tiba</strong>
                            <p class="mb-0"><?= isset($shippingData['perkiraan_tiba']) ? date('d F Y', strtotime($shippingData['perkiraan_tiba'])) : 'Tidak tersedia'; ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                             <strong><?= isset($shippingData['nama_kurir']) ? htmlspecialchars($shippingData['nama_kurir']) : 'Kurir'; ?></strong>
                            <p class="mb-0"><?= isset($shippingData['nomor_resi']) ? htmlspecialchars($shippingData['nomor_resi']) : 'Tidak tersedia'; ?></p>
                        </div>
                    </div>
                </div>

                <div class="progress-tracker">
                    <?php 
                        $widthPercentage = ($currentStep - 1) * 33.33; 
                    ?>
                    <div class="progress-bar-line" style="width: <?= $widthPercentage ?>%;"></div>
                    <div class="progress-step <?= $currentStep >= 1 ? 'active' : '' ?>">
                        <div class="progress-icon"><i class="bi bi-journal-check"></i></div>
                        <div class="progress-label">Pesanan Dibuat</div>
                    </div>
                    <div class="progress-step <?= $currentStep >= 2 ? 'active' : '' ?>">
                        <div class="progress-icon"><i class="bi bi-box-seam"></i></div>
                        <div class="progress-label">Pesanan Diproses</div>
                    </div>
                    <div class="progress-step <?= $currentStep >= 3 ? 'active' : '' ?>">
                        <div class="progress-icon"><i class="bi bi-truck"></i></div>
                        <div class="progress-label">Pesanan Dikirim</div>
                    </div>
                    <div class="progress-step <?= $currentStep >= 4 ? 'active' : '' ?>">
                        <div class="progress-icon"><i class="bi bi-house-door-fill"></i></div>
                        <div class="progress-label">Tiba di Tujuan</div>
                    </div>
                </div>
                
                <hr class="my-4">

                <div class="card">
                    <div class="card-header fw-bold">Rincian Pengiriman</div>
                    <div class="card-body pt-3">
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Jasa Pengiriman</div>
                            <div class="col-sm-8"><?= isset($shippingData['nama_kurir']) ? htmlspecialchars($shippingData['nama_kurir']) : '-'; ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">No. Resi</div>
                            <div class="col-sm-8"><?= isset($shippingData['nomor_resi']) ? htmlspecialchars($shippingData['nomor_resi']) : '-'; ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Alamat</div>
                            <div class="col-sm-8"><?= isset($shippingData['alamat_pengiriman']) ? htmlspecialchars($shippingData['alamat_pengiriman']) : '-'; ?></div>
                        </div>
                    </div>
                </div>
                 <a href="history_pembayaran.php" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
        </section>
    </main>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>