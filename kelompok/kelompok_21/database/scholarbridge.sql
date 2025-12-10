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

ALTER TABLE tutor 
ADD COLUMN harga_per_sesi INT DEFAULT 100000,
ADD COLUMN rating DECIMAL(3,2) DEFAULT 4.5,
ADD COLUMN foto_profil VARCHAR(255),
ADD COLUMN telepon VARCHAR(20),
ADD COLUMN pengalaman_mengajar INT DEFAULT 1,
ADD COLUMN deskripsi TEXT;

INSERT INTO tutor (nama_lengkap, email, keahlian, pendidikan, status, harga_per_sesi, rating, telepon, pengalaman_mengajar, deskripsi) VALUES 
('M. Ilham Saputra', 'ilham.math@gmail.com', 'Matematika', 'S1 Pendidikan Matematika Unila', 'Aktif', 150000, 4.8, '081234567801', 5, 'Tutor Matematika berpengalaman untuk tingkat SD hingga SMA. Spesialisasi persiapan UTBK dan OSN.'),
('Sarah Amelia', 'sarah.amelia@yahoo.com', 'Fisika', 'S1 Fisika Itera (Institut Teknologi Sumatera)', 'Aktif', 175000, 4.9, '081234567802', 4, 'Lulusan Fisika dengan pengalaman mengajar privat dan bimbel. Metode pembelajaran interaktif dan mudah dipahami.'),
('Ahmad Fauzi', 'fauzi.english@gmail.com', 'Bahasa Inggris', 'S1 Sastra Inggris UIN Raden Intan', 'Cuti', 120000, 4.6, '081234567803', 3, 'English tutor fokus pada conversation, grammar, dan TOEFL preparation.'),
('Dinda Pertiwi', 'dinda.code@outlook.com', 'Koding', 'S1 Informatika Univ. Teknokrat Indonesia', 'Aktif', 200000, 4.9, '081234567804', 3, 'Programmer dan tutor coding untuk pemula. Mengajarkan Python, JavaScript, HTML/CSS, dan dasar pemrograman.'),
('Bayu Nugroho', 'bayu.nugroho@gmail.com', 'Biologi', 'S1 Kedokteran Univ. Malahayati', 'Non-Aktif', 180000, 4.7, '081234567805', 6, 'Mahasiswa kedokteran yang berpengalaman mengajar Biologi SMP dan SMA serta persiapan masuk kedokteran.'),
('Citra Lestari', 'citra.l@gmail.com', 'Kimia', 'S1 Kimia Murni Unila', 'Aktif', 160000, 4.8, '081234567806', 4, 'Tutor Kimia untuk SMA dan persiapan ujian masuk PTN. Metode belajar fun dan aplikatif.'),
('Eko Prasetyo', 'eko.music@gmail.com', 'Musik', 'S1 Seni Musik Ibi Darmajaya', 'Aktif', 100000, 4.5, '081234567807', 7, 'Guru musik profesional. Mengajar gitar, piano, dan vokal untuk semua usia.'),
('Rina Aulia', 'rina.aulia@yahoo.com', 'Ekonomi', 'S1 Akuntansi UBL (Univ. Bandar Lampung)', 'Aktif', 140000, 4.7, '081234567808', 5, 'Tutor Ekonomi dan Akuntansi untuk SMA. Berpengalaman membantu siswa lolos SBMPTN jurusan ekonomi.');

-- Tabel tutor_mapel: menyimpan mata pelajaran yang bisa diajarkan oleh setiap tutor
CREATE TABLE IF NOT EXISTS tutor_mapel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    nama_mapel VARCHAR(50) NOT NULL,
    jenjang ENUM('SD', 'SMP', 'SMA', 'Umum') DEFAULT 'Umum',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tutor_id) REFERENCES tutor(id) ON DELETE CASCADE
);

INSERT INTO tutor_mapel (tutor_id, nama_mapel, jenjang) VALUES 
-- Tutor 1: M. Ilham Saputra (Matematika)
(1, 'Matematika', 'SD'),
(1, 'Matematika', 'SMP'),
(1, 'Matematika', 'SMA'),
-- Tutor 2: Sarah Amelia (Fisika)
(2, 'Fisika', 'SMP'),
(2, 'Fisika', 'SMA'),
(2, 'IPA', 'SD'),
-- Tutor 3: Ahmad Fauzi (Bahasa Inggris)
(3, 'Bahasa Inggris', 'SD'),
(3, 'Bahasa Inggris', 'SMP'),
(3, 'Bahasa Inggris', 'SMA'),
(3, 'TOEFL Preparation', 'Umum'),
-- Tutor 4: Dinda Pertiwi (Koding)
(4, 'Pemrograman Python', 'Umum'),
(4, 'Web Development', 'Umum'),
(4, 'Informatika', 'SMA'),
-- Tutor 5: Bayu Nugroho (Biologi)
(5, 'Biologi', 'SMP'),
(5, 'Biologi', 'SMA'),
(5, 'IPA', 'SD'),
-- Tutor 6: Citra Lestari (Kimia)
(6, 'Kimia', 'SMP'),
(6, 'Kimia', 'SMA'),
(6, 'IPA', 'SD'),
-- Tutor 7: Eko Prasetyo (Musik)
(7, 'Musik - Gitar', 'Umum'),
(7, 'Musik - Piano', 'Umum'),
(7, 'Musik - Vokal', 'Umum'),
-- Tutor 8: Rina Aulia (Ekonomi)
(8, 'Ekonomi', 'SMA'),
(8, 'Akuntansi', 'SMA'),
(8, 'IPS', 'SMP');