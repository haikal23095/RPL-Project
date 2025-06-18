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
        h5{
            font-size: 18px !important;
            color: #ff771d !important;
            font-weight: bold !important;
        }
        strong{
            color: #ff771d;
        }
        div.my-1 {
            color: #FF8C12;
        }
        .btn-primary {
            background-color: #FFC300 !important;
            border: 1px solid #FFC300 !important;
        }
        .badge{
            background-color: #1A877E !important;
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
        .btn-outline-primary {
            background-color: transparent !important;
            border: 1px solid #FF8C12 !important;
            color: #FF8C12 !important;
        }
        .btn-outline-primary:hover{
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
        .pesanan-card {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
            border-radius: .75rem;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease-in-out;
        }
        .pesanan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .pesanan-header {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6;
        }
        .product-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 1rem;
            border-bottom: 1px solid #f1f1f1;
        }
        .product-img-gallery {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: .5rem;
            border: 1px solid #eee;
        }
        .card-footer-actions {
            padding: 0.75rem 1.25rem;
            background-color: #fdfdfd;
        }
        .total-harga {
            color: #fd7e14;
            font-weight: 700;
        }
        .modal-body .product-image-small {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
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
                                            <a href="checkout.php?ulang=<?= $pesanan_item['id_pesanan'] ?>" class="btn btn-success btn-sm">Beli Lagi</a>
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
    const detailModal = document.getElementById('detailModal');
    if(detailModal) {
        detailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const idPesanan = button.getAttribute('data-id');
            const modalContent = document.getElementById('modal-detail-content');
            const modalTitle = document.getElementById('detailModalLabel');

            modalTitle.textContent = 'Detail Pesanan #' + idPesanan;
            modalContent.innerHTML = `<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat detail...</p></div>`;

            // Panggil file AJAX untuk mengisi konten modal
            fetch('pesanan_detail_ajax.php?id_pesanan=' + idPesanan)
                .then(response => {
                    if (!response.ok) { throw new Error('Gagal mengambil data'); }
                    return response.text();
                })
                .then(html => {
                    modalContent.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalContent.innerHTML = '<div class="alert alert-danger">Gagal memuat detail pesanan. Silakan coba lagi nanti.</div>';
                });
        });
    }

    // --- NEW JAVASCRIPT FOR CANCEL MODAL ---
    const cancelModal = document.getElementById('cancelModal');
    if(cancelModal) {
        cancelModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Button that triggered the modal
            const idPesanan = button.getAttribute('data-id-pesanan'); // Extract info from data-* attributes
            const inputOrderId = cancelModal.querySelector('#cancelOrderId');

            if (inputOrderId) {
                inputOrderId.value = idPesanan;
            }
        });

        // Optional: Reset form fields when modal is closed
        cancelModal.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('formPembatalan');
            if (form) {
                form.reset(); // Reset form fields
                // Optionally clear textarea/select manually if reset() doesn't work for them
                form.querySelector('#alasan_pembatalan_modal').value = '';
                form.querySelector('#deskripsi_pembatalan_modal').value = '';
            }
        });
    }
});
</script>


<!-- Vendor JS Files -->
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>

</body>
</html>