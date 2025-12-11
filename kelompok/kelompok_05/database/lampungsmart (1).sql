-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 11, 2025 at 03:29 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lampungsmart`
--

-- --------------------------------------------------------

--
-- Table structure for table `kontak`
--

CREATE TABLE `kontak` (
  `id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subjek` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('baru','dibaca','direspon') COLLATE utf8mb4_unicode_ci DEFAULT 'baru',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengaduan`
--

CREATE TABLE `pengaduan` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `judul` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','proses','selesai','ditolak') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengaduan`
--

INSERT INTO `pengaduan` (`id`, `user_id`, `judul`, `deskripsi`, `lokasi`, `foto`, `status`, `created_at`) VALUES
(1, 2, 'Jalan Berlubang di Jl. Ahmad Yani', 'Jalan Ahmad Yani dekat perempatan rusak parah, banyak lubang besar yang membahayakan pengendara. Sudah 3 bulan tidak diperbaiki.', 'Jl. Ahmad Yani, Teluk Betung', 'jalan_rusak_1.jpg', 'pending', '2025-11-25 16:31:50'),
(2, 3, 'Lampu Jalan Mati di Perumahan Griya Asri', 'Lampu jalan di area perumahan Griya Asri sudah mati semua sejak 1 minggu lalu. Sangat gelap di malam hari.', 'Perumahan Griya Asri, Way Halim', 'lampu_mati.jpg', 'pending', '2025-11-30 16:31:50'),
(3, 4, 'Tumpukan Sampah di Pasar Tradisional', 'Sampah menumpuk di belakang Pasar Tugu dan menimbulkan bau tidak sedap. Perlu penambahan TPS.', 'Pasar Tugu, Teluk Betung', 'sampah_pasar.jpg', 'pending', '2025-12-05 16:31:50'),
(4, 5, 'Drainase Tersumbat Menyebabkan Banjir', 'Drainase di Jl. Cut Nyak Dien tersumbat total. Setiap hujan selalu banjir setinggi 50cm. Warga kesulitan keluar rumah.', 'Jl. Cut Nyak Dien, Kedaton', 'banjir_drainase.jpg', 'pending', '2025-11-20 16:31:50'),
(5, 6, 'Taman Kota Kurang Terawat', 'Taman bermain anak di Taman Gajah rusak dan kotor. Ayunan patah dan perosotan berkarat.', 'Taman Gajah, Enggal', 'taman_rusak.jpg', 'pending', '2025-12-07 16:31:50'),
(6, 2, 'Jalan Sempit Butuh Pelebaran', 'Jalan di Gang Mawar sangat sempit, mobil sulit papasan. Perlu pelebaran jalan untuk akses ambulans.', 'Gang Mawar, Sukarame', 'jalan_sempit.jpg', 'proses', '2025-11-28 16:31:50'),
(7, 3, 'Pipa Air PDAM Bocor', 'Pipa air PDAM di Jl. Raden Intan bocor besar, air terbuang percuma dan jalan jadi becek.', 'Jl. Raden Intan, Tanjung Karang', 'pipa_bocor.jpg', 'pending', '2025-12-03 16:31:50');

-- --------------------------------------------------------

--
-- Table structure for table `pengaduan_upvotes`
--

