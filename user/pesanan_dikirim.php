<?php
// Koneksi ke database
require_once '../db.php';
session_start();
$page = "pesanan_dikirim";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil filter status pesanan (default: hanya 'Dikirim')
$status_filter = isset($_GET['filter']) ? $_GET['filter'] : ['Dikirim'];
function formatCurrency($number) {
    return 'Rp ' . number_format($number ?? 0, 0, ',', '.');
}

// Pastikan `$status_filter` adalah array
if (!is_array($status_filter)) {
    $status_filter = [$status_filter];
}

// Query untuk mengambil data pesanan dengan status "Dikirim"
$query = "SELECT pesanan.* FROM pesanan 
          JOIN user ON user.id_user = pesanan.id_user 
          WHERE pesanan.status_pesanan = 'Dikirim' AND user.nama = ? 
          ORDER BY pesanan.tanggal_pesanan DESC";
$stmt = $kon->prepare($query);
$stmt->bind_param("s", $_SESSION['user']);
$stmt->execute();
$pesanan_list = $stmt->get_result();
$pesanan_list = $pesanan_list->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Dikirim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .pesanan-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .pesanan-card img {
            max-width: 100px;
            border-radius: 8px;
        }
        .btn-orange {
            background-color: #FF8800 !important;
            border: none !important;
            color: white !important;
        }
        
    </style>
    <?php include 'aset.php'; ?>
</head>
<body>

<!-- Header -->
<?php require "atas.php"; ?>
<!-- End Header -->

<!-- Sidebar -->
<?php require "profil_menu.php"; ?>
<!-- End Sidebar -->

<main id="main" class="main">
    <div class="pagetitle">
            <h1><i class="bi bi-truck"></i> PESANAN DIKIRIM</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">PROFIL</a></li>
                    <li class="breadcrumb-item active">PESANAN DIKIRIM</li>
                </ol>
            </nav>
        </div>
    
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>DAFTAR PESANAN DIKIRIM</h4>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <?php if (count($pesanan_list) > 0): ?>
                        <?php foreach ($pesanan_list as $pesanan): ?>
                            <?php
                            // Query untuk mendapatkan semua gambar produk dalam pesanan ini
                            $gambar_stmt = $kon->prepare("SELECT p.gambar, p.nama_produk FROM pesanan_detail dp 
                                                        JOIN produk p ON dp.id_produk = p.id_produk 
                                                        WHERE dp.id_pesanan = ?");
                            $gambar_stmt->bind_param("s", $pesanan['id_pesanan']);
                            $gambar_stmt->execute();
                            $gambar_result = $gambar_stmt->get_result();
                            $produk_images = $gambar_result->fetch_all(MYSQLI_ASSOC);
                            $gambar_stmt->close();
                            ?>
                            <div class="card pesanan-card mb-4">
                                <div class="card-header pesanan-header d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="my-1">
                                        <strong>ID Pesanan:</strong> #<?= htmlspecialchars($pesanan['id_pesanan']) ?>
                                    </div>
                                    <span class="badge bg-primary my-1"><?= htmlspecialchars($pesanan['status_pesanan']) ?></span>
                                </div>
                                
                                <div class="product-gallery">
                                    <?php if (!empty($produk_images)): ?>
                                        <?php foreach ($produk_images as $img): ?>
                                            <img src="../uploads/<?= htmlspecialchars($img['gambar']) ?>" class="product-img-gallery" title="<?= htmlspecialchars($img['nama_produk']) ?>">
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted small px-3">Tidak ada gambar produk.</p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-footer-actions">
                                    <div class="row align-items-center">
                                        <div class="col-md-6 mb-2 mb-md-0">
                                            <span class="text-muted">Total Belanja:</span>
                                            <h5 class="total-harga d-inline-block ms-2 mb-0"><?= formatCurrency($pesanan['total_harga']) ?></h5>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <button class="btn btn-outline-primary btn-sm btn-detail" 
                                                    type="button" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailModal"
                                                    data-id="<?= $pesanan['id_pesanan'] ?>"> Detail Pesanan
                                            </button>
                                            <a href="form_pembatalan.php?id_pesanan=<?= $pesanan['id_pesanan'] ?>" 
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin mengajukan pembatalan untuk pesanan ini?');">Batalkan
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-body text-center p-5">
                                <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                                <h5 class="mt-3">Tidak Ada Pesanan yang Dikirim</h5>
                                <p class="text-muted">Semua pesanan Anda sudah dalam pengiriman atau telah selesai. <br>Lihat halaman lain untuk melacaknya.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</main>

<!-- Modal Detail Pesanan -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Pesanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modal-detail-content">
        <!-- Isi detail pesanan akan dimuat via AJAX -->
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <div>Memuat detail...</div>
        </div>
      </div>
    </div>
  </div>
</div>


<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/chart.js/chart.umd.js"></script>
<script src="../assets/vendor/echarts/echarts.min.js"></script>
<script src="../assets/vendor/quill/quill.min.js"></script>
<script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="../assets/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
<script src="../assets/js/main.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const detailButtons = document.querySelectorAll('.btn-detail');
    detailButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const idPesanan = this.getAttribute('data-id');
            const modalContent = document.getElementById('modal-detail-content');
            modalContent.innerHTML = `<div class="text-center py-4">
                <div class="spinner-border text-primary" role="status"></div>
                <div>Memuat detail...</div>
            </div>`;
            fetch('pesanan_detail_ajax.php?id_pesanan=' + idPesanan)
                .then(res => res.text())
                .then(html => {
                    modalContent.innerHTML = html;
                });
        });
    });
});
</script>
</body>
</html>
