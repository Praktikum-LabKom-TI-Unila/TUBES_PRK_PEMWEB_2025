DROP DATABASE IF EXISTS myunila_lostfound;
CREATE DATABASE myunila_lostfound;
USE myunila_lostfound;

-- 1. Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    identity_number VARCHAR(20) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    avatar VARCHAR(255) DEFAULT 'default.jpg',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
);

-- 2. Tabel Locations
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
);

-- 3. Tabel Categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
);

-- 4. Tabel Items
CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    location_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    type ENUM('lost', 'found') NOT NULL,
    incident_date DATE NOT NULL,
    image_path VARCHAR(255) NULL,
    status ENUM('open', 'process', 'closed') DEFAULT 'open',
    
    is_safe_claim TINYINT(1) DEFAULT 0,
    security_question VARCHAR(255) NULL,
    security_answer VARCHAR(255) NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (location_id) REFERENCES locations(id),
    
    INDEX idx_title (title),
    INDEX idx_type (type)
);

-- 5. Tabel Claims
CREATE TABLE claims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    user_id INT NOT NULL,
    status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    verification_answer VARCHAR(255) NULL,
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    
    FOREIGN KEY (item_id) REFERENCES items(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 6. Tabel Comments
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    user_id INT NOT NULL,
    body TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL,
    
    FOREIGN KEY (item_id) REFERENCES items(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 7. Tabel Notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(50) DEFAULT 'default', 
    link VARCHAR(255) NULL, 
    is_read TINYINT(1) DEFAULT 0, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id)
);


-- Insert Categories
INSERT INTO categories (id, name) VALUES
(1, 'Elektronik'),
(2, 'Dokumen'),
(3, 'Pakaian'),
(4, 'Aksesoris'),
(5, 'Kunci'),
(6, 'Tas & Dompet'),
(7, 'Buku & Alat Tulis'),
(8, 'Kendaraan'),
(9, 'Lainnya');

-- Insert Locations
INSERT INTO locations (id, name) VALUES
(1, 'Gedung Rektorat'),
(2, 'Gedung Serba Guna (GSG)'),
(3, 'Perpustakaan Pusat'),
(4, 'Kantin Terpadu'),
(5, 'Gedung A - FMIPA'),
(6, 'Gedung B - Fakultas Teknik'),
(7, 'Gedung C - FISIP'),
(8, 'Gedung D - Fakultas Hukum'),
(9, 'Gedung E - Fakultas Ekonomi'),
(10, 'Gedung F - FKIP'),
(11, 'Gedung G - Fakultas Pertanian'),
(12, 'Gedung H - Fakultas Kedokteran'),
(13, "Masjid Al-Wasi'i"),
(14, 'Lapangan Olahraga'),
(15, 'Parkiran Motor Pusat'),
(16, 'Parkiran Mobil Pusat'),
(17, 'UPT Bahasa'),
(18, 'Poliklinik Unila'),
(19, 'Asrama Mahasiswa'),
(20, 'Lainnya');

INSERT INTO users (id, name, identity_number, email, password, phone, role, is_active) VALUES
(1, 'Administrator', 'ADMIN001', 'admin@unila.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'admin', 1),
(2, 'Budi Santoso', '1817051001', 'budi@students.unila.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08987654321', 'user', 1),
(3, 'Siti Aminah', '1817051002', 'siti@students.unila.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '08111222333', 'user', 1),
(4, 'Spammer Jahat', '1817051003', 'hacker@students.unila.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '000000000', 'user', 0); -- User is_active = 0 (banned)

INSERT INTO items (user_id, category_id, location_id, title, description, type, status, image_path, incident_date, created_at) VALUES
(2, 1, 6, 'Laptop ASUS ROG', 'Laptop gaming hilang di ruang kelas gedung teknik, warna hitam dengan stiker Apple di belakang. Kondisi masih mulus, spesifikasi i7 gen 10.', 'lost', 'open', 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400', CURDATE(), NOW()),
(3, 2, 3, 'KTM dan KTP', 'Dokumen penting berupa KTM Unila dan KTP hilang di area perpustakaan pusat lantai 2. Nama tercantum: Siti Aminah. Mohon bantuannya.', 'lost', 'open', 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=400', CURDATE(), NOW()),
(2, 6, 15, 'Dompet Kulit Coklat', 'Ditemukan dompet kulit warna coklat di parkiran motor pusat. Berisi uang tunai dan beberapa kartu. Silakan hubungi dengan menyebutkan ciri-ciri.', 'found', 'open', 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=400', CURDATE(), NOW()),
(3, 5, 4, 'Kunci Motor Honda Beat', 'Kunci motor Honda Beat dengan gantungan boneka karakter Rilakkuma. Hilang di area kantin terpadu sekitar jam 12 siang.', 'lost', 'open', 'https://images.unsplash.com/photo-1582139329536-e7284fece509?w=400', CURDATE(), NOW()),
(2, 7, 3, 'Buku Catatan Biru', 'Ditemukan buku catatan berwarna biru di perpustakaan. Berisi catatan mata kuliah Kalkulus. Ada nama inisial "RD" di cover.', 'found', 'open', 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400', CURDATE(), NOW()),
(3, 4, 13, 'Kacamata Hitam', 'Kacamata hitam merk Ray-Ban hilang di Masjid Al-Wasi\'i setelah sholat Dzuhur. Frame hitam dengan lensa polarized.', 'lost', 'process', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400', CURDATE(), NOW()),
(2, 1, 2, 'Smartwatch Apple Watch', 'Ditemukan Apple Watch Series 7 warna silver di GSG setelah acara seminar. Kondisi mati baterai, ada wallpaper foto keluarga.', 'found', 'process', 'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?w=400', CURDATE(), NOW()),
(3, 6, 6, 'Tas Ransel Hitam', 'Tas ransel merk Eiger warna hitam hilang di gedung fakultas teknik lantai 3. Berisi laptop, buku, dan alat tulis. Sangat penting!', 'lost', 'open', 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400', CURDATE(), NOW()),
(2, 3, 10, 'Jaket Almamater Unila', 'Ditemukan jaket almamater Unila ukuran L di FKIP. Ada name tag dengan nama "Ahmad" di bagian dalam. Warna biru khas Unila.', 'found', 'closed', 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400', CURDATE(), NOW()),
(3, 1, 1, 'Charger Laptop Lenovo', 'Charger laptop merk Lenovo 65W hilang di gedung rektorat ruang tunggu. Kabel warna hitam dengan adaptor kotak.', 'lost', 'closed', 'https://images.unsplash.com/photo-1625948515291-69613efd103f?w=400', CURDATE(), NOW());