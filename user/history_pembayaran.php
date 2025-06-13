<?php
session_start();
include('../db.php'); // Koneksi ke database
$page = "history_pembayaran";

// Pastikan user login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['user'];
$kue_user = mysqli_query($kon, "SELECT id_user FROM user WHERE nama = '$userName'");
$row_user = mysqli_fetch_array($kue_user);
$userId = $row_user['id_user'];
$_SESSION['user_id'] = $userId; // Simpan user_id ke session agar mudah diakses file AJAX

// Tambahkan penanganan filter tanggal dengan validasi
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

// Validasi rentang tanggal
$dateQuery = "";
$bindTypes = "i";
$bindParams = [&$userId];

if ($start_date && $end_date && strtotime($start_date) <= strtotime($end_date)) {
    $dateQuery = " AND p.tanggal_pesanan BETWEEN ? AND ?";
    $bindTypes .= "ss";
    
    // Tambahkan waktu ke end_date agar mencakup seluruh hari
    $end_date_full = $end_date . ' 23:59:59';

    $bindParams[] = &$start_date;
    $bindParams[] = &$end_date_full;
} else {
    $start_date = null;
    $end_date = null;
}

// Query utama untuk mengambil daftar pesanan
$sql = "SELECT p.id_pesanan, p.total_harga, p.status_pesanan, p.tanggal_pesanan, p.sudah_dinilai,
        pb.metode_pembayaran, pb.status_pembayaran, 
        pg.alamat_pengiriman, pg.nomor_resi, pg.nama_kurir, pg.tanggal_kirim, pg.perkiraan_tiba
        FROM pesanan p
        LEFT JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan
        LEFT JOIN pengiriman_pesanan pg ON p.id_pesanan = pg.id_pesanan
        WHERE p.id_user = ? $dateQuery
        ORDER BY p.tanggal_pesanan DESC";

