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

// Ambil kategori untuk modal
$categories = [];
$categoryQuery = $kon->query("SELECT id_kategori, nama_kategori FROM kategori");
while ($row = $categoryQuery->fetch_assoc()) {
    $categories[] = $row;
}

// Proses penambahan produk jika form di-submit via AJAX
if (isset($_POST['add_product_ajax'])) {
    // Validasi input
    $requiredFields = ['name', 'category', 'price', 'description', 'stock']; 
    $missingFields = [];

    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            $missingFields[] = $field;
        }
    }

    if (!empty($missingFields)) {
        echo json_encode(['success' => false, 'message' => 'Harap isi semua field yang diperlukan.']);
        exit;
    }

    // Proses upload gambar
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['image']['name']);
        $targetDir = "../uploads/";
        $targetFilePath = $targetDir . $imageName;

        // Hanya izinkan beberapa ekstensi file
        $allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        if (in_array($fileType, $allowedFileTypes)) {
            // Pastikan nama file unik
            $imageName = uniqid() . '_' . $imageName;
            $targetFilePath = $targetDir . $imageName;

            // Upload file ke direktori target
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $imageUploaded = true;
            } else {
                $imageUploaded = false;
                echo json_encode(['success' => false, 'message' => 'Gagal mengunggah gambar.']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Format file gambar tidak valid. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Tidak ada gambar yang dipilih atau terjadi kesalahan saat mengunggah.']);
        exit;
    }

    // Jika gambar berhasil diunggah, lanjutkan dengan validasi kategori
    if ($imageUploaded) {
        $nama_produk = htmlspecialchars($_POST['name']);
        $harga_produk = floatval($_POST['price']);
        $deskripsi_produk = htmlspecialchars($_POST['description']);
        $kategori_produk = intval($_POST['category']);
        $stok_produk = intval($_POST['stock']);

        // Tambahkan validasi kategori sebelum insert
        $check_kategori = "SELECT COUNT(*) as count FROM kategori WHERE id_kategori = ?";
        $stmt_check = $kon->prepare($check_kategori);
        $stmt_check->bind_param("i", $kategori_produk);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();

        if ($row_check['count'] > 0) {
            // Kategori valid, lanjutkan insert
            $query = "INSERT INTO produk (nama_produk, harga, deskripsi, id_kategori, stok, gambar) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $kon->prepare($query);
            $stmt->bind_param("sssiis", $nama_produk, $harga_produk, $deskripsi_produk, $kategori_produk, $stok_produk, $imageName);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Produk berhasil ditambahkan!']);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Kategori produk tidak valid']);
            exit;
        }
    }
}

// Ambil produk dari database setiap kali halaman dimuat
$sql = "SELECT p.*, k.nama_kategori, k.id_kategori FROM produk p JOIN kategori k ON p.id_kategori = k.id_kategori";
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

