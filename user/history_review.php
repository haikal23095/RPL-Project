<?php
session_start();
include('../db.php'); // Koneksi ke database
$page = "history_review";

// Pastikan user login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$usernama = $_SESSION['user']; // Ambil nama user dari session

// Ambil semua ulasan dari database untuk user yang login
$sql = "SELECT rp.id_review, rp.id_produk, rp.rating_produk, rp.rating_pelayanan, rp.rating_pengiriman, rp.komentar, rp.tanggal_review, rp.komentar_admin, 
        p.nama_produk, p.gambar, u.nama
        FROM review_produk rp
        JOIN produk p ON rp.id_produk = p.id_produk
        JOIN user u ON rp.id_user = u.id_user
        WHERE u.nama = ?
        ORDER BY rp.tanggal_review DESC";

$stmt = mysqli_prepare($kon, $sql);
mysqli_stmt_bind_param($stmt, "s", $usernama);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$reviews = [];
if ($result) {
    $reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
} else {
    // Sebaiknya ada penanganan error jika query gagal
    die("Gagal mengambil data ulasan: " . mysqli_error($kon));
}
mysqli_stmt_close($stmt);

// Logika untuk pesan balasan (jika ada)
$successMessage = '';
$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_review'])) {
    // Logika untuk memproses balasan admin bisa ditaruh di sini jika halaman ini juga untuk admin.
    // Namun karena ini di folder /user, kita asumsikan ini hanya untuk tampilan.
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Riwayat Ulasan</title>
    
    <?php include "aset.php"; ?>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif;
            color: #2D3A3A !important;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .rating-star {
            color: #ffc107; /* Warna kuning untuk bintang */
        }
    </style>
</head>
<body>
    <?php require "atas.php"; ?>
    <?php require "profil_menu.php"; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-chat-quote"></i> RIWAYAT ULASAN</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">PROFIL</a></li>
                    <li class="breadcrumb-item active">RIWAYAT ULASAN</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Ulasan yang Telah Anda Berikan</h5>

                    <?php if ($successMessage): ?>
                        <div class="alert alert-success"><?= $successMessage; ?></div>
                    <?php elseif ($errorMessage): ?>
                        <div class="alert alert-danger"><?= $errorMessage; ?></div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Produk</th>
                                    <th>Rating Keseluruhan</th>
                                    <th>Ulasan Anda</th>
                                    <th>Balasan Admin</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reviews)): ?>
                                    <tr>
                                        <td colspan="6">
                                            <div class="alert alert-info text-center">Anda belum memberikan ulasan apapun.</div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reviews as $index => $review): ?>
                                        <tr>
                                            <td><?= $index + 1; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="../uploads/<?= htmlspecialchars($review['gambar']); ?>" alt="<?= htmlspecialchars($review['nama_produk']); ?>" class="product-img me-3">
                                                    <span><?= htmlspecialchars($review['nama_produk']); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <?php
                                                // --- PERBAIKAN LOGIKA ---
                                                // Hitung rata-rata rating untuk ulasan ini saja.
                                                $average_rating = ($review['rating_produk'] + $review['rating_pelayanan'] + $review['rating_pengiriman']) / 3;
                                                ?>
                                                <span class="fw-bold"><?= number_format($average_rating, 1); ?></span>
                                                <i class="bi bi-star-fill rating-star"></i>
                                            </td>
                                            <td><?= htmlspecialchars($review['komentar']); ?></td>
                                            <td>
                                                <?php if (empty($review['komentar_admin'])): ?>
                                                    <span class="text-muted fst-italic">Belum ada balasan</span>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($review['komentar_admin']); ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d M Y', strtotime($review['tanggal_review'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>