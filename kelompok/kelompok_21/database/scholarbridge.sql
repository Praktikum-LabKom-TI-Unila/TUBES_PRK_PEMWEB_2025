CREATE DATABASE scholarbridge;
USE scholarbridge;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'tutor', 'learner') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users ADD COLUMN status ENUM('active', 'pending', 'banned') DEFAULT 'active';

UPDATE users SET status = 'active';

CREATE TABLE IF NOT EXISTS siswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    jenjang ENUM('SD', 'SMP', 'SMA') NOT NULL,
    sekolah VARCHAR(100),
    kelas VARCHAR(50),
    minat TEXT,
    status ENUM('Aktif', 'Cuti', 'Non-Aktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO siswa (nim, nama_lengkap, email, jenjang, sekolah, kelas, minat, status) VALUES 
('2025001', 'M. Rizky Pratama', 'rizky.p@gmail.com', 'SMA', 'SMAN 2 Bandar Lampung', '12 IPA 1', 'Matematika, Fisika, Robotik', 'Aktif'),
('2025002', 'Alya Kinanti', 'alya.k@yahoo.com', 'SD', 'SD Al-Kautsar Bandar Lampung', 'Kelas 5B', 'Bahasa Inggris, Menggambar', 'Aktif'),
('2025003', 'Andreas Kurniawan', 'andreas.k@gmail.com', 'SMP', 'SMP Xaverius 1 Bandar Lampung', 'Kelas 9C', 'Biologi, Basket', 'Aktif'),
('2025004', 'Siti Fatimah', 'siti.fatimah@outlook.com', 'SMA', 'SMA YP Unila Bandar Lampung', '11 IPS 2', 'Ekonomi, Geografi, Akuntansi', 'Aktif'),
('2025005', 'Kevin Sanjaya', 'kevin.s@gmail.com', 'SD', 'SDN 2 Rawa Laut', 'Kelas 6A', 'Matematika, Olahraga', 'Non-Aktif'),
('2025006', 'Dinda Puspita', 'dinda.p@gmail.com', 'SMP', 'SMPN 1 Bandar Lampung', 'Kelas 8A', 'Bahasa Indonesia, Musik', 'Aktif'),
('2025007', 'Fajar Nugraha', 'fajar.nugraha@gmail.com', 'SMA', 'SMAN 9 Bandar Lampung', '10 IPA 3', 'Koding, Fisika', 'Cuti'),
('2025008', 'Grace Natalia', 'grace.n@yahoo.com', 'SD', 'SD BPK Penabur Bandar Lampung', 'Kelas 3', 'Menyanyi, Calistung', 'Aktif');

CREATE TABLE IF NOT EXISTS tutor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    keahlian VARCHAR(50) NOT NULL,
    pendidikan VARCHAR(100),   
    status ENUM('Aktif', 'Cuti', 'Non-Aktif') DEFAULT 'Aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO tutor (nama_lengkap, email, keahlian, pendidikan, status) VALUES 
('M. Ilham Saputra', 'ilham.math@gmail.com', 'Matematika', 'S1 Pendidikan Matematika Unila', 'Aktif'),
('Sarah Amelia', 'sarah.amelia@yahoo.com', 'Fisika', 'S1 Fisika Itera (Institut Teknologi Sumatera)', 'Aktif'),
('Ahmad Fauzi', 'fauzi.english@gmail.com', 'Bahasa Inggris', 'S1 Sastra Inggris UIN Raden Intan', 'Cuti'),
('Dinda Pertiwi', 'dinda.code@outlook.com', 'Koding', 'S1 Informatika Univ. Teknokrat Indonesia', 'Aktif'),
('Bayu Nugroho', 'bayu.nugroho@gmail.com', 'Biologi', 'S1 Kedokteran Univ. Malahayati', 'Non-Aktif'),
('Citra Lestari', 'citra.l@gmail.com', 'Kimia', 'S1 Kimia Murni Unila', 'Aktif'),
('Eko Prasetyo', 'eko.music@gmail.com', 'Musik', 'S1 Seni Musik Ibi Darmajaya', 'Aktif'),
('Rina Aulia', 'rina.aulia@yahoo.com', 'Ekonomi', 'S1 Akuntansi UBL (Univ. Bandar Lampung)', 'Aktif');