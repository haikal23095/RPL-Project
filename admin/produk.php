<?php
session_start();
include('../db.php'); 
$page = "produk";

if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit;
}

// Pastikan kategori sudah dimuat di session
if (!isset($_SESSION['categories'])) {
    $_SESSION['categories'] = [];

    $sql = "SELECT id_kategori, nama_kategori FROM kategori";
    $result = mysqli_query($kon, $sql);

    while ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['categories'][$row['id_kategori']] = $row['nama_kategori'];
    }
}

// Ambil produk dari database setiap kali halaman dimuat
$sql = "SELECT p.*, k.nama_kategori, k.id_kategori FROM produk p JOIN kategori k ON p.id_kategori = k.id_kategori"; // Ambil produk dari database
$result = mysqli_query($kon, $sql);
if (!$result) {
    die("Query gagal: " . mysqli_error($kon));
}
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = [
        'id' => $row['id_produk'],
        'name' => $row['nama_produk'],
        'category' => $row['nama_kategori'],
        'category_id' => $row['id_kategori'],
        'price' => $row['harga'],
        'stock' => $row['stok'],
        'image' => $row['gambar'],
        'description' => $row['deskripsi']
    ];
}

// Tambahkan debugging
if (empty($products)) {
    error_log("Tidak ada produk yang ditemukan di database");
}

// Simpan produk dalam session
$_SESSION['products'] = $products;

// Inisialisasi filteredProducts dengan semua produk
$filteredProducts = $products;

// Pencarian produk berdasarkan query
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = strtolower($_GET['query']);
    $filteredProducts = array_filter($filteredProducts, function($product) use ($query) {
        return strpos(strtolower($product['name']), $query) !== false;
    });
}

// Filter produk berdasarkan kategori dan harga
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter_category'])) {
    $selectedCategory = $_POST['category'] ?? '';
    $selectedPriceOrder = $_POST['price'] ?? '';

    // Filter kategori
    if (!empty($selectedCategory)) {
        $filteredProducts = array_filter($filteredProducts, function($product) use ($selectedCategory) {
            return $product['category'] === $selectedCategory;
        });
    }

    // Urutkan produk berdasarkan harga
    if ($selectedPriceOrder === 'asc') {
        usort($filteredProducts, function($a, $b) {
            return $a['price'] - $b['price'];
        });
    } elseif ($selectedPriceOrder === 'desc') {
        usort($filteredProducts, function($a, $b) {
            return $b['price'] - $a['price'];
        });
    }
}

$cart = $_SESSION['cart'] ?? [];
$wishlist = $_SESSION['wishlist'] ?? [];

// Cek notifikasi sukses atau error
$deleteSuccess = isset($_GET['delete_success']); // Cek apakah ada notifikasi sukses
$deleteError = isset($_GET['delete_error']); // Cek apakah ada notifikasi error
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Andika:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #FAF8F4;
            font-family: 'Poppins', sans-serif;
        }
        .btn-sukses {
            background: linear-gradient(to right, #EFAA31, #FF8A0D);
            border: none;
            font-weight: 600;
            color: #fff;
            border-radius: 10px;
            padding: 10px 20px;
        }
        .btn-sukses:hover {
            background: linear-gradient(to right, #FF8A0D, #EFAA31);
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease-in-out;
            padding: 16px;
        }
        .card:hover {
            transform: translateY(-4px);
        }
        .card-title {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 0;
        }
        .harga-text {
            color: #D9530B;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 0;
        }
        .btn-outline-dark {
            border-radius: 8px;
            padding: 0;
            font-size: 10px !important;
            font-weight: bold !important;
            width: 100px;
            height: 46px;
            position: relative;
            display: inline-block;
            text-decoration: none;
            font-family: 'Andika', sans-serif !important;
            border: 1px solid #000;
            color: #000;
            background: transparent;
        }
        .btn-outline-dark::before {
            content: "Detail Produk";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            white-space: nowrap;
            font-size: 10px !important;
            font-weight: bold !important;
            font-family: 'Andika', sans-serif !important;
        }
        .btn-hapus {
            background-color: #763D2D !important;
            color: #fff !important;
            border-radius: 8px;
            padding: 0;
            font-size: 10px !important;
            font-weight: bold !important;
            border: none;
            width: 100px;
            height: 46px;
            position: relative;
            display: inline-block;
            font-family: 'Andika', sans-serif !important;
        }
        .btn-hapus::before {
            content: "Hapus Produk";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            white-space: nowrap;
            font-size: 10px !important;
            font-weight: bold !important;
            font-family: 'Andika', sans-serif !important;
            color: #fff;
        }
        .card-img-top {
            border-radius: 16px;
            object-fit: contain;
            width: 100%;
            height: 170px;
            margin-bottom: 12px;
        }
        .card-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }
        .product-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.25rem;
        }
    </style>

    <?php include 'aset.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <script>
    function confirmDelete(productId) {
        if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'hapus_produk.php';
            
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'product_id';
            input.value = productId;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function confirmUpdate() {
        return confirm('Apakah Anda yakin ingin mengupdate produk ini?');
    }
    </script>
</head>
<body>
    <div class="wrapper">
        <!-- HEADER -->
        <?php require "atas.php"; ?>

        <!-- SIDEBAR -->
        <?php require "menu.php"; ?>
    </div>

    <main id="main" class="main">
        <div class="pagetitle d-flex align-items-center justify-content-start gap-3">
            <div>
                <h1><i class="fas fa-box"></i>&nbsp; DATA PRODUK</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                        <li class="breadcrumb-item active">DATA PRODUK</li>
                    </ol>
                </nav>
            </div>
        </div>
        <a href="tambah_produk.php" class="btn btn-sukses">+ Tambah Produk</a>
        <!-- Tampilkan Produk -->
        <div class="row mt-4 g-3">
            <?php if (count($filteredProducts) > 0): ?>
                <?php foreach ($filteredProducts as $product): ?>
                    <div class="col-md-3">
                        <div class="card">
                            <img src="../uploads/<?= htmlspecialchars($product['image']); ?>" class="card-img-top" alt="Gambar Produk">
                            <div class="product-info">
                                <h5 class="card-title text-dark"><?= htmlspecialchars($product['name']); ?></h5>
                                <p class="harga-text">IDR. <?= number_format($product['price'], 0, ',', '.'); ?></p>
                            </div>
                            <div class="card-buttons">
                                <a href="detail_produk.php?product_id=<?= urlencode($product['id']); ?>" class="btn btn-outline-dark"></a>
                                <button type="button" onclick="confirmDelete(<?= htmlspecialchars($product['id']); ?>)" class="btn btn-hapus"></button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-md-12">
                    <div class="alert alert-warning text-center">
                        Tidak ada produk ditemukan untuk kategori atau pencarian yang dipilih.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main><!-- End #main -->

<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Vendor JS Files -->
<script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../assets/vendor/chart.js/chart. umd.js"></script>
<script src="../assets/vendor/echarts/echarts.min.js"></script>
<script src="../assets/vendor/quill/quill.min.js"></script>
<script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="../assets/vendor/tinymce/tinymce.min.js"></script>
<script src="../assets/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
<script src="../assets/js/main.js"></script>
</body>
</html>