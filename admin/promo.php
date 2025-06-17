<?php
session_start();
require "../db.php";
$page = "promo";

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to generate a promo code
function generatePromoCode($length = 5) {
    return strtoupper(substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length));
}

// Handle promo code generation
if (isset($_POST['generate_promo'])) {
    $promo_code = generatePromoCode();
    $discount_type = $_POST['discount_type'];
    $discount_value = floatval($_POST['discount_value']);
    $usage_limit = intval($_POST['usage_limit']);

    // Validate input
    if ($discount_value <= 0 || $usage_limit <= 0) {
        echo "<script>alert('Invalid discount value or usage limit.');</script>";
    } else {
        $stmt = $kon->prepare("INSERT INTO promo (code, discount_type, discount_value, usage_limit) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $promo_code, $discount_type, $discount_value, $usage_limit);
        
        if ($stmt->execute()) {
            echo "<script>alert('Promo code successfully created: $promo_code');</script>";
        } else {
            echo "<script>alert('Failed to save promo code: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Kode Promo</title>
    <!-- <link rel="stylesheet" href="assets/css/styles.css"> -->

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">
    
    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <!--     -->
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
        header{
            background-color: #F8F7F1 !important;
        }
        .sidebar {
            background-color: #F8F7F1 !important;
        }
        h5 {
            font-weight: bold !important;
            padding-top: 0px !important;
            margin: 0px!important;
            margin-top: 0px !important;
            font-size: 18px !important;
        }
    </style>
    <?php include 'atas.php'; ?>
    <?php include 'menu.php'; ?>

    <main id="main" class="main">
        <div class="pagetitle">
            <h1><i class="bi bi-tags"></i>&nbsp; KODE PROMO</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">HOME</a></li>
                    <li class="breadcrumb-item active"> KODE PROMO</li>
                </ol>
            </nav>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mt-5">Generate Kode Promo</h5>
                            <form method="post">
                                <div class="mb-3">
                                    <label for="discount_type" class="form-label">Tipe Diskon</label>
                                    <select class="form-select" id="discount_type" name="discount_type" required>
                                        <option value="fixed">Jumlah Tetap</option>
                                        <option value="percentage">Persentase</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="discount_value" class="form-label">Nilai Diskon</label>
                                    <input type="number" class="form-control" id="discount_value" name="discount_value" required>
                                </div>
                                <div class="mb-3">
                                    <label for="usage_limit" class="form-label">Batas Penggunaan</label>
                                    <input type="number" class="form-control" id="usage_limit" name="usage_limit" required>
                                </div>
                                <button type="submit" name="generate_promo" class="btn btn-generate">GENERATE KODE PROMO</button>
                            </form>

                            <h5 class="card-title mt-5">Kode Promo yang Tersedia</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Kode Promo</th>
                                        <th>Tipe Diskon</th>
                                        <th>Nilai Diskon</th>
                                        <th>Batas Penggunaan</th>
                                        <th>Waktu Penggunaan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $promo_query = "SELECT * FROM promo";
                                    $promo_result = $kon->query($promo_query);
                                    while ($promo_row = $promo_result->fetch_assoc()) {
                                        $status = $promo_row['times_used'] >= $promo_row['usage_limit'] ? 'Expired' : 'Available';
                                        $discount_value = $promo_row['discount_type'] == 'fixed' ? 'Rp. ' . number_format($promo_row['discount_value'], 0, ',', '.') : $promo_row['discount_value'] . '%';

                                        echo "<tr>";
                                        echo "<td>{$promo_row['code']}</td>";
                                        echo "<td>" . ($promo_row['discount_type'] == 'fixed' ? 'Fixed Amount' : 'Percentage') . "</td>";
                                        echo "<td>$discount_value</td>";
                                        echo "<td>{$promo_row['usage_limit']}</td>";
                                        echo "<td>{$promo_row['times_used']}</td>";
                                        echo "<td>$status</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="../assets/vendor/chart.js/chart.umd.js"></script>
  <script src="../assets/vendor/echarts/echarts.min.js"></script>
  <script src="../assets/vendor/quill/quill.min.js"></script>
  <script src="../assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="../assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="../assets/vendor/php-email-form/validate.js"></script>

  <!-- Core Bootstrap JS -->
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>
</body>
</html>
