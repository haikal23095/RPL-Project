<?php
session_start();
include('../db.php');
$page = "info";


// Fetch all active informasipromo
$current_date = date('Y-m-d');
// $sql = "SELECT ip.id, ip.title, ip.promo_type, ip.description, ip.photo_url, ip.photo, ip.discount_percentage, ip.bonus_item, p.nama_produk, p.deskripsi, p.harga, p.gambar, ip.start_date, ip.end_date, ip.created_at FROM informasipromo ip LEFT JOIN produk p ON ip.id_produk = p.id_produk WHERE start_date <= ? AND end_date >= ? ORDER BY ip.created_at DESC;";
$sql = "SELECT * FROM informasipromo ip LEFT JOIN produk p ON ip.id_produk = p.id_produk WHERE start_date <= ? AND end_date >= ? ORDER BY ip.created_at DESC;";
$stmt = mysqli_prepare($kon, $sql);
mysqli_stmt_bind_param($stmt, "ss", $current_date, $current_date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$informasipromo = mysqli_fetch_all($result, MYSQLI_ASSOC);
// foreach ($informasipromo as $promo) {
//     var_dump($promo);
//     echo "<br><br>";
// }
// die();

// Separate informasipromo by type
$all_informasipromo = $informasipromo;
$discount_informasipromo = array_filter($informasipromo, fn($promo) => $promo['promo_type'] === 'discount');
$bonus_informasipromo = array_filter($informasipromo, function($promo) {
    return $promo['promo_type'] === 'bonus';
});

// Handle promo deletion
if (isset($_POST['delete_promo']) && isset($_POST['promo_id'])) {
    $promo_id = $_POST['promo_id'];
    $delete_sql = "DELETE FROM informasipromo WHERE id = ?";
    $delete_stmt = mysqli_prepare($kon, $delete_sql);
    mysqli_stmt_bind_param($delete_stmt, "i", $promo_id);
    
    if (mysqli_stmt_execute($delete_stmt)) {
        $_SESSION['success_message'] = "Promo berhasil dihapus!";
        header('Location: informasipromo.php');
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal menghapus promo.";
    }
};
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Promo - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #F8F7F1;
        }
        
        .promo-card {
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .promo-card:hover {
            transform: scale(1.05);
        }
        
        /* New Orange Card Styles */
        .orange-promo-card {
            background: linear-gradient(135deg, #ff7b00 0%, #ff9500 100%);
            /* padding: 12px; */
            border-radius: 15px;
            position: relative;
            overflow: hidden;
            min-height: 280px;
            color: white;
            border: none;
            box-shadow: 0 8px 25px rgba(255, 123, 0, 0.3);
        }
        
        .orange-promo-card .card-img-top {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 120px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            z-index: 2;
        }

        .container {
            background-color: #fff;
            border-radius: 24px;
        }
        
        .orange-promo-card .card-body {
            position: relative;
            z-index: 3;
            padding: 25px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-bonus {
            background-color: white;
        }

        .card-bonus p {
            color: black;
        }
        
        .promo-title {
            font-size: 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
            /* margin-bottom: 5px; */
            line-height: 1.2;
        }
        
        .promo-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 15px;
        }
        
        .promo-price {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .promo-discount {
            width: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            align-self: center;
            margin-bottom: 16px;
            background: #FF7A57;
            backdrop-filter: blur(10px);
            border-radius: 25px;
            padding: 8px 15px;
            font-size: 1.1rem;
            font-weight: bold;
            color: white;
        }

        .promo-bonus {
            background: linear-gradient(135deg, #ff8c00, #ff6b00);
            width: 100%;
            align-self: center;
            text-transform: uppercase;
            font-size: 1.4rem;
        }
        
        .beruntung {
            font-size: 0.8rem;
        }
        
        .promo-validity {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-bottom: 15px;
        }
        
        .card-actions {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 8px;
            z-index: 4;
        }
        
        .h-bonus {
            /* background-color: tomato; */
            width: 100%;
            position: absolute;
            display: flex;
            justify-content: space-between;
            top: 15px;
            left: 0;
            padding: 0 20px;
            z-index: 4;
        }
        
        .action-btn {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            transition: all 0.3s;
            backdrop-filter: blur(10px);
        }
        
        .edit-btn {
            background: #E78738;
            color: #515151;
        }
        
        .delete-btn {
            background: #763D2D;
            color: white;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
            backdrop-filter: blur(15px);
        }
        
        .promo-type-btn {
            color: #fff;
            background-color: #FF8C12;
            border: 2px solid #FF8C12;
            padding: 10px 20px;
            margin: 5px;
            border-radius: 20px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .promo-type-btn.active {
            background-color: #FFC300;
            border-color: #FFC300;
            color: #fff;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .orange-promo-card .card-img-top {
                width: 80px;
                height: 70px;
                top: 15px;
                right: 15px;
            }
            
            .promo-title {
                font-size: 1.2rem;
            }
            
            .card-actions {
                top: 10px;
                right: 10px;
            }
        }
    </style>
    <?php include 'aset.php'; ?>
</head>
<body>
    <?php require "atas.php"; ?>
    <?php require "menu.php"; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-megaphone"></i>&nbsp; INFORMASI PROMO</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                    <li class="breadcrumb-item active">INFORMASI PROMO</li>
                </ol>
            </nav>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); endif; ?>

        <section class="section">
            <div class="row mt-4">
                <div class="col-lg-12">
                    <div class="container">
                        <div class="card-body">
                            <div class="d-flex justify-content-end align-items-center">
                                <a href="tambah_promo.php" class="btn btn-tambahPromo mt-4">
                                    <i class="bi bi-plus-circle"></i> Tambah Promo Baru
                                </a>
                            </div>
                            <div class="text-center mb-4">
                                <button class="promo-type-btn active" data-filter="all">Semua Promo</button>
                                <button class="promo-type-btn" data-filter="discount">Promo Diskon</button>
                                <button class="promo-type-btn" data-filter="bonus">Promo Bonus</button>
                            </div>
                            <div class="row" id="promoContainer">
                                <?php foreach ($informasipromo as $promo): ?>
                                <div class="col-md-4 col-lg-3 promo-card" data-type="<?= htmlspecialchars($promo['promo_type']) ?>">
                                    <div class="card orange-promo-card" style="border-radius: 16px;">
                                        <!-- Action Buttons -->
                                        <?php if ($promo['promo_type'] === 'discount' && !empty($promo['discount_percentage'])): ?>
                                            <div class="card-actions">
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus promo ini?');">
                                                    <input type="hidden" name="promo_id" value="<?= $promo['id'] ?>">
                                                    <button type="submit" name="delete_promo" class="action-btn delete-btn">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                                <a href="editpromo.php?id=<?= $promo['id'] ?>" class="action-btn edit-btn">
                                                    <img src="../assets/img/edit.svg" alt="edit-btn" style="width: 20px;">
                                                </a>
                                            </div>
                                        <?php elseif ($promo['promo_type'] === 'bonus'): ?>
                                            <div class="card-actions h-bonus">
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus promo ini?');">
                                                    <input type="hidden" name="promo_id" value="<?= $promo['id'] ?>">
                                                    <button type="submit" name="delete_promo" class="action-btn delete-btn">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                                <a href="editpromo.php?id=<?= $promo['id'] ?>" class="action-btn edit-btn">
                                                    <img src="../assets/img/edit.svg" alt="edit-btn" style="width: 20px;">
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Product Image -->
                                        <?php if (!empty($promo['photo_url'])): ?>
                                            <!-- <img src="../uploads/<?= htmlspecialchars($promo['photo_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($promo['title']) ?>"> -->
                                             <img src="../assets/img/barang A.png" alt="" class="mb-4">
                                        <?php endif; ?>
                                        
                                        <!-- Discount Badge -->
                                        <?php if ($promo['promo_type'] === 'discount' && !empty($promo['discount_percentage'])): ?>
                                            <div class="card-body pb-3 pt-0">
                                                <div>
                                                    <h5 class="promo-title"><?= htmlspecialchars($promo['title']) ?></h5>
                                                    <p class="promo-subtitle"><?= htmlspecialchars($promo['description']) ?></p>
                                                    
                                                    <div class="promo-validity">
                                                        Sampai <?= date('d M Y', strtotime($promo['end_date'])) ?>
                                                    </div>
                                                </div>
                                                <div class="mb-0">
                                                    <div class="promo-subtitle mb-0 text-decoration-line-through">
                                                        IDR 700.000 <?= $promo['harga']; ?>
                                                    </div>
                                                    <div class="promo-title">
                                                        IDR 500.000
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="promo-discount">- <?= $promo['discount_percentage'] ?>%</div>
                                        <?php elseif ($promo['promo_type'] === 'bonus'): ?>
                                            <div class="card-body card-bonus pb-3">
                                                <div>
                                                    <div class="promo-discount promo-bonus">DISKON UP TO 90%</div>
                                                    <p>+ Bonus <?= $promo['bonus_item'] ?></p>
                                                    <img src="../uploads/<?= $promo['gambar'] ?>" alt="adslf">
                                                    <div class="promo-discount promo-bonus beruntung mb-0">
                                                        raih keberuntunganmu!
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

   <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.umd.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>

  <!-- Core Bootstrap JS -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Promo type filtering
            const filterButtons = document.querySelectorAll('.promo-type-btn');
            const promoCards = document.querySelectorAll('.promo-card');

            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    const filter = this.getAttribute('data-filter');

                    promoCards.forEach(card => {
                        const cardType = card.getAttribute('data-type');
                        
                        if (filter === 'all' || cardType === filter) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>