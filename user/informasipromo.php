<?php
session_start();
include('../db.php');
$page = "info";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$userName = $_SESSION['user'];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$userName'");
$row_user = mysqli_fetch_array($kue_user);
$user_id = $row_user['id_user']; // Get the actual user ID

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
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .promo-section-title {
            color: #f4a460;
            border-bottom: 2px solid #f4a460;
            margin-bottom: 10px;
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
            color: #f4a460;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .discount-badge {
            background-color:rgb(248, 120, 73);
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
            margin-top: 10px;
        }
        .special-offer h3 {
            color: #f4a460;
            font-weight: bold;
        }
        h3{
            color: #f4a460;
        }
        .btn-primary {
            background-color: transparent !important;
            color: #f4a460 !important;
            border: 1px solid #f4a460 !important;
        }
        .btn-primary:hover {
            background-color: #f4a460 !important;
            color: #f4a460 !important;
            border: 1px solid #f4a460 !important;
        }
        h4{
            font-weight: bold !important;
        }
    </style>
    <?php include 'aset.php'; ?>
</head>
<body>
    <?php require "atas.php"; ?>
    <?php require "menu.php"; ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif;
            color: #2D3A3A !important;
        }
    </style>
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


                    <div class="promo-section">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="promo-card" id="filterDiscount"> <div class="card-body text-center">
                                        <h4 class="card-title">PROMO DISKON</h4>
                                        <p class="card-text">Nikmati berbagai penawaran diskon spesial untuk produk pilihan</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="promo-card" id="filterBonus"> <div class="card-body text-center">
                                        <h4 class="card-title">PROMO BONUS</h4>
                                        <p class="card-text">Dapatkan bonus menarik dengan pembelian produk tertentu</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Special Offer Section -->
                    <div class="special-offer">
                        <h3>PENAWARAN SPESIAL</h3>
                        <h4>SPESIAL 5.5</h4>
                    </div>

                    <!-- Actual Promo Cards -->
                    <div class="promo-section">
                        <?php if (empty($informasipromo)): ?>
                            <div class="alert alert-info text-center">
                                Saat ini tidak ada promo yang tersedia. Silakan cek kembali nanti!
                            </div>
                        <?php else: ?>
                            <div class="promo-scroll-container">
                                <div class="row promo-row" id="promoContainer" style="margin-left: 40px;">
                                    <?php foreach ($informasipromo as $promo): ?>
                                        <div class="col-md-3 promo-card pt-3" href="promo.php?" data-type="<?= htmlspecialchars($promo['promo_type']) ?>">
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
                                                            <?php if ($promo['promo_type'] === 'discount'): ?>
                                                            <a href="promo.php?id=<?= $promo['id'] ?>" class="btn btn-primary" style="margin-top: 20px;">Lihat Promo</a>
                                                            <?php endif; ?>
                                                        </span>
                                                    </div>
                                                    <div class="d-flex mt-3">
                                                        <?php if ($promo['promo_type'] === 'bonus' && !empty($promo['bonus_item'])): ?>
                                                            <form method="POST" action="add_to_cart.php" class="d-inline">
                                                                <input type="hidden" name="product_id" value="<?= htmlspecialchars($promo['bonus_item']); ?>">
                                                                <button type="submit" name="add_to_cart" class="btn btn-success">Add to Cart</button>
                                                            </form>
                                                        <?php elseif ($promo['promo_type'] === 'bonus'): ?>
                                                            <span class="text-danger">Produk bonus tidak diatur.</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const promoCards = document.querySelectorAll('.promo-card[data-type]'); // Select all actual promo cards
            const filterDiscount = document.getElementById('filterDiscount');
            const filterBonus = document.getElementById('filterBonus');
            const showAllPromosBtn = document.getElementById('showAllPromos');

            function filterPromos(type) {
                promoCards.forEach(card => {
                    if (type === 'all' || card.dataset.type === type) {
                        card.style.display = 'block'; // Show card
                    } else {
                        card.style.display = 'none'; // Hide card
                    }
                });
            }

            // Event Listeners for filter cards
            if (filterDiscount) {
                filterDiscount.addEventListener('click', function() {
                    filterPromos('discount');
                });
            }

            if (filterBonus) {
                filterBonus.addEventListener('click', function() {
                    filterPromos('bonus');
                });
            }

            // Event Listener for "Show All" button
            if (showAllPromosBtn) {
                showAllPromosBtn.addEventListener('click', function() {
                    filterPromos('all');
                });
            }

            // Initially show all promos when the page loads
            filterPromos('all');
        });
    </script>
</body>
</html>
