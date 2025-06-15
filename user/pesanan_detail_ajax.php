<?php
require_once '../db.php';

if (!isset($_GET['id_pesanan'])) {
    echo '<div class="alert alert-danger">ID Pesanan tidak ditemukan.</div>';
    exit;
}

$id_pesanan = $_GET['id_pesanan'];

// 1. Ambil data pesanan utama dengan LEFT JOIN (sesuai referensi history_pembayaran.php)
$pesanan_stmt = $kon->prepare(
    "SELECT p.id_pesanan, p.total_harga, p.status_pesanan, p.tanggal_pesanan,
            pb.metode_pembayaran, pb.status_pembayaran,
            pg.alamat_pengiriman, pg.nomor_resi, pg.nama_kurir
     FROM pesanan p
     LEFT JOIN pembayaran pb ON p.id_pesanan = pb.id_pesanan
     LEFT JOIN pengiriman_pesanan pg ON p.id_pesanan = pg.id_pesanan
     WHERE p.id_pesanan = ?"
);
$pesanan_stmt->bind_param("s", $id_pesanan);
$pesanan_stmt->execute();
$pesanan_result = $pesanan_stmt->get_result();
$pesanan = $pesanan_result->fetch_assoc();
$pesanan_stmt->close();

if (!$pesanan) {
    echo '<div class="alert alert-danger">Detail pesanan tidak ditemukan.</div>';
    exit;
}

// 2. Ambil data detail produk (menggunakan tabel 'pesanan_detail' sesuai referensi)
$detail_stmt = $kon->prepare(
    "SELECT pd.jumlah, pd.subtotal, p.nama_produk, p.gambar, p.harga as harga
     FROM pesanan_detail pd 
     JOIN produk p ON pd.id_produk = p.id_produk 
     WHERE pd.id_pesanan = ?"
);
$detail_stmt->bind_param("s", $id_pesanan);
$detail_stmt->execute();
$detail_result = $detail_stmt->get_result();
$detail_pesanan = $detail_result->fetch_all(MYSQLI_ASSOC);
$detail_stmt->close();

// 3. Logika perhitungan subtotal, ongkir, dan diskon (mengikuti referensi)
$subtotal_produk = 0;
foreach ($detail_pesanan as $detail) {
    $subtotal_produk += $detail['subtotal'];
}

// Hitung ongkir dan diskon secara dinamis seperti di history_pembayaran.php
$ongkir_dihitung = $subtotal_produk * 0.10; // Asumsi ongkir 10% dari subtotal
$total_seharusnya = $subtotal_produk + $ongkir_dihitung;
$diskon_dihitung = $total_seharusnya - $pesanan['total_harga'];

// Pastikan diskon tidak negatif
if ($diskon_dihitung < 0) {
    $diskon_dihitung = 0;
}


function formatCurrency($number) {
    return 'Rp ' . number_format($number ?? 0, 0, ',', '.');
}
?>

<div class="row">
    <div class="col-md-6">
        <p><strong>Status Pesanan:</strong> <br><span class="badge bg-primary"><?= htmlspecialchars($pesanan['status_pesanan']); ?></span></p>
        <p><strong>No. Pesanan:</strong> <br>#<?= htmlspecialchars($pesanan['id_pesanan']); ?></p>
        <p><strong>Tanggal Pembelian:</strong> <br><?= date('d M Y, H:i', strtotime($pesanan['tanggal_pesanan'])); ?></p>
    </div>
    <div class="col-md-6">
        <h6>Informasi Pengiriman</h6>
        <p class="mb-0"><strong>Kurir:</strong> <?= htmlspecialchars($pesanan['nama_kurir'] ?? '-'); ?></p>
        <p class="mb-0"><strong>No. Resi:</strong> <?= htmlspecialchars($pesanan['nomor_resi'] ?? 'Belum tersedia'); ?></p>
        <p><strong>Alamat:</strong> <br><?= htmlspecialchars($pesanan['alamat_pengiriman'] ?? 'Alamat tidak tersedia.'); ?></p>
    </div>
</div>

<hr>

<h6>Rincian Produk</h6>
<?php if (!empty($detail_pesanan)): ?>
    <?php foreach ($detail_pesanan as $detail): ?>
    <div class="d-flex align-items-center mb-3 border-bottom pb-2">
        <img src="../uploads/<?= htmlspecialchars($detail['gambar']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: .375rem; margin-right: 1rem;" alt="<?= htmlspecialchars($detail['nama_produk']) ?>">
        <div class="flex-grow-1">
            <div class="fw-bold"><?= htmlspecialchars($detail['nama_produk']) ?></div>
            <div class="text-muted"><?= htmlspecialchars($detail['jumlah']) ?> x <?= formatCurrency($detail['harga']) ?></div>
        </div>
        <div class="fw-bold"><?= formatCurrency($detail['subtotal']) ?></div>
    </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="text-muted">Tidak ada rincian produk untuk pesanan ini.</p>
<?php endif; ?>


<h6>Rincian Pembayaran</h6>
<table class="table table-sm">
    <tbody>
        <tr>
            <td>Metode Pembayaran</td>
            <td class="text-end"><?= htmlspecialchars($pesanan['metode_pembayaran']); ?></td>
        </tr>
        <tr>
            <td>Subtotal Produk</td>
            <td class="text-end"><?= formatCurrency($subtotal_produk); ?></td>
        </tr>
        <tr>
            <td>Ongkos Kirim</td>
            <td class="text-end"><?= formatCurrency($ongkir_dihitung); ?></td>
        </tr>
        <tr>
            <td>Diskon</td>
            <td class="text-end text-danger">- <?= formatCurrency($diskon_dihitung); ?></td>
        </tr>
        <tr class="fw-bold fs-5">
            <td>Total Pembayaran</td>
            <td class="text-end"><?= formatCurrency($pesanan['total_harga']); ?></td>
        </tr>
    </tbody>
</table>