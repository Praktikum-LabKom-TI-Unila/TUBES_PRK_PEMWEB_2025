CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    npm VARCHAR(15) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    avatar VARCHAR(255) DEFAULT 'default.jpg',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
);

CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at DATETIME NULL
);

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

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) NULL, 
    is_read TINYINT(1) DEFAULT 0, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id)
);

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
(13, 'Masjid Al-Wasi''i'),
(14, 'Lapangan Olahraga'),
(15, 'Parkiran Motor Pusat'),
(16, 'Parkiran Mobil Pusat'),
(17, 'UPT Bahasa'),
(18, 'Poliklinik Unila'),
(19, 'Asrama Mahasiswa'),
(20, 'Lainnya');

INSERT INTO users (id, name, npm, email, password, phone, role, is_active) VALUES
(1, 'Administrator', '0000000000', 'admin@unila.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'admin', 1);