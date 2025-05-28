<?php
session_start();
require "../db.php";
$page = "promo";

if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$nama = $_SESSION['user']; 
$user_id = null;

$user_query = "SELECT id_user FROM user WHERE nama = ?";
$stmt_user = mysqli_prepare($kon, $user_query);
mysqli_stmt_bind_param($stmt_user, "s", $nama);
mysqli_stmt_execute($stmt_user);
mysqli_stmt_bind_result($stmt_user, $user_id);
mysqli_stmt_fetch($stmt_user);
mysqli_stmt_close($stmt_user);

if (!$user_id) {
    echo "<div class='alert alert-danger'>User not found.</div>";
    exit;
}

// Query untuk mendapatkan semua promo yang tersedia
$promo_query = "SELECT p.id, p.code, p.discount_type, p.discount_value, 
                p.times_used, p.usage_limit
                FROM promo p";
$promo_result = mysqli_query($kon, $promo_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Kode Promo - CasaLuxe</title>
    
    <!-- Bootstrap CSS -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .header-title {
            color: #333;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }
        .promo-section {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .section-title {
            color: #333;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .promo-item {
            border-left: 4px solid #007bff;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            position: relative;
        }
        .promo-title {
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        .promo-detail {
            margin-bottom: 5px;
            font-size: 14px;
        }
        .promo-expiry {
            color: #6c757d;
            font-size: 13px;
        }
        .promo-code {
            position: absolute;
            right: 15px;
            top: 15px;
            font-weight: bold;
            color: #28a745;
        }
        .divider {
            border-top: 1px dashed #ddd;
            margin: 20px 0;
        }
        .action-section {
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            background-color: #f8f9fa;
            border-radius: 5px;
            cursor: pointer;
        }
        .action-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .customer-service {
            text-align: center;
            margin-top: 30px;
            color: #6c757d;
            font-size: 14px;
        }
        .usage-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
    <?php include 'aset.php'; ?>
</head>
<body>
    <?php include 'atas.php'; ?>
    <?php include 'menu.php'; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-tag"></i> Kode Promo</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                    <li class="breadcrumb-item active">KODE PROMO</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="header-title">CasaLuxe</h2>
                            <h3 class="text-center mb-4">KODE PROMO</h3>
                            
                            <div class="divider"></div>
                            
                            <div class="promo-section">
                                <h4 class="section-title">INFORMASI PROMO</h4>
                                
                                <div class="promo-item">
                                    <div class="promo-title">FAVORIT</div>
                                </div>
                                
                                <?php
                                if ($promo_result && mysqli_num_rows($promo_result) > 0) {
                                    while ($promo = mysqli_fetch_assoc($promo_result)) {
                                        // $end_date = date('d-m-Y', strtotime($promo['end_date']));
                                        // $min_purchase = number_format($promo['min_purchase'], 0, ',', '.');
                                        
                                        // Format nilai diskon
                                        $discount_value = ($promo['discount_type'] == 'fixed') 
                                            ? 'Rp ' . number_format($promo['discount_value'], 0, ',', '.') 
                                            : $promo['discount_value'] . '%';
                                        
                                        // Hitung sisa kuota
                                        $remaining = $promo['usage_limit'] - $promo['times_used'];
                                        ?>
                                        <div class="promo-item">
                                            <span class="promo-code"><?= htmlspecialchars($promo['code']) ?></span>
                                            <div class="promo-title">
                                                Discount <?= $discount_value ?>
                                                <?= ($promo['discount_type'] == 'percentage') ? 'Up to IDR 100.000' : '' ?>
                                            </div>
                                            <div class="promo-detail">
                                                Min. Total Buy IDR 
                                            </div>
                                            <div class="promo-expiry">
                                                Until <?= htmlspecialchars('xxxx') ?>
                                            </div>
                                            <div class="usage-info">
                                                Used: <?= $promo['times_used'] ?>/<?= $promo['usage_limit'] ?> 
                                                (Remaining: <?= $remaining ?>)
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo "<div class='alert alert-info'>No promo codes available at the moment.</div>";
                                }
                                ?>
                            </div>
                            
                            <div class="divider"></div>
                            
                            <div class="action-section" onclick="copyAllPromoCodes()">
                                <div class="action-title">KLAIM</div>
                            </div>
                            
                            <div class="action-section" onclick="window.location.href='how_to_use.php'">
                                <div class="action-title">CARA PENGGUNAAN</div>
                            </div>
                            
                            <div class="divider"></div>
                            
                            <div class="customer-service">
                                PELAYANAN PELANGGAN
                            </div>
                        </div>
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
        function copyAllPromoCodes() {
            let codes = [];
            document.querySelectorAll('.promo-code').forEach(el => {
                codes.push(el.textContent);
            });
            
            if (codes.length > 0) {
                navigator.clipboard.writeText(codes.join('\n')).then(() => {
                    alert('All promo codes copied to clipboard!\n' + codes.join('\n'));
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                });
            } else {
                alert('No promo codes available to copy.');
            }
        }
        
        // Fungsi untuk copy single promo code
        function copyPromoCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                alert('Promo code copied: ' + code);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
</body>
</html>