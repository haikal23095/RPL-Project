<?php
session_start();
include '../db.php';
include '../notification_functions.php';
$page = "notifikasi";
// Fungsi untuk memeriksa stok produk
function checkProductStock($kon) {
    $query = "SELECT id_produk, nama_produk, stok FROM produk WHERE stok < 15";
    $result = mysqli_query($kon, $query);
    
    while ($product = mysqli_fetch_assoc($result)) {
        // Periksa apakah notifikasi sudah dibuat dalam 24 jam terakhir
        $query_notif = "SELECT * FROM notifications 
                       WHERE type = 'admin' 
                       AND title = 'Stok Produk Rendah' 
                       AND message LIKE '%{$product['nama_produk']}%' 
                       AND id_produk = {$product['id_produk']}
                       AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $result_notif = mysqli_query($kon, $query_notif);
        
        if (mysqli_num_rows($result_notif) == 0) {
            // Jika tidak ada notifikasi dalam 24 jam terakhir, buat notifikasi baru
            createNotification($kon, 'admin', 'Stok Produk Rendah', 
                "Produk {$product['nama_produk']} tersisa {$product['stok']} unit", 
                null, $product['id_produk']);
        }
    }
}

// Validasi Login Admin
if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    echo "<script>
            alert('Anda harus login sebagai admin');
            window.location.href='../login.php';
          </script>";
    exit();
}

// Generate admin notifications hanya jika belum ada notifikasi dalam 5 menit terakhir
$check_recent = "SELECT COUNT(*) as count FROM notifications 
                WHERE type = 'admin' 
                AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
$result_recent = mysqli_query($kon, $check_recent);
$recent_count = mysqli_fetch_assoc($result_recent)['count'];

if ($recent_count == 0) {
    checkProductStock($kon);
    monitorPesananChanges($kon);
    monitorReviewChanges($kon);
}

// Tandai Semua Notifikasi Dibaca
if (isset($_POST['mark_all_read'])) {
    try {
        $query_mark = "UPDATE notifications SET is_read = 1 WHERE type = 'admin'";
        $stmt = mysqli_prepare($kon, $query_mark);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_stmt_error($stmt)) {
            throw new Exception("Error marking notifications as read");
        }
        
        mysqli_stmt_close($stmt);
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}

