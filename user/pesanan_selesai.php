<?php
require '../db.php';
session_start();
$page = "pesanan_selesai";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
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

        <?php if (count($pesanan) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Tanggal</th>
                            <th>Total Harga</th>
                            <th>Status Review</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pesanan as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id_pesanan']) ?></td>
                            <td><?= date('d-m-Y', strtotime($row['tanggal_pesanan'])) ?></td>
                            <td>IDR <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['review_status']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary btn-detail"
                                    data-id="<?= $row['id_pesanan'] ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#detailModal">
                                    Detail
                                </button>
                                <?php if ($row['review_status'] === 'Menunggu Dinilai'): ?>
                                    <a href="review.php?id=<?= $row['id_pesanan'] ?>" class="btn btn-secondary btn-sm">Nilai</a>
                                <?php else: ?>
                                    <a href="checkout.php?ulang=<?= $row['id_pesanan'] ?>" class="btn btn-success btn-sm">Beli Lagi</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>Tidak ada pesanan untuk ditampilkan.</p>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Detail Pesanan -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <!-- Konten detail akan dimuat via AJAX -->
    </div>
  </div>
</div>
<!-- End Modal -->

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