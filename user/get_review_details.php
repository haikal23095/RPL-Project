<?php
session_start();
include('../db.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login.']);
    exit();
}

$userId = $_SESSION['user_id']; // Asumsi user_id disimpan di session saat login
$orderId = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($orderId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID pesanan tidak valid.']);
    exit();
}

// Ambil produk dan cek apakah sudah di-review
$sql = "SELECT pd.id_produk, pr.nama_produk, pr.gambar, 
               (SELECT COUNT(*) FROM review_produk rp WHERE rp.id_pesanan = pd.id_pesanan AND rp.id_produk = pd.id_produk AND rp.id_user = ?) as review_count
        FROM pesanan_detail pd
        JOIN produk pr ON pd.id_produk = pr.id_produk
        WHERE pd.id_pesanan = ?
        ORDER BY pr.nama_produk ASC";

$stmt = mysqli_prepare($kon, $sql);
mysqli_stmt_bind_param($stmt, "ii", $userId, $orderId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
mysqli_stmt_close($stmt);

if (empty($products)) {
    echo json_encode(['success' => false, 'message' => 'Tidak ada produk yang ditemukan untuk pesanan ini.']);
} else {
    echo json_encode(['success' => true, 'products' => $products]);
}
?>