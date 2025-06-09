<?php
require_once '../db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
}

if (isset($_GET['id'])) {
    $id_pesanan = intval($_GET['id']);
    $nama_user = $_SESSION['user'];
    // Pastikan pesanan milik user yang login dan statusnya belum dibatalkan/selesai
    $cek = $kon->prepare("SELECT * FROM pesanan JOIN user ON user.id_user = pesanan.id_user WHERE id_pesanan = ? AND nama = ? AND status_pesanan NOT IN ('Dibatalkan', 'Selesai')");
    $cek->bind_param("ii", $id_pesanan, $nama_user);
    $cek->execute();
    $result = $cek->get_result();

    if ($result->num_rows > 0) {
        $query = "UPDATE pesanan SET status_pesanan = 'Dibatalkan' WHERE id_pesanan = ?";
        $stmt = $kon->prepare($query);
        $stmt->bind_param("i", $id_pesanan);
        $stmt->execute();
    }
}



header("Location: pesanan_diproses.php");
exit;