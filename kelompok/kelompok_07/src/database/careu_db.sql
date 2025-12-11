-- ============================================
-- CareU Database Schema
-- Platform Crowdfunding Mahasiswa
-- ============================================

-- DROP DATABASE IF EXISTS careu_db;

-- Buat database
CREATE DATABASE IF NOT EXISTS careu_db 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;


-- Buat user untuk aplikasi CareU
CREATE USER IF NOT EXISTS 'careu_user'@'localhost' IDENTIFIED BY 'careu_pass123';

-- Berikan hak akses ke database careu_db
GRANT ALL PRIVILEGES ON careu_db.* TO 'careu_user'@'localhost';

-- Refresh privileges
FLUSH PRIVILEGES;

-- Gunakan database
USE careu_db;

-- ============================================
-- Tabel: users
-- Menyimpan data pengguna (admin dan donatur)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: campaigns
-- Menyimpan data campaign crowdfunding
-- ============================================
CREATE TABLE IF NOT EXISTS campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    background TEXT,
    target_amount DECIMAL(15,2) NOT NULL,
    current_amount DECIMAL(15,2) DEFAULT 0,
    deadline DATE NOT NULL,
    category VARCHAR(100),
    image_url VARCHAR(500),
    video_url VARCHAR(500),
    status ENUM('active', 'closed', 'completed') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_deadline (deadline),
    INDEX idx_created_by (created_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: donations
-- Menyimpan data donasi dari pengguna
-- ============================================
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,
    user_id INT,
    amount DECIMAL(15,2) NOT NULL,
    payment_method ENUM('qris', 'transfer') NOT NULL,
    proof_image VARCHAR(500),
    is_anonymous BOOLEAN DEFAULT FALSE,
    status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_campaign_id (campaign_id),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: campaign_updates
-- Menyimpan update perkembangan campaign
-- ============================================
CREATE TABLE IF NOT EXISTS campaign_updates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    INDEX idx_campaign_id (campaign_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Tabel: fund_reports
-- Menyimpan laporan penggunaan dana campaign
-- ============================================
CREATE TABLE IF NOT EXISTS fund_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    expense_amount DECIMAL(15,2) NOT NULL,
    receipt_image VARCHAR(500),
    distribution_image VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    INDEX idx_campaign_id (campaign_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Data Default: Admin
-- ============================================
-- Password: admin123 (sudah di-hash dengan password_hash PHP)
-- Hash ini akan di-generate ulang saat aplikasi pertama kali dijalankan
-- Jika perlu, gunakan: php -r "echo password_hash('admin123', PASSWORD_DEFAULT);"
INSERT INTO users (username, email, password, full_name, role) 
VALUES ('admin', 'admin@careu.com', '$2y$12$GcE/YoxmuPh3N7Faw/S7PeWltZc6MkBVljwXbbiKyWonDL/HckNs.', 'Administrator', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- ============================================
-- Data Sample (Opsional)
-- ============================================

-- Sample User
INSERT INTO users (username, email, password, full_name, role) 
VALUES ('user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User Test', 'user')
ON DUPLICATE KEY UPDATE username=username;

-- Sample Campaign (jika ada admin dengan id=1)
INSERT INTO campaigns (title, description, background, target_amount, deadline, category, status, created_by)
VALUES (
    'Bantuan Biaya Kuliah Mahasiswa Berprestasi',
    'Membantu mahasiswa berprestasi yang kesulitan biaya kuliah semester ini.',
    'Mahasiswa ini memiliki IPK tinggi namun mengalami kesulitan ekonomi. Bantuan ini akan digunakan untuk membayar SPP dan kebutuhan kuliah.',
    5000000.00,
    DATE_ADD(CURDATE(), INTERVAL 30 DAY),
    'pendidikan',
    'active',
    1
)
ON DUPLICATE KEY UPDATE title=title;

-- ============================================
-- Selesai
-- ============================================
