<?php
require_once '../db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = $_POST['id_pesanan'] ?? '';
    $alasan_pembatalan = $_POST['alasan_pembatalan'] ?? '';
    $deskripsi_pembatalan = $_POST['deskripsi_pembatalan'] ?? '';
    $nama_user = $_SESSION['user'];

    // Pastikan ID Pesanan atau Alasan Pembatalan tidak kosong
    if (empty($id_pesanan) || empty($alasan_pembatalan)) {
        $_SESSION['pesan_error'] = "ID Pesanan atau Alasan Pembatalan tidak boleh kosong.";
        header("Location: pesanan_diproses.php");
        exit;
    }

    // Ambil ID user untuk keamanan query
    $user_stmt = $kon->prepare("SELECT id_user FROM user WHERE nama = ?");
    $user_stmt->bind_param("s", $nama_user);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user_row = $user_result->fetch_assoc();
    $userId = $user_row['id_user'];
    $user_stmt->close();

    // 1. Cek apakah pesanan memang milik user ini dan statusnya 'Diproses'
    //    DAN BELUM ADA permintaan pembatalan yang 'Pending' untuk pesanan ini.
    $check_stmt = $kon->prepare("SELECT p.id_pesanan FROM pesanan p
                                 LEFT JOIN status_pembatalan sp ON p.id_pesanan = sp.id_pesanan
                                 WHERE p.id_pesanan = ? AND p.id_user = ? AND p.status_pesanan = 'Diproses'
                                 AND (sp.id_status IS NULL OR sp.status_pembatalan != 'Pending')"); // Pastikan belum ada request pending
    $check_stmt->bind_param("si", $id_pesanan, $userId);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // 2. Masukkan permintaan pembatalan ke tabel status_pembatalan
        $insert_cancel_stmt = $kon->prepare("INSERT INTO status_pembatalan (id_pesanan, status_pembatalan, alasan) VALUES (?, ?, ?)");
        $initial_cancel_status = 'Pending'; // Sesuai dengan enum di tabel Anda
        $insert_cancel_stmt->bind_param("sss", $id_pesanan, $initial_cancel_status, $alasan_pembatalan);

        if ($insert_cancel_stmt->execute()) {
            $_SESSION['pesan_sukses'] = "Pengajuan pembatalan pesanan #" . htmlspecialchars($id_pesanan) . " berhasil dikirim. Menunggu persetujuan admin.";
        } else {
            $_SESSION['pesan_error'] = "Gagal mengajukan pembatalan pesanan. Error: " . $kon->error;
        }
        $insert_cancel_stmt->close();
    } else {
        $_SESSION['pesan_error'] = "Pesanan tidak ditemukan, tidak dapat dibatalkan, atau sudah ada permintaan pembatalan tertunda.";
    }
    $check_stmt->close();

    header("Location: pesanan_diproses.php");
    exit;
} else {
    $_SESSION['pesan_error'] = "Akses tidak sah.";
    header("Location: pesanan_diproses.php");
    exit;
}
?>