// Cek notifikasi suksess atau error
$deleteSuccess = isset($_GET['delete_success']);
$deleteError = isset($_GET['delete_error']);
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
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif;
            color: #2D3A3A;
        }
        .sidebar {
            background-color: #F8F7F1 !important;
        }
        header{
            background-color: #F8F7F1 !important;
        }
        .btn-suksess {
            background: linear-gradient(to right, #EFAA31, #FF8A0D);
            border: none;
            font-weight: 600;
            color: #ffffff;
            border-radius: 10px;
            padding: 10px 20px;
        }
        .btn-suksess:hover {
            background: linear-gradient(to right, #FF8A0D, #EFAA31);
        }
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease-in-out;
            padding: 16px;
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .card:hover {
            transform: translateY(-4px);
        }
        .card-title {
            font-size: 10px;
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
            color: #2D3A3A !important;
            border-radius: 8px;
            padding: 0 5px;
            font-size: 11.3px !important;
            font-weight: bold !important;
            width: 100px;
            height: 46px;
            text-decoration: none;
            font-family: 'Andika', sans-serif !important;
            border: 1px solid #2D3A3A !important;
            background-color: transparent;
            display: flex; 
            align-items: center !important; 
            justify-content: center !important;
            transition:  background-color 0.7s ease;
        }

        .btn-outline-dark:hover {
            color: #ffffff !important;
            background-color: #2D3A3A !important;
            border: 1px solid transparent !important;
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

        /* Modal Styles */
        .modal-dialog {
            max-width: 800px;
        }
        .modal-content {
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .modal-header {
            background: linear-gradient(135deg, #ff8c00, #ff6b00);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .btn-tambah-modal {
            background-color: #1A877E;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
        }
        .btn-tambah-modal:hover {
            background-color: #157066;
            color: white;
        }
        .btn-batal-modal {
            background-color: #763D2D;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 600;
        }
        .btn-batal-modal:hover {
            background-color: #5d2f23;
            color: white;
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

    // Function untuk handle form submit dengan AJAX
    function submitProductForm() {
        var formData = new FormData($('#tambahProdukForm')[0]);
        formData.append('add_product_ajax', '1');
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    var result = JSON.parse(response);
                    if (result.success) {
                        alert(result.message);
                        $('#tambahProdukModal').modal('hide');
                        location.reload(); // Refresh halaman untuk menampilkan produk baru
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (e) {
                    console.error('Response parsing error:', e);
                    console.log('Raw response:', response);
                }
            },
            error: function(xhr, status, error) {
                alert('Terjadi kesalahan: ' + error);
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    }

    // Reset form ketika modal ditutup
    $('#tambahProdukModal').on('hidden.bs.modal', function () {
        $('#tambahProdukForm')[0].reset();
    });
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
                <h1><i class="bi bi-box-seam-fill"></i>&nbsp; DATA PRODUK</h1>
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                        <li class="breadcrumb-item active">DATA PRODUK</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        <!-- Tombol Tambah Produk yang akan membuka modal -->
        <button type="button" class="btn-tambah" data-bs-toggle="modal" data-bs-target="#tambahProdukModal">
            <i class="bi bi-plus"></i>&nbsp; TAMBAH PRODUK
        </button>

        <!-- Modal Tambah Produk -->
        <div class="modal fade" id="tambahProdukModal" tabindex="-1" aria-labelledby="tambahProdukModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahProdukModalLabel">
                            Tambah Produk Baru
                        </h5>
                        
                    </div>
                    <div class="modal-body">
                        <form id="tambahProdukForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Kategori</label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="" disabled selected>Pilih Kategori</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id_kategori'] ?>">
                                            <?= htmlspecialchars($category['nama_kategori']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Harga</label>
                                <input type="number" class="form-control" id="price" name="price" required>
                            </div>

                            <div class="mb-3">
                                <label for="stock" class="form-label">Stok</label>
                                <input type="number" class="form-control" id="stock" name="stock" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi Produk</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Unggah Gambar Produk</label>
                                <input type="file" class="form-control" id="image" name="image" required accept="image/*">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-batal-modal text-white" data-bs-dismiss="modal" style="background-color: #763D2D;">Batalkan</button>
                        <button type="button" class="btn btn-tambah-modal text-white" onclick="submitProductForm()" style="background-color: #1A877E;">Tambah Produk</button>
                    </div>
                </div>
            </div>
        </div>

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
                               <a href="detail_produk.php?product_id=<?= urlencode($product['id']); ?>" class="btn btn-outline-dark">Detail Produk</a>
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
<script src="../assets/vendor/chart.js/chart.umd.js"></script>
<script src="../assets/vendor/echarts/echarts.min.js"></script>
<script src="../assets/vendor/quill/quill.min.js"></script>
<script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
<script src="../assets/vendor/tinymce/tinymce.min.js"></script>
<script src="../assets/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
<script src="../assets/js/main.js"></script>
</body>
</html>