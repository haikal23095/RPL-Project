-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 28, 2025 at 01:46 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

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
-- Table structure for table `informasipromo`
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
-- Dumping data for table `informasipromo`
--

INSERT INTO `informasipromo` (`id`, `id_produk`, `title`, `description`, `photo_url`, `photo`, `promo_type`, `start_date`, `end_date`, `discount_percentage`, `bonus_item`, `created_at`) VALUES
(44, 0, 'PC DENGAN BONUS MONITOR!!!', 'Beli PC dapat monitor gratis? kapan lagi? AYO SEGERA BELI SEKARANG!!!!', '../uploads/fotosiswa.jpeg', NULL, 'bonus', '2024-12-20', '2024-12-27', 0, 4, '2024-12-20 07:03:43');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi`, `created_at`) VALUES
(1, 'CONTROLER', '', '2024-11-11 21:37:50'),
(2, 'PC', '', '2024-11-11 21:38:00'),
(3, 'AKSESORIS', '', '2024-11-11 21:38:01'),
(4, 'GAMING', '', '2024-11-11 21:38:03'),
(5, 'CONSOLE', '', '2024-12-09 13:29:15');

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
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
-- Dumping data for table `keranjang`
--

INSERT INTO `keranjang` (`id_keranjang`, `user_id`, `id_produk`, `jumlah`, `created_at`, `updated_at`) VALUES
(60, 2, 3, 1, '2025-05-27 11:27:23', '2025-05-27 11:27:23');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id_message` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender` enum('admin','user') NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
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
(19, 2, 'admin', 'Iya ada apa ya? apakah ada yang bisa kami bantu?', '2024-12-15 08:30:04');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
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
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `id_user`, `id_produk`, `type`, `title`, `message`, `image`, `created_at`, `is_read`) VALUES
(155, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan Anda sudah dikirim. Terima kasih!', NULL, '2024-12-14 15:40:25', 1),
(156, 2, NULL, 'user', 'Pesanan Selesai', 'Pesanan Anda telah selesai. Terima kasih!', NULL, '2024-12-14 16:20:40', 1),
(157, 2, NULL, 'user', 'Pesanan Selesai', 'Pesanan Anda telah selesai. Terima kasih!', NULL, '2024-12-14 16:20:41', 1),
(159, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan Anda sudah dikirim. Terima kasih!', NULL, '2024-12-15 08:05:34', 1),
(163, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan Anda sudah dikirim. Terima kasih!', NULL, '2024-12-16 04:45:50', 1),
(165, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan Anda sudah dikirim. Terima kasih!', NULL, '2024-12-17 06:35:55', 1),
(166, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan Anda sudah dikirim. Terima kasih!', NULL, '2024-12-17 06:35:55', 1),
(167, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan Anda sudah dikirim. Terima kasih!', NULL, '2024-12-17 06:35:55', 1),
(168, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan Anda sudah dikirim. Terima kasih!', NULL, '2024-12-17 06:35:55', 1),
(169, 2, NULL, 'user', 'Promo Tersedia', 'Promo test sedang berlangsung! Diskon hingga 20%. test', NULL, '2024-12-17 10:34:08', 1),
(170, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 5.720.000', NULL, '2024-12-19 15:09:51', 1),
(171, NULL, 2, 'admin', 'Stok Produk Rendah', 'Produk XBOX Series X tersisa 1 unit', NULL, '2024-12-19 15:11:22', 1),
(172, NULL, 3, 'admin', 'Review Baru', 'Review baru dari Tria Yunita Krismiyanto untuk produk Dualshock Controller dengan rating 5/5', NULL, '2024-12-19 15:48:20', 0),
(173, 2, NULL, 'user', 'Pesanan Selesai', 'Pesanan Anda telah selesai. Terima kasih!', NULL, '2024-12-19 16:21:08', 1),
(174, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 220.000', NULL, '2024-12-19 16:38:45', 0),
(175, NULL, 3, 'admin', 'Review Baru', 'Review baru dari 2 untuk produk Dualshock Controller \r\n            dengan rating 5/5', NULL, '2024-12-19 16:38:45', 0),
(176, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 220.000', NULL, '2024-12-19 16:51:02', 0),
(177, NULL, 3, 'admin', 'Review Baru', 'Review baru dari 2 untuk produk Dualshock Controller \r\n            dengan rating 5/5', NULL, '2024-12-19 17:02:34', 0),
(178, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 6.020.000', NULL, '2024-12-19 17:05:27', 0),
(179, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 6.020.000', NULL, '2024-12-19 17:05:27', 1),
(180, NULL, 3, 'admin', 'Review Baru', 'Review baru dari Tria Yunita Krismiyanto untuk produk Dualshock Controller \r\n            dengan rating 5/5', NULL, '2024-12-19 17:10:26', 0),
(181, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-19 18:57:24', 0),
(182, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-19 18:57:24', 1),
(183, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-19 18:57:24', 0),
(184, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-19 18:57:24', 1),
(185, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 220.000', NULL, '2024-12-19 18:57:25', 0),
(186, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 220.000', NULL, '2024-12-19 18:57:25', 1),
(187, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 220.000', NULL, '2024-12-19 18:57:25', 0),
(188, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 220.000', NULL, '2024-12-19 18:57:25', 1),
(189, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-19 18:57:25', 0),
(190, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-19 18:57:25', 1),
(191, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-19 18:57:25', 0),
(192, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-19 18:57:25', 1),
(193, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 220.000', NULL, '2024-12-19 18:57:25', 0),
(194, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 220.000', NULL, '2024-12-19 18:57:25', 1),
(195, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-19 18:57:26', 0),
(196, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-19 18:57:26', 1),
(197, 2, NULL, 'user', 'Promo Tersedia', 'Promo Promo Binus sedang berlangsung! Dapatkan bonus Test. Bonus Promo Baru', NULL, '2024-12-20 06:41:04', 1),
(198, 2, NULL, 'user', 'Promo Tersedia', 'Promo PC DENGAN BONUS MONITOR!!! sedang berlangsung! Dapatkan bonus 4. Beli PC dapat monitor gratis? kapan lagi? AYO SEGERA BELI SEKARANG!!!!', NULL, '2024-12-20 07:12:19', 1),
(199, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 55.520.000', NULL, '2024-12-20 07:42:23', 0),
(200, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 55.520.000', NULL, '2024-12-20 07:42:24', 0),
(201, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 55.520.000', NULL, '2024-12-20 07:42:24', 0),
(202, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 55.520.000', NULL, '2024-12-20 07:42:24', 0),
(203, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-20 07:42:24', 0),
(204, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-20 07:42:24', 0),
(205, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 220.000', NULL, '2024-12-20 09:50:33', 0),
(206, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 220.000', NULL, '2024-12-20 09:50:33', 0),
(207, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 320.000', NULL, '2024-12-20 09:50:33', 0),
(208, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 320.000', NULL, '2024-12-20 09:50:34', 0),
(209, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-20 09:50:34', 0),
(210, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-20 09:50:34', 0),
(211, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 320.000', NULL, '2024-12-20 09:50:34', 0),
(212, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 320.000', NULL, '2024-12-20 09:50:34', 0),
(213, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 520.000', NULL, '2024-12-20 09:50:34', 0),
(214, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 520.000', NULL, '2024-12-20 09:50:34', 0),
(215, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 320.000', NULL, '2024-12-20 09:50:34', 0),
(216, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 320.000', NULL, '2024-12-20 09:50:34', 0),
(217, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 320.000', NULL, '2024-12-20 09:50:35', 0),
(218, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 320.000', NULL, '2024-12-20 09:50:35', 0),
(219, NULL, NULL, 'admin', 'Pesanan Baru', 'Pesanan baru dari Tria Yunita Krismiyanto dengan total Rp 220.000', NULL, '2025-05-25 07:13:42', 0),
(220, 2, NULL, 'user', 'Pesanan Diterima', 'Pesanan Anda telah kami terima dan sedang diproses. Total pembayaran: Rp 220.000', NULL, '2025-05-25 07:13:42', 0),
(221, 2, NULL, 'user', 'Pesanan Dikirim', 'Pesanan #83 sedang dalam pengiriman. Mohon tunggu sampai pesanan tiba.', NULL, '2025-05-25 07:14:05', 0),
(222, 2, NULL, 'user', 'Pesanan Selesai', 'Pesanan #83 telah selesai. Terima kasih telah berbelanja!', NULL, '2025-05-25 07:14:34', 0),
(223, NULL, 2, 'admin', 'Review Baru', 'Review baru dari Tria Yunita Krismiyanto untuk produk XBOX Series X \r\n            dengan rating 5/5', NULL, '2025-05-26 06:01:45', 0),
(224, NULL, 3, 'admin', 'Review Baru', 'Review baru dari Tria Yunita Krismiyanto untuk produk Dualshock Controller \r\n            dengan rating 2/5', NULL, '2025-05-26 06:01:45', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pembatalan_pesanan`
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
-- Dumping data for table `pembatalan_pesanan`
--

