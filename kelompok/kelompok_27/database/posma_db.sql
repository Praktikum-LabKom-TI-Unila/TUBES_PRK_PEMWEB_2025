-- DATABASE: POSMA (POS Mahasiswa)
CREATE DATABASE IF NOT EXISTS posma_db;
USE posma_db;

-- 1. Tabel Users (Manajemen Akses)
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, 
    role ENUM('admin', 'staff') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Tabel Suppliers (Mitra Konsinyasi)
CREATE TABLE suppliers (
    id_supplier INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    kategori ENUM('internal', 'eksternal') NOT NULL, 
    is_active ENUM('1', '0') DEFAULT '1' 
);

-- 3. Tabel Barang (Inventori)
CREATE TABLE barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    harga_jual DECIMAL(10, 2) NOT NULL,
    stok INT NOT NULL,
    id_supplier INT NULL, 
    is_active ENUM('1', '0') DEFAULT '1',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_supplier) REFERENCES suppliers(id_supplier) ON DELETE SET NULL
);

-- 4. Tabel Transaksi (Header Struk)
CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    no_faktur VARCHAR(50) NOT NULL, 
    tanggal_transaksi DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_bayar DECIMAL(10, 2) NOT NULL,
    metode_pembayaran ENUM('cash', 'qris') NOT NULL,
    id_user INT NOT NULL, 
    FOREIGN KEY (id_user) REFERENCES users(id_user)
);

-- 5. Tabel Detail Transaksi (Keranjang Belanja)
CREATE TABLE detail_transaksi (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT NOT NULL,
    id_barang INT NOT NULL,
    harga_saat_transaksi DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi),
    FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
);

-- Password default: '123456' 
-- Nanti gunakan password_hash('123456', PASSWORD_DEFAULT) di PHP
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$Xw..ContohHash..', 'admin'),
('kasir', '$2y$10$Xw..ContohHash..', 'staff');