$stmt = mysqli_prepare($kon, $sql);
call_user_func_array([$stmt, 'bind_param'], array_merge([$bindTypes], $bindParams));
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
$noOrdersMessage = (empty($orders) && ($start_date && $end_date)) ? "Tidak ada pesanan dalam rentang tanggal yang dipilih" : null;
mysqli_stmt_close($stmt);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Histori Pembelian</title>
    
    <?php include "aset.php"; ?>
    
    <style>
        .table tr:hover td { background: #f8f9fa; }
        .product-img { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
        .collapse h6 { color: #333; font-weight: 600; margin: 15px 0 10px 0; }
        .btn-detail i { transition: transform 0.3s ease; }
        .btn-detail.collapsed i { transform: rotate(0deg); }
        .btn-detail:not(.collapsed) i { transform: rotate(180deg); }
        .review-product-item { display: flex; align-items: flex-start; }
        .review-product-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px; }
        .review-product-item .details { flex-grow: 1; }
        .modal-body .form-select, .modal-body .form-control { margin-bottom: 0.5rem; }
    </style>
</head>
<body>
    <?php require "atas.php"; ?>
    <?php require "profil_menu.php"; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-clock-history"></i> Histori Pembelian</h1>
            <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="index.php">Profil</a></li><li class="breadcrumb-item active">Histori Pembelian</li></ol></nav>
        </div>
        
        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Filter Pesanan</h5>
                    <form method="GET" action="" onsubmit="return validateDateRange()">
                        <div class="input-group mb-3">
                            <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($start_date ?? ''); ?>" required>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($end_date ?? ''); ?>" required>
                            <button class="btn btn-primary" type="submit"><i class="bi bi-funnel"></i> Filter</button>
                            <?php if ($start_date && $end_date): ?>
                                <a href="history_pembayaran.php" class="btn btn-secondary">Reset</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Daftar Pesanan</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Detail</th>
                                    <th>Tanggal</th>
                                    <th>ID Pesanan</th>
                                    <th>Total</th>
                                    <th>Status Pesanan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($noOrdersMessage)): ?>
                                    <tr><td colspan="6"><div class="alert alert-info"><?= $noOrdersMessage ?></div></td></tr>
                                <?php elseif(empty($orders)): ?>
                                     <tr><td colspan="6"><div class="alert alert-info">Anda belum memiliki riwayat pesanan.</div></td></tr>
                                <?php endif; ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><button class="btn btn-link btn-sm btn-detail collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetail<?= $order['id_pesanan'] ?>"><i class="bi bi-chevron-down"></i></button></td>
                                        <td><?= date('d M Y', strtotime($order['tanggal_pesanan'])); ?></td>
                                        <td>#<?= htmlspecialchars($order['id_pesanan']); ?></td>
                                        <td>Rp <?= number_format($order['total_harga'], 0, ',', '.'); ?></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($order['status_pesanan']); ?></span></td>
                                        <td class="text-center">
                                            <?php if ($order['status_pesanan'] === 'Selesai'): ?>
                                                <?php if (empty($order['sudah_dinilai'])): ?>
                                                    <button type="button" class="btn btn-primary btn-sm open-review-modal" data-order-id="<?= $order['id_pesanan'] ?>">Nilai</button>
                                                <?php else: ?>
                                                    <button class="btn btn-success btn-sm" disabled>Dinilai</button>
                                                <?php endif; ?>
                                                 <a href="checkout.php?ulang=<?= $order['id_pesanan'] ?>" class="btn btn-outline-success btn-sm">Beli Lagi</a>
                                            <?php elseif ($order['status_pesanan'] === 'Diproses'): ?>
                                                <a href="form_pembatalan.php?id_pesanan=<?= urlencode($order['id_pesanan']); ?>" class="btn btn-danger btn-sm">Batalkan</a>
                                            <?php elseif ($order['status_pesanan'] === 'Dibatalkan'): ?>
                                                 <a href="checkout.php?ulang=<?= $order['id_pesanan'] ?>" class="btn btn-success btn-sm">Beli Lagi</a>
                                            <?php else: ?>
                                                <a href="lacak.php?id=<?= $order['id_pesanan'] ?>" class="btn btn-info btn-sm">Lacak</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr class="collapse" id="collapseDetail<?= $order['id_pesanan'] ?>">
                                        <td colspan="6">
                                            <div class="p-3 bg-light">
                                                <h6>Daftar Produk</h6>
                                                <table class="table table-sm table-borderless">
                                                    <tbody>
                                                    <?php
                                                    $id_pesanan = $order['id_pesanan'];
                                                    $produkQ = mysqli_query($kon, "SELECT pd.*, pr.nama_produk, pr.gambar, pr.harga FROM pesanan_detail pd JOIN produk pr ON pd.id_produk = pr.id_produk WHERE pd.id_pesanan = $id_pesanan");
                                                    while ($produk = mysqli_fetch_assoc($produkQ)): ?>
                                                        <tr>
                                                            <td><img src="../uploads/<?= htmlspecialchars($produk['gambar']); ?>" class="product-img"></td>
                                                            <td><?= htmlspecialchars($produk['nama_produk']); ?><br><small class="text-muted"><?= $produk['jumlah']; ?> x Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></small></td>
                                                            <td class="text-end">Rp <?= number_format($produk['subtotal'], 0, ',', '.'); ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                    </tbody>
                                                </table>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Informasi Pembayaran</h6>
                                                        <p class="mb-0"><strong>Metode:</strong> <?= htmlspecialchars($order['metode_pembayaran']); ?></p>
                                                        <p><strong>Status:</strong> <?= htmlspecialchars($order['status_pembayaran'] ?? '-'); ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Informasi Pengiriman</h6>
                                                        <p class="mb-0"><strong>Alamat:</strong> <?= htmlspecialchars($order['alamat_pengiriman'] ?? '-'); ?></p>
                                                        <p class="mb-0"><strong>Kurir:</strong> <?= htmlspecialchars($order['nama_kurir'] ?? '-'); ?> (<?= htmlspecialchars($order['nomor_resi'] ?? '-'); ?>)</p>
                                                        <p><strong>Perkiraan Tiba:</strong> <?= $order['perkiraan_tiba'] ? date('d M Y', strtotime($order['perkiraan_tiba'])) : '-'; ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Beri Ulasan untuk Pesanan #<span id="modal-order-id"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modal-alert-placeholder"></div>
                    <form id="reviewForm">
                        <input type="hidden" id="form-order-id" name="order_id">
                        <div id="review-product-list">
                            <div class="text-center" id="modal-loader"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="submit-review-btn">Kirim Ulasan</button>
                </div>
            </div>
        </div>
    </div>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>

    // 1. Validasi Rentang Tanggal
    function validateDateRange() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
            alert('Tanggal mulai harus sebelum atau sama dengan tanggal akhir.');
            return false;
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', function () {
        // 2. Logika untuk Modal Review
        const reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));
        const modalBody = document.getElementById('review-product-list');
        const modalOrderIdSpan = document.getElementById('modal-order-id');
        const formOrderIdInput = document.getElementById('form-order-id');
        const modalLoader = document.getElementById('modal-loader');
        const modalAlertPlaceholder = document.getElementById('modal-alert-placeholder');

        document.querySelectorAll('.open-review-modal').forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.dataset.orderId;
                
                modalOrderIdSpan.textContent = orderId;
                formOrderIdInput.value = orderId;
                
                modalLoader.style.display = 'block';
                modalBody.innerHTML = '';
                modalBody.appendChild(modalLoader);
                modalAlertPlaceholder.innerHTML = '';

                fetch(`get_review_details.php?order_id=${orderId}`)
                    .then(response => response.json())
                    .then(data => {
                        modalLoader.style.display = 'none';
                        if (data.success) {
                            buildReviewForm(data.products);
                        } else {
                            showAlert(data.message, 'danger');
                        }
                    })
                    .catch(() => showAlert('Gagal memuat data produk.', 'danger'));
                
                reviewModal.show();
            });
        });

        function buildReviewForm(products) {
            let formHtml = '';
            let hasReviewableItems = false;
            products.forEach((product, index) => {
                if(product.review_count > 0) {
                     formHtml += `<div class="alert alert-light p-2">Anda sudah memberikan ulasan untuk <strong>${product.nama_produk}</strong>.</div>`;
                } else {
                    hasReviewableItems = true;
                    formHtml += `
                        <div class="review-product-item mb-4 border-bottom pb-3">
                            <img src="../uploads/${product.gambar}" alt="${product.nama_produk}">
                            <div class="details">
                                <h6 class="mb-2">${product.nama_produk}</h6>
                                <input type="hidden" name="reviews[${index}][product_id]" value="${product.id_produk}">
                                <select name="reviews[${index}][rating_produk]" class="form-select form-select-sm" required><option value="" disabled selected>Rating Produk...</option><option value="5">⭐⭐⭐⭐⭐</option><option value="4">⭐⭐⭐⭐</option><option value="3">⭐⭐⭐</option><option value="2">⭐⭐</option><option value="1">⭐</option></select>
                                <select name="reviews[${index}][rating_pelayanan]" class="form-select form-select-sm" required><option value="" disabled selected>Rating Pelayanan...</option><option value="5">⭐⭐⭐⭐⭐</option><option value="4">⭐⭐⭐⭐</option><option value="3">⭐⭐⭐</option><option value="2">⭐⭐</option><option value="1">⭐</option></select>
                                <select name="reviews[${index}][rating_pengiriman]" class="form-select form-select-sm" required><option value="" disabled selected>Rating Pengiriman...</option><option value="5">⭐⭐⭐⭐⭐</option><option value="4">⭐⭐⭐⭐</option><option value="3">⭐⭐⭐</option><option value="2">⭐⭐</option><option value="1">⭐</option></select>
                                <textarea name="reviews[${index}][komentar]" class="form-control" rows="2" placeholder="Bagikan pendapatmu tentang produk ini..." required></textarea>
                            </div>
                        </div>
                    `;
                }
            });
            modalBody.innerHTML = formHtml;
            // Sembunyikan tombol submit jika semua item sudah diulas
            document.getElementById('submit-review-btn').style.display = hasReviewableItems ? 'inline-block' : 'none';
        }

        document.getElementById('submit-review-btn').addEventListener('click', function() {
            const form = document.getElementById('reviewForm');
            const formData = new FormData(form);
            const submitBtn = this;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengirim...`;

            fetch('submit_review.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    reviewModal.hide();
                    alert(data.message);
                    location.reload();
                } else {
                    showAlert(data.message || 'Terjadi kesalahan.', 'danger');
                }
            })
            .catch(() => showAlert('Terjadi kesalahan jaringan.', 'danger'))
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Kirim Ulasan';
            });
        });

        function showAlert(message, type) {
            modalAlertPlaceholder.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
        }
    });
    </script>

</body>
</html>