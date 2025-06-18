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
$query = "SELECT produk.id_produk, produk.nama_produk, produk.deskripsi, produk.stok, k.nama_kategori, k.id_kategori, produk.harga, produk.gambar, 
                 SUM(pesanan_detail.jumlah) AS total_dibeli
          FROM produk
          JOIN pesanan_detail ON produk.id_produk = pesanan_detail.id_produk
          JOIN kategori k ON produk.id_kategori = k.id_kategori
          GROUP BY produk.id_produk
          HAVING total_dibeli > 10
          ORDER BY total_dibeli DESC";
$result = mysqli_query($kon, $query);

// Mulai halaman HTML
function formatCurrency($number) {
    return 'Rp ' . number_format($number ?? 0, 0, ',', '.');
}
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
        div.product-name{
            font-size: 14px;
        }
        main{
            padding: 10px !important;
        }
        .page-header {
            padding: 0 0;
            margin-bottom: 10px;
        }
        .page-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
        }
        .page-header p {
            font-size: 1.1rem;
            color: #6c757d;
        }
        .btn-filter-dropdown {
            background-color: #fff;
            border: 1px solid #dee2e6;
            color: #333;
            font-weight: 500;
        }
        .btn-filter-dropdown:hover, .btn-filter-dropdown:focus {
            background-color: #f8f9fa;
            border-color: #adb5bd;
        }

        .product-card {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: .75rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .product-card .card-img-container {
            position: relative;
        }
        .product-card-img-top {
            aspect-ratio: 1 / 1;
            object-fit: cover;
        }
        .btn-wishlist {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(2px);
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #eee;
            color: #333;
            transition: all 0.2s ease;
        }
        .btn-wishlist:hover {
            background-color: #e21d1d;
            color: #fff;
        }
        .product-card .card-body {
            padding: 1rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        /* PERUBAHAN TAMPILAN NAMA PRODUK & HARGA */
        .product-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        .product-name {
            /* font-size: 1.05rem; */
            font-weight: 600;
            color: #fd7e14;
            flex-grow: 1;
            padding-right: 10px;
        }
        .product-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fd7e14; /* Warna orange */
            white-space: nowrap;
        }
        
        /* PERUBAHAN TAMPILAN GRUP TOMBOL */
        .product-card .button-group {
            margin-top: auto;
            display: flex;
            gap: 10px; /* Jarak antar tombol */
        }
        .product-card .btn {
            font-weight: 600;
            flex: 1; /* Membuat kedua tombol memiliki lebar yang sama */
        }
        .btn-buy-now {
            background-color: #1A877E; /* Warna hijau toska */
            border-color: #1A877E;
            color: #fff;
            padding-top: 13px;
            padding-bottom: 13px;
            font-size: 10px;
        }
        .btn-buy-now:hover {
            background-color: #1A877E;
            border-color: #1A877E;
            box-shadow: 0 0 12px rgba(26, 135, 126, 0.64);
            color: #ffffff !important;
        }
        .btn-add-to-cart {
            background-color:transparent; /* Warna kuning-oranye */
            border-color: #ffc107;
            padding-top: 13px;
            padding-bottom: 13px;
            font-size: 10px;
            color: #ffc107;
        }
        .btn-add-to-cart:hover {
            background-color: #ffc107;
            border-color: #ffc107;
            color: #ffffff;
        }
        .standalone-back-button-container {
            margin-bottom: 15px; 
            padding-left: 0px; 
        }
        .standalone-back-button {
            display: inline-flex;
            align-items: center;
            text-decoration: none; 
            color: #6c757d;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 8px;
            transition: background-color 0.2s ease-in-out; 
        }
        .standalone-back-button:hover {
            background-color: #e9ecef; 
            color: #495057;
        }
        .standalone-back-button .bi {
            font-size: 1.1em;
            margin-right: 8px; 
        }
    </style>
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
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col">
                <div class="card h-50 shadow-sm border-0 rounded-4">
                    <a href="detail_produk.php?product_id=<?= $row['id_produk']; ?>">
                        <img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" class="card-img-top rounded-top-4 w-100" alt="Gambar Produk" style="height: 236px; object-fit: contain;">
                    </a>
                    <div class="card-body mt-4">
                        <div class="product-info d-flex flex-column mb-0">
                            <div class="product-name mb-2 fs-5"><?= htmlspecialchars($row['nama_produk']); ?></div>
                            <div class="ap">
                                <p class="card-text mb-0 mt-3 small text-muted">Kategori: <?= htmlspecialchars($row['nama_kategori']) ?></p>
                                <div class="product-price"><?= formatCurrency($row['harga']); ?></div>
                            </div>
                        </div>
                        
                        <div class="button-group d-flex gap-3 mt-4">
                            <form method="POST" action="checkout.php" class="w-50">
                                <input type="hidden" name="product_id" value="<?= $row['id_produk']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" name="buy_now" class="btn btn-buy-now w-100">Beli Sekarang</button>
                            </form>
                            <form method="POST" action="add_to_cart.php" class="w-50">
                                <input type="hidden" name="product_id" value="<?= $row['id_produk']; ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-add-to-cart w-100">Masuk Keranjang</button>
                            </form>
                        </div>
                    </div>
                </div>


                <!-- <div class="mt-auto d-flex justify-content-between gap-2">
                    <form method="POST" action="add_to_cart.php" class="w-50">
                        <input type="hidden" name="product_id" value="<?= $row['id_produk']; ?>">
                        <button type="submit" name="add_to_cart" class="btn btn-sm text-white w-100 fw-semibold btn-custom-small" style="background-color: #1a877e;">BELI SEKARANG</button>
                    </form>
                    <a href="detail_produk.php?product_id=<?= $row['id_produk']; ?>" class="btn btn-warning text-white btn-sm w-50 fw-semibold btn-custom-small">MASUK KERANJANG</a>
                </div> -->

            
            
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