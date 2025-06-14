<?php
session_start();
include('../db.php');
$page = "info";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user'];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user_id'");
$row_user = mysqli_fetch_array($kue_user);

// Fetch all active informasipromo
$current_date = date('Y-m-d');
$sql = "SELECT * FROM informasipromo WHERE start_date <= ? AND end_date >= ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($kon, $sql);
mysqli_stmt_bind_param($stmt, "ss", $current_date, $current_date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$informasipromo = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Separate informasipromo by type
$all_informasipromo = $informasipromo;
$discount_informasipromo = array_filter($informasipromo, function($promo) {
    return $promo['promo_type'] === 'discount';
});
$bonus_informasipromo = array_filter($informasipromo, function($promo) {
    return $promo['promo_type'] === 'bonus';
});

$cartSuccess = isset($_GET['cart_success']);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Informasi Promo - User</title>
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Bootstrap CSS -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .promo-header {
            background: linear-gradient(135deg, #4154f1, #6a11cb);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            text-align: center;
        }
        .promo-header h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .promo-header h2 {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }
        .promo-section {
            margin-bottom: 3rem;
        }
        .promo-section-title {
            color: #4154f1;
            border-bottom: 2px solid #4154f1;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .promo-card {
            margin-bottom: 20px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }
        .promo-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .promo-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .card-body {
            padding: 1.5rem;
        }
        .card-title {
            color: #4154f1;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .discount-badge {
            background-color: #ff6b6b;
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9rem;
        }
        .special-offer {
            background-color: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            margin-top: 2rem;
        }
        .special-offer h3 {
            color: #4154f1;
            font-weight: bold;
        }
        .divider {
            border-top: 2px dashed #4154f1;
            margin: 2rem 0;
        }
    </style>
    <?php include 'aset.php'; ?>
</head>
<body>
    <?php require "atas.php"; ?>
    <?php require "menu.php"; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-megaphone"></i>&nbsp;INFORMASI PROMO</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                    <li class="breadcrumb-item active">INFORMASI PROMO</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Promo Header Section -->
                    <!-- <div class="promo-header">
                        <h1>DAPATKAN DISKON SAMPAI 100%</h1>
                        <h2>AMBIL PROMO REDEEM-MU</h2>
                    </div> -->


                    <div class="d-flex justify-content-center mb-4">
                        <img src="../assets/img/promo1.png" alt="promo1" style="width: 100%; max-width: 1500px;" class="">
                    </div>
                                        <!-- All Promo Section -->

                    <img src="../assets/img/promo1.png" alt="promo1" style="margin-bottom:40px; width: 1000px;">
                    <!-- All Promo Section -->

                    <div class="promo-section">
                        <h3 class="promo-section-title">SEMUA PROMO</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="promo-card">
                                    <div class="card-body text-center">
                                        <h4 class="card-title">PROMO DISKON</h4>
                                        <p class="card-text">Nikmati berbagai penawaran diskon spesial untuk produk pilihan</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="promo-card">
                                    <div class="card-body text-center">
                                        <h4 class="card-title">PROMO BONUS</h4>
                                        <p class="card-text">Dapatkan bonus menarik dengan pembelian produk tertentu</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <!-- Special Offer Section -->
                    <div class="special-offer">
                        <h3>PENAWARAN SPESIAL</h3>
                        <h4>SPECIAL 5.5</h4>
                        <p>GRATIS BIAYA KIRIM HINGGA 2025</p>
                    </div>

                    <!-- Actual Promo Cards -->
                    <div class="promo-section">
                        <?php if (empty($informasipromo)): ?>
                            <div class="alert alert-info text-center">
                                Saat ini tidak ada promo yang tersedia. Silakan cek kembali nanti!
                            </div>
                        <?php else: ?>
                            <div class="row" id="promoContainer">
                                <?php foreach ($informasipromo as $promo): ?>
                                    <div class="col-md-3 promo-card" data-type="<?= htmlspecialchars($promo['promo_type']) ?>">
                                        <div class="card">
                                            <?php if (!empty($promo['photo_url'])): ?>
                                                <img src="../admin/uploads/<?= htmlspecialchars($promo['photo_url']); ?>" class="card-img-top" alt="<?= htmlspecialchars($promo['title']) ?>">
                                            <?php else: ?>
                                                <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">No Image</div>
                                            <?php endif; ?>
                                            <div class="card-body">
                                                <?php if ($promo['promo_type'] === 'discount'): ?>
                                                    <span class="discount-badge">Diskon <?= $promo['discount_percentage'] ?>%</span>
                                                    <h5 class="card-title mt-2"><?= htmlspecialchars($promo['title']) ?></h5>
                                                <?php elseif ($promo['promo_type'] === 'bonus'): ?>
                                                    <h5 class="card-title"><?= htmlspecialchars($promo['title']) ?></h5>
                                                <?php endif; ?>
                                                <p class="card-text"><?= htmlspecialchars($promo['description']) ?></p>
                                                <p class="card-text">
                                                    <small class="text-muted">Berlaku sampai: <?= htmlspecialchars($promo['end_date']) ?></small>
                                                </p>
                                                <div class="countdown mt-3">
                                                    <i class="bi bi-clock me-2"></i>
                                                    <span class="days-left">
                                                        <?php
                                                        $end = new DateTime($promo['end_date']);
                                                        $now = new DateTime();
                                                        $interval = $end->diff($now);
                                                        echo $interval->days . ' hari tersisa';
                                                        ?>
                                                    </span>
                                                </div>
                                                <div class="d-flex mt-3">
                                                    <?php if ($promo['promo_type'] === 'bonus'): ?>
                                                        <form method="POST" action="add_to_cart.php" class="d-inline">
                                                            <input type="hidden" name="product_id" value="<?= $promo['bonus_item']; ?>">
                                                            <button type="submit" name="add_to_cart" class="btn btn-success">Add to Cart</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Core Bootstrap JS -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>
