<?php
session_start();
include('../db.php');
$page = "info";
// Function to handle file upload
function uploadFile($file) {
    $target_dir = __DIR__ . "../uploads/"; // Pastikan path absolut
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Buat folder jika belum ada
    }

    $target_file = $target_dir . basename($file["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validasi gambar
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return "File is not an image.";
    }

    // Validasi ukuran
    if ($file["size"] > 500000) {
        return "Sorry, your file is too large.";
    }

    // Validasi format file
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        return "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
    }

    // Unggah file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        // Return path yang dapat diakses dari URL
        return "../uploads/" . basename($file["name"]);
    } else {
        return "Sorry, there was an error uploading your file.";
    }
}

// Ambil data produk untuk dropdown item bonus
$productQuery = "SELECT id_produk, nama_produk FROM produk";
$productResult = mysqli_query($kon, $productQuery);
$products = [];
if ($productResult) {
    $products = mysqli_fetch_all($productResult, MYSQLI_ASSOC);
}

// Fetch all promos
$sql = "SELECT * FROM informasipromo ORDER BY created_at DESC";
$result = mysqli_query($kon, $sql);
$promos = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produk = $_POST['bonus_item'] ?? 0;
    $title = $_POST['title'];
    $description = $_POST['description'];
    $promo_type = $_POST['promo_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $discount_percentage = $_POST['discount_percentage'] ?? null;
    $bonus_item = $_POST['bonus_item'] ?? null;

    // Handle file upload
    // $photo_url = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $imageName = basename($_FILES['photo']['name']);
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
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePath)) {
                    $imageUploaded = true;
                } else {
                    $imageUploaded = false;
                    $errorMessage = "Gagal mengunggah gambar.";
                }
            } else {
                $imageUploaded = false;
                $errorMessage = "Format file gambar tidak valid. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
            }
        } else {
            $imageUploaded = false;
            $errorMessage = "Tidak ada gambar yang dipilih atau terjadi kesalahan saat mengunggah.";
        }
    // if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    //     $photo_url = uploadFile($_FILES['photo']);
    //     if (strpos($photo_url, 'Sorry') === 0) {
    //         $upload_error = $photo_url;
    //         $photo_url = '';
    //     }
    // }

    $sql = "INSERT INTO informasipromo (id_produk, title, description, photo_url, promo_type, start_date, end_date, discount_percentage, bonus_item) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($kon, $sql);
    mysqli_stmt_bind_param($stmt, "issssssss", $id_produk, $title, $description, $imageName, $promo_type, $start_date, $end_date, $discount_percentage, $bonus_item);
    mysqli_stmt_execute($stmt);

    header("Location: informasipromo.php");
    exit();
}
?>


<?php
// Place this near the top of your admin_promos.php file, after the session_start() call
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Promo - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php include 'aset.php'; ?>
</head>
<body>
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
         /* Styling untuk tombol kembali (baru) */
        .standalone-back-button-container {
            margin-bottom: 15px; /* Jarak bawah dari tombol kembali */
            padding-left: 0px; /* Sesuaikan padding agar sejajar dengan konten */
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
    <div class="wrapper">
        <?php require "atas.php"; ?>
        <?php require "menu.php"; ?>
    </div>

    <main id="main" class="main">
        <div class="standalone-back-button-container">
            <a href="informasipromo.php" class="standalone-back-button">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>
        </div>
        <div class="pagetitle">
            <h1><i class="bi bi-megaphone"></i>&nbsp; TAMBAH PROMO</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                    <li class="breadcrumb-item"><a href="informasipromo.php">INFORMASI PROMO</a></li>
                    <li class="breadcrumb-item active">TAMBAH PROMO</li>
                </ol>
            </nav>
        </div>
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Tambah Promo Baru</h5>
                            <?php if(isset($upload_error)): ?>
                                <div class="alert alert-danger"><?php echo $upload_error; ?></div>
                            <?php endif; ?>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Promo</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="photo" class="form-label">Foto Promo</label>
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                </div>
                                <div class="mb-3">
                                    <label for="promo_type" class="form-label">Jenis Promo</label>
                                    <select class="form-select" id="promo_type" name="promo_type" required>
                                        <option value="discount">Diskon</option>
                                        <option value="bonus">Bonus</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">Tanggal Berakhir</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                                <div class="mb-3" id="discount_field">
                                    <label for="discount_percentage" class="form-label">Persentase Diskon</label>
                                    <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" min="0" max="100">
                                </div>
                                <div class="mb-3" id="bonus_field" style="display:none;">
                                    <label for="bonus_item" class="form-label">Item Bonus</label>
                                    <select class="form-select" id="bonus_item" name="bonus_item">
                                        <option value="">-- Pilih Item Bonus --</option>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?= htmlspecialchars($product['id_produk']); ?>">
                                                <?= htmlspecialchars($product['nama_produk']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <a href=""></a><button type="submit" class="btn btn-tambah">Tambah Promo</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
       document.getElementById('promo_type').addEventListener('change', function() {
            var discountField = document.getElementById('discount_field');
            var bonusField = document.getElementById('bonus_field');
            if (this.value === 'discount') {
                discountField.style.display = 'block';
                bonusField.style.display = 'none';
            } else {
                discountField.style.display = 'none';
                bonusField.style.display = 'block';
            }
        });
    </script>
</body>
</html>
