<?php
session_start();
include('../db.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Anda harus login.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak valid.']);
    exit();
}

$userId = $_SESSION['user_id'];
$orderId = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$reviews = isset($_POST['reviews']) ? $_POST['reviews'] : [];

if ($orderId <= 0 || empty($reviews)) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
    exit();
}

$errors = [];
$successCount = 0;

mysqli_begin_transaction($kon);

try {
    foreach ($reviews as $reviewData) {
        $id_produk = intval($reviewData['product_id']);
        $rating_produk = intval($reviewData['rating_produk']);
        $rating_pelayanan = intval($reviewData['rating_pelayanan']);
        $rating_pengiriman = intval($reviewData['rating_pengiriman']);
        $komentar = trim($reviewData['komentar']);

        if ($rating_produk < 1 || $rating_produk > 5 || empty($komentar)) {
            $errors[] = "Ulasan atau rating untuk produk ID $id_produk tidak valid.";
            continue;
        }

        // Cek duplikasi
        $sql_check = "SELECT COUNT(*) FROM review_produk WHERE id_user = ? AND id_produk = ? AND id_pesanan = ?";
        $stmt_check = mysqli_prepare($kon, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "iii", $userId, $id_produk, $orderId);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $reviewCount = mysqli_fetch_row($result_check)[0];
        mysqli_stmt_close($stmt_check);

        if ($reviewCount > 0) continue; // Lewati jika sudah pernah review

        // Insert ulasan baru
        $sql_insert = "INSERT INTO review_produk (id_user, id_produk, id_pesanan, rating_produk, rating_pelayanan, rating_pengiriman, komentar) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($kon, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "iiiiiis", $userId, $id_produk, $orderId, $rating_produk, $rating_pelayanan, $rating_pengiriman, $komentar);
        
        if (mysqli_stmt_execute($stmt_insert)) {
            $successCount++;
        } else {
            $errors[] = "Gagal menyimpan ulasan untuk produk ID $id_produk.";
        }
        mysqli_stmt_close($stmt_insert);
    }
    
    // Jika ada ulasan yang berhasil, update status pesanan
    if ($successCount > 0) {
        $sql_update = "UPDATE pesanan SET sudah_dinilai = 1 WHERE id_pesanan = ? AND id_user = ?";
        $stmt_update = mysqli_prepare($kon, $sql_update);
        mysqli_stmt_bind_param($stmt_update, "ii", $orderId, $userId);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);
    }
    
    if (!empty($errors)) {
        throw new Exception(implode("\n", $errors));
    }

    mysqli_commit($kon);
    echo json_encode(['success' => true, 'message' => 'Terima kasih! Ulasan Anda telah berhasil dikirim.']);

} catch (Exception $e) {
    mysqli_rollback($kon);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>