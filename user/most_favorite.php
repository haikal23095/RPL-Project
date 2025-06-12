<?php
// Koneksi ke database
include '../db.php';
session_start();
$page = "fav";

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// Query untuk mendapatkan produk yang paling banyak dibeli
$query = "SELECT produk.id_produk, produk.nama_produk, produk.deskripsi, produk.harga, produk.gambar, 
                 SUM(pesanan_detail.jumlah) AS total_dibeli
          FROM produk
          JOIN pesanan_detail ON produk.id_produk = pesanan_detail.id_produk
          GROUP BY produk.id_produk
          HAVING total_dibeli > 10
          ORDER BY total_dibeli DESC";
$result = mysqli_query($kon, $query);

// Mulai halaman HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Most Favorite Items</title>
    <!-- Link Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            min-height: 450px; /* Ukuran kartu seragam */
        }
        .card-img-top {
            height: 250px; /* Ukuran gambar seragam */
            object-fit: cover; /* Memastikan gambar tidak terdistorsi */
            border-radius: 15px 15px 0 0;
        }
        .card-body {
            min-height: 150px; /* Menyesuaikan tinggi deskripsi produk agar seragam */
        }
        
        .card-body h6 {
        font-size: 0.95rem;
        }
        .card-body p {
        font-size: 0.85rem;
        }
        .btn-warning {
        background-color: #f4a825;
        border: none;
        }
        .btn-warning:hover {
        background-color: #d38d1c;
        }
        .btn-outline-dark {
        border-color: #ccc;
        }
        .btn-custom-small {
            font-size: 0.75rem !important;
            padding: 0.3rem 0.5rem !important;
        }

    </style>
    <?php include 'aset.php'; ?>
</head>

<body>

  <!-- ======= Header ======= -->
  <?php require "atas.php"; ?>
  <!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <?php require "menu.php"; ?>
  <!-- End Sidebar-->
    <main id="main" class="main">
        <div class="pagetitle">
      <h1><i class="bi bi-star-fill"></i>&nbsp; PRODUK FAVORIT</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
          <li class="breadcrumb-item active">PRODUK FAVORIT</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <div class="container mt-5">
        <h2 class="text-center mb-4 fw-bold">PRODUK FAVORIT</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col">
            <div class="card h-100 shadow-sm border-0 rounded-4">
                <a href="detail_produk.php?product_id=<?= $row['id_produk']; ?>">
                <img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" class="card-img-top rounded-top-4" alt="Gambar Produk" style="height: 200px; object-fit: cover;">
                </a>
                <div class="card-body d-flex flex-column justify-content-between">
                <h6 class="fw-semibold mb-1"><?= htmlspecialchars($row['nama_produk']); ?></h6>
                <p class="text-warning fw-bold mb-2">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>


                <div class="mt-auto d-flex justify-content-between gap-2">
                    <form method="POST" action="add_to_cart.php" class="w-50">
                        <input type="hidden" name="product_id" value="<?= $row['id_produk']; ?>">
                        <button type="submit" name="add_to_cart" class="btn btn-sm text-white w-100 fw-semibold btn-custom-small" style="background-color: #1a877e;">BELI SEKARANG</button>
                    </form>
                    <a href="detail_produk.php?product_id=<?= $row['id_produk']; ?>" class="btn btn-warning text-white btn-sm w-50 fw-semibold btn-custom-small">MASUK KERANJANG</a>
                </div>

            
            
            </div>
        </div>
            </div>
        <?php } ?>
        </div>
    </div>
    </main>

    <!-- Link Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core Bootstrap JS -->
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>