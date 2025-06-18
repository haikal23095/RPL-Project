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


// --- PROMO CLAIM PROCESSING LOGIC (MOVED FROM claim_promo.php) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['promo_id'])) {
    $promo_id = $_POST['promo_id'];

    // 1. Fetch promo details and lock the row to prevent race conditions during updates
    $promo_query_for_claim = "SELECT id, code, times_used, usage_limit FROM promo WHERE id = ? FOR UPDATE";
    $stmt_promo_for_claim = mysqli_prepare($kon, $promo_query_for_claim);
    mysqli_stmt_bind_param($stmt_promo_for_claim, "i", $promo_id);
    mysqli_stmt_execute($stmt_promo_for_claim);
    $promo_result_for_claim = mysqli_stmt_get_result($stmt_promo_for_claim);
    $promo_data = mysqli_fetch_assoc($promo_result_for_claim);
    mysqli_stmt_close($stmt_promo_for_claim);
    // print_r($promo_data); // Debugging line to check promo data
    if (!$promo_data) {
        $claim_message = 'Promo tidak ditemukan.';
        $message_type = 'danger';
        echo "Error: Promo tidak ditemukan.";
    } else {
        // Start a transaction for atomicity
        mysqli_begin_transaction($kon);
        $claim_successful = false;
        try {
            // Check if usage limit is reached (assuming 0 means unlimited)
            if ($promo_data['usage_limit'] > 0 && $promo_data['times_used'] >= $promo_data['usage_limit']) {
                throw new Exception('limit_reached');
            }
            
            // print_r($promo_data);
            $check_claim_query = "SELECT COUNT(*) FROM user_promo_codes WHERE user_id = $user_id AND promo_id = $promo_id";
            $result = mysqli_query($kon, $check_claim_query);
            $row = mysqli_fetch_row($result);
            $claimed_count = $row[0];

            print_r($claimed_count); // Debugging line to check claimed count
            
            if ($claimed_count > 0) {
                echo "<script>alert('Promo sudah diklaim sebelumnya!');</script>";
                throw new Exception('already_claimed');
            }

            // Record the claim in the user_promo_code table
            // Make sure your user_promo_code table has `promo_code` column, and you pass the actual code
            $insert_claim_query = "INSERT INTO user_promo_codes (user_id, promo_id, promo_code, times_used, is_active) VALUES (?, ?, ?, 1, 1)";
            $stmt_insert_claim = mysqli_prepare($kon, $insert_claim_query);
            // Assuming 'expires_at' from the promo table is the valid expiry for the claimed code
            mysqli_stmt_bind_param($stmt_insert_claim, "iis", $user_id, $promo_id, $promo_data['code']);

            if (!mysqli_stmt_execute($stmt_insert_claim)) {
                throw new Exception('Database error on insert claim: ' . mysqli_error($kon));
            }
            mysqli_stmt_close($stmt_insert_claim);

            // Increment times_used for the promo in the main promo table (if it's not unlimited)
            if ($promo_data['usage_limit'] > 0) {
                $update_promo_query = "UPDATE promo SET times_used = times_used + 1 WHERE id = ?";
                $stmt_update_promo = mysqli_prepare($kon, $update_promo_query);
                mysqli_stmt_bind_param($stmt_update_promo, "i", $promo_id);
                if (!mysqli_stmt_execute($stmt_update_promo)) {
                    throw new Exception('Database error on update promo usage: ' . mysqli_error($kon));
                }
                mysqli_stmt_close($stmt_update_promo);
                echo "<script>alert('Promo berhasil diklaim!');</script>";
            }

            mysqli_commit($kon); // Commit the transaction
            $claim_successful = true;

        } catch (Exception $e) {
            mysqli_rollback($kon); // Rollback on error
            $errorCode = $e->getMessage();
            if ($errorCode === 'limit_reached') {
                $claim_message = 'Kuota promo sudah habis.';
                $message_type = 'warning'; // Changed to warning as it's a known state
            } elseif ($errorCode === 'already_claimed') {
                $claim_message = 'Anda sudah mengklaim promo ini.';
                $message_type = 'info'; // Changed to info as it's a known state
            } elseif ($errorCode === 'promo_expired') {
                $claim_message = 'Promo sudah kadaluarsa.';
                $message_type = 'warning';
            } else {
                $claim_message = 'Terjadi kesalahan saat mengklaim promo: ' . $e->getMessage();
                $message_type = 'danger';
            }
        }

        if ($claim_successful) {
            $claim_message = 'Promo berhasil diklaim!';
            $message_type = 'success';
        }
    }
}
// --- END PROMO CLAIM PROCESSING LOGIC ---
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
                                
                                <?php
                                if ($promo_result && mysqli_num_rows($promo_result) > 0) {
                                    while ($promo = mysqli_fetch_assoc($promo_result)) {
                                        
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
                                            <div class="claim-button-container">

                                                    <form method="POST" action="promo.php" style="display: inline;">
                                                        <input type="hidden" name="promo_id" value="<?= htmlspecialchars($promo['id']) ?>">
                                                        <button type="submit" class="btn btn-primary btn-sm">Klaim Promo</button>
                                                    </form>
         
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