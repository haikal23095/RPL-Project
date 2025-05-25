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
    <title>Daftar Produk - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }

        .wrapper {
            display: block;
        }

        .content {
            flex: 1;
            padding-left: 30px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: transparent;
        }

        .card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 166, 0, 0.5);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .card-img-top {
            border-radius: 15px 15px 0 0;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .card:hover .card-img-top {
            transform: scale(1.05);
        }

        .card-body {
            background: rgba(0, 0, 0, 0.4);
            padding: 1.5rem;
            border-radius: 0 0 15px 15px;
        }

        .card-title {
            color: #ffa500 !important;
        }
        .card-text {
            color: #fff;
            margin-bottom: 0.8rem;
        }

        .card-text strong {
            color: #ffa500;
        }

        .btn {
            border: none;
            padding: 8px 15px;
            margin: 5px 0;
            transition: all 0.3s ease;
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        .btn-info {
            background: rgba(255, 165, 0, 0.8);
            color: #000;
        }

        .btn-info:hover {
            background: #ffa500;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: rgba(220, 53, 69, 0.8);
            color: #fff;
        }

        .btn-danger:hover {
            background: #dc3545;
            transform: translateY(-2px);
        }

        .btn-sukses {
            background: linear-gradient(45deg, #ff6b00, #ffa500);
            color: white !important;
            padding: 10px 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-sukses:hover {
            background: linear-gradient(45deg, #ffa500, #ff6b00);
            transform: translateY(-2px);
            border: transparent;
            box-shadow: 0 5px 15px rgba(255, 107, 0, 0.3);
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
        <div class="pagetitle">
            <h1><i class="bi bi-grid"></i>&nbsp; PRODUK</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                    <li class="breadcrumb-item active">PRODUK</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <!-- Tombol Tambah Produk -->
        <div class="mb-3">
            <a href="tambah_produk.php" class="btn btn-sukses w-auto">Tambah Produk</a>
        </div>

        <!-- Tampilkan Produk -->
        <div class="row g-3">
            <?php if (count($filteredProducts) > 0): ?>
                <?php foreach ($filteredProducts as $product): ?>
                    <div class="col-md-3">
                        <div class="card">
                            <img src="../uploads/<?= htmlspecialchars($product['image']); ?>" class="card-img-top" alt="Gambar Produk">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?= htmlspecialchars($product['category']); ?></p>
                                <p class="card-text"><strong>Harga: </strong>Rp <?= number_format($product['price'], 0, ',', '.'); ?></p>
                                <p class="card-text"><strong>Stok: </strong><?= htmlspecialchars($product['stock']); ?> pcs</p>
                                <!-- Detail Produk dan Edit Produk -->
                                <a href="detail_produk.php?product_id=<?= urlencode($product['id']); ?>" class="btn btn-info mb-2">Detail Produk</a>
                                <!-- Tombol Hapus Produk -->
                                <button type="button" onclick="confirmDelete(<?= htmlspecialchars($product['id']); ?>)" class="btn btn-danger">Hapus Produk</button>
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