// Hapus Semua Notifikasi
if (isset($_POST['clear_notifications'])) {
    try {
        $query_clear = "DELETE FROM notifications WHERE type = 'admin'";
        $stmt = mysqli_prepare($kon, $query_clear);
        mysqli_stmt_execute($stmt);
        
        if (mysqli_stmt_error($stmt)) {
            throw new Exception("Error clearing notifications");
        }
        
        mysqli_stmt_close($stmt);
        
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}

// Query untuk Menampilkan Notifikasi
try {
    $query_notif = "SELECT * FROM notifications WHERE type = 'admin' ORDER BY created_at DESC LIMIT 5000";
    $result_notif = mysqli_query($kon, $query_notif);

    // Hitung Notifikasi Belum Dibaca
    $query_unread = "SELECT COUNT(*) as unread_count FROM notifications WHERE type = 'admin' AND is_read = 0";
    $result_unread = mysqli_query($kon, $query_unread);
    $unread = mysqli_fetch_assoc($result_unread)['unread_count'];
} catch (Exception $e) {
    error_log($e->getMessage());
    $result_notif = null;
    $unread = 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif;
            color: #2D3A3A;
        }
        .sidebar {
            background-color: #F8F7F1 !important;
        }
        header{
            background-color: #F8F7F1 !important;
        }
        .btn-outline-secondary {
            color: #2D3A3A !important;
            background-color: transparent !important;
            border: 1px solid #2D3A3A !important;
            border-radius: 0.375rem; /* Bootstrap default for btn-sm */
            padding: 0.25rem 0.5rem; /* Bootstrap default for btn-sm */
            font-size: 0.875rem; /* Bootstrap default for btn-sm */
            transition: all 0.2s ease-in-out;
        }

        .btn-outline-secondary:hover {
            background-color: #2D3A3A !important;
            color: #fff !important;
            border: 1px solid transparent !important;
        }

        /* Untuk tombol "Hapus Semua" */
        .btn-outline-danger {
            color: #763D2D !important;
            background-color: transparent !important;
            border: 1px solid #763D2D !important;
            border-radius: 0.375rem; /* Bootstrap default for btn-sm */
            padding: 0.25rem 0.5rem; /* Bootstrap default for btn-sm */
            font-size: 0.875rem; /* Bootstrap default for btn-sm */
            transition: all 0.2s ease-in-out;
        }

        .btn-outline-danger:hover {
            background-color: #763D2D !important;
            color: #fff !important;
            border: 1px solid transparent !important;
        }

        .btn-outline-primary {
            color: #2D3A3A !important;
            background-color: transparent !important;
            border: 1px solid #2D3A3A !important;
            border-radius: 0.375rem; /* Bootstrap default for btn-sm */
            padding: 0.25rem 0.5rem; /* Bootstrap default for btn-sm */
            font-size: 0.875rem; /* Bootstrap default for btn-sm */
            transition: all 0.2s ease-in-out;
        }

        .btn-outline-primary:hover {
            background-color: #2D3A3A !important;
            color: #fff !important;
            border: 1px solid transparent !important;
        }


        .notification-container {
            max-width: 1000px;
            margin: 30px auto;
        }
        .notification-card {
            background: #fff;
            border-left: 6px solid #FF9900;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }
        .notification-card.unread {
            background-color:rgb(255, 255, 255);
        }
        .notification-icon {
            font-size: 2rem;
            color: #FF9900;
            margin-right: 15px;
        }
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .notification-title {
            font-weight: 600;
            font-size: 18px;
        }
        .notification-date {
            font-size: 14px;
            color: #888;
        }
        .badge-new {
            background-color: #FFD700;
            color: #ffffff;
            font-weight: bold !important;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 6px;
            margin-left: 10px;
        }
    </style>
    <?php include 'aset.php'; ?>
</head>
<body>
    <?php 
    require "atas.php"; 
    require "menu.php"; 
    ?>    
    <main id="main" class="main">
        
        <div class="container notification-container">
            <div class=" justify-content-between align-items-center mb-4">
                <div class=" pagetitle">
                    <h1><i class="bi bi-bell"></i>&nbsp; NOTIFIKASI <?php if ($unread > 0): ?>
                            <span class="badge bg-danger"><?= $unread ?></span>
                        <?php endif; ?></h1>
                    <nav class="justify-content-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                            <li class="breadcrumb-item active">NOTIFIKASI</li>
                        </ol>
                    </nav>
                </div>
                
                
                <?php if ($result_notif && mysqli_num_rows($result_notif) > 0): ?>
                    <div class="btn-group">
                        <form method="POST" class="me-2">
                            <button type="submit" name="mark_all_read" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-check-double"></i> Tandai Dibaca
                            </button>
                        </form>
                        <form method="POST">
                            <button type="submit" name="clear_notifications" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin menghapus semua notifikasi?');">
                                <i class="fas fa-trash"></i> Hapus Semua
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!$result_notif || mysqli_num_rows($result_notif) == 0): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p>Tidak ada notifikasi</p>
            </div>
        <?php else: ?>
            <?php mysqli_data_seek($result_notif, 0); while ($notif = mysqli_fetch_assoc($result_notif)): ?>
                <div class="notification-card <?= $notif['is_read'] == 0 ? 'unread' : '' ?>">
                    <div class="d-flex">
                        <i class="bi bi-box notification-icon"></i>
                        <div class="flex-grow-1">
                            <div class="notification-header">
                                <span class="notification-title">
                                    <?= htmlspecialchars($notif['title']) ?>
                                    <?php if($notif['is_read'] == 0): ?>
                                        <span class="badge-new">Baru</span>
                                    <?php endif; ?>
                                </span>
                                <span class="notification-date">
                                    <?= date('d M H:i', strtotime($notif['created_at'])) ?>
                                </span>
                            </div>
                            <p class="mb-2"><?= htmlspecialchars($notif['message']) ?></p>
                            <a href="detail_produk.php?product_id=<?= $notif['id_produk'] ?>" class="btn btn-sm btn-outline-primary">
                                Lihat Produk
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
        
    </script>
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
</body>
</html>