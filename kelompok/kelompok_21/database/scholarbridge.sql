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

-- ========================================
-- TABEL BOOKING SYSTEM
-- ========================================

-- Tabel subjects: mata pelajaran yang ditawarkan tutor dengan harga spesifik
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tutor_id INT NOT NULL,
    subject_name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tutor_id) REFERENCES tutor(id) ON DELETE CASCADE
);

-- Insert data subjects berdasarkan tutor yang ada
INSERT INTO subjects (tutor_id, subject_name, price, description) VALUES 
-- Tutor 1: M. Ilham Saputra (Matematika)
(1, 'Matematika SD', 120000, 'Matematika untuk siswa SD kelas 1-6, fokus pada pemahaman konsep dasar'),
(1, 'Matematika SMP', 150000, 'Matematika untuk siswa SMP kelas 7-9, persiapan ujian sekolah'),
(1, 'Matematika SMA & UTBK', 180000, 'Matematika SMA kelas 10-12 dan persiapan UTBK, OSN'),
-- Tutor 2: Sarah Amelia (Fisika)
(2, 'Fisika SMP', 150000, 'Fisika dasar untuk siswa SMP dengan metode eksperimen sederhana'),
(2, 'Fisika SMA & UTBK', 200000, 'Fisika SMA dan persiapan UTBK, fokus pada pemecahan soal'),
(2, 'IPA SD', 100000, 'IPA terpadu untuk siswa SD'),
-- Tutor 3: Ahmad Fauzi (Bahasa Inggris)
(3, 'English for Kids (SD)', 100000, 'Bahasa Inggris dasar untuk anak-anak dengan metode fun learning'),
(3, 'English SMP', 120000, 'Grammar, reading, dan conversation untuk siswa SMP'),
(3, 'English SMA', 140000, 'Bahasa Inggris SMA dan persiapan ujian'),
(3, 'TOEFL Preparation', 200000, 'Persiapan TOEFL iBT dan ITP untuk kuliah/beasiswa'),
-- Tutor 4: Dinda Pertiwi (Koding)
(4, 'Python untuk Pemula', 180000, 'Belajar Python dari nol hingga bisa membuat project'),
(4, 'Web Development', 220000, 'HTML, CSS, JavaScript untuk membuat website'),
(4, 'Informatika SMA', 160000, 'Mata pelajaran Informatika untuk siswa SMA'),
-- Tutor 5: Bayu Nugroho (Biologi)
(5, 'Biologi SMP', 150000, 'Biologi untuk siswa SMP dengan praktikum virtual'),
(5, 'Biologi SMA', 180000, 'Biologi SMA dan persiapan masuk kedokteran'),
-- Tutor 6: Citra Lestari (Kimia)
(6, 'Kimia SMP', 140000, 'Kimia dasar untuk siswa SMP'),
(6, 'Kimia SMA & UTBK', 180000, 'Kimia SMA dan persiapan UTBK dengan metode aplikatif'),
-- Tutor 7: Eko Prasetyo (Musik)
(7, 'Kursus Gitar', 120000, 'Belajar gitar akustik/elektrik dari dasar hingga mahir'),
(7, 'Kursus Piano', 150000, 'Piano untuk pemula hingga intermediate'),
(7, 'Vocal Training', 100000, 'Latihan vokal dan teknik bernyanyi'),
-- Tutor 8: Rina Aulia (Ekonomi)
(8, 'Ekonomi SMA', 150000, 'Ekonomi mikro dan makro untuk siswa SMA'),
(8, 'Akuntansi SMA', 160000, 'Akuntansi dasar dan lanjutan untuk SMA'),
(8, 'IPS SMP', 120000, 'Ilmu Pengetahuan Sosial untuk siswa SMP');

-- Tabel bookings: pemesanan jadwal belajar
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    learner_id INT NOT NULL,
    tutor_id INT NOT NULL,
    subject_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    duration INT DEFAULT 60 COMMENT 'Durasi dalam menit',
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (learner_id) REFERENCES siswa(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_id) REFERENCES tutor(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
);

