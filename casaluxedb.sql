-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 18 Jun 2025 pada 08.32
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
-- Database: `casaluxedb`
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
(44, 0, 'PC DENGAN BONUS MONITOR!!!', 'Beli PC dapat monitor gratis? kapan lagi? AYO SEGERA BELI SEKARANG!!!!', '../uploads/BG LOGIN 3.jpg', NULL, 'bonus', '2025-06-04', '2025-12-05', 0, 4, '2024-12-20 07:03:43'),
(45, 0, 'Diskon Natal', 'Mendekati Natal Ada Diskon', '../uploads/profile-img.jpg', NULL, 'discount', '2025-06-03', '2025-12-31', 20, 0, '2024-12-21 03:05:17'),
(48, 0, 'DISKON 90%', 'diskon 90% untuk membeli apapun yang anda inginkan', '../uploads/barang A.png', NULL, 'discount', '2025-06-13', '2025-06-20', 90, 0, '2025-06-13 07:33:10');

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
(7, 'Alat Makan', '', '2025-06-13 14:35:33'),
(8, 'Kursi', '', '2025-06-13 14:35:39'),
(9, 'Meja', '', '2025-06-13 14:35:47'),
(10, 'Aksesoris ', '', '2025-06-13 14:35:54'),
(11, 'Alat Rumah Tangga', '', '2025-06-13 14:36:05');

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
(21, 7, 'admin', 'iya ada apa?', '2024-12-22 14:40:57'),
(22, 2, 'user', 'tes', '2025-06-13 11:38:14'),
(23, 2, 'user', 'tes', '2025-06-13 11:38:17'),
(24, 2, 'user', 'tes', '2025-06-13 11:42:56'),
(25, 2, 'user', 'tes', '2025-06-13 11:43:01'),
(26, 2, 'user', 'haloo', '2025-06-13 11:43:09'),
(27, 2, 'user', 'test', '2025-06-13 11:43:15'),
(28, 2, 'user', 'tes', '2025-06-13 11:46:02'),
(29, 2, 'user', 'assalamualaikum masbro', '2025-06-13 11:46:10'),
(30, 2, 'user', 'lah', '2025-06-13 11:46:18'),
(31, 2, 'admin', 'kenapa yaa??', '2025-06-13 12:05:17'),
(32, 2, 'admin', 'ada yang bisa dibantu?', '2025-06-13 12:05:24'),
(33, 5, 'admin', 'hai juga', '2025-06-13 12:05:38'),
(34, 7, 'admin', 'bantu apa kak?', '2025-06-13 12:05:47');

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
(409, 7, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 1.720.000', NULL, '2025-05-27 23:20:49', 0),
(410, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 122.000', NULL, '2025-06-09 06:22:49', 0),
(412, NULL, 8, 'admin', 'Review Baru', 'Review baru dari Tria Yunita Krismiyanto untuk produk PEES \r\n            dengan rating /5', NULL, '2025-06-09 06:22:50', 0),
(413, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 122.000', NULL, '2025-06-09 06:23:41', 0),
(415, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 122.000', NULL, '2025-06-09 06:23:53', 0),
(417, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 122.000', NULL, '2025-06-09 06:23:57', 0),
(419, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 122.000', NULL, '2025-06-09 06:23:57', 0),
(421, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 122.000', NULL, '2025-06-09 06:23:58', 0),
(423, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 122.000', NULL, '2025-06-09 06:24:01', 0),
(425, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 122.000', NULL, '2025-06-09 06:24:03', 0),
(428, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2025-06-09 07:06:45', 0),
(430, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2025-06-09 07:06:53', 0),
(432, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2025-06-09 07:06:53', 0),
(434, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2025-06-09 07:06:57', 0),
(436, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2025-06-09 07:06:58', 0),
(438, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2025-06-13 09:33:29', 0),
(440, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.760.000', NULL, '2025-06-13 09:33:30', 0),
(442, NULL, 7, 'admin', 'Review Baru', 'Review baru dari Tria Yunita Krismiyanto untuk produk Meja Belajar Mini Kayu \r\n            dengan rating 5/5', NULL, '2025-06-13 09:33:30', 0),
(443, NULL, 8, 'admin', 'Review Baru', 'Review baru dari Tria Yunita Krismiyanto untuk produk Meja Belajar Retractable \r\n            dengan rating 2/5', NULL, '2025-06-13 09:33:30', 0),
(445, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2025-06-13 09:58:07', 0),
(447, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.760.000', NULL, '2025-06-13 09:58:08', 0),
(449, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 935.000', NULL, '2025-06-13 09:58:08', 0),
(451, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2025-06-13 10:16:12', 0),
(453, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.760.000', NULL, '2025-06-13 10:16:12', 0),
(455, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 935.000', NULL, '2025-06-13 10:16:13', 0),
(457, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 540.000', NULL, '2025-06-13 10:16:13', 0),
(459, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.810.000', NULL, '2025-06-13 10:16:13', 0),
(461, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2025-06-13 10:16:21', 0),
(463, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.760.000', NULL, '2025-06-13 10:16:21', 0),
(465, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 935.000', NULL, '2025-06-13 10:16:21', 0),
(467, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 540.000', NULL, '2025-06-13 10:16:21', 0),
(469, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.810.000', NULL, '2025-06-13 10:16:22', 0),
(471, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2025-06-13 10:16:22', 0),
(473, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.760.000', NULL, '2025-06-13 10:16:22', 0),
(475, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 935.000', NULL, '2025-06-13 10:16:22', 0),
(477, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 540.000', NULL, '2025-06-13 10:16:22', 0),
(479, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.810.000', NULL, '2025-06-13 10:16:22', 0),
(481, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2025-06-13 10:22:57', 0),
(483, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.760.000', NULL, '2025-06-13 10:22:57', 0),
(485, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 935.000', NULL, '2025-06-13 10:22:58', 0),
(487, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 540.000', NULL, '2025-06-13 10:22:58', 0),
(489, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.810.000', NULL, '2025-06-13 10:22:58', 0),
(492, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2025-06-13 10:23:14', 0),
(494, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.760.000', NULL, '2025-06-13 10:23:14', 0),
(496, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 935.000', NULL, '2025-06-13 10:23:14', 0),
(498, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 540.000', NULL, '2025-06-13 10:23:14', 0),
(500, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.810.000', NULL, '2025-06-13 10:23:14', 0),
(503, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2025-06-13 10:23:17', 0),
(505, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.760.000', NULL, '2025-06-13 10:23:19', 0),
(507, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 935.000', NULL, '2025-06-13 10:23:19', 0),
(509, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 540.000', NULL, '2025-06-13 10:23:19', 0),
(511, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.810.000', NULL, '2025-06-13 10:23:19', 0),
(514, 2, NULL, 'user', 'Promo Tersedia', 'Promo DISKON 90% sedang berlangsung! Diskon hingga 90%. diskon 90% untuk membeli apapun yang anda inginkan', NULL, '2025-06-13 10:23:20', 0),
(515, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2025-06-13 10:23:20', 0),
(516, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 6.020.000', NULL, '2025-06-13 10:23:20', 0),
(517, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.760.000', NULL, '2025-06-13 10:23:20', 0),
(518, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 1.760.000', NULL, '2025-06-13 10:23:20', 0),
(519, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 935.000', NULL, '2025-06-13 10:23:20', 0),
(520, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 935.000', NULL, '2025-06-13 10:23:20', 0),
(521, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 540.000', NULL, '2025-06-13 10:23:20', 0),
(522, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 540.000', NULL, '2025-06-13 10:23:20', 0),
(523, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 1.810.000', NULL, '2025-06-13 10:23:20', 0),
(524, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 1.810.000', NULL, '2025-06-13 10:23:20', 0),
(525, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan #23 sedang dalam pengiriman. Mohon tunggu sampai pesanan tiba.', NULL, '2025-06-13 10:23:20', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `created_at`) VALUES
(1, 'firmansyahhaikal86@gmail.com', '81d49ff7842f4a7278a9dd27c5d0aed1b7f1b9d5add0716e1e690ff1bc76d637', '2025-06-15 03:54:45', '2025-06-15 00:54:45');

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
(1, 21, 1, 'berubah_pikiran', 'blablabla', '2025-06-17 18:14:15', NULL, '2025-06-17 16:14:15'),
(2, 22, 1, 'harga_lebih_murah', 'wkwkwkwk', '2025-06-18 07:21:39', NULL, '2025-06-18 05:21:39'),
(3, 24, 1, 'barang_salah', 'hehehe', '2025-06-18 08:00:34', NULL, '2025-06-18 06:00:34');

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
(9, 19, 'Transfer Bank', 'Dibayar', '2025-06-13 16:32:59'),
(10, 20, 'Transfer Bank', 'Dibayar', '2025-06-13 16:54:32'),
(11, 21, 'Transfer Bank', 'Dibayar', '2025-06-13 17:09:45'),
(12, 22, 'Transfer Bank', 'Dibayar', '2025-06-13 17:11:14'),
(13, 23, 'Transfer Bank', 'Dibayar', '2025-06-13 17:17:10'),
(14, 24, 'Transfer Bank', 'Dibayar', '2025-06-13 17:35:57');

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
(9, 19, 2, 'RSI20250613113259714', 'J&T', 'Bangkalan Halim Perdana Kusuma 2\r\n', '2025-06-13 16:32:59', '2025-06-16 16:32:59', NULL, 'dalam_pengiriman', 160000.00, '2025-06-13 09:32:59', '2025-06-13 09:32:59'),
(10, 20, 2, 'RSI20250613115432815', 'Pos Indonesia', 'Bangkalan Halim Perdana Kusuma 2\r\n', '2025-06-13 16:54:32', '2025-06-16 16:54:32', NULL, 'dalam_pengiriman', 85000.00, '2025-06-13 09:54:32', '2025-06-13 09:54:32'),
(11, 21, 2, 'RSI20250613120945721', 'Pos Indonesia', 'Bangkalan Halim Perdana Kusuma 2\r\n', '2025-06-13 17:09:45', '2025-06-16 17:09:45', NULL, 'dalam_pengiriman', 90000.00, '2025-06-13 10:09:45', '2025-06-13 10:09:45'),
(12, 22, 2, 'RSI20250613121114157', 'J&T', 'Bangkalan Halim Perdana Kusuma 2\r\n', '2025-06-13 17:11:14', '2025-06-16 17:11:14', NULL, 'dalam_pengiriman', 210000.00, '2025-06-13 10:11:14', '2025-06-13 10:11:14'),
(13, 23, 2, 'RSI20250613121710841', 'SiCepat', 'Bangkalan Halim Perdana Kusuma 2\r\n', '2025-06-13 17:17:10', '2025-06-16 17:17:10', NULL, 'dalam_pengiriman', 5000.00, '2025-06-13 10:17:10', '2025-06-13 10:17:10'),
(14, 24, 2, 'RSI20250613123557574', 'J&T', 'Bangkalan Halim Perdana Kusuma 2\r\n', '2025-06-13 17:35:57', '2025-06-16 17:35:57', NULL, 'dalam_pengiriman', 20000.00, '2025-06-13 10:35:57', '2025-06-13 10:35:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `tanggal_pesanan` date DEFAULT current_timestamp(),
  `status_pesanan` enum('Menunggu Pembayaran','Diproses','Dikirim','Selesai','Dibatalkan') DEFAULT 'Menunggu Pembayaran',
  `total_harga` decimal(10,2) NOT NULL,
  `sudah_dinilai` int(11) DEFAULT NULL,
  `notifikasi_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_user`, `tanggal_pesanan`, `status_pesanan`, `total_harga`, `sudah_dinilai`, `notifikasi_status`) VALUES
(19, 2, '2025-06-13', 'Diproses', 1760000.00, NULL, 0),
(20, 2, '2025-06-13', 'Selesai', 935000.00, NULL, 0),
(21, 2, '2025-06-13', 'Dikirim', 540000.00, NULL, 0),
(22, 2, '2025-06-13', 'Diproses', 1810000.00, NULL, 0),
(23, 2, '2025-06-13', 'Selesai', 30000.00, 1, 0),
(24, 2, '2025-06-13', 'Dibatalkan', 120000.00, NULL, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan_detail`
--

CREATE TABLE `pesanan_detail` (
  `id_pesanan_detail` int(11) NOT NULL,
  `id_produk` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `subtotal` int(11) DEFAULT NULL,
  `id_pesanan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesanan_detail`
--

INSERT INTO `pesanan_detail` (`id_pesanan_detail`, `id_produk`, `jumlah`, `subtotal`, `id_pesanan`) VALUES
(14, 2, 2, 1200000, 19),
(15, 9, 2, 400000, 19),
(16, 9, 1, 200000, 20),
(17, 12, 1, 50000, 20),
(18, 2, 1, 600000, 20),
(19, 2, 1, 600000, 21),
(20, 3, 1, 300000, 21),
(21, 2, 1, 600000, 22),
(22, 3, 1, 300000, 22),
(23, 8, 1, 1200000, 22),
(24, 12, 1, 50000, 23),
(25, 9, 1, 200000, 24);

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
(2, 'Kursi Sofa Pink Empuk', 'Sofa Mini Pink Empuk Lucu dan nyaman dengan cooling foam', 600000.00, 95, 8, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'barang A.png'),
(3, 'Kursi Kantor Pink', 'Kursi kantor empuk pink dengan design ergonomis', 300000.00, 98, 8, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'barang B.png'),
(5, 'Kursi Kantor Hitam', 'Kursi Kantor Plastik Hitam Design Ergonomis', 300000.00, 100, 8, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'barang C.png'),
(7, 'Meja Belajar Mini Kayu', 'meja belajar kayu berbentuk mini design minimalis', 700000.00, 200, 9, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'barang D.png'),
(8, 'Meja Belajar Retractable', 'Meja Kerja Dengan Fitur Retractable', 1200000.00, 199, 9, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 'barang E.png'),
(9, 'Toples Kaca Imut', 'Toples kaca serbaguna dengan bentuk lucu', 200000.00, 296, 7, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '684bd6eb2c152_barang F.png'),
(10, 'Kotak Pink', 'Kotak Pink serbaguna untuk menaruh alat tulis', 75000.00, 500, 10, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '684bd75044aa3_barang G.png'),
(11, 'Kursi Sofa Orange Empuk', 'Kursi Sofa Orange Empuk dengan teknologi cooling foam', 500000.00, 150, 8, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '684bd790a19c0_barang H.png'),
(12, 'Toples Kaca', 'Toples Kaca Serbaguna', 50000.00, 298, 7, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '684bd8744531d_barang I.png'),
(13, 'Gantungan Serbaguna', 'Gantungan pakaian serbaguna yang bisa muat banyak pakaian', 20000.00, 1000, 11, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '684bd89c59059_barang J.png');

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
(10, 'IVRKH', 'percentage', 50.00, 996, 25, '2024-12-17 06:17:10'),
(11, 'ESOUK', 'fixed', 15000.00, 0, 20, '2024-12-17 06:17:23'),
(12, 'ACD5Y', 'fixed', 15000.00, 0, 20, '2024-12-17 06:28:02'),
(13, '2HSLK', 'fixed', 300000.00, 104, 46, '2024-12-19 13:19:49'),
(14, '14J60', 'fixed', 200000.00, 0, 50, '2024-12-19 19:25:10'),
(15, 'ITJW8', 'percentage', 50.00, 0, 50, '2024-12-22 12:22:41'),
(16, 'DF918', 'fixed', 500000.00, 49, 1, '2024-12-22 14:45:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `review_produk`
--

CREATE TABLE `review_produk` (
  `id_review` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `rating_produk` int(11) NOT NULL,
  `rating_pelayanan` int(11) NOT NULL,
  `rating_pengiriman` int(11) NOT NULL,
  `komentar` text DEFAULT NULL,
  `komentar_admin` varchar(255) DEFAULT NULL,
  `tanggal_review` datetime DEFAULT current_timestamp(),
  `notifikasi_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `review_produk`
--

INSERT INTO `review_produk` (`id_review`, `id_user`, `id_produk`, `id_pesanan`, `rating_produk`, `rating_pelayanan`, `rating_pengiriman`, `komentar`, `komentar_admin`, `tanggal_review`, `notifikasi_status`) VALUES
(1, 2, 8, 1, 1, 1, 1, 'wkwkwkwk', 'ada masalah apa ya kak? bisa chat sama customer service yaa kalau ada masalah', '2025-06-08 18:15:32', 1),
(2, 2, 7, 17, 5, 5, 5, 'keren banget ini barangnya asli', 'terimakasihh, silahkan berbelanja lagi', '2025-06-09 14:03:35', 1),
(3, 2, 8, 17, 2, 5, 4, 'dapet barang cacat pabrik payahhh', 'Maaf ya nanti bisa kita ganti kak kalau mau', '2025-06-09 14:03:35', 1),
(4, 2, 12, 23, 5, 5, 5, 'HEBAT', 'makasih yah', '2025-06-13 17:57:00', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `status_pembatalan`
--

CREATE TABLE `status_pembatalan` (
  `id_status` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `status_pembatalan` enum('Pending','Disetujui','Ditolak') DEFAULT 'Pending',
  `alasan` text DEFAULT NULL,
  `tanggal_request` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `status_pembatalan`
--

INSERT INTO `status_pembatalan` (`id_status`, `id_pesanan`, `status_pembatalan`, `alasan`, `tanggal_request`, `tanggal_update`) VALUES
(1, 22, 'Pending', 'Ingin membatalkan pesanan karena salah pesan', '2025-06-18 05:58:21', '2025-06-18 05:58:21'),
(2, 19, 'Pending', 'berubah_pikiran', '2025-06-18 06:27:45', '2025-06-18 06:27:45');

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
  `level` enum('admin','user','cs') NOT NULL,
  `foto` varchar(255) NOT NULL,
  `active` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `nama`, `email`, `password`, `no_tlp`, `alamat`, `level`, `foto`, `active`) VALUES
(1, 'Rafi Mumtaz Hajjid', 'rafimumtaz86@gmail.com', '123', '+6285648475948', 'Bekasi City', 'admin', '1734507173_messages-3.jpg', '2025-06-18 12:56:04'),
(2, 'Tria Yunita Krismiyanto', 'triayunita07@gmail.com', '123', '+6285648475948', 'Bangkalan Halim Perdana Kusuma 2\r\n', 'user', '1734507541_messages-2.jpg', '2025-06-18 10:36:21'),
(5, 'Tria Krismiyanto Yunita', 'triayunita02@gmail.com', '123', '+6282132690717', 'Koperindag Blok C No 3', 'user', '', '2024-12-18 13:48:06'),
(6, 'asep prayogi', 'asepyogi@gmail.com', '123', '+6282132690717', 'Medan', 'user', '', '2024-12-22 21:47:43'),
(7, 'coba123', 'coba@gmail.com', '123', '+6282132690717', 'coba233', 'user', '1734877511_1734507173_messages-3.jpg', '2024-12-22 22:04:01'),
(8, 'Customer Service', 'cs@gmail.com', '123', '+6282132690717', 'Koperindag Blok C No 3', 'cs', '../uploads/profile-img.jpg', '2025-06-13 19:02:34');

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

--
-- Dumping data untuk tabel `user_promo_codes`
--

INSERT INTO `user_promo_codes` (`id_user_promo_code`, `user_id`, `promo_id`, `promo_code`, `times_used`, `created_at`, `expires_at`, `is_active`) VALUES
(17, 2, 10, 'IVRKH', 1, '2025-06-17 15:49:35', NULL, 1);

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
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
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
  ADD KEY `id_user` (`id_user`) USING BTREE;

--
-- Indeks untuk tabel `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  ADD PRIMARY KEY (`id_pesanan_detail`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_pesanan` (`id_pesanan`);

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
-- Indeks untuk tabel `status_pembatalan`
--
ALTER TABLE `status_pembatalan`
  ADD PRIMARY KEY (`id_status`),
  ADD UNIQUE KEY `unique_pesanan` (`id_pesanan`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id_keranjang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

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
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=526;

--
-- AUTO_INCREMENT untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pembatalan_pesanan`
--
ALTER TABLE `pembatalan_pesanan`
  MODIFY `id_pembatalan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `pengiriman_pesanan`
--
ALTER TABLE `pengiriman_pesanan`
  MODIFY `id_pengiriman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  MODIFY `id_pesanan_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `promo`
--
ALTER TABLE `promo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `review_produk`
--
ALTER TABLE `review_produk`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `status_pembatalan`
--
ALTER TABLE `status_pembatalan`
  MODIFY `id_status` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `topik`
--
ALTER TABLE `topik`
  MODIFY `id_topik` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `user_promo_codes`
--
ALTER TABLE `user_promo_codes`
  MODIFY `id_user_promo_code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id_wishlist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ketidakleluasaan untuk tabel `pesanan_detail`
--
ALTER TABLE `pesanan_detail`
  ADD CONSTRAINT `pesanan_detail_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`),
  ADD CONSTRAINT `pesanan_detail_ibfk_2` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`);

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
-- Ketidakleluasaan untuk tabel `status_pembatalan`
--
ALTER TABLE `status_pembatalan`
  ADD CONSTRAINT `status_pembatalan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE;

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
