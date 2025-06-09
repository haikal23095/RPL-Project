<?php
require_once '../db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id_pesanan = intval($_GET['id']);
    $query = "UPDATE pesanan SET status_pesanan = 'Selesai' WHERE id_pesanan = ?";
    $stmt = $kon->prepare($query);
    $stmt->bind_param("i", $id_pesanan);
    $stmt->execute();
}

header("Location: pesanan_dikirim.php");
exit;