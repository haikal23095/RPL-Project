<?php
session_start();
include('../db.php');
$page = "dashboard";

// --- SEMUA LOGIKA PHP TETAP SAMA, TIDAK DIUBAH ---

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$nama_user = $_SESSION['user'];
$kue_user_stmt = $kon->prepare("SELECT * FROM user WHERE nama = ?");
$kue_user_stmt->bind_param("s", $nama_user);
$kue_user_stmt->execute();
$row_user = $kue_user_stmt->get_result()->fetch_assoc();
$userId = $row_user['id_user'];
$kue_user_stmt->close();

$sql_kategori = "SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($kon, $sql_kategori);
$categories = [];
while ($row = mysqli_fetch_assoc($result_kategori)) {
    $categories[] = $row;
}

$sql_produk = "SELECT p.*, k.nama_kategori 
               FROM produk p 
               JOIN kategori k ON p.id_kategori = k.id_kategori
               WHERE p.id_produk NOT IN (SELECT bonus_item FROM informasipromo)";
$result_produk = mysqli_query($kon, $sql_produk);
$products = [];
while ($row = mysqli_fetch_assoc($result_produk)) {
    $products[] = [
        'id' => $row['id_produk'],
        'name' => $row['nama_produk'],
        'category' => $row['nama_kategori'],
        'price' => $row['harga'],
        'stock' => $row['stok'],
        'image' => $row['gambar'],
        'description' => $row['deskripsi']
    ];
}

$filteredProducts = $products;

$selectedCategory = '';
if (isset($_GET['category'])) {
    $selectedCategory = $_GET['category'];
    if (!empty($selectedCategory)) {
        $filteredProducts = array_filter($products, function ($product) use ($selectedCategory) {
            return $product['category'] === $selectedCategory;
        });
    }
}

if (isset($_POST['add_to_wishlist'])) {
    $productId = $_POST['product_id'];
    $currentCategory = $_GET['category'] ?? '';

    $checkSql = "SELECT * FROM wishlist WHERE user_id = ? AND id_produk = ?";
    $checkStmt = $kon->prepare($checkSql);
    $checkStmt->bind_param("ii", $userId, $productId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows == 0) {
        $insertSql = "INSERT INTO wishlist (user_id, id_produk) VALUES (?, ?)";
        $insertStmt = $kon->prepare($insertSql);
        $insertStmt->bind_param("ii", $userId, $productId);
        $insertStmt->execute();
        $insertStmt->close();
    }
    $checkStmt->close();
    
    $redirectUrl = "produk.php?wishlist_success=1";
    if (!empty($currentCategory)) {
        $redirectUrl .= "&category=" . urlencode($currentCategory);
    }
    header("Location: " . $redirectUrl);
    exit();
}

$wishlistSuccess = isset($_GET['wishlist_success']);
$cartSuccess = isset($_GET['cart_success']);

function formatCurrency($number) {
    return 'Rp ' . number_format($number ?? 0, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Produk Kami</title>
    
    <?php include 'aset.php'; ?>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif;
            color: #2D3A3A !important;
        }
        div.product-name{
            font-size: 14px;
        }
        main{
            padding: 40px !important;
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
            font-size: 1.05rem;
            font-weight: 600;
            color: #212529;
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
</head>

<body>
    <?php require "atas.php"; ?>
    <?php require "menu.php"; ?>

    <main id="main" class="main">
        <section class="section">
            <div class="page-header row align-items-center">
                <div class="col-md-8">
                            <div class="standalone-back-button-container">
                    <a href="index.php" class="standalone-back-button">
                        <i class="bi bi-arrow-left"></i>
                        Kembali
                    </a>
                </div>
                    <div class="pagetitle">
                      <h1><i class="bi bi-grid"></i>&nbsp;PRODUK</h1>
                      <nav>
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                          <li class="breadcrumb-item active">PRODUK</li>
                        </ol>
                      </nav>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <form method="POST" id="filter-form" class="filter-form d-inline-block">
                        <select name="category" class="form-select" onchange="document.getElementById('filter-form').submit()">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['nama_kategori']) ?>"
                                    <?= ($selectedCategory == $category['nama_kategori']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
            </div>
            
            <?php if ($wishlistSuccess): ?>
                <div class="alert alert-success">Produk berhasil ditambahkan ke wishlist!</div>
            <?php endif; ?>
            <?php if ($cartSuccess): ?>
                <div class="alert alert-success">Produk berhasil ditambahkan ke keranjang!</div>
            <?php endif; ?>

            <div class="row gy-4">
                <?php if (count($filteredProducts) > 0): ?>
                    <?php foreach ($filteredProducts as $product): ?>
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="card product-card h-100">
                                <div class="card-img-container">
                                    <a href="detail_produk.php?product_id=<?= urlencode($product['id']); ?>">
                                        <img src="../uploads/<?= htmlspecialchars($product['image']); ?>" class="card-img-top product-card-img-top" alt="<?= htmlspecialchars($product['name']); ?>">
                                    </a>
                                    <form method="POST">
                                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                        <button type="submit" name="add_to_wishlist" class="btn btn-wishlist">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="card-body">
                                    <div class="product-info">
                                        <div class="product-name"><?= htmlspecialchars($product['name']); ?></div>
                                        <div class="product-price"><?= formatCurrency($product['price']); ?></div>
                                    </div>
                                    
                                    <div class="button-group">
                                    <?php if ($product['stock'] > 0): ?>
                                        <form method="POST" action="checkout.php" class="w-50">
                                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" name="buy_now" class="btn btn-buy-now w-100">Beli Sekarang</button>
                                        </form>
                                        <form method="POST" action="add_to_cart.php" class="w-50">
                                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                            <button type="submit" name="add_to_cart" class="btn btn-add-to-cart w-100">Masuk Keranjang</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary w-100" disabled>Stok Habis</button>
                                    <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            Tidak ada produk yang ditemukan untuk kategori "<?= htmlspecialchars($selectedCategory) ?>".
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>