INSERT INTO `pembatalan_pesanan` (`id_pembatalan`, `id_pesanan`, `id_user`, `alasan_pembatalan`, `deskripsi_pembatalan`, `tanggal_pembatalan`, `catatan_admin`, `dibuat_pada`) VALUES
(2, 41, 1, 'harga_lebih_murah', 'test', '2024-12-17 12:04:50', NULL, '2024-12-17 11:04:50'),
(3, 41, 1, 'harga_lebih_murah', 'tes', '2024-12-17 12:05:41', NULL, '2024-12-17 11:05:41');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `status_pembayaran` enum('Belum Dibayar','Dibayar') DEFAULT 'Belum Dibayar',
  `tanggal_pembayaran` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembayaran`
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
(70, 83, 'Transfer Bank', 'Dibayar', '2025-05-25 14:13:28');

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman_pesanan`
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
-- Dumping data for table `pengiriman_pesanan`
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
(67, 83, 2, 'RSI20250525091328769', 'SiCepat', 'Koperindag Blok C No 3', '2025-05-25 09:13:28', '2025-05-28 09:13:28', NULL, 'dalam_pengiriman', 20000.00, '2025-05-25 07:13:28', '2025-05-25 07:13:28');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
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
-- Dumping data for table `pesanan`
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
(47, 2, 2, 1, '2024-12-18 18:01:10', 'Selesai', 6020000.00, 1),
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
(71, 2, 3, 1, '2024-12-20 01:55:27', 'Dikirim', 220000.00, 1),
(72, 2, 3, 1, '2024-12-20 01:55:45', 'Dikirim', 520000.00, 1),
(73, 2, 3, 1, '2024-12-20 14:20:07', 'Dikirim', 55520000.00, 1),
(74, 2, 4, 1, '2024-12-20 14:20:07', 'Diproses', 55520000.00, 1),
(75, 2, 3, 1, '2024-12-20 14:36:44', 'Dikirim', 520000.00, 1),
(76, 2, 3, 1, '2024-12-20 15:07:09', 'Dikirim', 220000.00, 1),
(77, 2, 3, 1, '2024-12-20 16:33:10', 'Dikirim', 320000.00, 1),
(78, 2, 3, 1, '2024-12-20 16:33:39', 'Dikirim', 520000.00, 1),
(79, 2, 3, 1, '2024-12-20 16:34:00', 'Dikirim', 320000.00, 1),
(80, 2, 3, 1, '2024-12-20 16:34:31', 'Dikirim', 520000.00, 1),
(81, 2, 3, 1, '2024-12-20 16:49:50', 'Dikirim', 320000.00, 1),
(82, 2, 3, 1, '2024-12-20 16:50:10', 'Selesai', 320000.00, 1),
(83, 2, 3, 1, '2025-05-25 14:13:28', 'Selesai', 220000.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
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
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `deskripsi`, `harga`, `stok`, `id_kategori`, `created_at`, `updated_at`, `gambar`) VALUES
(2, 'XBOX Series X', 'Xbox ', 6000000.00, 0, 5, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '67568e7f40ff5_fotosiswa.jpeg'),
(3, 'Dualshock Controller', 'Controller Dual Shock Terbaru', 500000.00, 120, 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '675c3d0ce7df2_fotosiswa.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `promo`
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
-- Dumping data for table `promo`
--

INSERT INTO `promo` (`id`, `code`, `discount_type`, `discount_value`, `usage_limit`, `times_used`, `created_at`) VALUES
(10, 'IVRKH', 'percentage', 50.00, 0, 20, '2024-12-17 06:17:10'),
(11, 'ESOUK', 'fixed', 15000.00, 0, 20, '2024-12-17 06:17:23'),
(12, 'ACD5Y', 'fixed', 15000.00, 0, 20, '2024-12-17 06:28:02'),
(13, '2HSLK', 'fixed', 300000.00, 104, 46, '2024-12-19 13:19:49'),
(14, '14J60', 'fixed', 200000.00, 39, 11, '2024-12-19 19:25:10');

-- --------------------------------------------------------

--
-- Table structure for table `review_produk`
--

CREATE TABLE `review_produk` (
  `id_review` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `rating_produk` int(11) DEFAULT NULL,
  `rating_pelayanan` int(11) NOT NULL,
  `rating_pengiriman` int(11) NOT NULL,
  `komentar` text DEFAULT NULL,
  `komentar_admin` varchar(255) DEFAULT NULL,
  `tanggal_review` datetime DEFAULT current_timestamp(),
  `notifikasi_status` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review_produk`
--

INSERT INTO `review_produk` (`id_review`, `id_user`, `id_produk`, `id_pesanan`, `rating_produk`, `rating_pelayanan`, `rating_pengiriman`, `komentar`, `komentar_admin`, `tanggal_review`, `notifikasi_status`) VALUES
(1, 2, 2, 6, 3, 2, 5, 'b aja', NULL, '2025-05-27 12:08:57', 0);

-- --------------------------------------------------------

--
-- Table structure for table `user`
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
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `email`, `password`, `no_tlp`, `alamat`, `level`, `foto`, `active`) VALUES
(1, 'Hajjid Rafi Mumtaz', 'rafimumtaz86@gmail.com', '123', '081513099954', 'Bekasi City', 'admin', '1734507173_messages-3.jpg', '2025-05-27 11:38:28'),
(2, 'Tria Yunita Krismiyanto', 'triayunita07@gmail.com', '123', '0888883838383', 'Bangkalan Halim Perdana Kusuma 2\r\n', 'user', '1734507541_messages-2.jpg', '2025-05-27 11:38:56'),
(5, 'Tria Krismiyanto Yunita', 'triayunita02@gmail.com', '123', '08587617989', 'Koperindag Blok C No 3', 'user', '', '2025-05-25 14:10:08'),
(6, 'asep prayogi', 'asepyogi@gmail.com', '123', '123456', 'Medan', 'user', '', '2025-05-24 09:24:55'),
(30, 'Customer Service', 'cs@gmail.com', '123', '081513099954', 'kantor ', 'cs', '1748157071_Screenshot (152).png', '2025-05-25 14:10:52');

-- --------------------------------------------------------

--
-- Table structure for table `user_promo_codes`
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
-- Table structure for table `wishlist`
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
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id_wishlist`, `user_id`, `id_produk`, `created_at`, `updated_at`, `notifikasi_restock`) VALUES
(2, 1, 2, '2024-12-19 18:00:41', '2024-12-19 18:00:41', 0),
(3, 2, 2, '2025-05-25 03:19:25', '2025-05-25 03:19:25', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `informasipromo`
--
ALTER TABLE `informasipromo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id_keranjang`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id_message`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembatalan_pesanan`
--
ALTER TABLE `pembatalan_pesanan`
  ADD PRIMARY KEY (`id_pembatalan`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indexes for table `pengiriman_pesanan`
--
ALTER TABLE `pengiriman_pesanan`
  ADD PRIMARY KEY (`id_pengiriman`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`) USING BTREE,
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_user` (`id_user`) USING BTREE;

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `promo`
--
ALTER TABLE `promo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `review_produk`
--
ALTER TABLE `review_produk`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_produk` (`id_produk`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_promo_codes`
--
ALTER TABLE `user_promo_codes`
  ADD PRIMARY KEY (`id_user_promo_code`),
  ADD UNIQUE KEY `promo_code` (`promo_code`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `promo_id` (`promo_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id_wishlist`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `id_produk` (`id_produk`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `informasipromo`
--
ALTER TABLE `informasipromo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id_keranjang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=225;

--
-- AUTO_INCREMENT for table `pembatalan_pesanan`
--
ALTER TABLE `pembatalan_pesanan`
  MODIFY `id_pembatalan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `pengiriman_pesanan`
--
ALTER TABLE `pengiriman_pesanan`
  MODIFY `id_pengiriman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `promo`
--
ALTER TABLE `promo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `review_produk`
--
ALTER TABLE `review_produk`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `user_promo_codes`
--
ALTER TABLE `user_promo_codes`
  MODIFY `id_user_promo_code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id_wishlist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`),
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `pembatalan_pesanan`
--
ALTER TABLE `pembatalan_pesanan`
  ADD CONSTRAINT `pembatalan_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`),
  ADD CONSTRAINT `pembatalan_pesanan_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pengiriman_pesanan`
--
ALTER TABLE `pengiriman_pesanan`
  ADD CONSTRAINT `pengiriman_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pengiriman_pesanan_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`);

--
-- Constraints for table `review_produk`
--
ALTER TABLE `review_produk`
  ADD CONSTRAINT `review_produk_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `review_produk_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
