<?php
session_start();
require "../db.php";
$page = "dashboard";

if (!isset($_SESSION["user"])) {
    header("Location: ../login.php");
}

// GET ID FROM USER
$user = $_SESSION["user"];
$kue_user = mysqli_query($kon, "SELECT * FROM user WHERE nama = '$user'");
$row_user = mysqli_fetch_array($kue_user);

$cartSuccess = isset($_GET['cart_success']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Beranda CasaLuxe</title>
    <link href="../assets/img/LOGOCASALUXE2.png" rel="icon">
    <link href="../assets/img/LOGOCASALUXE2.png" rel="apple-touch-icon">
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="../assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <?php require "atas.php"; ?>
    <?php require "menu.php"; ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        
        :root {
            --casaluxe-primary: #EFAA31;
            --casaluxe-dark: #2D3A3A;
            --casaluxe-light-bg: #F8F7F1;
            --casaluxe-green : #1a877e;
        }
        
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif !important;
            color: #2D3A3A !important;
        }

        .main-content {
            padding: 1.5rem;
        }

        #featuredProductCarousel {
            height: 350px;
            border-radius: 0.5rem;
            overflow: hidden;
            background-color: #e9ecef;
        }

        #featuredProductCarousel .carousel-inner,
        #featuredProductCarousel .carousel-item {
            height: 100%;
        }

        #featuredProductCarousel .featured-image {
            width: 100%;
            height: 100%;
            object-fit: contain; /* Menggunakan 'contain' sesuai kode terakhir Anda */
            object-position: center;
        }

        .product-name-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%);
            color: white;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }

        .product-card {
            border-radius: 0.5rem;
            transition: transform 0.2s ease-in-out;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .product-card .card-img-top {
            height: 13rem;
            object-fit: cover;
        }

        .product-card .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .btn-casaluxe-primary {
            background-color: var(--casaluxe-green);
            color: #fff;
            border-color: var(--casaluxe-green);
        }
        .btn-casaluxe-primary:hover {
            background-color: #1a877e;
            border-color: #1a877e;
            color: #fff;
        }
        .btn-casaluxe-primaryy {
            background-color: var(--casaluxe-primary);
            color: #fff;
            border-color: var(--casaluxe-primary);
        }
        .btn-casaluxe-primaryy:hover {
            background-color:#ffa811;
            border-color: #ffa811;
            color: #fff;
        }
        .btn-outline-casaluxe-primary {
            border: 1px solid var(--casaluxe-primary);
            color: var(--casaluxe-primary);
        }
        .btn-outline-casaluxe-primary:hover {
            background-color: var(--casaluxe-primary);
            color: #fff;
        }
        .breadcrumb {
        font-size: 14px;
        font-family: "Andika", sans-serif;
        color: #EFAA31 !important;
        font-weight: 600;
        }
        .card-text {
            color: #ff771d;
        }
        .breadcrumb a {
        color: #EFAA31 !important;
        transition: 0.3s;
        }

        .breadcrumb a:hover {
        color: #EFAA31 !important;
        }

        .breadcrumb .breadcrumb-item::before {
        color: #EFAA31 !important;
        }

        .breadcrumb .active {
        color: #ff771d !important;
        font-weight: 800;
        }
    </style>

    <main id="main" class="main">
        <div class="main-content">
            <div class="mb-4">
                <h1 class="fs-4 fw-bold mb-2" style="color: var(--casaluxe-dark);"><i class="bi bi-grid me-2"></i>&nbsp; BERANDA</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none" style="color: var(--casaluxe-dark);">HOME</a></li>
                        <li class="breadcrumb-item active" aria-current="page" style="color: #6c757d;">BERANDA</li>
                    </ol>
                </nav>
            </div>

            <?php if ($cartSuccess): ?>
                <div class="alert alert-success">Produk berhasil ditambahkan ke keranjang!</div>
            <?php endif; ?>
            
            <section class="row g-4 mb-4"> 
                <div class="col-lg-8">
                    <div id="featuredProductCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <a href="#" class="text-decoration-none">
                                    <img src="../assets/img/BARANG A.png" class="d-block featured-image" alt="Meja Kantor">
                                    <div class="product-name-overlay">Kursi Sofa Pink Empuk</div>
                                </a>
                            </div>
                            <div class="carousel-item">
                                <a href="#" class="text-decoration-none">
                                    <img src="../assets/img/BARANG B.png" class="d-block featured-image" alt="Kursi Lounge">
                                    <div class="product-name-overlay">Kursi Kantor Pink</div>
                                </a>
                            </div>
                            <div class="carousel-item">
                                <a href="#" class="text-decoration-none">
                                    <img src="../assets/img/BARANG C.png" class="d-block featured-image" alt="Bangku Ottoman">
                                    <div class="product-name-overlay">Kursi Kantor Hitam</div>
                                </a>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#featuredProductCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#featuredProductCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>

                <div class="col-lg-4 d-flex align-items-stretch"> 
                    <div class="card shadow-sm p-3 text-center d-flex flex-column justify-content-center align-items-center w-100">
                        <img src="../assets/img/BARANG B.png" alt="Product Tersedia" class="img-fluid rounded mb-3" style="max-height: 200px; object-fit: contain;">
                        <button class="btn btn-casaluxe-primaryy fw-bold py-2 px-4 rounded-lg shadow-sm">TERSEDIA SEKARANG</button>
                    </div>
                </div>
            </section>

            <section class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="fs-5 fw-bold" style="color: var(--casaluxe-dark);">ALL PRODUCT</h2>
                    <a href="produk.php" class="text-decoration-none fw-semibold d-flex align-items-center" style="color: var(--casaluxe-primary);">
                        Produk Lainnya <i class="bi bi-chevron-right ms-1 fs-6"></i>
                    </a>
                </div>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
                    <?php
                        $query = "SELECT p.id_produk, p.nama_produk, p.harga, p.stok, k.nama_kategori, p.gambar 
                                  FROM produk p 
                                  JOIN kategori k ON p.id_kategori = k.id_kategori 
                                  LIMIT 4";
                        $result = mysqli_query($kon, $query);

                        while ($product = mysqli_fetch_assoc($result)) {
                            echo '<div class="col">';
                            echo '  <div class="card shadow-sm overflow-hidden product-card">'; 
                            echo '      <a href="detail_produk.php?product_id=' . urlencode($product['id_produk']) . '">';
                            echo '          <img src="../uploads/' . htmlspecialchars($product['gambar']) . '" class="card-img-top" alt="' . htmlspecialchars($product['nama_produk']) . '">';
                            echo '      </a>';
                            echo '      <div class="card-body p-3">'; 
                            echo '        <div>';
                            echo '            <h5 class="card-title fs-6 fw-bold mb-1">' . htmlspecialchars($product['nama_produk']) . '</h5>';
                            echo '            <p class="card-text mb-1 small text-muted">Kategori: ' . htmlspecialchars($product['nama_kategori']) . '</p>';
                            echo '            <p class="card-text fw-semibold">Rp ' . number_format($product['harga'], 0, ',', '.') . '</p>';
                            echo '        </div>';
                            echo '        <div class="d-flex gap-2 mt-auto pt-3">';
                            
                            if ($product['stok'] > 0) {
                    ?>
                                <form method="POST" action="checkout.php">
                                    <input type="hidden" name="product_id" value="<?= $product['id_produk']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" name="buy_now" class="btn btn-casaluxe-primary btn-sm fw-semibold w-100">Beli Sekarang</button>
                                </form>
                                <form method="POST" action="add_to_cart.php">
                                    <input type="hidden" name="product_id" value="<?= $product['id_produk']; ?>">
                                    <button type="submit" name="add_to_cart" class="btn btn-outline-casaluxe-primary btn-sm fw-semibold w-100">Masuk Keranjang</button>
                                </form>
                    <?php
                            } else {
                    ?>
                                <button class="btn btn-secondary w-100" disabled>Stok Habis</button>
                    <?php
                            }
                            
                            echo '        </div>'; // End d-flex
                            echo '      </div>'; // End card-body
                            echo '  </div>'; // End card
                            echo '</div>'; // End col
                        }
                    ?>
                </div>
            </section>
        </div>
    </main>
    
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>
</html>