-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 08 Des 2025 pada 10.00
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
-- Database: `evoting`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Administrator', 'admin@himatro.ac.id', 'admin123', '2025-12-07 05:57:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `chairman_name` varchar(100) NOT NULL,
  `vice_chairman_name` varchar(100) NOT NULL,
  `chairman_photo` varchar(255) DEFAULT 'assets/default.png',
  `vice_chairman_photo` varchar(255) DEFAULT 'assets/default.png',
  `vision` text DEFAULT NULL,
  `mission` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `candidates`
--

INSERT INTO `candidates` (`id`, `chairman_name`, `vice_chairman_name`, `chairman_photo`, `vice_chairman_photo`, `vision`, `mission`, `created_at`) VALUES
(1, 'Rizki Maulana', 'Ahmad Fauzi', 'https://i.pravatar.cc/150?img=11', 'https://i.pravatar.cc/150?img=12', 'Himatro Inovatif, Elektro Berprestasi', '1. Meningkatkan kualitas akademik.\n2. Mengembangkan soft skill anggota.', '2025-12-08 10:00:00'),
(2, 'Sarah Fitriani', 'Budi Santoso', 'https://i.pravatar.cc/150?img=5', 'https://i.pravatar.cc/150?img=3', 'Sinergi untuk Kemajuan Bersama', '1. Membangun kolaborasi antar angkatan.\n2. Mengoptimalkan program kerja.', '2025-12-08 10:05:00'),
(3, 'Dimas Pratama', 'Cindy Permata', 'https://i.pravatar.cc/150?img=59', 'https://i.pravatar.cc/150?img=49', 'Elektro Unggul, Mandiri, dan Berkarakter', '1. Menanamkan nilai integritas.\n2. Mewujudkan kemandirian organisasi.', '2025-12-08 10:10:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `nim` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `has_voted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `nim`, `email`, `password`, `has_voted`, `created_at`) VALUES
(1, 'Fadhil', 'Akbar', '123', 'gerald123@himatro.ac.id', '$2y$10$kUGsFlMdh2RVsaf93aqcleZNubGCm262tYl/hm4rcxvsdd1WMvtCu', 0, '2025-12-07 04:26:58'),
(2, 'am', 'rama', '2315', 'rama@himatro.ac.id', '$2y$10$/c1a5iJW8k0XPh4rVJmGF.7.pm4OX6DtQc9ihSPlEhLGas17dC.xO', 0, '2025-12-07 05:45:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim` (`nim`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE;
COMMIT;