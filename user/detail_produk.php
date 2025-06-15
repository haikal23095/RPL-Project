<?php
session_start();
require_once '../db.php';

// --- LOGIKA PHP TETAP SAMA ---

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

// Menggunakan 'id' sesuai dengan link dari halaman produk
if (!isset($_GET['product_id']) || !is_numeric($_GET['product_id'])) {
    header("Location: produk.php");
    exit();
}

$productId = intval($_GET['product_id']);

$stmt = $kon->prepare("
    SELECT p.*, k.nama_kategori 
    FROM produk p
    LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
    WHERE p.id_produk = ?
");
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>
        alert('Produk tidak ditemukan');
        window.location.href = 'produk.php';
    </script>";
    exit();
}

$product = $result->fetch_assoc();

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

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail: <?= htmlspecialchars($product['nama_produk']) ?></title>
    
    <?php include 'aset.php'; ?>
    <style>
        body { background-color: #f8f9fa; }
        .product-detail-card {
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }
        .main-product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 1rem 0 0 1rem;
        }
        .detail-content {
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .product-category {
            font-size: 0.9rem;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
        }
        .product-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fd7e14;
        }
        .product-title {
            font-weight: 700;
            font-size: 2.2rem;
            color: #212529;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
        }
        .product-description {
            color: #495057;
            margin-bottom: 1rem;
            flex-grow: 1; 
        }
        .product-stock {
            font-size: 0.9rem;
            font-weight: 500;
            color: #6c757d;
            margin-bottom: 1rem;
        }
        .btn-action {
            font-weight: 600;
            padding: 0.8rem 1rem;
            border-radius: .5rem;
            width: 100%;
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
        }
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .btn-buy-now {
            background-color: #1abc9c;
            border-color: #1abc9c;
            color: #fff;
        }
        .btn-add-to-cart {
            background-color: #f39c12;
            border-color: #f39c12;
            color: #fff;
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .btn-back, .btn-wishlist {
            font-size: 1.5rem;
            color: #333;
            background: none;
            border: none;
            padding: 0.25rem 0.5rem;
        }
        .btn-wishlist:hover {
            color: #e74c3c; 
        }
         @media (max-width: 991px) {
            .main-product-image {
                 border-radius: 1rem 1rem 0 0;
            }
        }
    </style>
</head>
<body>
    <?php require "atas.php"; ?>
    <?php require "menu.php"; ?>

    <main id="main" class="main">
        <div class="container my-5">
            <div class="card product-detail-card border-0">
                <div class="row g-0">
                    <div class="col-lg-6">
                        <img src="../uploads/<?= htmlspecialchars($product['gambar']) ?>" alt="<?= htmlspecialchars($product['nama_produk']) ?>" class="main-product-image">
                    </div>

                    <div class="col-lg-6">
                        <div class="detail-content">
                            <div class="header-actions">
                                <a href="produk.php" class="btn-back"><i class="bi bi-arrow-left"></i></a>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="product_id" value="<?= $product['id_produk']; ?>">
                                    <button type="submit" name="add_to_wishlist" class="btn btn-wishlist">
                                        <i class="bi bi-heart"></i>
                                    </button>   
                                </form>
                            </div>

                            <div>
                                <div class="product-category mb-2"><?= htmlspecialchars($product['nama_kategori'] ?? 'Tidak Berkategori') ?></div>
                                <div class="product-price">Rp. <?= number_format($product['harga'], 0, ',', '.') ?></div>
                                <h1 class="product-title"><?= htmlspecialchars($product['nama_produk']) ?></h1>
                                <p class="product-description"><?= nl2br(htmlspecialchars($product['deskripsi'])) ?></p>
                            </div>

                            <div class="mt-auto">
                                <div class="product-stock">Sisa Stok: <?= htmlspecialchars($product['stok']) ?></div>
                                
                                <?php if ($wishlistSuccess): ?>
                                    <div class="alert alert-success">Produk berhasil ditambahkan ke wishlist!</div>
                                <?php endif; ?>

                                <?php if ($product['stok'] > 0): ?>
                                    <form action="add_to_cart.php" method="POST" class="d-grid">
                                        <input type="hidden" name="product_id" value="<?= $product['id_produk']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" name="add_to_cart" class="btn btn-add-to-cart btn-action">
                                            <i class="bi bi-cart-plus-fill"></i> Masuk Keranjang
                                        </button>
                                    </form>
                                    <form action="checkout.php" method="POST" class="d-grid mt-2">
                                        <input type="hidden" name="product_id" value="<?= $product['id_produk']; ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" name="buy_now" class="btn btn-buy-now btn-action">
                                            Beli Sekarang
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-action" disabled>Stok Habis</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>

<?php
$stmt->close();
$kon->close();
?>