CREATE TABLE `pengaduan_upvotes` (
  `id` int NOT NULL,
  `pengaduan_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengaduan_upvotes`
--

INSERT INTO `pengaduan_upvotes` (`id`, `pengaduan_id`, `user_id`, `created_at`) VALUES
(37, 1, 1, '2025-11-25 16:31:50'),
(38, 1, 2, '2025-11-26 16:31:50'),
(39, 1, 3, '2025-11-27 16:31:50'),
(40, 1, 4, '2025-11-28 16:31:50'),
(41, 1, 5, '2025-11-29 16:31:50'),
(42, 1, 6, '2025-11-30 16:31:50'),
(43, 2, 1, '2025-11-30 16:31:50'),
(44, 2, 2, '2025-12-01 16:31:50'),
(45, 2, 3, '2025-12-02 16:31:50'),
(46, 2, 4, '2025-12-03 16:31:50'),
(47, 2, 5, '2025-12-04 16:31:50'),
(48, 2, 6, '2025-12-05 16:31:50'),
(49, 3, 1, '2025-12-05 16:31:50'),
(50, 3, 2, '2025-12-06 16:31:50'),
(51, 3, 3, '2025-12-07 16:31:50'),
(52, 3, 4, '2025-12-08 16:31:50'),
(53, 4, 1, '2025-11-20 16:31:51'),
(54, 4, 2, '2025-11-21 16:31:51'),
(55, 4, 3, '2025-11-22 16:31:51'),
(56, 4, 4, '2025-11-23 16:31:51'),
(57, 4, 5, '2025-11-24 16:31:51'),
(58, 4, 6, '2025-11-25 16:31:51'),
(59, 5, 1, '2025-12-07 16:31:51'),
(60, 5, 2, '2025-12-08 16:31:51'),
(61, 5, 3, '2025-12-09 16:31:51'),
(62, 6, 1, '2025-11-28 16:31:51'),
(63, 6, 2, '2025-11-29 16:31:51'),
(64, 6, 3, '2025-12-01 16:31:51'),
(65, 6, 4, '2025-12-03 16:31:51'),
(66, 6, 5, '2025-12-04 16:31:51'),
(67, 7, 1, '2025-12-03 16:31:51'),
(68, 7, 2, '2025-12-04 16:31:51'),
(69, 7, 3, '2025-12-05 16:31:51'),
(70, 7, 4, '2025-12-06 16:31:51'),
(71, 7, 5, '2025-12-07 16:31:51'),
(72, 7, 6, '2025-12-08 16:31:51');

-- --------------------------------------------------------

--
-- Table structure for table `tanggapan`
--

CREATE TABLE `tanggapan` (
  `id` int NOT NULL,
  `pengaduan_id` int NOT NULL,
  `admin_id` int NOT NULL,
  `isi_tanggapan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `umkm`
--

CREATE TABLE `umkm` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `nama_usaha` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bidang_usaha` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat_usaha` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_pemilik` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','warga') COLLATE utf8mb4_unicode_ci DEFAULT 'warga',
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.jpg',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `profile_photo`, `created_at`, `updated_at`) VALUES
(1, 'Sulthon', 'test@example.com', '$2y$10$KexQfPJhiRAPNu1/4P.FAuOaeEHPKNIDxzKuOnUwoDKTd7Jc2ARsO', 'warga', 'default.jpg', '2025-12-10 05:37:08', '2025-12-10 05:37:08'),
(2, 'Admin LampungSmart', 'admin@lampungsmart.go.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'default.jpg', '2025-12-10 16:26:05', '2025-12-10 16:26:05'),
(3, 'Budi Santoso', 'budi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'warga', 'default.jpg', '2025-12-10 16:26:05', '2025-12-10 16:26:05'),
(4, 'Siti Rahmawati', 'siti@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'warga', 'default.jpg', '2025-12-10 16:26:05', '2025-12-10 16:26:05'),
(5, 'Andi Wijaya', 'andi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'warga', 'default.jpg', '2025-12-10 16:26:05', '2025-12-10 16:26:05'),
(6, 'Dewi Lestari', 'dewi@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'warga', 'default.jpg', '2025-12-10 16:26:05', '2025-12-10 16:26:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kontak`
--
ALTER TABLE `kontak`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kontak_status` (`status`),
  ADD KEY `idx_kontak_created` (`created_at`);

--
-- Indexes for table `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pengaduan_upvotes`
--
ALTER TABLE `pengaduan_upvotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote` (`pengaduan_id`,`user_id`),
  ADD KEY `idx_upvote_pengaduan` (`pengaduan_id`),
  ADD KEY `fk_pengaduan_upvotes_user` (`user_id`);

--
-- Indexes for table `tanggapan`
--
ALTER TABLE `tanggapan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengaduan_id` (`pengaduan_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `umkm`
--
ALTER TABLE `umkm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kontak`
--
ALTER TABLE `kontak`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengaduan`
--
ALTER TABLE `pengaduan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pengaduan_upvotes`
--
ALTER TABLE `pengaduan_upvotes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `tanggapan`
--
ALTER TABLE `tanggapan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `umkm`
--
ALTER TABLE `umkm`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD CONSTRAINT `pengaduan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengaduan_upvotes`
--
ALTER TABLE `pengaduan_upvotes`
  ADD CONSTRAINT `fk_pengaduan_upvotes_pengaduan` FOREIGN KEY (`pengaduan_id`) REFERENCES `pengaduan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pengaduan_upvotes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tanggapan`
--
ALTER TABLE `tanggapan`
  ADD CONSTRAINT `tanggapan_ibfk_1` FOREIGN KEY (`pengaduan_id`) REFERENCES `pengaduan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tanggapan_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `umkm`
--
ALTER TABLE `umkm`
  ADD CONSTRAINT `umkm_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