-- Insert sample bookings
INSERT INTO bookings (learner_id, tutor_id, subject_id, booking_date, booking_time, duration, status, notes) VALUES
-- Booking dari Rizky Pratama (siswa_id=1) - SMA
(1, 1, 3, '2025-12-15', '14:00:00', 90, 'confirmed', 'Persiapan ujian matematika semester, fokus pada integral dan turunan'),
(1, 2, 5, '2025-12-18', '16:00:00', 60, 'pending', 'Belajar fisika gelombang dan bunyi'),
-- Booking dari Alya Kinanti (siswa_id=2) - SD
(2, 3, 7, '2025-12-14', '10:00:00', 60, 'completed', 'English conversation practice, belajar vocabulary harian'),
(2, 2, 6, '2025-12-20', '15:00:00', 60, 'confirmed', 'Belajar IPA tentang sistem tata surya'),
-- Booking dari Andreas Kurniawan (siswa_id=3) - SMP
(3, 5, 14, '2025-12-16', '13:00:00', 90, 'confirmed', 'Biologi sel dan jaringan, persiapan ulangan'),
(3, 8, 23, '2025-12-22', '11:00:00', 60, 'pending', 'IPS - Geografi Indonesia'),
-- Booking dari Siti Fatimah (siswa_id=4) - SMA
(4, 8, 21, '2025-12-17', '14:30:00', 60, 'confirmed', 'Ekonomi mikro dan perilaku konsumen'),
(4, 8, 22, '2025-12-19', '16:00:00', 90, 'pending', 'Akuntansi - jurnal umum dan buku besar'),
-- Booking dari Dinda Puspita (siswa_id=6) - SMP
(6, 3, 8, '2025-12-13', '09:00:00', 60, 'completed', 'Grammar dasar - tenses dan sentence structure'),
(6, 7, 18, '2025-12-21', '10:00:00', 60, 'confirmed', 'Belajar kunci dasar gitar dan lagu sederhana'),
-- Booking dari Fajar Nugraha (siswa_id=7) - SMA
(7, 4, 11, '2025-12-23', '15:00:00', 90, 'pending', 'Belajar Python - variables, loops, dan functions'),
(7, 2, 5, '2025-12-25', '13:00:00', 60, 'pending', 'Fisika - gerak parabola dan hukum Newton'),
-- Booking dari Grace Natalia (siswa_id=8) - SD
(8, 7, 19, '2025-12-24', '11:00:00', 60, 'confirmed', 'Piano pemula - belajar not balok dan lagu anak');

-- Tabel reviews: rating dan review dari learner
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    learner_id INT NOT NULL,
    tutor_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (learner_id) REFERENCES siswa(id) ON DELETE CASCADE,
    FOREIGN KEY (tutor_id) REFERENCES tutor(id) ON DELETE CASCADE,
    UNIQUE KEY unique_booking_review (booking_id)
);

-- Insert sample reviews untuk booking yang sudah completed
INSERT INTO reviews (booking_id, learner_id, tutor_id, rating, review_text) VALUES
(3, 2, 3, 5, 'Pak Ahmad sangat sabar dalam mengajar. Metode conversation practice-nya sangat membantu meningkatkan kepercayaan diri saya berbahasa Inggris. Highly recommended! 👍'),
(9, 6, 3, 5, 'Penjelasan grammar sangat jelas dan mudah dipahami. Kakak Fauzi memberikan banyak contoh praktis yang langsung bisa diaplikasikan. Terima kasih!');

-- Update rating tutor berdasarkan reviews yang ada
UPDATE tutor t 
SET rating = (
    SELECT COALESCE(ROUND(AVG(r.rating), 1), t.rating)
    FROM reviews r 
    WHERE r.tutor_id = t.id
)
WHERE EXISTS (
    SELECT 1 FROM reviews r WHERE r.tutor_id = t.id
);

-- Create indexes untuk performance
CREATE INDEX idx_bookings_learner ON bookings(learner_id);
CREATE INDEX idx_bookings_tutor ON bookings(tutor_id);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_bookings_date ON bookings(booking_date);
CREATE INDEX idx_reviews_tutor ON reviews(tutor_id);
CREATE INDEX idx_subjects_tutor ON subjects(tutor_id);