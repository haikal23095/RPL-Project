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
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$userName'");
$row_user = mysqli_fetch_array($kue_user);
$userId = $row_user['id_user']; 

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
    $bindParams[] = &$start_date;
    $bindParams[] = &$end_date;
} else {
    $start_date = null;
    $end_date = null;
}

// Perbaikan query dengan filter tanggal
$sql = "SELECT p.id_pesanan, p.total_harga, p.status_pesanan, p.tanggal_pesanan,
        pb.metode_pembayaran, pb.status_pembayaran, 
        pg.alamat_pengiriman, pg.nomor_resi, pg.nama_kurir, pg.tanggal_kirim, pg.perkiraan_tiba
        FROM pesanan p
        LEFT JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan
        LEFT JOIN pengiriman_pesanan pg ON p.id_pesanan = pg.id_pesanan
        WHERE p.id_user = ? $dateQuery
        ORDER BY p.tanggal_pesanan DESC";

// Persiapkan statement
$stmt = mysqli_prepare($kon, $sql);
call_user_func_array([$stmt, 'bind_param'], array_merge([$bindTypes], $bindParams));
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
$noOrdersMessage = empty($orders) ? "Tidak ada pesanan dalam rentang tanggal yang dipilih" : null;
mysqli_stmt_close($stmt);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Histori Pembelian</title>
    <!-- Favicons -->
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

    <!-- Template Main CSS File -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        /* Card & Table Styling */

        .table tr:hover td {
            background: #e9eff9;
        }

        /* Product Styling */

        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        .product-name {
            font-weight: 500;
            color: #333;
        }


        .btn-detail:hover {
            color: #0056b3;
        }

       /* Tambahkan ini untuk memastikan warna h6 tetap terlihat */
        .collapse h6 {
            color: #333; /* Atur ulang warna teks */
            font-weight: 600; /* Atur ketebalan huruf */
            margin: 10px 0; /* Jarak atas dan bawah */
        }

        /* Tambahkan gaya lain jika perlu */
        .collapse td {
            background: #f8f9fa; /* Latar belakang kolom collapse */
            padding: 15px; /* Padding pada kolom */
        }

        .rotate {
            transform: rotate(180deg);
            transition: transform 0.3s;
        }

        .card-title {
            margin-bottom: 20px; /* Jarak bawah untuk judul */
            font-size: 1.5rem; /* Ukuran font untuk judul */
            font-weight: 600; /* Ketebalan huruf */
            color: #333; /* Warna teks */
        }

        /* Tambahkan ini untuk mengubah kursor menjadi pointer pada input date */
        input[type="date"] {
            cursor: pointer; /* Mengubah kursor menjadi pointer */
        }

        /* Tambahkan ini untuk menyamakan ukuran tombol dengan input date */
        .input-group .btn {
            height: calc(2.25rem + 2px); /* Sesuaikan tinggi tombol dengan input */
            padding: 0.375rem 0.75rem; /* Sesuaikan padding tombol */
        }

    </style>
