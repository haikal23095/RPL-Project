<?php
session_start();

// Cek apakah user adalah admin
if (!isset($_SESSION["admin"])) {
    echo "unauthorized";
    exit;
}

// Cek apakah request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "invalid_method";
    exit;
}

// Cek apakah parameter yang diperlukan ada
if (!isset($_POST['id_pesanan']) || !isset($_POST['action'])) {
    echo "missing_parameters";
    exit;
}

$id_pesanan = (int)$_POST['id_pesanan'];
$action = $_POST['action'];

// Validasi action
if (!in_array($action, ['approve', 'reject'])) {
    echo "invalid_action";
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
    echo "connection_failed";
    exit;
}

try {
    // Mulai transaksi
    $pdo->beginTransaction();
    
    // Tentukan status berdasarkan action
    $status_pembatalan = ($action === 'approve') ? 'Disetujui' : 'Ditolak';
    
    // Update atau insert status pembatalan
    $update_query = "INSERT INTO status_pembatalan (id_pesanan, status_pembatalan, alasan) 
                     VALUES (?, ?, 'Ingin membatalkan pesanan karena salah pesan')
                     ON DUPLICATE KEY UPDATE 
                     status_pembatalan = VALUES(status_pembatalan),
                     tanggal_update = CURRENT_TIMESTAMP";
    
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->execute([$id_pesanan, $status_pembatalan]);
    
    // Jika disetujui, update status pesanan menjadi 'Dibatalkan' jika belum
    if ($action === 'approve') {
        $update_pesanan_query = "UPDATE pesanan SET status_pesanan = 'Dibatalkan' WHERE id_pesanan = ?";
        $update_pesanan_stmt = $pdo->prepare($update_pesanan_query);
        $update_pesanan_stmt->execute([$id_pesanan]);
    }
    
    // Commit transaksi
    $pdo->commit();
    
    echo "success";
    
} catch (PDOException $e) {
    // Rollback jika ada error
    $pdo->rollback();
    echo "database_error";
}
?>