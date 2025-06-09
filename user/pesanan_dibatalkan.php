<?php
// Koneksi ke database
require_once '../db.php';
session_start();
$page = "pesanan_dibatalkan";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$nama_user = $_SESSION['user'];
// Query untuk mengambil data pesanan dengan status 'Dibatalkan'
// Query untuk mengambil list pesanan user yang statusnya 'Dibatalkan'
$stmt = $kon->prepare("SELECT * FROM pesanan JOIN user ON user.id_user = pesanan.id_user WHERE status_pesanan = 'Dibatalkan' AND nama = ? ORDER BY tanggal_pesanan DESC");
$stmt->bind_param("i", $nama_user);
$stmt->execute();
$result = $stmt->get_result();
$pesanan = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Dibatalkan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/img/LOGOCASALUXE2.png" rel="icon">
    <link href="../assets/img/LOGOCASALUXE2.png" rel="apple-touch-icon">

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
        body {
            background-color: #f5f5f5;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            min-height: 150px;
        }
        .card-img-top {
            height: 150px;
            object-fit: cover;
            border-radius: 15px 15px 0 0;
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
            background-color: orange;
            color: white;
        }
    </style>
    <?php include 'aset.php'; ?>
</head>
<body>

<?php require "atas.php"; ?>
<!-- End Header -->

<!-- ======= Sidebar ======= -->
<?php require "profil_menu.php"; ?>
<!-- End Sidebar-->

<main id="main" class="main">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Pesanan Dibatalkan</h4>
        </div>

        <?php if (count($pesanan) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Tanggal</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Notifikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pesanan as $i => $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id_pesanan']) ?></td>
                            <td><?= date('d-m-Y', strtotime($row['tanggal_pesanan'])) ?></td>
                            <td>IDR <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($row['status_pesanan']) ?></td>
                            <td><?= htmlspecialchars($row['notifikasi_status']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary btn-detail"
                                    data-id="<?= $row['id_pesanan'] ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#detailModal">
                                    Detail
                                </button>
                                <a href="checkout.php?ulang=<?= $row['id_pesanan'] ?>" class="btn btn-success btn-sm">Beli Lagi</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p>Tidak ada pesanan yang dibatalkan.</p>
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
<script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/chart.js/chart.umd.js"></script>
<script src="../assets/vendor/echarts/echarts.min.js"></script>
<script src="../assets/vendor/quill/quill.min.js"></script>
<script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="../assets/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
<script src="../assets/js/main.js"></script>
</body>
</html>
