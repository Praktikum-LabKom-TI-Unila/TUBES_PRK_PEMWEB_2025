-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 04 Des 2025 pada 12.05
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lokapos`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) DEFAULT 'default.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `stock`, `image`) VALUES
(1, 'Roti Bakar Coklat', 15000.00, 18, 'roti-bakar.jpg'),
(2, 'Mie Goreng Spesial', 18000.00, 23, 'mie-goreng.jpg'),
(3, 'Ayam Geprek', 20000.00, 10, 'ayam-geprek.jpg'),
(4, 'Kentang Goreng', 12000.00, 28, 'kentang-goreng.jpg'),
(5, 'Es Teh Manis', 5000.00, 46, 'es-teh.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `total_amount`, `created_at`) VALUES
(4, 8, 20000.00, '2025-12-04 11:02:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaction_details`
--

CREATE TABLE `transaction_details` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaction_details`
--

INSERT INTO `transaction_details` (`id`, `transaction_id`, `product_id`, `qty`, `subtotal`) VALUES
(10, 4, 3, 1, 20000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('seller','admin') DEFAULT 'seller',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(6, 'Seller', 'seller@gmail.com', '$2y$10$BcM/T6xF1CDiYJjUesybXe3nvo.SkOKo.x2kKXTo7Xovdq852QWpK', 'seller', '2025-12-04 09:50:52'),
(8, 'Admin Utama', 'admin@gmail.com', '$2y$10$PanA9QtGWtpy2TOoC2x8zupE1tWnjBZz9TAStxWj.mXvTm1eAxbdK', 'admin', '2025-12-04 11:01:45');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- 1. Tambah kolom Kategori di tabel Produk (Untuk fitur Filter & Search)
ALTER TABLE products 
ADD COLUMN category ENUM('makanan', 'minuman', 'snack', 'lainnya') DEFAULT 'lainnya';

-- 2. Tambah kolom-kolom baru di tabel Transaksi (Untuk fitur Bayar & Diskon)
ALTER TABLE transactions 
ADD COLUMN payment_method VARCHAR(20) DEFAULT 'CASH', -- Bisa CASH, QRIS, TRANSFER
ADD COLUMN total_tax DECIMAL(10,2) DEFAULT 0,         -- Simpan nominal pajak
ADD COLUMN total_discount DECIMAL(10,2) DEFAULT 0,    -- Simpan nominal diskon
ADD COLUMN pay_amount DECIMAL(10,2) DEFAULT 0,        -- Uang yang dibayar
ADD COLUMN change_amount DECIMAL(10,2) DEFAULT 0;     -- Kembalian

-- 3. Update Data Dummy Kategori (Supaya filternya nanti bisa langsung dites)
UPDATE products SET category = 'makanan' WHERE name LIKE '%ayam%' OR name LIKE '%roti%' OR name LIKE '%mie%' OR name LIKE '%nasi%';
UPDATE products SET category = 'minuman' WHERE name LIKE '%es%' OR name LIKE '%kopi%' OR name LIKE '%jus%';
UPDATE products SET category = 'snack' WHERE name LIKE '%kentang%';

ALTER TABLE `transactions` 
ADD COLUMN `midtrans_order_id` VARCHAR(100) NULL AFTER `id`,
ADD COLUMN `midtrans_snap_token` VARCHAR(255) NULL AFTER `midtrans_order_id`,
ADD COLUMN `midtrans_transaction_status` ENUM('pending', 'settlement', 'capture', 'deny', 'cancel', 'expire', 'failure') NULL AFTER `midtrans_snap_token`,
ADD COLUMN `midtrans_payment_type` VARCHAR(50) NULL AFTER `midtrans_transaction_status`,
ADD INDEX `idx_midtrans_order` (`midtrans_order_id`);

-- Update payment_method enum to include more options
ALTER TABLE `transactions` 
MODIFY COLUMN `payment_method` ENUM('CASH', 'QRIS', 'TRANSFER', 'MIDTRANS') DEFAULT 'CASH';
