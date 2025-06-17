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

// Query untuk statistik permintaan pembatalan
$stats_query = "SELECT 
    COUNT(CASE WHEN status_pesanan = 'Semua' THEN 1 END) as semua,
    COUNT(CASE WHEN status_pesanan = 'Pending' THEN 1 END) as pending,
    COUNT(CASE WHEN status_pesanan = 'Ditolak' THEN 1 END) as ditolak,
    COUNT(CASE WHEN status_pesanan = 'Disetujui' THEN 1 END) as disetujui
    FROM pesanan WHERE status_pesanan IN ('Dibatalkan', 'Pending', 'Ditolak', 'Disetujui')";

$stats_stmt = $pdo->prepare($stats_query);
$stats_stmt->execute();
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Set default values jika null
$stats['semua'] = $stats['semua'] ?? 150;
$stats['pending'] = $stats['pending'] ?? 10;
$stats['ditolak'] = $stats['ditolak'] ?? 30;
$stats['disetujui'] = $stats['disetujui'] ?? 0;

// Query untuk menampilkan daftar pesanan yang dibatalkan
$query = "SELECT p.id_pesanan, u.nama AS customer_name, 
                 p.tanggal_pesanan, p.total_harga, p.status_pesanan,
                 'Ingin membatalkan pesanan karena salah pesan' as alasan
          FROM pesanan p
          JOIN user u ON p.id_user = u.id_user
          WHERE p.status_pesanan = 'Dibatalkan'
          ORDER BY p.tanggal_pesanan DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$pesanan_dibatalkan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Dibatalkan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Andika', sans-serif;;
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
        
        .stats-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
        
        .stats-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
            font-size: 1.1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1.5rem;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.875rem;
            color: #6c757d;
            text-transform: capitalize;
        }
        
        .stat-semua .stat-number { color: #6c757d; }
        .stat-pending .stat-number { color: #f4a460; }
        .stat-ditolak .stat-number { color: #dc3545; }
        .stat-disetujui .stat-number { color: #28a745; }
        
        .search-filter-container {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #e9ecef;
        }
        
        .search-filter-row {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-input {
            flex: 1;
            min-width: 200px;
            position: relative;
        }
        
        .search-input input {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.75rem 2.5rem 0.75rem 1rem;
            font-size: 0.875rem;
            width: 100%;
        }
        
        .search-input .search-icon {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .filter-select {
            min-width: 120px;
        }
        
        .filter-select select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
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
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.875rem;
        }
        
        .btn-approve {
            background-color: #28a745;
            color: white;
        }
        
        .btn-reject {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-approve:hover {
            background-color: #218838;
        }
        
        .btn-reject:hover {
            background-color: #5a6268;
        }
        
        .reason-text {
            color: #6c757d;
            font-size: 0.8rem;
            max-width: 200px;
            text-align: left;
        }
        
        .no-data {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 2rem;
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
                <i class="bi bi-x-circle"></i>
                PESANAN DIBATALKAN
            </h1>
            
            <!-- Breadcrumb -->
            <nav class="breadcrumb-nav">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">HOME</a></li>
                    <li class="breadcrumb-item active">PESANAN MASUK</li>
                </ol>
            </nav>
        </div>

        <!-- Statistics Section -->
        <div class="stats-container">
            <h6 class="stats-title">Permintaan Pembatalan</h6>
            <div class="stats-grid">
                <div class="stat-item stat-semua">
                    <div class="stat-number"><?= $stats['semua'] ?></div>
                    <div class="stat-label">Order</div>
                    <small class="text-muted">Semua</small>
                </div>
                <div class="stat-item stat-pending">
                    <div class="stat-number"><?= $stats['pending'] ?></div>
                    <div class="stat-label">Order</div>
                    <small class="text-muted">Pending</small>
                </div>
                <div class="stat-item stat-ditolak">
                    <div class="stat-number"><?= $stats['ditolak'] ?></div>
                    <div class="stat-label">Order</div>
                    <small class="text-muted">Ditolak</small>
                </div>
                <div class="stat-item stat-disetujui">
                    <div class="stat-number"><?= $stats['disetujui'] ?></div>
                    <div class="stat-label">Order</div>
                    <small class="text-muted">Disetujui</small>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="search-filter-container">
            <div class="search-filter-row">
                <div class="search-input">
                    <input type="text" class="form-control" placeholder="Search..." id="searchInput">
                    <i class="bi bi-search search-icon"></i>
                </div>
                <div class="filter-select">
                    <select class="form-select" id="filterSelect">
                        <option value="">FILTER</option>
                        <option value="pending">Pending</option>
                        <option value="ditolak">Ditolak</option>
                        <option value="disetujui">Disetujui</option>
                    </select>
                </div>
                <div class="filter-select">
                    <select class="form-select" id="categorySelect">
                        <option value="">KATEGORI</option>
                        <option value="furniture">Furniture</option>
                        <option value="elektronik">Elektronik</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="orders-table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Order ID</th>
                        <th>Nama User</th>
                        <th>Tanggal</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($pesanan_dibatalkan)) {
                        foreach ($pesanan_dibatalkan as $index => $order) {
                            ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td class="order-id">#<?= str_pad($order['id_pesanan'], 6, '0', STR_PAD_LEFT) ?></td>
                                <td class="customer-name"><?= htmlspecialchars($order['customer_name']) ?></td>
                                <td class="order-date"><?= date('Y-m-d', strtotime($order['tanggal_pesanan'])) ?></td>
                                <td class="reason-text"><?= htmlspecialchars($order['alasan']) ?></td>
                                <td>
                                    <span class="status-pending">PENDING</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn btn-approve" onclick="approveOrder(<?= $order['id_pesanan'] ?>)" title="Setujui">
                                            <i class="bi bi-check"></i>
                                        </button>
                                        <button class="action-btn btn-reject" onclick="rejectOrder(<?= $order['id_pesanan'] ?>)" title="Tolak">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='7' class='no-data'>Tidak ada data pesanan yang dibatalkan.</td></tr>";
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
// Search functionality
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Filter functionality
document.getElementById('filterSelect').addEventListener('change', function() {
    const filterValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (filterValue === '') {
            row.style.display = '';
        } else {
            const statusCell = row.querySelector('.status-pending');
            if (statusCell && statusCell.textContent.toLowerCase().includes(filterValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
});

// Action functions
function approveOrder(orderId) {
    if (confirm('Apakah Anda yakin ingin menyetujui pembatalan pesanan ini?')) {
        // Here you would typically send an AJAX request to update the order status
        fetch('update_cancellation_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id_pesanan=${orderId}&action=approve`
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'success') {
                location.reload();
            } else {
                alert('Gagal memproses permintaan.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan.');
        });
    }
}

function rejectOrder(orderId) {
    if (confirm('Apakah Anda yakin ingin menolak pembatalan pesanan ini?')) {
        fetch('update_cancellation_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id_pesanan=${orderId}&action=reject`
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'success') {
                location.reload();
            } else {
                alert('Gagal memproses permintaan.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan.');
        });
    }
}
</script>
</body>
</html>