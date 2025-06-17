<?php
session_start();
$page = "order";
if (!isset($_SESSION["admin"])) {
    header("Location: ../login.php");
    exit;
}

$host = 'localhost';
$dbname = 'casaluxedb';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Filter status jika ada
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Query untuk menampilkan daftar pesanan dengan join tabel user dan produk
$query = "SELECT p.id_pesanan, u.nama AS customer_name, 
                 p.tanggal_pesanan, p.total_harga, p.status_pesanan
          FROM pesanan p
          JOIN user u ON p.id_user = u.id_user";

if (!empty($status_filter)) {
    $query .= " WHERE p.status_pesanan = :status";
}
$query .= " ORDER BY p.tanggal_pesanan DESC";

$stmt = $pdo->prepare($query);

if (!empty($status_filter)) {
    $stmt->bindParam(':status', $status_filter);
}
$stmt->execute();
$pesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
        .main-content {
            padding: 1rem .5rem;
            max-width: 1800px;
            margin: 0 auto;
        }
        
        .page-header {
            margin-bottom: 1.5rem;
        }
        
        .page-title {
            color: #333;
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .breadcrumb-nav {
            background: none;
            padding: 0;
            margin: 0.5rem 0 1.5rem 0;
            font-size: 0.875rem;
        }
        
        .breadcrumb-nav .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }
        
        .breadcrumb-item a {
            color: #6c757d;
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: #f4a460;
            font-weight: 500;
        }
        
        .cancel-button {
            background-color: #f4a460;
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .cancel-button:hover {
            background-color: #e6935a;
            color: white;
        }
        
        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
        
        .filter-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            font-size: 1rem;
        }
        
        .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
        }
        
        .form-select:focus {
            border-color: #f4a460;
            box-shadow: 0 0 0 0.2rem rgba(244, 164, 96, 0.25);
        }
        
        .orders-table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
        
        .table {
            margin: 0;
            font-size: 0.875rem;
        }
        
        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            color: #495057;
            font-weight: 600;
            padding: 1rem 0.75rem;
            text-align: center;
            font-size: 0.875rem;
            letter-spacing: 0.3px;
        }
        
        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .order-id {
            font-weight: 600;
            color: #333;
        }
        
        .customer-name {
            font-weight: 500;
            color: #333;
        }
        
        .order-date {
            color: #6c757d;
        }
        
        .order-total {
            font-weight: 600;
            color: #28a745;
        }
        
        .status-select {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            font-size: 0.8rem;
            min-width: 120px;
            background-color: white;
        }
        
        .status-select:focus {
            border-color: #f4a460;
            box-shadow: 0 0 0 0.2rem rgba(244, 164, 96, 0.25);
        }
        
        .action-icon {
            color: #6c757d;
            font-size: 1.1rem;
            cursor: pointer;
        }
        
        .action-icon:hover {
            color: #f4a460;
        }
        
        .no-data {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 2rem;
        }
        
        .quantity-badge {
            background-color: #e9ecef;
            color: #495057;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
        }
    </style>
    <?php include 'aset.php'; ?>
</head>
<body>
<?php include 'atas.php'; ?>
<?php include 'menu.php'; ?>

<main id="main" class="main">
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="bi bi-grid"></i>
                PESANAN MASUK
            </h1>
            
            <!-- Breadcrumb -->
            <nav class="breadcrumb-nav">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">HOME</a></li>
                    <li class="breadcrumb-item active">PESANAN MASUK</li>
                </ol>
            </nav>
            
            <!-- Cancel Button -->
            <a href="pesanan_dibatalkan.php" class="btn cancel-button text-white" style="background: linear-gradient(to right, #EFAA31, #FF8A0D); font-weight: 600;">PESANAN DIBATALKAN</a>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <h6 class="filter-title">Filter Status</h6>
            <form method="GET" class="row">
                <div class="col-md-3">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <option value="Diproses" <?= $status_filter == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                        <option value="Dikirim" <?= $status_filter == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                        <option value="Dibatalkan" <?= $status_filter == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                </div>
            </form>
        </div>

        <!-- Orders Table -->
        <div class="orders-table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Tanggal</th>
                        <th>Nama Pembeli</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($pesanan)) {
                        foreach ($pesanan as $index => $order) {
                            if($order['status_pesanan'] !== "Selesai"){
                            ?>
                            <tr>
                                <td class="order-id">#<?= str_pad($order['id_pesanan'], 3, '0', STR_PAD_LEFT) ?></td>
                                <td class="order-date"><?= date('d-m-Y', strtotime($order['tanggal_pesanan'])) ?></td>
                                <td class="customer-name"><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td class="order-total">IDR <?= number_format($order['total_harga'], 0, ',', '.') ?></td>
                                <td><span class="quantity-badge">1</span></td>
                                <td>
                                    <select class="form-select status-select status-dropdown" 
                                            data-id="<?= $order['id_pesanan'] ?>">
                                        <option value="Diproses" <?= $order['status_pesanan'] == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                                        <option value="Dikirim" <?= $order['status_pesanan'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                        <option value="Dibatalkan" <?= $order['status_pesanan'] == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                    </select>
                                </td>
                                <td>
                                    <i class="bi bi-eye action-icon" title="View Details"></i>
                                </td>
                            </tr>
                            <?php
                            }
                        } 
                    } else {
                        echo "<tr><td colspan='7' class='no-data'>Tidak ada data pesanan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
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
  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/main.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const statusDropdowns = document.querySelectorAll('.status-dropdown');
    
    statusDropdowns.forEach(dropdown => {
        dropdown.addEventListener('change', function () {
            const id = this.getAttribute('data-id');
            const newStatus = this.value;

            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id_pesanan=${id}&status_pesanan=${newStatus}`
            })
            .then(response => response.text())
            .then(data => {
                if (data === 'success') {
                    location.reload();
                } else {
                    alert('Gagal memperbarui status.');
                }
            });
        });
    });
});
</script>
</body>
</html>