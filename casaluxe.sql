-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 28 Bulan Mei 2025 pada 07.04
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gamify`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `feedback`
--

CREATE TABLE `feedback` (
  `id` int(64) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `content`, `created_at`) VALUES
(3, 'Tria Yunita Krismiyanto', 'Aduh', '2024-12-18 06:10:00'),
(4, 'coba123', 'test', '2024-12-22 14:30:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `informasipromo`
--

CREATE TABLE `informasipromo` (
  `id` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `photo_url` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `promo_type` enum('discount','bonus') NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `discount_percentage` int(11) DEFAULT NULL,
  `bonus_item` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `informasipromo`
--

INSERT INTO `informasipromo` (`id`, `id_produk`, `title`, `description`, `photo_url`, `photo`, `promo_type`, `start_date`, `end_date`, `discount_percentage`, `bonus_item`, `created_at`) VALUES
(44, 0, 'PC DENGAN BONUS MONITOR!!!', 'Beli PC dapat monitor gratis? kapan lagi? AYO SEGERA BELI SEKARANG!!!!', '../uploads/BG LOGIN 3.jpg', NULL, 'bonus', '2024-12-20', '2024-12-27', 0, 4, '2024-12-20 07:03:43'),
(45, 0, 'Diskon Natal', 'Mendekati Natal Ada Diskon', '../uploads/profile-img.jpg', NULL, 'discount', '2024-12-21', '2024-12-28', 20, 0, '2024-12-21 03:05:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi`, `created_at`) VALUES
(1, 'CONTROLER', '', '2024-11-11 21:37:50'),
(2, 'PC', '', '2024-11-11 21:38:00'),
(3, 'AKSESORIS', '', '2024-11-11 21:38:01'),
(4, 'GAMING', '', '2024-11-11 21:38:03'),
(5, 'CONSOLE', '', '2024-12-09 13:29:15'),
(6, 'test', '', '2024-12-22 21:37:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `keranjang`
--

CREATE TABLE `keranjang` (
  `id_keranjang` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `keranjang`
--

INSERT INTO `keranjang` (`id_keranjang`, `user_id`, `id_produk`, `jumlah`, `created_at`, `updated_at`) VALUES
(61, 2, 3, 1, '2024-12-22 19:24:34', '2024-12-22 19:24:34'),
(66, 7, 6, 5, '2024-12-22 21:57:53', '2024-12-22 21:58:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `komentar`
--

CREATE TABLE `komentar` (
  `id_komentar` int(11) NOT NULL,
  `id_topik` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `isi_komentar` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `komentar`
--

INSERT INTO `komentar` (`id_komentar`, `id_topik`, `id_user`, `isi_komentar`, `created_at`) VALUES
(1, 1, 5, 'Anjay', '2024-12-18 08:05:46'),
(2, 2, 5, 'Ada budget berapa kak?', '2024-12-18 10:09:12'),
(3, 1, 5, '', '2024-12-18 10:43:21'),
(4, 3, 4, 'Hai', '2024-12-18 13:02:16'),
(5, 3, 5, 'Keren\r\n', '2024-12-18 13:03:48'),
(6, 3, 6, 'Apa nih yang keren\r\n', '2024-12-18 13:04:05'),
(7, 3, 6, 'Kabarin dong\r\n', '2024-12-18 13:04:14'),
(8, 3, 6, 'Baru nimbrung nih', '2024-12-18 13:04:21'),
(9, 3, 5, 'Nih mending PS 5 atau 4\r\n', '2024-12-18 13:04:49'),
(10, 3, 5, 'Yah menurutku aku PS 5 aja gak sih', '2024-12-18 13:05:00'),
(11, 3, 5, 'Aku pengennya itu', '2024-12-18 13:05:05'),
(12, 3, 4, 'Yah kalau menurut admin sih,mana baiknya buat kamu', '2024-12-18 13:05:34'),
(13, 3, 4, 'Semua barang dikita ready yah,jangan lupa checkout', '2024-12-18 13:05:49'),
(14, 3, 4, 'Semangatt', '2024-12-18 13:05:53'),
(15, 3, 2, 'haduh', '2024-12-19 17:21:15'),
(16, 6, 6, 'Mending PS sih kalo mau ngegame doang\r\n', '2024-12-19 17:41:57'),
(17, 6, 1, 'Betul sekali mas asep prayogi\r\n', '2024-12-19 19:02:39'),
(18, 7, 1, 'coba game pokemon sword and shield (atmin rapi)', '2024-12-21 05:47:26'),
(19, 3, 1, 'aku mau ini', '2024-12-21 05:47:53'),
(20, 7, 7, 'saya suka animal crossing\r\n', '2024-12-22 15:06:45'),
(21, 7, 2, 'okee thanks ya\r\n', '2024-12-22 15:07:20');

-- --------------------------------------------------------

--
-- Struktur dari tabel `komunitas`
--

CREATE TABLE `komunitas` (
  `id_komunitas` int(11) NOT NULL,
  `nama_komunitas` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `dibuat_oleh` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `komunitas`
--

INSERT INTO `komunitas` (`id_komunitas`, `nama_komunitas`, `deskripsi`, `dibuat_oleh`, `created_at`, `gambar`) VALUES
(1, 'PS 5', 'Kelz bang', 5, '2024-12-18 06:18:29', NULL),
(2, 'CONSOLE', 'MANTAP', 5, '2024-12-18 06:48:30', NULL),
(3, 'PC', 'MANTAP', 5, '2024-12-18 06:57:20', NULL),
(7, 'Nintendo', 'Nintendo', 1, '2024-12-21 03:04:08', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `messages`
--

CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender` enum('admin','user') NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `messages`
--

INSERT INTO `messages` (`id_message`, `user_id`, `sender`, `message`, `timestamp`) VALUES
(6, 1, 'user', 'Hallo', '2024-10-29 00:04:33'),
(7, 1, 'admin', 'Hai rafi', '2024-10-29 00:04:43'),
(8, 1, 'user', 'Aku ada masalah nih', '2024-10-29 00:04:53'),
(9, 1, 'admin', 'Masalah apa tuh kalau boleh tau', '2024-10-29 00:05:03'),
(10, 1, 'user', 'Waduh apa yah', '2024-10-29 00:05:13'),
(11, 1, 'admin', 'Jangan ragu kalau mau ceita', '2024-10-29 00:05:27'),
(12, 1, 'user', 'Waduh apa yah', '2024-10-29 00:05:36'),
(13, 1, 'admin', 'Jangan ragu kalau mau ceita', '2024-10-29 00:05:42'),
(14, 1, 'admin', 'Hai', '2024-11-17 23:56:39'),
(15, 1, 'user', 'Hallo min', '2024-11-17 23:57:01'),
(16, 1, 'user', '', '2024-11-17 23:57:01'),
(17, 5, 'user', 'Hai', '2024-11-18 01:26:59'),
(18, 2, 'user', 'Halo', '2024-12-15 08:13:16'),
(19, 2, 'admin', 'Iya ada apa ya? apakah ada yang bisa kami bantu?', '2024-12-15 08:30:04'),
(20, 7, 'user', 'tolong bantu saya', '2024-12-22 14:40:35'),
(21, 7, 'admin', 'iya ada apa?', '2024-12-22 14:40:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `type` enum('admin','user') NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifications`
--

INSERT INTO `notifications` (`id`, `id_user`, `id_produk`, `type`, `title`, `message`, `image`, `created_at`, `is_read`) VALUES
(323, 2, NULL, 'user', 'Promo Tersedia', 'Promo PC DENGAN BONUS MONITOR!!! sedang berlangsung! Dapatkan bonus 4. Beli PC dapat monitor gratis? kapan lagi? AYO SEGERA BELI SEKARANG!!!!', NULL, '2024-12-22 12:23:36', 0),
(324, 2, NULL, 'user', 'Promo Tersedia', 'Promo Diskon Natal sedang berlangsung! Diskon hingga 20%. Mendekati Natal Ada Diskon', NULL, '2024-12-22 12:23:36', 0),
(325, NULL, NULL, 'user', 'Promo Tersedia', 'Promo PC DENGAN BONUS MONITOR!!! sedang berlangsung! Dapatkan bonus 4. Beli PC dapat monitor gratis? kapan lagi? AYO SEGERA BELI SEKARANG!!!!', NULL, '2024-12-22 14:27:46', 0),
(326, NULL, NULL, 'user', 'Promo Tersedia', 'Promo Diskon Natal sedang berlangsung! Diskon hingga 20%. Mendekati Natal Ada Diskon', NULL, '2024-12-22 14:27:46', 0),
(327, NULL, NULL, 'user', 'Promo Tersedia', 'Promo PC DENGAN BONUS MONITOR!!! sedang berlangsung! Dapatkan bonus 4. Beli PC dapat monitor gratis? kapan lagi? AYO SEGERA BELI SEKARANG!!!!', NULL, '2024-12-22 14:27:49', 0),
(328, NULL, NULL, 'user', 'Promo Tersedia', 'Promo Diskon Natal sedang berlangsung! Diskon hingga 20%. Mendekati Natal Ada Diskon', NULL, '2024-12-22 14:27:51', 0),
(329, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 519.950', NULL, '2024-12-22 14:32:59', 0),
(335, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:34:43', 0),
(337, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:34:43', 0),
(340, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:35:01', 0),
(342, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:35:01', 0),
(344, NULL, 3, 'admin', 'Stok Produk Rendah', 'Produk Dualshock Controller tersisa 0 unit', NULL, '2024-12-22 14:39:27', 0),
(345, NULL, 4, 'admin', 'Stok Produk Rendah', 'Produk PC Gaming Intel i9 gen 12 + Monitor tersisa 0 unit', NULL, '2024-12-22 14:39:27', 0),
(347, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:39:27', 0),
(349, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:39:27', 0),
(352, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:39:55', 0),
(354, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:39:55', 0),
(357, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:39:57', 0),
(359, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:39:57', 0),
(362, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:39:58', 0),
(364, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:39:58', 0),
(367, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:40:19', 0),
(369, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:40:20', 0),
(372, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:40:20', 0),
(374, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:40:20', 0),
(377, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:40:23', 0),
(379, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:40:23', 0),
(381, 7, NULL, 'user', 'Promo Tersedia', 'Promo PC DENGAN BONUS MONITOR!!! sedang berlangsung! Dapatkan bonus 4. Beli PC dapat monitor gratis? kapan lagi? AYO SEGERA BELI SEKARANG!!!!', NULL, '2024-12-22 14:40:23', 0),
(382, 7, NULL, 'user', 'Promo Tersedia', 'Promo Diskon Natal sedang berlangsung! Diskon hingga 20%. Mendekati Natal Ada Diskon', NULL, '2024-12-22 14:40:23', 0),
(383, 7, NULL, 'user', 'Pesanan Dikirim', 'Pesanan #87 sedang dalam pengiriman. Mohon tunggu sampai pesanan tiba.', NULL, '2024-12-22 14:40:23', 0),
(384, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:40:23', 0),
(385, 7, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 1.720.000', NULL, '2024-12-22 14:40:23', 0),
(386, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:40:23', 0),
(387, 7, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 1.720.000', NULL, '2024-12-22 14:40:23', 0),
(388, 7, 3, 'user', 'Produk Tersedia', 'Produk Dualshock Controller di wishlist Anda sekarang tersedia!', NULL, '2024-12-22 14:42:31', 0),
(389, 7, NULL, 'user', 'Pesanan Dikirim', 'Pesanan #87 sedang dalam pengiriman. Mohon tunggu sampai pesanan tiba.', NULL, '2024-12-22 14:42:31', 0),
(390, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:42:31', 0),
(391, 7, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 1.720.000', NULL, '2024-12-22 14:42:31', 0),
(392, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 14:42:31', 0),
(393, 7, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 1.720.000', NULL, '2024-12-22 14:42:31', 0),
(394, 7, NULL, 'user', 'Pesanan Selesai', 'Pesanan #87 telah selesai. Terima kasih telah berbelanja!', NULL, '2024-12-22 15:02:19', 0),
(395, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 15:02:19', 0),
(396, 7, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 1.720.000', NULL, '2024-12-22 15:02:19', 0),
(397, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2024-12-22 15:02:19', 0),
(398, 7, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 1.720.000', NULL, '2024-12-22 15:02:19', 0),
(399, 7, NULL, 'user', 'Pesanan Selesai', 'Pesanan #90 telah selesai. Terima kasih telah berbelanja!', NULL, '2024-12-22 15:02:19', 0),
(400, NULL, 3, 'admin', 'Review Baru', 'Review baru dari coba123 untuk produk Dualshock Controller \r\n            dengan rating 5/5', NULL, '2024-12-22 15:02:19', 0),
(401, NULL, 3, 'admin', 'Review Baru', 'Review baru dari coba123 untuk produk Dualshock Controller \r\n            dengan rating 5/5', NULL, '2024-12-22 15:02:20', 0),
(402, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2025-05-27 23:12:56', 0),
(403, 7, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 1.720.000', NULL, '2025-05-27 23:12:56', 0),
(404, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2025-05-27 23:12:56', 0),
(405, 7, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 1.720.000', NULL, '2025-05-27 23:12:56', 0),
(406, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2025-05-27 23:20:49', 0),
(407, 7, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 1.720.000', NULL, '2025-05-27 23:20:49', 0),
(408, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari coba123 dengan total Rp 1.720.000', NULL, '2025-05-27 23:20:49', 0),
(409, 7, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 1.720.000', NULL, '2025-05-27 23:20:49', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembatalan_pesanan`
--

CREATE TABLE `pembatalan_pesanan` (
  `id_pembatalan` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `alasan_pembatalan` enum('berubah_pikiran','harga_lebih_murah','barang_salah','pengiriman_lama','masalah_pembayaran','lainnya') NOT NULL,
  `deskripsi_pembatalan` text DEFAULT NULL,
  `tanggal_pembatalan` datetime NOT NULL,
  `catatan_admin` text DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembatalan_pesanan`
--

INSERT INTO `pembatalan_pesanan` (`id_pembatalan`, `id_pesanan`, `id_user`, `alasan_pembatalan`, `deskripsi_pembatalan`, `tanggal_pembatalan`, `catatan_admin`, `dibuat_pada`) VALUES
(2, 41, 1, 'harga_lebih_murah', 'test', '2024-12-17 12:04:50', NULL, '2024-12-17 11:04:50'),
(3, 41, 1, 'harga_lebih_murah', 'tes', '2024-12-17 12:05:41', NULL, '2024-12-17 11:05:41'),
(4, 80, 1, 'lainnya', 'Maaf', '2024-12-20 11:17:43', NULL, '2024-12-20 10:17:43'),
(5, 78, 1, 'masalah_pembayaran', 'Gajado', '2024-12-20 11:22:11', NULL, '2024-12-20 10:22:11'),
(6, 77, 1, 'barang_salah', 'Malas', '2024-12-21 05:42:12', NULL, '2024-12-21 04:42:12'),
(7, 86, 1, 'masalah_pembayaran', 'pepe', '2024-12-21 06:21:17', NULL, '2024-12-21 05:21:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `status_pembayaran` enum('Belum Dibayar','Dibayar') DEFAULT 'Belum Dibayar',
  `tanggal_pembayaran` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `metode_pembayaran`, `status_pembayaran`, `tanggal_pembayaran`) VALUES
(2, 6, 'COD', 'Dibayar', '2024-12-14 13:31:46'),
(4, 8, 'Transfer Bank', 'Dibayar', '2024-12-14 14:35:56'),
(5, 9, 'Kartu Kredit', 'Dibayar', '2024-12-14 16:24:38'),
(6, 10, 'Transfer Bank', 'Dibayar', '2024-12-14 22:39:53'),
(7, 11, 'Transfer Bank', 'Dibayar', '2024-12-14 23:12:05'),
(8, 12, 'Transfer Bank', 'Dibayar', '2024-12-14 23:13:56'),
(9, 13, 'Kartu Kredit', 'Dibayar', '2024-12-14 23:41:39'),
(10, 14, 'Transfer Bank', 'Dibayar', '2024-12-16 11:45:03'),
(11, 15, 'Kartu Kredit', 'Dibayar', '2024-12-16 21:31:20'),
(18, 22, 'Transfer Bank', 'Dibayar', '2024-12-16 21:37:14'),
(34, 38, 'Transfer Bank', 'Dibayar', '2024-12-16 21:55:50'),
(35, 39, 'Kartu Kredit', 'Dibayar', '2024-12-16 21:56:04'),
(36, 40, 'Kartu Kredit', 'Dibayar', '2024-12-17 16:29:17'),
(37, 41, 'Transfer Bank', 'Dibayar', '2024-12-17 17:54:53'),
(38, 42, 'Kartu Kredit', 'Dibayar', '2024-12-18 17:21:08'),
(39, 43, 'Kartu Kredit', 'Dibayar', '2024-12-18 17:21:55'),
(40, 44, 'Kartu Kredit', 'Dibayar', '2024-12-18 17:27:17'),
(41, 45, 'Transfer Bank', 'Dibayar', '2024-12-18 17:40:59'),
(42, 46, 'Transfer Bank', 'Dibayar', '2024-12-18 17:51:22'),
(43, 47, 'COD', 'Dibayar', '2024-12-18 18:01:10'),
(47, 60, 'Kartu Kredit', 'Dibayar', '2024-12-19 22:41:56'),
(48, 61, 'Transfer Bank', 'Dibayar', '2024-12-19 23:14:01'),
(49, 62, 'Kartu Kredit', 'Dibayar', '2024-12-19 23:30:51'),
(50, 63, 'Transfer Bank', 'Dibayar', '2024-12-19 23:48:44'),
(51, 64, 'Kartu Kredit', 'Dibayar', '2024-12-20 00:04:34'),
(52, 65, 'Kartu Kredit', 'Dibayar', '2024-12-20 01:01:22'),
(53, 66, 'Kartu Kredit', 'Dibayar', '2024-12-20 01:02:18'),
(54, 67, 'Transfer Bank', 'Dibayar', '2024-12-20 01:03:07'),
(55, 68, 'Transfer Bank', 'Dibayar', '2024-12-20 01:28:23'),
(56, 69, 'Transfer Bank', 'Dibayar', '2024-12-20 01:28:41'),
(57, 70, 'Transfer Bank', 'Dibayar', '2024-12-20 01:29:37'),
(58, 71, 'Transfer Bank', 'Dibayar', '2024-12-20 01:55:28'),
(59, 72, 'Kartu Kredit', 'Dibayar', '2024-12-20 01:55:46'),
(60, 73, 'Kartu Kredit', 'Dibayar', '2024-12-20 14:20:07'),
(61, 74, 'Kartu Kredit', 'Dibayar', '2024-12-20 14:20:07'),
(62, 75, 'Kartu Kredit', 'Dibayar', '2024-12-20 14:36:45'),
(63, 76, 'Transfer Bank', 'Dibayar', '2024-12-20 15:07:09'),
(64, 77, 'Kartu Kredit', 'Dibayar', '2024-12-20 16:33:10'),
(65, 78, 'Kartu Kredit', 'Dibayar', '2024-12-20 16:33:39'),
(66, 79, 'Transfer Bank', 'Dibayar', '2024-12-20 16:34:00'),
(67, 80, 'Kartu Kredit', 'Dibayar', '2024-12-20 16:34:31'),
(68, 81, 'Kartu Kredit', 'Dibayar', '2024-12-20 16:49:51'),
(69, 82, 'Kartu Kredit', 'Dibayar', '2024-12-20 16:50:10'),
(70, 83, 'Transfer Bank', 'Dibayar', '2024-12-20 17:13:59'),
(71, 84, 'Kartu Kredit', 'Dibayar', '2024-12-21 11:40:03'),
(72, 85, 'Kartu Kredit', 'Dibayar', '2024-12-21 12:09:45'),
(73, 86, 'Transfer Bank', 'Dibayar', '2024-12-21 12:21:06'),
(74, 87, 'Kartu Kredit', 'Dibayar', '2024-12-22 21:32:02'),
(75, 88, 'Transfer Bank', 'Dibayar', '2024-12-22 21:34:33'),
(76, 89, 'Transfer Bank', 'Dibayar', '2024-12-22 21:34:33'),
(77, 90, 'Kartu Kredit', 'Dibayar', '2024-12-22 22:01:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengiriman_pesanan`
--

CREATE TABLE `pengiriman_pesanan` (
  `id_pengiriman` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nomor_resi` varchar(50) NOT NULL,
  `nama_kurir` varchar(100) NOT NULL,
  `alamat_pengiriman` text NOT NULL,
  `tanggal_kirim` datetime NOT NULL,
  `perkiraan_tiba` datetime DEFAULT NULL,
  `tanggal_tiba` datetime DEFAULT NULL,
  `status_pengiriman` enum('dalam_pengiriman','sudah_sampai','terlambat') NOT NULL,
  `biaya_kirim` decimal(10,2) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  `diperbarui_pada` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengiriman_pesanan`
--

INSERT INTO `pengiriman_pesanan` (`id_pengiriman`, `id_pesanan`, `id_user`, `nomor_resi`, `nama_kurir`, `alamat_pengiriman`, `tanggal_kirim`, `perkiraan_tiba`, `tanggal_tiba`, `status_pengiriman`, `biaya_kirim`, `dibuat_pada`, `diperbarui_pada`) VALUES
(2, 6, 2, 'RSI20241214073146274', 'SiCepat', 'Koperindag Blok C No 3', '2024-12-14 07:31:46', '2024-12-17 07:31:46', NULL, 'dalam_pengiriman', 20000.00, '2024-12-14 06:31:46', '2024-12-14 06:31:46'),
(4, 8, 2, 'RSI20241214083556978', 'SiCepat', 'Koperindag Blok C No 3', '2024-12-14 08:35:56', '2024-12-17 08:35:56', NULL, 'dalam_pengiriman', 20000.00, '2024-12-14 07:35:56', '2024-12-14 07:35:56'),
(5, 9, 2, 'RSI20241214102438368', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-14 10:24:38', '2024-12-17 10:24:38', NULL, 'dalam_pengiriman', 20000.00, '2024-12-14 09:24:38', '2024-12-14 09:24:38'),
(6, 10, 2, 'RSI20241214163953679', 'SiCepat', 'Koperindag Blok C No 3', '2024-12-14 16:39:53', '2024-12-17 16:39:53', NULL, 'dalam_pengiriman', 20000.00, '2024-12-14 15:39:53', '2024-12-14 15:39:53'),
(7, 11, 2, 'RSI20241214171205490', 'SiCepat', 'Koperindag Blok C No 3', '2024-12-14 17:12:05', '2024-12-17 17:12:05', NULL, 'dalam_pengiriman', 20000.00, '2024-12-14 16:12:05', '2024-12-14 16:12:05'),
(8, 12, 2, 'RSI20241214171356283', 'J&T', 'Koperindag Blok C No 3', '2024-12-14 17:13:56', '2024-12-17 17:13:56', NULL, 'dalam_pengiriman', 20000.00, '2024-12-14 16:13:56', '2024-12-14 16:13:56'),
(9, 13, 2, 'RSI20241214174139284', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-14 17:41:39', '2024-12-17 17:41:39', NULL, 'dalam_pengiriman', 20000.00, '2024-12-14 16:41:39', '2024-12-14 16:41:39'),
(10, 14, 2, 'RSI20241216054504873', 'JNE', 'Koperindag Blok C No 3', '2024-12-16 05:45:04', '2024-12-19 05:45:04', NULL, 'dalam_pengiriman', 20000.00, '2024-12-16 04:45:04', '2024-12-16 04:45:04'),
(11, 15, 2, 'RSI20241216153120290', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-16 15:31:20', '2024-12-19 15:31:20', NULL, 'dalam_pengiriman', 20000.00, '2024-12-16 14:31:20', '2024-12-16 14:31:20'),
(18, 22, 2, 'RSI20241216153714917', 'J&T', 'Koperindag Blok C No 3', '2024-12-16 15:37:14', '2024-12-19 15:37:14', NULL, 'dalam_pengiriman', 20000.00, '2024-12-16 14:37:14', '2024-12-16 14:37:14'),
(34, 38, 2, 'RSI20241216155550771', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-16 15:55:50', '2024-12-19 15:55:50', NULL, 'dalam_pengiriman', 20000.00, '2024-12-16 14:55:50', '2024-12-16 14:55:50'),
(35, 39, 2, 'RSI20241216155604532', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-16 15:56:04', '2024-12-19 15:56:04', NULL, 'dalam_pengiriman', 20000.00, '2024-12-16 14:56:04', '2024-12-16 14:56:04'),
(36, 40, 2, 'RSI20241217102917877', 'J&T', 'Bangkalan', '2024-12-17 10:29:17', '2024-12-20 10:29:17', NULL, 'dalam_pengiriman', 20000.00, '2024-12-17 09:29:17', '2024-12-17 09:29:17'),
(37, 41, 2, 'RSI20241217115453184', 'J&T', 'Koperindag Blok C No 3', '2024-12-17 11:54:53', '2024-12-20 11:54:53', NULL, 'dalam_pengiriman', 20000.00, '2024-12-17 10:54:53', '2024-12-17 10:54:53'),
(38, 42, 2, 'RSI20241218112108107', 'JNE', 'Koperindag Blok C No 3', '2024-12-18 11:21:08', '2024-12-21 11:21:08', NULL, 'dalam_pengiriman', 20000.00, '2024-12-18 10:21:08', '2024-12-18 10:21:08'),
(39, 43, 2, 'RSI20241218112155840', 'J&T', 'Koperindag Blok C No 3', '2024-12-18 11:21:55', '2024-12-21 11:21:55', NULL, 'dalam_pengiriman', 20000.00, '2024-12-18 10:21:55', '2024-12-18 10:21:55'),
(40, 44, 2, 'RSI20241218112717277', 'SiCepat', 'Koperindag Blok C No 3', '2024-12-18 11:27:17', '2024-12-21 11:27:17', NULL, 'dalam_pengiriman', 20000.00, '2024-12-18 10:27:17', '2024-12-18 10:27:17'),
(41, 45, 2, 'RSI20241218114059775', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-18 11:40:59', '2024-12-21 11:40:59', NULL, 'dalam_pengiriman', 20000.00, '2024-12-18 10:40:59', '2024-12-18 10:40:59'),
(42, 46, 2, 'RSI20241218115122358', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-18 11:51:22', '2024-12-21 11:51:22', NULL, 'dalam_pengiriman', 20000.00, '2024-12-18 10:51:22', '2024-12-18 10:51:22'),
(43, 47, 2, 'RSI20241218120110807', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-18 12:01:10', '2024-12-21 12:01:10', NULL, 'dalam_pengiriman', 20000.00, '2024-12-18 11:01:10', '2024-12-18 11:01:10'),
(44, 60, 2, 'RSI20241219164156692', 'JNE', 'Koperindag Blok C No 3', '2024-12-19 16:41:56', '2024-12-22 16:41:56', NULL, 'dalam_pengiriman', 20000.00, '2024-12-19 15:41:56', '2024-12-19 15:41:56'),
(45, 61, 2, 'RSI20241219171402439', 'JNE', 'Koperindag Blok C No 3', '2024-12-19 17:14:02', '2024-12-22 17:14:02', NULL, 'dalam_pengiriman', 20000.00, '2024-12-19 16:14:02', '2024-12-19 16:14:02'),
(46, 62, 2, 'RSI20241219173051286', 'J&T', 'Koperindag Blok C No 3', '2024-12-19 17:30:51', '2024-12-22 17:30:51', NULL, 'dalam_pengiriman', 20000.00, '2024-12-19 16:30:51', '2024-12-19 16:30:51'),
(47, 63, 2, 'RSI20241219174844647', 'SiCepat', 'Koperindag Blok C No 3', '2024-12-19 17:48:44', '2024-12-22 17:48:44', NULL, 'dalam_pengiriman', 20000.00, '2024-12-19 16:48:44', '2024-12-19 16:48:44'),
(48, 64, 2, 'RSI20241219180434933', 'SiCepat', 'Koperindag Blok C No 3', '2024-12-19 18:04:34', '2024-12-22 18:04:34', NULL, 'dalam_pengiriman', 20000.00, '2024-12-19 17:04:34', '2024-12-19 17:04:34'),
(49, 65, 2, 'RSI20241219190122616', 'JNE', 'Koperindag Blok C No 3', '2024-12-19 19:01:22', '2024-12-22 19:01:22', NULL, 'dalam_pengiriman', 20000.00, '2024-12-19 18:01:22', '2024-12-19 18:01:22'),
(50, 66, 2, 'RSI20241219190218138', 'SiCepat', 'Koperindag Blok C No 3', '2024-12-19 19:02:18', '2024-12-22 19:02:18', NULL, 'dalam_pengiriman', 20000.00, '2024-12-19 18:02:18', '2024-12-19 18:02:18'),
(51, 67, 2, 'RSI20241219190307735', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-19 19:03:07', '2024-12-22 19:03:07', NULL, 'dalam_pengiriman', 20000.00, '2024-12-19 18:03:07', '2024-12-19 18:03:07'),
(52, 68, 2, 'RSI20241219192823880', 'SiCepat', 'Koperindag Blok C No 3', '2024-12-19 19:28:23', '2024-12-22 19:28:23', NULL, 'dalam_pengiriman', 20000.00, '2024-12-19 18:28:23', '2024-12-19 18:28:23'),
(53, 69, 2, 'RSI20241219192841846', 'JNE', 'Koperindag Blok C No 3', '2024-12-19 19:28:41', '2024-12-22 19:28:41', NULL, 'dalam_pengiriman', 20000.00, '2024-12-19 18:28:41', '2024-12-19 18:28:41'),
(54, 70, 2, 'RSI20241219192937831', 'JNE', 'Koperindag Blok C No 3', '2024-12-19 19:29:37', '2024-12-22 19:29:37', NULL, 'dalam_pengiriman', 20000.00, '2024-12-19 18:29:37', '2024-12-19 18:29:37'),
(55, 71, 2, 'RSI20241219195528658', 'JNE', 'Koperindag Blok C No 3', '2024-12-19 19:55:28', '2024-12-22 19:55:28', NULL, 'dalam_pengiriman', 20000.00, '2024-12-19 18:55:28', '2024-12-19 18:55:28'),
(56, 72, 2, 'RSI20241219195546102', 'SiCepat', 'Koperindag Blok C No 3', '2024-12-19 19:55:46', '2024-12-22 19:55:46', NULL, 'dalam_pengiriman', 20000.00, '2024-12-19 18:55:46', '2024-12-19 18:55:46'),
(57, 73, 2, 'RSI20241220082007479', 'SiCepat', 'Koperindag Blok C No 3', '2024-12-20 08:20:07', '2024-12-23 08:20:07', NULL, 'dalam_pengiriman', 20000.00, '2024-12-20 07:20:07', '2024-12-20 07:20:07'),
(58, 74, 2, 'RSI20241220082007132', 'SiCepat', 'Koperindag Blok C No 3', '2024-12-20 08:20:07', '2024-12-23 08:20:07', NULL, 'dalam_pengiriman', 20000.00, '2024-12-20 07:20:07', '2024-12-20 07:20:07'),
(59, 75, 2, 'RSI20241220083645984', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-20 08:36:45', '2024-12-23 08:36:45', NULL, 'dalam_pengiriman', 20000.00, '2024-12-20 07:36:45', '2024-12-20 07:36:45'),
(60, 76, 2, 'RSI20241220090709514', 'JNE', 'Koperindag Blok C No 3', '2024-12-20 09:07:09', '2024-12-23 09:07:09', NULL, 'dalam_pengiriman', 20000.00, '2024-12-20 08:07:09', '2024-12-20 08:07:09'),
(61, 77, 2, 'RSI20241220103310613', 'SiCepat', 'Koperindag Blok C No 3', '2024-12-20 10:33:10', '2024-12-23 10:33:10', NULL, 'dalam_pengiriman', 20000.00, '2024-12-20 09:33:10', '2024-12-20 09:33:10'),
(62, 78, 2, 'RSI20241220103339848', 'JNE', 'Koperindag Blok C No 3', '2024-12-20 10:33:39', '2024-12-23 10:33:39', NULL, 'dalam_pengiriman', 20000.00, '2024-12-20 09:33:39', '2024-12-20 09:33:39'),
(63, 79, 2, 'RSI20241220103400203', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-20 10:34:00', '2024-12-23 10:34:00', NULL, 'dalam_pengiriman', 20000.00, '2024-12-20 09:34:00', '2024-12-20 09:34:00'),
(64, 80, 2, 'RSI20241220103431301', 'J&T', 'Koperindag Blok C No 3', '2024-12-20 10:34:31', '2024-12-23 10:34:31', NULL, 'dalam_pengiriman', 20000.00, '2024-12-20 09:34:31', '2024-12-20 09:34:31'),
(65, 81, 2, 'RSI20241220104951590', 'J&T', 'Koperindag Blok C No 3', '2024-12-20 10:49:51', '2024-12-23 10:49:51', NULL, 'dalam_pengiriman', 20000.00, '2024-12-20 09:49:51', '2024-12-20 09:49:51'),
(66, 82, 2, 'RSI20241220105010971', 'JNE', 'Koperindag Blok C No 3', '2024-12-20 10:50:10', '2024-12-23 10:50:10', NULL, 'dalam_pengiriman', 20000.00, '2024-12-20 09:50:10', '2024-12-20 09:50:10'),
(67, 83, 2, 'RSI20241220111359258', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-20 11:13:59', '2024-12-23 11:13:59', NULL, 'dalam_pengiriman', 20000.00, '2024-12-20 10:13:59', '2024-12-20 10:13:59'),
(68, 84, 2, 'RSI20241221054003503', 'JNE', 'Koperindag Blok C No 3', '2024-12-21 05:40:03', '2024-12-24 05:40:03', NULL, 'dalam_pengiriman', 20000.00, '2024-12-21 04:40:03', '2024-12-21 04:40:03'),
(69, 85, 2, 'RSI20241221060945654', 'JNE', 'Koperindag Blok C No 3', '2024-12-21 06:09:45', '2024-12-24 06:09:45', NULL, 'dalam_pengiriman', 20000.00, '2024-12-21 05:09:45', '2024-12-21 05:09:45'),
(70, 86, 2, 'RSI20241221062106395', 'JNE', 'Koperindag Blok C No 3', '2024-12-21 06:21:06', '2024-12-24 06:21:06', NULL, 'dalam_pengiriman', 20000.00, '2024-12-21 05:21:06', '2024-12-21 05:21:06'),
(71, 87, 7, 'RSI20241222153202210', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-22 15:32:02', '2024-12-25 15:32:02', NULL, 'dalam_pengiriman', 20000.00, '2024-12-22 14:32:02', '2024-12-22 14:32:02'),
(72, 88, 7, 'RSI20241222153433974', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-22 15:34:33', '2024-12-25 15:34:33', NULL, 'dalam_pengiriman', 20000.00, '2024-12-22 14:34:33', '2024-12-22 14:34:33'),
(73, 89, 7, 'RSI20241222153433623', 'Pos Indonesia', 'Koperindag Blok C No 3', '2024-12-22 15:34:33', '2024-12-25 15:34:33', NULL, 'dalam_pengiriman', 20000.00, '2024-12-22 14:34:33', '2024-12-22 14:34:33'),
(74, 90, 7, 'RSI20241222160104563', 'J&T', 'Koperindag Blok C No 3', '2024-12-22 16:01:04', '2024-12-25 16:01:04', NULL, 'dalam_pengiriman', 20000.00, '2024-12-22 15:01:04', '2024-12-22 15:01:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(64) NOT NULL,
  `tanggal_pesanan` datetime DEFAULT current_timestamp(),
  `status_pesanan` enum('Menunggu Pembayaran','Diproses','Dikirim','Selesai','Dibatalkan') DEFAULT 'Menunggu Pembayaran',
  `total_harga` decimal(10,2) NOT NULL,
  `notifikasi_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_user`, `id_produk`, `jumlah`, `tanggal_pesanan`, `status_pesanan`, `total_harga`, `notifikasi_status`) VALUES
(6, 2, 2, 2, '2024-12-14 13:31:46', 'Selesai', 12000000.00, 1),
(8, 2, 2, 1, '2024-12-14 14:35:55', 'Selesai', 6000000.00, 1),
(9, 2, 3, 1, '2024-12-14 16:24:38', 'Selesai', 500000.00, 1),
(10, 2, 3, 10, '2024-12-14 22:39:53', 'Selesai', 5000000.00, 1),
(11, 2, 2, 5, '2024-12-14 23:12:05', 'Selesai', 30000000.00, 1),
(12, 2, 2, 1, '2024-12-14 23:13:56', 'Selesai', 6000000.00, 1),
(13, 2, 2, 5, '2024-12-14 23:41:39', 'Selesai', 30000000.00, 1),
(14, 2, 3, 15, '2024-12-16 11:45:00', 'Selesai', 7500000.00, 1),
(15, 2, 2, 1, '2024-12-16 21:31:20', 'Selesai', 6000000.00, 1),
(22, 2, 2, 1, '2024-12-16 21:37:14', 'Selesai', 6000000.00, 1),
(38, 2, 2, 1, '2024-12-16 21:55:50', 'Selesai', 6000000.00, 1),
(39, 2, 2, 1, '2024-12-16 21:56:04', 'Selesai', 6000000.00, 1),
(40, 2, 3, 1, '2024-12-17 16:29:17', 'Dibatalkan', 500000.00, 1),
(41, 2, 2, 1, '2024-12-17 17:54:53', 'Dibatalkan', 6000000.00, 1),
(42, 2, 3, 1, '2024-12-18 17:21:08', 'Dikirim', 520000.00, 1),
(43, 2, 2, 1, '2024-12-18 17:21:55', 'Dikirim', 6020000.00, 1),
(44, 2, 3, 1, '2024-12-18 17:27:16', 'Dikirim', 520000.00, 1),
(45, 2, 2, 1, '2024-12-18 17:40:58', 'Dikirim', 6020000.00, 1),
(46, 2, 3, 1, '2024-12-18 17:51:22', 'Dikirim', 520000.00, 1),
(47, 2, 2, 1, '2024-12-18 18:01:10', 'Dikirim', 6020000.00, 1),
(48, 2, 3, 1, '2024-12-19 19:13:19', 'Diproses', 520000.00, 1),
(49, 2, 3, 1, '2024-12-19 19:37:26', 'Diproses', 520000.00, 1),
(50, 2, 3, 1, '2024-12-19 20:03:45', 'Diproses', 20000.00, 1),
(51, 2, 3, 1, '2024-12-19 20:41:09', 'Diproses', 20001.00, 1),
(52, 2, 3, 1, '2024-12-19 21:40:23', 'Diproses', 520000.00, 1),
(53, 2, 3, 1, '2024-12-19 21:51:12', 'Diproses', 490000.00, 1),
(54, 2, 2, 1, '2024-12-19 22:03:29', 'Diproses', 5720000.00, 1),
(55, 2, 3, 1, '2024-12-19 22:11:55', 'Diproses', 220000.00, 1),
(56, 2, 3, 1, '2024-12-19 22:14:10', 'Selesai', 220000.00, 1),
(60, 2, 3, 1, '2024-12-19 22:41:56', 'Selesai', 220000.00, 1),
(61, 2, 3, 1, '2024-12-19 23:14:00', 'Selesai', 220000.00, 1),
(62, 2, 3, 1, '2024-12-19 23:30:51', 'Diproses', 220000.00, 1),
(63, 2, 3, 1, '2024-12-19 23:48:44', 'Selesai', 220000.00, 1),
(64, 2, 2, 1, '2024-12-20 00:04:34', 'Diproses', 6020000.00, 1),
(65, 2, 3, 1, '2024-12-20 01:01:21', 'Diproses', 520000.00, 1),
(66, 2, 3, 1, '2024-12-20 01:02:18', 'Diproses', 520000.00, 1),
(67, 2, 3, 1, '2024-12-20 01:03:06', 'Diproses', 220000.00, 1),
(68, 2, 3, 1, '2024-12-20 01:28:23', 'Diproses', 220000.00, 1),
(69, 2, 3, 1, '2024-12-20 01:28:40', 'Diproses', 520000.00, 1),
(70, 2, 3, 1, '2024-12-20 01:29:37', 'Diproses', 520000.00, 1),
(71, 2, 3, 1, '2024-12-20 01:55:27', 'Diproses', 220000.00, 1),
(72, 2, 3, 1, '2024-12-20 01:55:45', 'Diproses', 520000.00, 1),
(73, 2, 3, 1, '2024-12-20 14:20:07', 'Diproses', 55520000.00, 1),
(74, 2, 4, 1, '2024-12-20 14:20:07', 'Diproses', 55520000.00, 1),
(75, 2, 3, 1, '2024-12-20 14:36:44', 'Diproses', 520000.00, 1),
(76, 2, 3, 1, '2024-12-20 15:07:09', 'Diproses', 220000.00, 1),
(77, 2, 3, 1, '2024-12-20 16:33:10', 'Dibatalkan', 320000.00, 1),
(78, 2, 3, 1, '2024-12-20 16:33:39', 'Dibatalkan', 520000.00, 1),
(79, 2, 3, 1, '2024-12-20 16:34:00', 'Selesai', 320000.00, 1),
(80, 2, 3, 1, '2024-12-20 16:34:31', 'Dibatalkan', 520000.00, 1),
(81, 2, 3, 1, '2024-12-20 16:49:50', 'Dikirim', 320000.00, 1),
(82, 2, 3, 1, '2024-12-20 16:50:10', 'Selesai', 320000.00, 1),
(83, 2, 3, 1, '2024-12-20 17:13:59', 'Selesai', 520000.00, 1),
(84, 2, 3, 1, '2024-12-21 11:40:03', 'Selesai', 520000.00, 1),
(85, 2, 2, 2, '2024-12-21 12:09:45', 'Selesai', 12020000.00, 1),
(86, 2, 3, 1, '2024-12-21 12:21:06', 'Dibatalkan', 520000.00, 1),
(87, 7, 3, 1, '2024-12-22 21:32:02', 'Selesai', 519950.00, 1),
(88, 7, 3, 1, '2024-12-22 21:34:33', 'Diproses', 1720000.00, 0),
(89, 7, 5, 1, '2024-12-22 21:34:33', 'Diproses', 1720000.00, 0),
(90, 7, 3, 1, '2024-12-22 22:01:04', 'Selesai', 520000.00, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `deskripsi`, `harga`, `stok`, `id_kategori`, `created_at`, `updated_at`, `gambar`) VALUES
(2, 'XBOX Series X', 'Xbox ', 6000000.00, 100, 6, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'product-2.jpg'),
(3, 'Dualshock Controller', 'Controller Dual Shock Terbaru', 500000.00, 49, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'BG LOGIN 3.jpg'),
(4, 'PC Gaming Intel i9 gen 12 + Monitor', 'Pc Gaming spek gahar dan dapat bonus monitor gratis', 55000000.00, 0, 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'profile-img.jpg'),
(5, 'Headset Steelseries', 'Headset Gaming Steelseries', 1200000.00, 59, 3, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '6766b487cc0bf_product-5.jpg'),
(6, 'CONTROLLER + FREE FIGURE', 'Produk Promo', 300000.00, 30, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '6768046350e23_BG LOGIN 3.jpg'),
(7, 'Coba ', 'test', 1000.00, 50, 6, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '676825be062b9_product-2.jpg'),
(8, 'PEES', 'test', 100000.00, 30, 5, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '6768279f47f17_6768046350e23_BG LOGIN 3.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `promo`
--

CREATE TABLE `promo` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `discount_type` enum('fixed','percentage') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `usage_limit` int(11) NOT NULL DEFAULT 1,
  `times_used` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `promo`
--

INSERT INTO `promo` (`id`, `code`, `discount_type`, `discount_value`, `usage_limit`, `times_used`, `created_at`) VALUES
(10, 'IVRKH', 'percentage', 50.00, 0, 20, '2024-12-17 06:17:10'),
(11, 'ESOUK', 'fixed', 15000.00, 0, 20, '2024-12-17 06:17:23'),
(12, 'ACD5Y', 'fixed', 15000.00, 0, 20, '2024-12-17 06:28:02'),
(13, '2HSLK', 'fixed', 300000.00, 105, 45, '2024-12-19 13:19:49'),
(14, '14J60', 'fixed', 200000.00, 0, 50, '2024-12-19 19:25:10'),
(15, 'ITJW8', 'percentage', 50.00, 0, 50, '2024-12-22 12:22:41'),
(16, 'DF918', 'fixed', 500000.00, 50, 0, '2024-12-22 14:45:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `review_produk`
--

CREATE TABLE `review_produk` (
  `id_review` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `komentar` text DEFAULT NULL,
  `komentar_admin` varchar(255) DEFAULT NULL,
  `tanggal_review` datetime DEFAULT current_timestamp(),
  `notifikasi_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `review_produk`
--

INSERT INTO `review_produk` (`id_review`, `id_user`, `id_produk`, `id_pesanan`, `rating`, `komentar`, `komentar_admin`, `tanggal_review`, `notifikasi_status`) VALUES
(3, 2, 2, 8, 5, 'GEGE', NULL, '2024-12-14 15:17:46', 1),
(4, 2, 2, 6, 5, 'BAGUS', NULL, '2024-12-14 15:48:28', 1),
(5, 2, 3, 9, 5, 'Mantap', NULL, '2024-12-14 16:58:03', 1),
(6, 2, 2, 12, 5, 'Sangat Bagus', NULL, '2024-12-14 23:20:30', 1),
(7, 2, 3, 10, 5, 'Sangat Baik', NULL, '2024-12-14 23:32:30', 1),
(8, 2, 2, 11, 3, 'Kualitas Barang Sesuai Harga', NULL, '2024-12-14 23:32:48', 1),
(9, 2, 2, 13, 1, 'Foto produknya jelek', 'Mang Ngapa', '2024-12-16 11:47:47', 1),
(10, 2, 3, 60, 5, 'DISKON', NULL, '2024-12-19 22:48:05', 1),
(11, 2, 3, 61, 5, 'MANTAP', NULL, '2024-12-19 23:37:51', 1),
(12, 2, 3, 63, 5, 'i', NULL, '2024-12-20 00:02:20', 1),
(13, 2, 3, 56, 5, 'tes', NULL, '2024-12-20 00:10:03', 1),
(14, 7, 3, 87, 5, 'test', NULL, '2024-12-22 21:59:18', 1),
(15, 7, 3, 90, 5, 'bagus', NULL, '2024-12-22 22:02:11', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `topik`
--

CREATE TABLE `topik` (
  `id_topik` int(11) NOT NULL,
  `id_komunitas` int(11) NOT NULL,
  `judul_topik` varchar(255) NOT NULL,
  `deskripsi_topik` text NOT NULL,
  `dibuat_oleh` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `topik`
--

INSERT INTO `topik` (`id_topik`, `id_komunitas`, `judul_topik`, `deskripsi_topik`, `dibuat_oleh`, `created_at`) VALUES
(1, 3, 'Saran PC ', 'Butuh PC SPEK GEMING BUDGET 1 JT', 5, '2024-12-18 07:12:12'),
(2, 2, 'Saran Console', 'Saran dong gez', 5, '2024-12-18 10:08:57'),
(3, 1, 'PS 5?', 'Saran PS4 atau PS 5 nih', 5, '2024-12-18 10:45:14'),
(6, 1, 'Bingung', 'PC apa PS?', 2, '2024-12-19 17:41:18'),
(7, 7, 'Game Nintendo Bagus?', 'Kalo ada yang tau game nintendo yang bagus dong tolong beri tahu', 2, '2024-12-21 05:46:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_tlp` varchar(50) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `level` enum('admin','user') NOT NULL,
  `foto` varchar(255) NOT NULL,
  `active` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama`, `email`, `password`, `no_tlp`, `alamat`, `level`, `foto`, `active`) VALUES
(1, 'Rafi Mumtaz Hajjid', 'rafimumtaz86@gmail.com', '123', '081513099954', 'Bekasi City', 'admin', '1734507173_messages-3.jpg', '2025-05-28 07:05:06'),
(2, 'Tria Yunita Krismiyanto', 'triayunita07@gmail.com', '123', '0888883838383', 'Bangkalan Halim Perdana Kusuma 2\r\n', 'user', '1734507541_messages-2.jpg', '2025-05-28 07:07:38'),
(5, 'Tria Krismiyanto Yunita', 'triayunita02@gmail.com', '123', '08587617989', 'Koperindag Blok C No 3', 'user', '', '2024-12-18 13:48:06'),
(6, 'asep prayogi', 'asepyogi@gmail.com', '123', '123456', 'Medan', 'user', '', '2024-12-22 21:47:43'),
(7, 'coba123', 'coba@gmail.com', '123', '123456098', 'coba233', 'user', '1734877511_1734507173_messages-3.jpg', '2024-12-22 22:04:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_promo_codes`
--

CREATE TABLE `user_promo_codes` (
  `id_user_promo_code` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `promo_id` int(11) NOT NULL,
  `promo_code` varchar(255) NOT NULL,
  `times_used` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `wishlist`
--

CREATE TABLE `wishlist` (
  `id_wishlist` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `notifikasi_restock` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `wishlist`
--

INSERT INTO `wishlist` (`id_wishlist`, `user_id`, `id_produk`, `created_at`, `updated_at`, `notifikasi_restock`) VALUES
(6, 2, 2, '2024-12-21 05:34:00', '2024-12-21 05:35:58', 1),
(7, 7, 3, '2024-12-22 14:41:30', '2024-12-22 14:42:31', 1);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `informasipromo`
--
ALTER TABLE `informasipromo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id_keranjang`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `komentar`
--
ALTER TABLE `komentar`
  ADD PRIMARY KEY (`id_komentar`),
  ADD KEY `id_topik` (`id_topik`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `komunitas`
--
ALTER TABLE `komunitas`
  ADD PRIMARY KEY (`id_komunitas`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indeks untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pembatalan_pesanan`
--
ALTER TABLE `pembatalan_pesanan`
  ADD PRIMARY KEY (`id_pembatalan`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indeks untuk tabel `pengiriman_pesanan`
--
ALTER TABLE `pengiriman_pesanan`
  ADD PRIMARY KEY (`id_pengiriman`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`) USING BTREE,
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_user` (`id_user`) USING BTREE;

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indeks untuk tabel `review_produk`
--
ALTER TABLE `review_produk`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indeks untuk tabel `topik`
--
ALTER TABLE `topik`
  ADD PRIMARY KEY (`id_topik`),
  ADD KEY `id_komunitas` (`id_komunitas`),
  ADD KEY `dibuat_oleh` (`dibuat_oleh`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `user_promo_codes`
--
ALTER TABLE `user_promo_codes`
  ADD PRIMARY KEY (`id_user_promo_code`),
  ADD UNIQUE KEY `promo_code` (`promo_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `promo_id` (`promo_id`);

--
-- Indeks untuk tabel `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id_wishlist`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `id_produk` (`id_produk`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(64) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `informasipromo`
--
ALTER TABLE `informasipromo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id_keranjang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT untuk tabel `komentar`
--
ALTER TABLE `komentar`
  MODIFY `id_komentar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `komunitas`
--
ALTER TABLE `komunitas`
  MODIFY `id_komunitas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=410;

--
-- AUTO_INCREMENT untuk tabel `pembatalan_pesanan`
--
ALTER TABLE `pembatalan_pesanan`
  MODIFY `id_pembatalan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT untuk tabel `pengiriman_pesanan`
--
ALTER TABLE `pengiriman_pesanan`
  MODIFY `id_pengiriman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `promo`
--
ALTER TABLE `promo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `review_produk`
--
ALTER TABLE `review_produk`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `topik`
--
ALTER TABLE `topik`
  MODIFY `id_topik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `user_promo_codes`
--
ALTER TABLE `user_promo_codes`
  MODIFY `id_user_promo_code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id_wishlist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`),
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `komunitas`
--
ALTER TABLE `komunitas`
  ADD CONSTRAINT `komunitas_ibfk_1` FOREIGN KEY (`dibuat_oleh`) REFERENCES `user` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `pembatalan_pesanan`
--
ALTER TABLE `pembatalan_pesanan`
  ADD CONSTRAINT `pembatalan_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`),
  ADD CONSTRAINT `pembatalan_pesanan_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengiriman_pesanan`
--
ALTER TABLE `pengiriman_pesanan`
  ADD CONSTRAINT `pengiriman_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pengiriman_pesanan_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);

--
-- Ketidakleluasaan untuk tabel `review_produk`
--
ALTER TABLE `review_produk`
  ADD CONSTRAINT `review_produk_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `review_produk_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Ketidakleluasaan untuk tabel `topik`
--
ALTER TABLE `topik`
  ADD CONSTRAINT `topik_ibfk_1` FOREIGN KEY (`id_komunitas`) REFERENCES `komunitas` (`id_komunitas`),
  ADD CONSTRAINT `topik_ibfk_2` FOREIGN KEY (`dibuat_oleh`) REFERENCES `user` (`id_user`);

--
-- Ketidakleluasaan untuk tabel `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
