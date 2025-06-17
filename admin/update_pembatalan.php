<?php
session_start();
if (!isset($_SESSION["admin"])) {
    echo "unauthorized";
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
    echo "error";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pesanan = $_POST['id_pesanan'] ?? '';
    $action = $_POST['action'] ?? '';

    if (empty($id_pesanan) || empty($action)) {
        echo "invalid_data";
        exit;
    }

    try {
        if ($action === 'approve') {
            // Update status to approved/cancelled
            $query = "UPDATE pesanan SET status_pesanan = 'Disetujui' WHERE id_pesanan = :id_pesanan";
        } elseif ($action === 'reject') {
            // Update status to rejected/active again
            $query = "UPDATE pesanan SET status_pesanan = 'Ditolak' WHERE id_pesanan = :id_pesanan";
        } else {
            echo "invalid_action";
            exit;
        }

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id_pesanan', $id_pesanan);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "failed";
        }
    } catch (PDOException $e) {
        echo "error";
    }
} else {
    echo "invalid_method";
}
