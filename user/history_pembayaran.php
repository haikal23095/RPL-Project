<?php
session_start();
include('../db.php');
$page = "history_pembayaran";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['user'];
$kue_user = mysqli_query($kon, "SELECT id_user FROM user WHERE nama = '$userName'");
$row_user = mysqli_fetch_array($kue_user);
$userId = $row_user['id_user'];
$_SESSION['user_id'] = $userId;

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

$dateQuery = "";
$bindTypes = "i";
$bindParams = [&$userId];

if ($start_date && $end_date && strtotime($start_date) <= strtotime($end_date)) {
    $dateQuery = " AND p.tanggal_pesanan BETWEEN ? AND ?";
    $bindTypes .= "ss";
    $end_date_full = $end_date . ' 23:59:59';
    $bindParams[] = &$start_date;
    $bindParams[] = &$end_date_full;
} else {
    $start_date = null;
    $end_date = null;
}

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

function formatCurrency($number) {
    return 'Rp ' . number_format($number ?? 0, 0, ',', '.');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Histori Pembelian</title>
    <?php include "aset.php"; ?>
    <style>
        .order-card { transition: box-shadow .3s; border-radius: .5rem; }
        .order-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .product-item-mini { display: flex; align-items: center; padding: .5rem 0; border-bottom: 1px solid #f0f0f0; }
        .product-item-mini:last-child { border-bottom: none; }
        .product-img-mini { width: 50px; height: 50px; object-fit: cover; border-radius: .375rem; margin-right: 1rem; }
        .review-product-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px; }
    </style>
</head>
<body>
    <?php require "atas.php"; ?>
    <?php require "profil_menu.php"; ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif;
            color: #2D3A3A !important;
        }
        .sidebar {
            background-color: #F8F7F1 !important;
        }
        header{
            background-color: #F8F7F1 !important;
        }
        h5{
            font-size: 18px !important;
            color: #2D3A3A !important;
            font-weight: bold !important;
        }
        strong{
            color: #ff771d;
        }
        .btn-primary {
            background-color: #FFC300 !important;
            border: 1px solid #FFC300 !important;
        }
        .badge{
            background-color: #FFBB34 !important;
        }
        .btn-danger {
            background-color: transparent !important;
            border: 1px solid #763D2D !important;
            color: #763D2D !important;
        }
        .btn-danger:hover{
            background-color: #763D2D !important;
            color: #ffffff !important;
        }
        .btn-outline-secondary {
            background-color: transparent !important;
            border: 1px solid #FF8C12 !important;
            color: #FF8C12 !important;
        }
        .btn-outline-secondary:hover{
            background-color: #FF8C12 !important;
            color: #ffffff !important;
        }
        .btn-outline-success {
            background-color: transparent !important;
            border: 1px solid #1A877E !important;
            color: #1A877E !important;
        }
        .btn-outline-success:hover{
            background-color: #1A877E !important;
            color: #ffffff !important;
        }
        .btn-success {
            background-color: transparent !important;
            border: 1px solid #1A877E !important;
            color: #1A877E !important;
        }
        .btn-success:hover{
            background-color: #1A877E !important;
            color: #ffffff !important;
        }
    </style>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-cash-stack"></i> RIWAYAT PEMBAYARAN</h1>
            <nav><ol class="breadcrumb"><li class="breadcrumb-item"><a href="profil.php">PROFIL</a></li><li class="breadcrumb-item active">RIWAYAT PEMBAYARAN</li></ol></nav>
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
                            <?php if ($start_date && $end_date): ?><a href="history_pembayaran.php" class="btn btn-secondary">Reset</a><?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="section">
            <?php if (!empty($noOrdersMessage)): ?>
                <div class="alert alert-info"><?= $noOrdersMessage ?></div>
            <?php elseif(empty($orders)): ?>
                <div class="alert alert-info">Anda belum memiliki riwayat pesanan.</div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="card order-card mb-3">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Pesanan #<?= htmlspecialchars($order['id_pesanan']); ?></strong>
                                <small class="text-muted ms-2"><?= date('d M Y', strtotime($order['tanggal_pesanan'])); ?></small>
                            </div>
                            <span class="badge bg-dark"><?= htmlspecialchars($order['status_pesanan']); ?></span>
                        </div>
                        <div class="card-body p-3">
                            <?php
                            $id_pesanan = $order['id_pesanan'];
                            $produkQ = mysqli_query($kon, "SELECT pd.*, pr.nama_produk, pr.gambar, pr.harga FROM pesanan_detail pd JOIN produk pr ON pd.id_produk = pr.id_produk WHERE pd.id_pesanan = $id_pesanan");
                            
                            $subtotal_produk = 0;
                            while ($produk_row = mysqli_fetch_assoc($produkQ)) {
                                $subtotal_produk += $produk_row['subtotal'];
                            }
                            
                            $ongkir_dihitung = $subtotal_produk * 0.10;
                            $total_seharusnya = $subtotal_produk + $ongkir_dihitung;
                            $diskon_dihitung = $total_seharusnya - $order['total_harga'];
                            if ($diskon_dihitung < 0) {
                                $diskon_dihitung = 0;
                            }
                            
                            mysqli_data_seek($produkQ, 0); 
                            
                            while ($produk = mysqli_fetch_assoc($produkQ)):
                            ?>
                                <div class="product-item-mini">
                                    <img src="../uploads/<?= htmlspecialchars($produk['gambar']); ?>" class="product-img-mini">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold"><?= htmlspecialchars($produk['nama_produk']); ?></div>
                                        <div class="text-muted"><?= $produk['jumlah']; ?> x <?= formatCurrency($produk['harga']); ?></div>
                                    </div>
                                    <div class="fw-bold"><?= formatCurrency($produk['subtotal']); ?></div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted">Total Pesanan:</span>
                                    <strong class="fs-5 ms-1"><?= formatCurrency($order['total_harga']); ?></strong>
                                </div>
                                <div class="text-end">
                                     <button class="btn btn-outline-secondary btn-sm open-detail-modal"
                                        data-bs-toggle="modal" data-bs-target="#detailModal"
                                        data-order-id="<?= htmlspecialchars($order['id_pesanan']); ?>"
                                        data-status="<?= htmlspecialchars($order['status_pesanan']); ?>"
                                        data-tanggal="<?= date('d M Y, H:i', strtotime($order['tanggal_pesanan'])); ?>"
                                        data-kurir="<?= htmlspecialchars($order['nama_kurir'] ?? '-'); ?>"
                                        data-resi="<?= htmlspecialchars($order['nomor_resi'] ?? '-'); ?>"
                                        data-alamat="<?= htmlspecialchars($order['alamat_pengiriman'] ?? '-'); ?>"
                                        data-metode="<?= htmlspecialchars($order['metode_pembayaran']); ?>"
                                        data-subtotal="<?= formatCurrency($subtotal_produk); ?>"
                                        data-ongkir="<?= formatCurrency($ongkir_dihitung); ?>"
                                        data-diskon="<?= formatCurrency($diskon_dihitung); ?>"
                                        data-total="<?= formatCurrency($order['total_harga']); ?>">
                                        Lihat Detail
                                    </button>

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
                                    <?php elseif ($order['status_pesanan'] !== 'Selesai' AND $order['status_pesanan'] !== 'Dibatalkan'  AND $order['status_pesanan'] !== 'Diproses'): ?>
                                        <button class="btn btn-warning btn-sm selesai-btn" data-id="<?= $order['id_pesanan']; ?>">Selesai</button>
                                        <a href="lacak.php?id=<?= $order['id_pesanan'] ?>" class="btn btn-info btn-sm">Lacak</a>
                                        <?php else: ?>
                                        <a href="lacak.php?id=<?= $order['id_pesanan'] ?>" class="btn btn-info btn-sm">Lacak</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
    
    <div class="modal fade" id="detailModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Detail Pesanan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="row"><div class="col-md-6"><p><strong>Status Pesanan:</strong> <br><span id="modal-detail-status" class="badge bg-primary"></span></p><p><strong>No. Pesanan:</strong> <br><span id="modal-detail-order-id"></span></p><p><strong>Tanggal Pembelian:</strong> <br><span id="modal-detail-tanggal"></span></p></div><div class="col-md-6"><h6>Informasi Pengiriman</h6><p class="mb-0"><strong>Kurir:</strong> <span id="modal-detail-kurir"></span></p><p class="mb-0"><strong>No. Resi:</strong> <span id="modal-detail-resi"></span></p><p><strong>Alamat:</strong> <br><span id="modal-detail-alamat"></span></p></div></div><hr><h6>Rincian Pembayaran</h6><table class="table table-sm"><tbody><tr><td>Metode Pembayaran</td><td class="text-end" id="modal-detail-metode"></td></tr><tr><td>Subtotal Produk</td><td class="text-end" id="modal-detail-subtotal"></td></tr><tr><td>Ongkos Kirim</td><td class="text-end" id="modal-detail-ongkir"></td></tr><tr><td>Diskon</td><td class="text-end text-danger" id="modal-detail-diskon"></td></tr><tr class="fw-bold"><td>Total Pembayaran</td><td class="text-end fs-5" id="modal-detail-total"></td></tr></tbody></table></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button></div></div></div></div>
    <div class="modal fade" id="reviewModal" tabindex="-1"><div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Beri Ulasan untuk Pesanan #<span id="modal-order-id"></span></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div id="modal-alert-placeholder"></div><form id="reviewForm"><input type="hidden" id="form-order-id" name="order_id"><div id="review-product-list"><div class="text-center" id="modal-loader"><div class="spinner-border"><span>Loading...</span></div></div></div></form></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button><button type="button" class="btn btn-primary" id="submit-review-btn">Kirim Ulasan</button></div></div></div></div>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>

    <script>
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
        // --- LOGIKA MODAL DETAIL (Tidak Diubah) ---
        document.querySelectorAll('.open-detail-modal').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modal-detail-status').textContent = this.dataset.status;
                document.getElementById('modal-detail-order-id').textContent = `#${this.dataset.orderId}`;
                document.getElementById('modal-detail-tanggal').textContent = this.dataset.tanggal;
                document.getElementById('modal-detail-kurir').textContent = this.dataset.kurir;
                document.getElementById('modal-detail-resi').textContent = this.dataset.resi;
                document.getElementById('modal-detail-alamat').textContent = this.dataset.alamat;
                document.getElementById('modal-detail-metode').textContent = this.dataset.metode;
                document.getElementById('modal-detail-subtotal').textContent = this.dataset.subtotal;
                document.getElementById('modal-detail-ongkir').textContent = this.dataset.ongkir;
                document.getElementById('modal-detail-diskon').textContent = `- ${this.dataset.diskon}`;
                document.getElementById('modal-detail-total').textContent = this.dataset.total;
            });
        });

        // --- LOGIKA MODAL REVIEW (Tidak Diubah) ---
        // (Kode modal review yang sudah ada tetap di sini)
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
                fetch(`get_review_details.php?order_id=${orderId}`).then(response => response.json()).then(data => {
                    modalLoader.style.display = 'none';
                    if (data.success) { buildReviewForm(data.products); } 
                    else { showAlert(data.message, 'danger'); }
                }).catch(() => showAlert('Gagal memuat data produk.', 'danger'));
                reviewModal.show();
            });
        });

        function buildReviewForm(products) {
            let formHtml = '', hasReviewableItems = false;
            products.forEach((product, index) => {
                if (product.review_count > 0) {
                    formHtml += `<div class="alert alert-light p-2">Anda sudah memberikan ulasan untuk <strong>${product.nama_produk}</strong>.</div>`;
                } else {
                    hasReviewableItems = true;
                    formHtml += `<div class="review-product-item mb-4 border-bottom pb-3"><img src="../uploads/${product.gambar}" alt="${product.nama_produk}"><div class="details"><h6 class="mb-2">${product.nama_produk}</h6><input type="hidden" name="reviews[${index}][product_id]" value="${product.id_produk}"><select name="reviews[${index}][rating_produk]" class="form-select form-select-sm" required><option value="" disabled selected>Rating Produk...</option><option value="5">⭐⭐⭐⭐⭐</option><option value="4">⭐⭐⭐⭐</option><option value="3">⭐⭐⭐</option><option value="2">⭐⭐</option><option value="1">⭐</option></select><select name="reviews[${index}][rating_pelayanan]" class="form-select form-select-sm" required><option value="" disabled selected>Rating Pelayanan...</option><option value="5">⭐⭐⭐⭐⭐</option><option value="4">⭐⭐⭐⭐</option><option value="3">⭐⭐⭐</option><option value="2">⭐⭐</option><option value="1">⭐</option></select><select name="reviews[${index}][rating_pengiriman]" class="form-select form-select-sm" required><option value="" disabled selected>Rating Pengiriman...</option><option value="5">⭐⭐⭐⭐⭐</option><option value="4">⭐⭐⭐⭐</option><option value="3">⭐⭐⭐</option><option value="2">⭐⭐</option><option value="1">⭐</option></select><textarea name="reviews[${index}][komentar]" class="form-control" rows="2" placeholder="Bagikan pendapatmu..." required></textarea></div></div>`;
                }
            });
            modalBody.innerHTML = formHtml;
            document.getElementById('submit-review-btn').style.display = hasReviewableItems ? 'inline-block' : 'none';
        }

        document.getElementById('submit-review-btn').addEventListener('click', function() {
            const form = document.getElementById('reviewForm');
            const formData = new FormData(form);
            const submitBtn = this;
            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Mengirim...`;
            fetch('submit_review.php', { method: 'POST', body: formData }).then(response => response.json()).then(data => {
                if (data.success) { reviewModal.hide(); alert(data.message); location.reload(); } 
                else { showAlert(data.message || 'Terjadi kesalahan.', 'danger'); }
            }).catch(() => showAlert('Terjadi kesalahan jaringan.', 'danger')).finally(() => { submitBtn.disabled = false; submitBtn.textContent = 'Kirim Ulasan'; });
        });

        function showAlert(message, type) {
            modalAlertPlaceholder.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show">${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
        }
        
        // --- KODE BARU UNTUK TOMBOL SELESAI ---
        document.querySelectorAll('.selesai-btn').forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.dataset.id;
                
                if (confirm(`Apakah Anda yakin ingin menyelesaikan pesanan #${orderId}? Tindakan ini tidak dapat diurungkan.`)) {
                    // Siapkan data untuk dikirim
                    const formData = new FormData();
                    formData.append('id_pesanan', orderId);
                    formData.append('status', 'selesai');
                    
                    // Kirim request ke backend
                    fetch('update_status.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Status pesanan berhasil diubah menjadi Selesai.');
                            location.reload(); // Muat ulang halaman untuk melihat perubahan
                        } else {
                            alert('Gagal memperbarui status: ' + (data.message || 'Error tidak diketahui.'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan jaringan saat mencoba memperbarui status.');
                    });
                }
            });
        });
        // --- AKHIR KODE BARU ---
    });
    </script>
</body>
</html>