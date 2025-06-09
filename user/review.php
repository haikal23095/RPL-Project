<?php
session_start();
include('../db.php'); // Koneksi ke database

// Pastikan user login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Ambil nama user dari session
$nama_user = $_SESSION["user"];

// Ambil data user
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$nama_user'");
$row_user = mysqli_fetch_array($kue_user);
$userId = $row_user['id_user'];

// Ambil ID pesanan dari URL
$orderId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validasi ID pesanan
if ($orderId <= 0) {
    die("ID pesanan tidak valid.");
}

// Inisialisasi pesan
$successMessage = '';
$errorMessage = '';

// Ambil semua produk dalam pesanan (dari pesanan_detail)
$sql = "SELECT pd.id_produk, pr.gambar, pr.nama_produk
        FROM pesanan_detail pd
        JOIN produk pr ON pd.id_produk = pr.id_produk
        WHERE pd.id_pesanan = ?";
$stmt = mysqli_prepare($kon, $sql);
mysqli_stmt_bind_param($stmt, "i", $orderId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$produk_pesanan = [];
while ($row = mysqli_fetch_assoc($result)) {
    $produk_pesanan[] = $row;
}
mysqli_stmt_close($stmt);

if (empty($produk_pesanan)) {
    die("Pesanan tidak ditemukan atau tidak ada produk di dalamnya.");
}



// Ambil informasi ulasan yang sudah ada
$sql = "SELECT COUNT(*) as count FROM review_produk WHERE id_produk = ? AND id_user = ? AND id_pesanan = ?";
$stmt = mysqli_prepare($kon, $sql);
mysqli_stmt_bind_param($stmt, "iii", $id_produk, $userId, $id_pesanan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$reviewCount = mysqli_fetch_assoc($result)['count'];
mysqli_stmt_close($stmt);

// Proses pengiriman ulasan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reviewed = false;
    foreach ($produk_pesanan as $produk) {
        $id_produk = $produk['id_produk'];
        $rating_produk = isset($_POST["rating_produk_$id_produk"]) ? intval($_POST["rating_produk_$id_produk"]) : 0;
        $rating_pelayanan = isset($_POST["rating_pelayanan_$id_produk"]) ? intval($_POST["rating_pelayanan_$id_produk"]) : 0;
        $rating_pengiriman = isset($_POST["rating_pengiriman_$id_produk"]) ? intval($_POST["rating_pengiriman_$id_produk"]) : 0;
        $review = isset($_POST["review_$id_produk"]) ? trim($_POST["review_$id_produk"]) : '';

        // Cek apakah sudah pernah review produk ini di pesanan ini
        $sql = "SELECT COUNT(*) as count FROM review_produk WHERE id_produk = ? AND id_user = ? AND id_pesanan = ?";
        $stmt = mysqli_prepare($kon, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $id_produk, $userId, $orderId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $reviewCount = mysqli_fetch_assoc($result)['count'];
        mysqli_stmt_close($stmt);

        if ($reviewCount > 0) {
            $errorMessage .= "Anda sudah memberikan ulasan untuk produk {$produk['nama_produk']}.<br>";
            continue;
        }

        // Validasi rating dan ulasan
        if ($rating_produk < 1 || $rating_produk > 5) {
            $errorMessage .= "Rating produk untuk {$produk['nama_produk']} harus antara 1 dan 5.<br>";
            continue;
        }
        if (empty($review)) {
            $errorMessage .= "Ulasan untuk {$produk['nama_produk']} tidak boleh kosong.<br>";
            continue;
        }

        // Simpan ulasan ke database
        $sql = "INSERT INTO review_produk (id_user, id_produk, id_pesanan, rating_produk, rating_pelayanan, rating_pengiriman, komentar) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($kon, $sql);
        mysqli_stmt_bind_param($stmt, "iiiiiis", $userId, $id_produk, $orderId, $rating_produk, $rating_pelayanan, $rating_pengiriman, $review);

        if (mysqli_stmt_execute($stmt)) {
            $reviewed = true;
        } else {
            $errorMessage .= "Terjadi kesalahan saat menambahkan ulasan untuk {$produk['nama_produk']}: " . mysqli_error($kon) . "<br>";
        }
        mysqli_stmt_close($stmt);
    }
    if ($reviewed && empty($errorMessage)) {
        $successMessage = "Ulasan berhasil ditambahkan!";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Ulasan Pesanan</title>
    <!-- Favicons -->
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

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

    <style>
        body {
            background-color: #f8f9fa; /* Warna latar belakang yang lebih terang */
        }

        .card {
            border-radius: 10px; /* Membuat sudut kartu lebih bulat */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Menambahkan bayangan pada kartu */
            margin-top: 20px; /* Menambahkan jarak atas untuk kartu */
            padding: 20px; /* Menambahkan padding di dalam kartu */
            background-color: #ffffff; /* Warna latar belakang kartu */
        }

        .card-title {
            font-size: 1.5rem; /* Ukuran font yang lebih besar untuk judul */
            margin-bottom: 20px; /* Jarak bawah yang lebih besar */
            color: #333; /* Warna teks yang lebih gelap */
        }

        .form-label {
            font-weight: bold; /* Membuat label lebih tebal */
            color: #555; /* Warna label yang lebih gelap */
        }

        .form-select, .form-control {
            border-radius: 5px; /* Membuat sudut input lebih bulat */
            border: 1px solid #ccc; /* Warna border yang lebih lembut */
            margin-bottom: 15px; /* Menambahkan jarak bawah untuk input */
        }

        .btn-primary {
            background-color: #007bff; /* Warna latar belakang tombol */
            border: none; /* Menghilangkan border default */
            border-radius: 5px; /* Membuat sudut tombol lebih bulat */
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Warna latar belakang saat hover */
        }
    </style>
</head>
<body>
     <!-- Header -->
     <?php require "atas.php"; ?>
    <!-- Sidebar -->
    <?php require "menu.php"; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-box-arrow-in-left"></i> Review Pesanan</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Review Pesanan</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card mb-4">
                        <h3 class="card-title">Ulasan untuk Pesanan #<?= htmlspecialchars($orderId); ?></h3>
                        
                        <!-- Pesan sukses atau error -->
                        <?php if ($successMessage): ?>
                            <div class="alert alert-success"><?= $successMessage; ?></div>
                        <?php elseif ($errorMessage): ?>
                            <div class="alert alert-danger"><?= $errorMessage; ?></div>
                        <?php endif; ?>

                        <!-- Formulir Ulasan -->
                        <form method="POST" class="mt-4">
                            <?php foreach ($produk_pesanan as $produk): ?>
                                <div class="mb-4 border-bottom pb-3">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <img src="../uploads/<?= htmlspecialchars($produk['gambar']); ?>" alt="Gambar Produk" class="img-fluid rounded" style="max-height: 100px;">
                                        </div>
                                        <div class="col-md-10">
                                            <h5><?= htmlspecialchars($produk['nama_produk']); ?></h5>
                                            <div class="mb-2">
                                                <label for="rating_produk_<?= $produk['id_produk'] ?>" class="form-label">Kualitas Produk:</label>
                                                <select name="rating_produk_<?= $produk['id_produk'] ?>" id="rating_produk_<?= $produk['id_produk'] ?>" class="form-select" required>
                                                    <option value="1">1 - Sangat Buruk</option>
                                                    <option value="2">2 - Buruk</option>
                                                    <option value="3">3 - Cukup</option>
                                                    <option value="4">4 - Baik</option>
                                                    <option value="5">5 - Sangat Baik</option>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label for="rating_pelayanan_<?= $produk['id_produk'] ?>" class="form-label">Kualitas Pelayanan:</label>
                                                <select name="rating_pelayanan_<?= $produk['id_produk'] ?>" id="rating_pelayanan_<?= $produk['id_produk'] ?>" class="form-select" required>
                                                    <option value="1">1 - Sangat Buruk</option>
                                                    <option value="2">2 - Buruk</option>
                                                    <option value="3">3 - Cukup</option>
                                                    <option value="4">4 - Baik</option>
                                                    <option value="5">5 - Sangat Baik</option>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label for="rating_pengiriman_<?= $produk['id_produk'] ?>" class="form-label">Kecepatan Pengiriman:</label>
                                                <select name="rating_pengiriman_<?= $produk['id_produk'] ?>" id="rating_pengiriman_<?= $produk['id_produk'] ?>" class="form-select" required>
                                                    <option value="1">1 - Sangat Buruk</option>
                                                    <option value="2">2 - Buruk</option>
                                                    <option value="3">3 - Cukup</option>
                                                    <option value="4">4 - Baik</option>
                                                    <option value="5">5 - Sangat Baik</option>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label for="review_<?= $produk['id_produk'] ?>" class="form-label">Deskripsi:</label>
                                                <textarea name="review_<?= $produk['id_produk'] ?>" id="review_<?= $produk['id_produk'] ?>" class="form-control" rows="3" placeholder="Ketikkan kesan anda disini" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <button type="submit" class="btn btn-primary w-100">Kirim Ulasan</button>
                            <a href="pesanan_selesai.php" class="btn btn-secondary w-100 mt-2">Kembali</a>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- Vendor JS Files -->
    <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/chart.js/chart.min.js"></script>
    <script src="../assets/vendor/echarts/echarts.min.js"></script>
    <script src="../assets/vendor/quill/quill.min.js"></script>
    <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>

    <!-- Template Main JS File -->
    <script src="../assets/js/main.js"></script>
</body>
</html>
