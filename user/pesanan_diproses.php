<?php
// Koneksi ke database
require_once '../db.php';
session_start();
$page = "pesanan_diproses";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$userName = $_SESSION['user'];
// Mengambil ID user untuk keamanan query
$user_stmt = $kon->prepare("SELECT id_user FROM user WHERE nama = ?");
$user_stmt->bind_param("s", $userName);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user_row = $user_result->fetch_assoc();
$userId = $user_row['id_user'];
$user_stmt->close();

// Query utama untuk mengambil data pesanan yang 'Diproses'
$query = "SELECT * FROM pesanan 
          WHERE id_user = ? AND status_pesanan = 'Diproses'
          ORDER BY tanggal_pesanan DESC";
$stmt = $kon->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$pesanan_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

function formatCurrency($number) {
    return 'Rp ' . number_format($number ?? 0, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Diproses</title>
    
    <?php include 'aset.php'; ?>

    <style>
        body {
            background-color: #f8f9fa;
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
            background-color: #f8f9fa;
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
</head>
<body>

<?php require "atas.php"; ?>
<?php require "profil_menu.php"; ?>

<main id="main" class="main">
    <div class="pagetitle">
        <h1><i class="bi bi-box-seam"></i> Pesanan Diproses</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="profil.php">Profil</a></li>
                <li class="breadcrumb-item active">Pesanan Diproses</li>
            </ol>
        </nav>
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
                                                data-id="<?= $pesanan['id_pesanan'] ?>">
                                            <i class="bi bi-eye"></i> Detail Pesanan
                                        </button>
                                        <a href="form_pembatalan.php?id_pesanan=<?= $pesanan['id_pesanan'] ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Yakin ingin mengajukan pembatalan untuk pesanan ini?');">
                                            <i class="bi bi-x-circle"></i> Batalkan
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

<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/main.js"></script>

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
});
</script>

</body>
</html>