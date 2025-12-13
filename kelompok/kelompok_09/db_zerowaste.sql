-- ==========================================
-- 1. Tabel Users (Pengguna)
-- ==========================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','donatur','mahasiswa') NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `is_active` tinyint(1) DEFAULT '1' COMMENT '1: Aktif, 0: Banned',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_zerowaste.users:
INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `role`, `no_hp`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
	
	(14, 'Mahasiswa', '$2y$10$S4uf8iJnU3hmANAku5jiPuwU94ENkjHnJujVRl5uLj.ejhmtT/FSq', 'Mahasiswa Testing', 'mahasiswa', '088813138383', 1, '2025-12-10 14:45:08', '2025-12-10 14:45:08', NULL),
	(15, 'Donatur', '$2y$10$/Yw8mhdLoLTIA8VWV6JRMOKCXG0MA43iuPm9T.rvBMSGL1Nh9wG9q', 'Donatur Testing', 'donatur', '081214515151', 1, '2025-12-10 14:46:03', '2025-12-10 14:46:03', NULL),
	(16, 'Admin', '$2y$10$vzf8Cn/ffuiMkiCRvRtcteO4JOvPtZPdIty9NrEcZP0IkdtkACtCC', 'Admin Testing', 'admin', '088813451214', 1, '2025-12-10 14:46:36', '2025-12-10 14:47:16', NULL);

-- ==========================================
-- 2. Tabel Categories (Kategori Makanan)
-- ==========================================
DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table db_zerowaste.categories: 
INSERT INTO `categories` (`id`, `nama_kategori`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 'Makanan Berat', '2025-12-06 06:49:21', '2025-12-06 06:49:21', NULL),
	(2, 'Snack', '2025-12-06 06:49:21', '2025-12-06 06:49:21', NULL),
	(3, 'Minuman', '2025-12-06 06:49:21', '2025-12-06 06:49:21', NULL);

-- ==========================================
-- 3. Tabel Food Stocks (Katalog Makanan)
-- ==========================================
DROP TABLE IF EXISTS `food_stocks`;
CREATE TABLE IF NOT EXISTS `food_stocks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `donatur_id` int NOT NULL,
  `category_id` int NOT NULL,
  `judul` varchar(100) NOT NULL,
  `deskripsi` text,
  `foto_path` varchar(255) NOT NULL,
  `jumlah_awal` int unsigned NOT NULL,
  `stok_tersedia` int unsigned NOT NULL,
  `lokasi_pickup` text NOT NULL,
  `batas_waktu` datetime NOT NULL COMMENT 'Deadline makanan bisa diambil',
  `jenis_makanan` enum('halal','non_halal') DEFAULT 'halal',
  `status` enum('tersedia','habis') DEFAULT 'tersedia',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `donatur_id` (`donatur_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `food_stocks_ibfk_1` FOREIGN KEY (`donatur_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `food_stocks_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ==========================================
-- 4. Tabel Claims (Transaksi Klaim)
-- ==========================================
DROP TABLE IF EXISTS `claims`;
CREATE TABLE IF NOT EXISTS `claims` (
  `id` int NOT NULL AUTO_INCREMENT,
  `food_id` int NOT NULL,
  `mahasiswa_id` int NOT NULL,
  `kode_tiket` varchar(10) NOT NULL,
  `status` enum('pending','diambil','batal','expired') DEFAULT 'pending',
  `alasan_batal` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu Klik Ambil',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `verified_at` datetime DEFAULT NULL COMMENT 'Waktu diambil di lokasi',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_tiket` (`kode_tiket`),
  KEY `food_id` (`food_id`),
  KEY `mahasiswa_id` (`mahasiswa_id`),
  CONSTRAINT `claims_ibfk_1` FOREIGN KEY (`food_id`) REFERENCES `food_stocks` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `claims_ibfk_2` FOREIGN KEY (`mahasiswa_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ==========================================
-- 5. Tabel Activity Logs (Audit Trail)
-- ==========================================
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `action` varchar(50) NOT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;





