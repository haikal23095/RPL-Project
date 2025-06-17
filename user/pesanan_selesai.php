<?php
require '../db.php';
session_start();
$page = "pesanan_selesai";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}   

function formatCurrency($number) {
    return 'Rp ' . number_format($number ?? 0, 0, ',', '.');
}

$nama_user = $_SESSION['user'];

// Ambil filter status pesanan dari checkbox (bisa lebih dari satu)
$status_filter = isset($_GET['filter']) ? $_GET['filter'] : '';


// Query untuk mengambil data pesanan berdasarkan filter
$query = "SELECT p.*, 
    CASE WHEN EXISTS (
        SELECT 1 FROM review_produk r WHERE r.id_pesanan = p.id_pesanan
    ) THEN 'Sudah Dinilai' ELSE 'Menunggu Dinilai' END AS review_status
    FROM pesanan p
    JOIN user u ON u.id_user = p.id_user
    WHERE p.status_pesanan = 'Selesai' AND u.nama = ?";

$params = [$nama_user];
$types = "s";

if ($status_filter == 'Sudah Dinilai' || $status_filter == 'Menunggu Dinilai') {
    $query .= " HAVING review_status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

$stmt = $kon->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$pesanan = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Dinilai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/img/favicon.png" rel="icon">
    <link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    <?php include 'aset.php'; ?>
    <style>
        body { background-color: #f5f5f5; }
        .pesanan-card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
        .btn-orange { background-color: orange; color: white; }
    </style>
</head>
<body>

<!-- ... existing code ... -->
<?php require "atas.php"; ?>
<!-- End Header -->

<!-- ======= Sidebar ======= -->
<?php require "profil_menu.php"; ?>
<!-- End Sidebar-->
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
        .product-img-gallery {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: .5rem;
            border: 1px solid #eee;
        }
 </style>
<main id="main" class="main">
    <div class="pagetitle">
        <h1><i class="bi bi-bag-check"></i> PESANAN SELESAI</h1>
           <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">PROFIL</a></li>
                <li class="breadcrumb-item active">PESANAN SELESAI</li>
            </ol>
        </nav>
    </div>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <form method="get" class="d-inline">
                <label>
                    <input type="radio" name="filter" value="" <?= $status_filter == '' ? 'checked' : '' ?>> Semua
                </label>
                <label>
                    <input type="radio" name="filter" value="Sudah Dinilai" <?= $status_filter == 'Sudah Dinilai' ? 'checked' : '' ?>> Sudah Dinilai
                </label>
                <label>
                    <input type="radio" name="filter" value="Menunggu Dinilai" <?= $status_filter == 'Menunggu Dinilai' ? 'checked' : '' ?>> Menunggu Dinilai
                </label>
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            </form>
        </div>

        <section class="section">
        <div class="row">
                <div class="col-lg-12">
                    <?php if (count($pesanan) > 0): // Changed $pesanan_list to $pesanan ?>
                        <?php foreach ($pesanan as $pesanan_item): // Changed $pesanan_list to $pesanan and used a different variable name for clarity ?>
                            <?php
                            // Query untuk mendapatkan semua gambar produk dalam pesanan ini
                            $gambar_stmt = $kon->prepare("SELECT p.gambar, p.nama_produk FROM pesanan_detail dp 
                                                                 JOIN produk p ON dp.id_produk = p.id_produk 
                                                                 WHERE dp.id_pesanan = ?");
                            $gambar_stmt->bind_param("s", $pesanan_item['id_pesanan']); // Use $pesanan_item here
                            $gambar_stmt->execute();
                            $gambar_result = $gambar_stmt->get_result();
                            $produk_images = $gambar_result->fetch_all(MYSQLI_ASSOC);
                            $gambar_stmt->close();
                            ?>
                            <div class="card pesanan-card mb-4">
                                <div class="card-header pesanan-header d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="my-1">
                                        <strong>ID Pesanan:</strong> #<?= htmlspecialchars($pesanan_item['id_pesanan']) ?>
                                    </div>
                                    <span class="badge bg-primary my-1"><?= htmlspecialchars($pesanan_item['status_pesanan']) ?></span>
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
                                            <h5 class="total-harga d-inline-block ms-2 mb-0"><?= formatCurrency($pesanan_item['total_harga']) ?></h5>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <button class="btn btn-outline-primary btn-sm btn-detail" 
                                                            type="button" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#detailModal"
                                                            data-id="<?= $pesanan_item['id_pesanan'] ?>"> Detail Pesanan
                                            </button>
                                            <a href="form_pembatalan.php?id_pesanan=<?= $pesanan_item['id_pesanan'] ?>" 
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
                                <h5 class="mt-3">Tidak Ada Pesanan yang Diproses</h5>
                                <p class="text-muted">Semua pesanan Anda sudah dalam pengiriman atau telah selesai. <br>Lihat halaman lain untuk melacaknya.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>
</main>



<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Pesanan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modal-detail-content">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">Memuat detail...</p>
        </div>
      </div>
    </div>
  </div>
</div>

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const detailButtons = document.querySelectorAll('.btn-detail');
    detailButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const idPesanan = this.getAttribute('data-id');
            const modalContent = document.querySelector('#detailModal .modal-content');
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

<!-- Vendor JS Files -->
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>

</body>
</html>