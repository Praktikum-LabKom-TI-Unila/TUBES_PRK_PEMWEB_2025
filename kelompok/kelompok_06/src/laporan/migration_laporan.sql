-- ========================================
-- DATABASE MODUL LAPORAN PENGADUAN
-- Anggota 3: Nabila Salwa
-- ========================================

-- Membuat tabel laporan
CREATE TABLE IF NOT EXISTS laporan (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    judul_laporan VARCHAR(255) NOT NULL,
    isi_laporan TEXT NOT NULL,
    status ENUM('pending', 'diproses', 'selesai', 'ditolak') DEFAULT 'pending',
    tanggal TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tanggal_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key ke tabel users (akan dibuat oleh Anggota 1)
    CONSTRAINT fk_laporan_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Index untuk mempercepat query
CREATE INDEX idx_user_id ON laporan(user_id);
CREATE INDEX idx_status ON laporan(status);
CREATE INDEX idx_tanggal ON laporan(tanggal);

-- ========================================
-- DATA DUMMY UNTUK TESTING (OPSIONAL)
-- ========================================
-- Uncomment jika ingin menambahkan data dummy untuk testing

-- INSERT INTO laporan (user_id, judul_laporan, isi_laporan, status) VALUES
-- (1, 'Bus Terlambat', 'Bus jurusan A-B sering terlambat 30 menit dari jadwal', 'pending'),
-- (2, 'AC Bus Rusak', 'AC di bus nomor 123 tidak berfungsi dengan baik', 'diproses'),
-- (1, 'Kursi Rusak', 'Terdapat kursi yang rusak di bus nomor 456', 'selesai');
