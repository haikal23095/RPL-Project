<?php

require_once '../db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id_pesanan = $_GET['id'];
    $nama_user = $_SESSION['user'];

    // Ambil ID user untuk keamanan query
    $user_stmt = $kon->prepare("SELECT id_user FROM user WHERE nama = ?");
    $user_stmt->bind_param("s", $nama_user);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user_row = $user_result->fetch_assoc();
    $userId = $user_row['id_user'];
    $user_stmt->close();

    // Pastikan pesanan adalah milik user yang sedang login dan statusnya 'Dikirim'
    $stmt = $kon->prepare("UPDATE pesanan SET status_pesanan = 'Selesai' WHERE id_pesanan = ? AND id_user = ? AND status_pesanan = 'Dikirim'");
    $stmt->bind_param("si", $id_pesanan, $userId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $_SESSION['pesan_sukses'] = "Pesanan #" . htmlspecialchars($id_pesanan) . " berhasil diselesaikan!";
        } else {
            $_SESSION['pesan_error'] = "Gagal menyelesaikan pesanan. Mungkin pesanan tidak ditemukan, bukan milik Anda, atau statusnya sudah bukan 'Dikirim'.";
        }
    } else {
        $_SESSION['pesan_error'] = "Terjadi kesalahan saat memperbarui status pesanan: " . $kon->error;
    }
    $stmt->close();

    header("Location: pesanan_selesai.php"); // Redirect ke halaman pesanan selesai
    exit;
} else {
    $_SESSION['pesan_error'] = "Akses tidak sah.";
    header("Location: pesanan_dikirim.php"); // Redirect kembali ke halaman pesanan dikirim jika akses tidak sah
    exit;
}