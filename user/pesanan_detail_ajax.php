<?php
require_once '../db.php';
session_start();

if (!isset($_SESSION['user'])) {
    exit('Unauthorized');
}

if (!isset($kon)) {
    exit('Database connection not found.');
}

$id_pesanan = isset($_GET['id_pesanan']) ? intval($_GET['id_pesanan']) : 0;
if ($id_pesanan <= 0) exit('ID tidak valid');

// Ambil detail pesanan dengan prepared statement
$stmt = $kon->prepare("SELECT pd.*, pr.nama_produk, pr.gambar, pr.harga 
          FROM pesanan_detail pd
          JOIN produk pr ON pd.id_produk = pr.id_produk
          WHERE pd.id_pesanan = ?");
$stmt->bind_param("i", $id_pesanan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<p>Tidak ada detail barang untuk pesanan ini.</p>";
    exit;
}
?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Gambar</th>
            <th>Nama Produk</th>
            <th>Jumlah</th>
            <th>Harga Satuan</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="" style="width:60px; height:60px; object-fit:cover;"></td>
            <td><?= htmlspecialchars($row['nama_produk']) ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td>IDR <?= number_format($row['harga'], 0, ',', '.') ?></td>
            <td>IDR <?= number_format($row['subtotal'], 0, ',', '.') ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>