</head>
<body>
    <!-- Header -->
    <?php require "atas.php"; ?>
    <!-- Sidebar -->
    <?php require "profil_menu.php"; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-cash-stack"></i> HISTORI PEMBAYARAN</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">PROFIL</a></li>
                    <li class="breadcrumb-item active">HISTORI PEMBAYARAN</li>
                </ol>
            </nav>
        </div>
        
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="card-title">Filter Pesanan</h2>
                            <form method="GET" action="" onsubmit="return validateDateRange()">
                                <div class="input-group mb-3">
                                    <input type="date" name="start_date" id="start_date" class="form-control" 
                                           placeholder="Tanggal Mulai" 
                                           value="<?= htmlspecialchars($start_date ?? ''); ?>" 
                                           required>
                                    <input type="date" name="end_date" id="end_date" class="form-control" 
                                           placeholder="Tanggal Akhir" 
                                           value="<?= htmlspecialchars($end_date ?? ''); ?>" 
                                           required>
                                    <button class="btn btn-primary" type="submit">Filter</button>
                                    <?php if ($start_date && $end_date): ?>
                                        <a href="history_pembayaran.php" class="btn btn-secondary">Reset</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                        <h2 class="card-title">Daftar Pesanan</h2>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Detail</th>
                                            <th>Tanggal</th>
                                            <th>ID Pesanan</th>
                                            <th>Total</th>
                                            <th>Status Pesanan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($noOrdersMessage): ?>
                                        <tr><td colspan="6"><div class="alert alert-info" role="alert"><?= $noOrdersMessage ?></div></td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>
                                                <button class="btn btn-link btn-detail" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#collapseDetail<?= $order['id_pesanan'] ?>"
                                                    aria-expanded="false"
                                                    aria-controls="collapseDetail<?= $order['id_pesanan'] ?>">
                                                    <i class="bi bi-chevron-down"></i>
                                                </button>
                                                
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($order['tanggal_pesanan'])); ?></td>
                                            <td><?= htmlspecialchars($order['id_pesanan']); ?></td>
                                            <td>Rp <?= number_format($order['total_harga'], 0, ',', '.'); ?></td>
                                            <td><?= htmlspecialchars($order['status_pesanan']); ?></td>
                                            <td>
                                                <!-- Selesai Button -->
                                                <?php if ($order['status_pesanan'] !== 'Selesai' AND $order['status_pesanan'] !== 'Dibatalkan'  AND $order['status_pesanan'] !== 'Diproses'): ?>
                                                    <button class="btn btn-warning btn-sm selesai-btn" data-id="<?= $order['id_pesanan']; ?>">Selesai</button>
                                                <?php endif; ?>
                                                <?php if ($order['status_pesanan'] === 'Selesai'): ?>
                                                    <a href="checkout.php?ulang=<?= $order['id_pesanan'] ?>" class="btn btn-success btn-sm">Beli Lagi</a>
                                                <?php endif; ?>
                                                <?php if ($order['status_pesanan'] === 'Dibatalkan'): ?>
                                                    <a href="checkout.php?ulang=<?= $order['id_pesanan'] ?>" class="btn btn-success btn-sm">Beli Lagi</a>
                                                <?php endif; ?>
                                                <?php if ($order['status_pesanan'] === 'Diproses'): ?>
                                                    <a href="form_pembatalan.php?id_pesanan=<?= urlencode($order['id_pesanan']); ?>" class="btn btn-danger btn-sm">Batalkan</a>
                                                <?php endif; ?>
                                                <!-- Lacak Button -->
                                                <?php if ($order['status_pesanan'] !== 'Selesai'  AND $order['status_pesanan'] !== 'Dibatalkan' AND $order['status_pesanan'] !== 'Diproses' ): ?>
                                                    <a href="lacak.php?id=<?= $order['id_pesanan'] ?>" 
                                                       class="btn btn-info btn-sm">Lacak</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr class="collapse" id="collapseDetail<?= $order['id_pesanan'] ?>">
                                            <td colspan="6">
                                                <div class="p-3">
                                                    <h6>Daftar Produk</h6>
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Gambar</th>
                                                                <th>Nama Produk</th>
                                                                <th>Jumlah</th>
                                                                <th>Harga Satuan</th>
                                                                <th>Subtotal</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        // Ambil produk untuk pesanan ini
                                                        $id_pesanan = $order['id_pesanan'];
                                                        $produkQ = mysqli_query($kon, "SELECT pd.*, pr.nama_produk, pr.gambar, pr.harga FROM pesanan_detail pd JOIN produk pr ON pd.id_produk = pr.id_produk WHERE pd.id_pesanan = $id_pesanan");
                                                        while ($produk = mysqli_fetch_assoc($produkQ)): ?>
                                                            <tr>
                                                                <td><img src="../uploads/<?= htmlspecialchars($produk['gambar']); ?>" class="product-img"></td>
                                                                <td><?= htmlspecialchars($produk['nama_produk']); ?></td>
                                                                <td><?= $produk['jumlah']; ?></td>
                                                                <td>Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></td>
                                                                <td>Rp <?= number_format($produk['subtotal'], 0, ',', '.'); ?></td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                        </tbody>
                                                    </table>
                                                    <h6 class="mt-3">Informasi Pembayaran</h6>
                                                    <p>
                                                        <strong>Metode Pembayaran:</strong> <?= htmlspecialchars($order['metode_pembayaran']); ?><br>      
                                                        <strong>Status Pembayaran:</strong> <?= htmlspecialchars($order['status_pembayaran'] ?? '-'); ?>
                                                    </p>
                                                    <h6>Informasi Pengiriman</h6>
                                                    <p>
                                                        <strong>Alamat:</strong> <?= htmlspecialchars($order['alamat_pengiriman'] ?? '-'); ?><br>
                                                        <strong>Nomor Resi:</strong> <?= htmlspecialchars($order['nomor_resi'] ?? '-'); ?><br>
                                                        <strong>Ekspedisi:</strong> <?= htmlspecialchars($order['nama_kurir'] ?? '-'); ?><br>
                                                        <strong>Tanggal Kirim:</strong> <?= htmlspecialchars($order['tanggal_kirim'] ?? '-'); ?><br>
                                                        <strong>Perkiraan Tiba:</strong> <?= htmlspecialchars($order['perkiraan_tiba'] ?? '-'); ?><br>
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>    
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- Vendor JS Files -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
    <script src="../assets/vendor/chart.js/chart.min.js"></script>
    <script src="../assets/vendor/echarts/echarts.min.js"></script>
    <script src="../assets/vendor/quill/quill.min.js"></script>
    <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
    <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="../assets/vendor/php-email-form/validate.js"></script>
                                            

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <script src="../assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.btn-detail').forEach(function(btn) {
                btn.addEventListener('click', function () {
                    const target = document.querySelector(this.getAttribute('data-bs-target'));
                    const icon = this.querySelector('i');
                    setTimeout(function() {
                        if (target.classList.contains('show')) {
                            icon.classList.remove('rotate');
                        } else {
                            icon.classList.add('rotate');
                        }
                    }, 350); // waktu animasi collapse Bootstrap
                });
            });
        });
        function validateDateRange() {
            const startDate = new Date(document.getElementById('start_date').value);
            const endDate = new Date(document.getElementById('end_date').value);
            if (startDate > endDate) {
                alert('Tanggal mulai harus sebelum atau sama dengan tanggal akhir');
                return false;
            }
            return true;
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selesaiButtons = document.querySelectorAll('.selesai-btn');

            selesaiButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const pesananId = this.getAttribute('data-id');

                    if (confirm('Apakah Anda yakin ingin menyelesaikan pesanan ini?')) {
                        fetch('update_status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `id_pesanan=${pesananId}&status=selesai`,
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Pesanan berhasil diselesaikan!');
                                location.reload(); // Reload to reflect changes
                            } else {
                                alert('Gagal menyelesaikan pesanan. Silakan coba lagi.');
                            }
                        })
                        .catch(err => {
                            console.error('Error:', err);
                            alert('Terjadi kesalahan. Silakan coba lagi.');
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
