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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* SEMUA CSS ANDA TETAP SAMA, TIDAK ADA PERUBAHAN */
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif !important;
            color: #2D3A3A !important;
        }
        main{
            padding: 10px !important;
        }
        .btn-buy-now {
            background-color: #1A877E !important;
            border-color: #1A877E !important;
            color: #fff !important;
            padding-top: 13px;
            padding-bottom: 13px;
            font-size: 10px;
        }
        .btn-buy-now:hover {
            background-color: #156c65;
            border-color: #156c65;
            box-shadow: 0 0 12px rgba(26, 135, 126, 0.64);
            color: #ffffff !important;
        }
        .btn-add-to-cart {
            background-color:transparent;
            border-color: #ffc107 !important;
            padding-top: 13px;
            padding-bottom: 13px;
            font-size: 10px;
            color: #ffc107 !important;
        }
        .btn-add-to-cart:hover {
            background-color: #ffc107 !important;
            border-color: #ffc107 !important;
            color: #ffffff !important;
        }
        .product-price {
            color: #ffc107 !important;
            font-weight: bold;
        }
    </style>
    <?php include 'aset.php'; ?>
</head>

<body>

    <?php require "atas.php"; ?>
    <?php require "menu.php"; ?>
    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-star-fill"></i>&nbsp; PRODUK FAVORIT</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                    <li class="breadcrumb-item active">PRODUK FAVORIT</li>
                </ol>
            </nav>
        </div><div class="container-fluid mt-4">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 rounded-4 d-flex flex-column">
                            <a href="detail_produk.php?product_id=<?= $row['id_produk']; ?>">
                                <img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" class="card-img-top" alt="Gambar Produk" style="height: 230px; object-fit: contain; padding: 10px;">
                            </a>
                            
                            <div class="card-body d-flex flex-column">
                                <div class="mb-2">
                                    <h5 class="card-title fs-6 fw-bold mb-1"><?= htmlspecialchars($row['nama_produk']); ?></h5>
                                    <p class="card-text small text-muted">Kategori: <?= htmlspecialchars($row['nama_kategori']) ?></p>
                                    <p class="product-price fw-bold fs-5"><?= formatCurrency($row['harga']); ?></p>
                                </div>
                                
                                <div class="button-group d-flex gap-2 mt-auto">
                                    <form method="POST" action="checkout.php" class="w-100">
                                        <input type="hidden" name="product_id" value="<?= $row['id_produk']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" name="buy_now" class="btn btn-buy-now w-100">Beli Sekarang</button>
                                    </form>
                                    <form method="POST" action="add_to_cart.php" class="w-100">
                                        <input type="hidden" name="product_id" value="<?= $row['id_produk']; ?>">
                                        <button type="submit" name="add_to_cart" class="btn btn-add-to-cart w-100">Masuk Keranjang</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div> </div> </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>