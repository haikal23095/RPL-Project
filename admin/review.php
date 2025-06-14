<?php
session_start();
include('../db.php'); // Koneksi ke database
$page = "review";

// Pastikan admin login
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit();
}

// Inisialisasi pesan
$successMessage = '';
$errorMessage = '';

// Proses pengiriman balasan admin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_review = isset($_POST['id_review']) ? intval($_POST['id_review']) : 0;
    $response = isset($_POST['response']) ? trim($_POST['response']) : '';

    // Validasi input
    if ($id_review <= 0 || empty($response)) {
        $errorMessage = "ID review atau balasan tidak valid.";
    } else {
        // Simpan balasan admin ke database
        $sql = "UPDATE review_produk SET komentar_admin = ? WHERE id_review = ?";
        $stmt = mysqli_prepare($kon, $sql);
        if ($stmt) { // Cek apakah persiapan berhasil
            mysqli_stmt_bind_param($stmt, "si", $response, $id_review);

            if (mysqli_stmt_execute($stmt)) {
                $successMessage = "Balasan berhasil ditambahkan.";
            } else {
                $errorMessage = "Terjadi kesalahan saat menambahkan balasan: " . mysqli_error($kon);
            }

            mysqli_stmt_close($stmt);
        } else {
            $errorMessage = "Gagal mempersiapkan pernyataan: " . mysqli_error($kon);
        }
    }
}

// Ambil semua ulasan dari database
$sql = "SELECT rp.id_review, rp.id_produk, rp.rating_produk, rp.komentar, rp.tanggal_review, rp.komentar_admin, 
        p.nama_produk, p.gambar, u.nama
        FROM review_produk rp
        JOIN produk p ON rp.id_produk = p.id_produk
        JOIN user u ON rp.id_user = u.id_user
        ORDER BY rp.tanggal_review DESC";
$result = mysqli_query($kon, $sql);
if ($result) {
    $reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
} else {
    $errorMessage = "Gagal mengambil data ulasan: " . mysqli_error($kon);
    $reviews = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Ulasan Produk</title>
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif;
            color: #2D3A3A;
        }
        h2 {
            font-size: 18px !important;
            font-weight: bold !important;
            color: #2D3A3A !important;
        }
        h5 {
            font-size: 18px !important;
            font-weight: bold !important;
            color: #2D3A3A !important;
        }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .rating-star {
            color: #ffc107;
        }
        .admin-reply {
            background-color: #f8f9fa;
            border-left: 3px solid #0d6efd;
            padding: 10px;
            margin-top: 10px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <?php require "atas.php"; ?>
    <?php require "menu.php"; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-chat-square-dots-fill"></i> ULASAN PRODUK</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                    <li class="breadcrumb-item active">ULASAN</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Daftar Ulasan dari Pengguna</h5>

                    <?php if ($successMessage): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= $successMessage; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php elseif ($errorMessage): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                             <?= $errorMessage; ?>
                             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Pengguna</th>
                                    <th>Rating</th>
                                    <th>Ulasan</th>
                                    <th>Balasan Anda</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reviews)): ?>
                                    <tr><td colspan="6" class="text-center">Belum ada ulasan.</td></tr>
                                <?php endif; ?>
                                <?php foreach ($reviews as $review): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="../uploads/<?= htmlspecialchars($review['gambar']); ?>" alt="<?= htmlspecialchars($review['nama_produk']); ?>" class="product-img me-2">
                                                <span><?= htmlspecialchars($review['nama_produk']); ?></span>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($review['nama']); ?></td>
                                        <td>
                                            <span class="fw-bold"><?= htmlspecialchars($review['rating_produk']); ?></span>
                                            <i class="bi bi-star-fill rating-star"></i>
                                        </td>
                                        <td><?= htmlspecialchars($review['komentar']); ?></td>
                                        <td>
                                            <?php if (empty($review['komentar_admin'])): ?>
                                                <form method="POST">
                                                    <input type="hidden" name="id_review" value="<?= $review['id_review']; ?>">
                                                    <div class="input-group">
                                                        <textarea name="response" class="form-control" rows="2" placeholder="Ketik balasan..." required></textarea>
                                                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-send"></i></button>
                                                    </div>
                                                </form>
                                            <?php else: ?>
                                                <div class="admin-reply">
                                                    <?= htmlspecialchars($review['komentar_admin']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('d M Y', strtotime($review['tanggal_review'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
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