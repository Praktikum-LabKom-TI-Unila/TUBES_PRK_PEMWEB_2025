-- Database: silatium
CREATE DATABASE IF NOT EXISTS silatium;
USE silatium;
-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Table: transportasi
CREATE TABLE IF NOT EXISTS transportasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_transportasi VARCHAR(100) NOT NULL,
    rute TEXT NOT NULL,
    jadwal VARCHAR(255) NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Table: laporan
CREATE TABLE IF NOT EXISTS laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul_laporan VARCHAR(255) NOT NULL,
    isi_laporan TEXT NOT NULL,
    status ENUM('received', 'processing', 'completed') DEFAULT 'received',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- Insert Default Admin (Optional for testing)
-- Password is 'admin123'
INSERT INTO users (name, email, password, role) VALUES 
('Administrator', 'admin@silatium.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');