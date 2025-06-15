<?php
session_start();
require_once '../db.php';
$page = "wishlist";

// --- SEMUA LOGIKA PHP TETAP SAMA, TIDAK DIUBAH ---

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user = $_SESSION["user"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);
$user_id = $row_user['id_user'];

$sql = "SELECT p.*, w.id_wishlist AS wishlist_id FROM wishlist w 
        JOIN produk p ON w.id_produk = p.id_produk 
        WHERE w.user_id = ?";

$stmt = $kon->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$wishlistItems = $result->fetch_all(MYSQLI_ASSOC);

function checkProductStock($kon, $productId) {
    $stockSql = "SELECT stok FROM produk WHERE id_produk = ?";
    $stockStmt = $kon->prepare($stockSql);
    $stockStmt->bind_param("i", $productId);
    $stockStmt->execute();
    $stockResult = $stockStmt->get_result();
    $product = $stockResult->fetch_assoc();
    return $product['stok'] > 0;
}

if (isset($_GET['remove'])) {
    $wishlistIdToRemove = intval($_GET['remove']);
    $deleteSql = "DELETE FROM wishlist WHERE id_wishlist = ? AND user_id = ?";
    $deleteStmt = $kon->prepare($deleteSql);
    $deleteStmt->bind_param("ii", $wishlistIdToRemove, $user_id);
    $deleteStmt->execute();
    
    header("Location: wishlist.php?removed=1");
    exit();
}

$cartSuccess = isset($_GET['cart_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Wishlist</title>
    
    <?php include 'aset.php'; ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');

        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif;
            color: #2D3A3A !important;
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
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .product-card .card-img-top {
            aspect-ratio: 1 / 1;
            object-fit: cover;
        }
        .product-card .card-body {
            padding: 1rem;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .product-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            flex-grow: 1;
        }
        .product-name {
            font-size: 1.05rem;
            font-weight: 600;
            color: #212529;
            padding-right: 10px;
        }
        .product-price {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fd7e14;
            white-space: nowrap;
        }
        .button-group {
            display: flex;
            gap: 10px;
        }
        .button-group .btn {
        flex: 1;
        font-weight: 600;
        padding: 0.6rem 0.5rem; 
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-add-cart-wishlist {
        background-color: #FFBB34;
        border-color: #FFBB34;
        color: #fff;
    }
    .btn-add-cart-wishlist:hover {
        background-color: #e9a92d; 
        border-color: #e9a92d;
    }
    .btn-remove-wishlist {
        background-color: #763D2D;
        border: 1px solid #763D2D;
        color: #fff;
    }
     .btn-remove-wishlist:hover {
        background-color: #5c2e22; 
        border-color: #5c2e22;
        color: #fff;
    }
    </style>
</head>
<body>

    <?php require "atas.php"; ?>
    <?php require "profil_menu.php"; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-heart-fill"></i>&nbsp; Daftar Keinginan</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                    <li class="breadcrumb-item active">DAFTAR KEINGINAN</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row gy-4">
                <?php if (empty($wishlistItems)): ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center p-5">
                                <i class="bi bi-heart fs-1 text-muted"></i>
                                <h5 class="mt-3">Wishlist Anda Kosong</h5>
                                <p class="text-muted">Simpan produk yang Anda sukai di sini untuk dilihat kembali nanti.</p>
                                <a href="produk.php" class="btn btn-primary">Mulai Belanja</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <?php if ($cartSuccess): ?>
                        <div class="alert alert-success">Produk berhasil ditambahkan ke keranjang!</div>
                    <?php endif; ?>
                    <?php foreach ($wishlistItems as $item): ?>
                        <div class="col-xl-3 col-lg-4 col-md-6">
                             <div class="card product-card">
                                <a href="detail_produk.php?product_id=<?= $item['id_produk']; ?>">
                                    <img src="../uploads/<?= htmlspecialchars($item['gambar']); ?>" class="card-img-top" alt="<?= htmlspecialchars($item['nama_produk']); ?>">
                                </a>
                                <div class="card-body">
                                    <div class="product-info">
                                        <div class="product-name"><?= htmlspecialchars($item['nama_produk']); ?></div>
                                        <div class="product-price">Rp <?= number_format($item['harga'], 0, ',', '.'); ?></div>
                                    </div>
                                    <div class="button-group">
                                        <a href="wishlist.php?remove=<?= $item['wishlist_id']; ?>" class="btn btn-remove-wishlist" onclick="return confirm('Yakin ingin menghapus item ini dari wishlist?');">Hapus</a>
                                        <?php if (checkProductStock($kon, $item['id_produk'])): ?>
                                            <form action="add_to_cart.php" method="POST" class="me-3">
                                                <input type="hidden" name="product_id" value="<?= $item['id_produk']; ?>">
                                                <button type="submit" name="add_to_cart" class="btn btn-add-cart-wishlist btn-sm">
                                                    <i class="bi bi-cart-plus-fill"></i> Masuk Keranjang
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-secondary w-100" disabled>Stok Habis</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>