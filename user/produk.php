<?php
session_start();
include('../db.php'); 
$page = "produk";

// --- SEMUA LOGIKA PHP TETAP SAMA, TIDAK DIUBAH ---

// Pastikan pengguna sudah login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$nama_user = $_SESSION['user'];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$nama_user'");
$row_user = mysqli_fetch_array($kue_user);
$userId = $row_user['id_user'];

// Ambil kategori dari database
$sql_kategori = "SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($kon, $sql_kategori);
$categories = [];
while ($row = mysqli_fetch_assoc($result_kategori)) {
    $categories[] = $row;
}

// Ambil semua produk yang bukan item bonus promo
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
        'price' => $row['harga'], // Menggunakan harga_produk sesuai skema DB
        'stock' => $row['stok'],
        'image' => $row['gambar'],
        'description' => $row['deskripsi'] 
    ];
}

$filteredProducts = $products;

// Logika filter Kategori
$selectedCategory = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category'])) {
    $selectedCategory = $_POST['category'];
    if (!empty($selectedCategory)) {
        $filteredProducts = array_filter($products, function($product) use ($selectedCategory) {
            return $product['category'] === $selectedCategory;
        });
    }
}

// Logika Wishlist
if (isset($_POST['add_to_wishlist'])) {
    $productId = $_POST['product_id'];
    
    $checkSql = "SELECT * FROM wishlist WHERE id_user = ? AND id_produk = ?";
    $checkStmt = $kon->prepare($checkSql);
    $checkStmt->bind_param("ii", $userId, $productId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows == 0) {
        $insertSql = "INSERT INTO wishlist (id_user, id_produk) VALUES (?, ?)";
        $insertStmt = $kon->prepare($insertSql);
        $insertStmt->bind_param("ii", $userId, $productId);
        $insertStmt->execute();
        $insertStmt->close();
    }
    $checkStmt->close();
    
    header("Location: produk.php?wishlist_success=1&category=" . urlencode($selectedCategory));
    exit();
}

$wishlistSuccess = isset($_GET['wishlist_success']);
$cartSuccess = isset($_GET['cart_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Produk Kami</title>
    
    <?php include 'aset.php'; ?>

    <style>
        .page-header {
            padding: 2rem 0;
            margin-bottom: 2rem;
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
            background-color: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
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
        .product-card .card-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .product-card .card-text {
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }
        .product-card .btn-add-to-cart {
            margin-top: auto; /* Mendorong tombol ke bawah */
            width: 100%;
            background-color: #fff;
            color: #0d6efd;
            border: 1px solid #0d6efd;
            font-weight: 600;
        }
        .product-card .btn-add-to-cart:hover {
            background-color: #0d6efd;
            color: #fff;
        }
        .filter-form .form-select {
            max-width: 200px;
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
                    <div class="pagetitle">
                      <h1><i class="bi bi-grid"></i>&nbsp;Produk</h1>
                      <nav>
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                          <li class="breadcrumb-item active">PRODUK</li>
                        </ol>
                      </nav>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <form method="POST" class="filter-form d-inline-block">
                        <select name="category" class="form-select" onchange="this.form.submit()">
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
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                        <button type="submit" name="add_to_wishlist" class="btn btn-wishlist">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['name']); ?></h5>
                                    <p class="card-text">Rp <?= number_format($product['price'], 0, ',', '.'); ?></p>
                                    
                                    <?php if ($product['stock'] > 0): ?>
                                        <form method="POST" action="add_to_cart.php" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                            <button type="submit" name="add_to_cart" class="btn btn-add-to-cart">Add to Cart</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary w-100" disabled>Stok Habis</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            Tidak ada produk yang ditemukan untuk kategori yang dipilih.
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