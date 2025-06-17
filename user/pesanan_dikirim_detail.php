<?php
require_once '../db.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: pesanan_dikirim.php");
    exit;
}

$id_pesanan = intval($_GET['id']);
$query = "SELECT p.*, pr.nama_produk, pr.harga, pr.gambar 
          FROM pesanan p 
          JOIN produk pr ON p.id_produk = pr.id_produk 
          WHERE p.id_pesanan = ?";
$stmt = $kon->prepare($query);
$stmt->bind_param("i", $id_pesanan);
$stmt->execute();
$result = $stmt->get_result();
$pesanan = $result->fetch_assoc();

if (!$pesanan) {
    echo "<p>Pesanan tidak ditemukan.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan Dikirim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Aclonica&family=Andika:ital,wght@0,400;0,700;1,400;1,700&family=Pixelify+Sans:wght@400..700&display=swap');
        body {
            background: #F8F7F1 !important;
            font-family: 'Andika', sans-serif !important;
            color: #2D3A3A !important;
        }
        .sidebar {
            background-color: #F8F7F1 !important;
        }
        header{
            background-color: #F8F7F1 !important;
        }
        .pesanan-detail-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 25px;
            margin-top: 30px;
            background: #fff;
        }
        .pesanan-detail-card img {
            max-width: 150px;
            border-radius: 8px;
        }
        .btn-orange {
            background-color: #FF8800 !important;
            border: none !important;
            color: white !important;
        }
    </style>
</head>
<body>
<div class="container">
    <h3 class="mt-4">Detail Pesanan Dikirim</h3>
    <div class="pesanan-detail-card row align-items-center">
        <div class="col-md-3">
            <img src="../uploads/<?php echo $pesanan['gambar']; ?>" alt="<?php echo $pesanan['nama_produk']; ?>">
        </div>
        <div class="col-md-9">
            <h5><?php echo $pesanan['nama_produk']; ?></h5>
            <p>Jumlah: x<?php echo $pesanan['jumlah']; ?></p>
            <p>Harga Satuan: IDR <?php echo number_format($pesanan['harga'], 0, ',', '.'); ?></p>
            <p>Total: IDR <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></p>
            <p>Status: <span class="badge bg-info"><?php echo $pesanan['status_pesanan']; ?></span></p>
            <p>Tanggal Pesan: <?php echo date('d-m-Y', strtotime($pesanan['tanggal_pesanan'])); ?></p>
            <a href="pesanan_dikirim.php" class="btn btn-secondary mt-2">Kembali</a>
            <?php if($pesanan['status_pesanan'] == 'Dikirim'): ?>
                <a href="selesai.php?id=<?php echo $pesanan['id_pesanan']; ?>" class="btn btn-orange mt-2">Pesanan Selesai</a>
                <a href="batalkan.php?id=<?php echo $pesanan['id_pesanan']; ?>" class="btn mt-2" style="background-color: #8B4513; color: white;">Batalkan</a>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>