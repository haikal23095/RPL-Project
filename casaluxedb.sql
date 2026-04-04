-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 27, 2026 at 04:26 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

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
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `no_tlp` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_general_ci,
  `level` enum('admin','user','cs') COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `active` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `email`, `password`, `no_tlp`, `alamat`, `level`, `foto`, `active`) VALUES
(1, 'Rafi Mumtaz Hajjid', 'rafimumtaz86@gmail.com', '123', '+6285648475948', 'Bekasi City', 'admin', '1734507173_messages-3.jpg', '2025-06-18 12:56:04'),
(2, 'Tria Yunita Krismiyanto', 'triayunita07@gmail.com', '123', '+6285648475948', 'Bangkalan Halim Perdana Kusuma 2\r\n', 'user', '1734507541_messages-2.jpg', '2025-06-18 10:36:21'),
(5, 'Tria Krismiyanto Yunita', 'triayunita02@gmail.com', '123', '+6282132690717', 'Koperindag Blok C No 3', 'user', '', '2024-12-18 13:48:06'),
(6, 'asep prayogi', 'asepyogi@gmail.com', '123', '+6282132690717', 'Medan', 'user', '', '2024-12-22 21:47:43'),
(7, 'coba123', 'coba@gmail.com', '123', '+6282132690717', 'coba233', 'user', '1734877511_1734507173_messages-3.jpg', '2024-12-22 22:04:01'),
(8, 'Customer Service', 'cs@gmail.com', '123', '+6282132690717', 'Koperindag Blok C No 3', 'cs', '../uploads/profile-img.jpg', '2025-06-13 19:02:34'),
(10, 'haikal', 'haikal@gmail.com', '123', '0987654321', 'jl xxx yyy no 7', 'user', NULL, '2026-03-27 23:13:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
