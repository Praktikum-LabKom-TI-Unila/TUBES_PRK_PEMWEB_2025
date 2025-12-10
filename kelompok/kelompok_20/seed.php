<?php
// seed.php - Script untuk Reset & Isi Data Dummy

// 1. KONEKSI DATABASE (Sesuaikan credential jika beda)
// seed.php
$host = 'localhost';
$db   = 'myunila_lostfound'; // Database sesuai dengan myunila_lostfound.sql
$user = 'root';       // Default Laragon
$pass = '';           // Default Laragon (KOSONG)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "✅ Koneksi Database Berhasil.\n";
} catch (\PDOException $e) {
    die("❌ Koneksi Gagal: " . $e->getMessage());
}

// 2. BERSIHKAN DATA LAMA (Reset)
try {
    // Matikan foreign key check biar bisa truncate
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    
    $pdo->exec("TRUNCATE TABLE notifications");
    $pdo->exec("TRUNCATE TABLE comments");
    $pdo->exec("TRUNCATE TABLE claims");
    $pdo->exec("TRUNCATE TABLE items");
    $pdo->exec("TRUNCATE TABLE users");
    $pdo->exec("TRUNCATE TABLE categories");
    $pdo->exec("TRUNCATE TABLE locations");
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "✅ Data lama berhasil dibersihkan.\n";
} catch (Exception $e) {
    die("❌ Gagal Reset Data: " . $e->getMessage());
}

// 3. ISI MASTER DATA (Kategori & Lokasi) - Sesuai dengan myunila_lostfound.sql
$categories = [
    'Elektronik',
    'Dokumen',
    'Pakaian',
    'Aksesoris',
    'Kunci',
    'Tas & Dompet',
    'Buku & Alat Tulis',
    'Kendaraan',
    'Lainnya'
];
foreach ($categories as $cat) {
    $stmt = $pdo->prepare("INSERT INTO categories (name, created_at) VALUES (?, NOW())");
    $stmt->execute([$cat]);
}
echo "✅ Kategori dummy dibuat (9 kategori).\n";

$locations = [
    'Gedung Rektorat',
    'Gedung Serba Guna (GSG)',
    'Perpustakaan Pusat',
    'Kantin Terpadu',
    'Gedung A - FMIPA',
    'Gedung B - Fakultas Teknik',
    'Gedung C - FISIP',
    'Gedung D - Fakultas Hukum',
    'Gedung E - Fakultas Ekonomi',
    'Gedung F - FKIP',
    'Gedung G - Fakultas Pertanian',
    'Gedung H - Fakultas Kedokteran',
    "Masjid Al-Wasi'i",
    'Lapangan Olahraga',
    'Parkiran Motor Pusat',
    'Parkiran Mobil Pusat',
    'UPT Bahasa',
    'Poliklinik Unila',
    'Asrama Mahasiswa',
    'Lainnya'
];
foreach ($locations as $loc) {
    $stmt = $pdo->prepare("INSERT INTO locations (name, created_at) VALUES (?, NOW())");
    $stmt->execute([$loc]);
}
echo "✅ Lokasi dummy dibuat (20 lokasi).\n";

// 4. ISI DATA USER (Admin & Mahasiswa)
// Password default: 'password123' (Dihash)
$password = password_hash('password123', PASSWORD_DEFAULT);

$users = [
    ['Admin Unila', 'ADMIN001', 'admin@unila.ac.id', '081234567890', 'admin', 1],
    ['Budi Santoso', '1817051001', 'budi@students.unila.ac.id', '08987654321', 'user', 1],
    ['Siti Aminah', '1817051002', 'siti@students.unila.ac.id', '08111222333', 'user', 1],
    ['Spammer Jahat', '1817051003', 'hacker@students.unila.ac.id', '000000000', 'user', 0], // User is_active = 0 (banned)
];

foreach ($users as $u) {
    $stmt = $pdo->prepare("INSERT INTO users (name, identity_number, email, phone, role, is_active, password, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$u[0], $u[1], $u[2], $u[3], $u[4], $u[5], $password]);
}
echo "✅ User dummy dibuat (Password semua: password123).\n";

// Ambil ID User untuk relasi item
$userId = $pdo->query("SELECT id FROM users WHERE email='budi@students.unila.ac.id'")->fetchColumn();
$userId2 = $pdo->query("SELECT id FROM users WHERE email='siti@students.unila.ac.id'")->fetchColumn();

// 5. ISI DATA BARANG (ITEMS) - Dengan Gambar Placeholder dari Unsplash
// Format: [user_id, category_id, location_id, title, description, type, status, image_path]

$items = [
    [$userId, 1, 6, 'Laptop ASUS ROG', 'Laptop gaming hilang di ruang kelas gedung teknik, warna hitam dengan stiker Apple di belakang. Kondisi masih mulus, spesifikasi i7 gen 10.', 'lost', 'open', 'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=400'],
    [$userId2, 2, 3, 'KTM dan KTP', 'Dokumen penting berupa KTM Unila dan KTP hilang di area perpustakaan pusat lantai 2. Nama tercantum: Siti Aminah. Mohon bantuannya.', 'lost', 'open', 'https://images.unsplash.com/photo-1589829085413-56de8ae18c73?w=400'],
    [$userId, 6, 15, 'Dompet Kulit Coklat', 'Ditemukan dompet kulit warna coklat di parkiran motor pusat. Berisi uang tunai dan beberapa kartu. Silakan hubungi dengan menyebutkan ciri-ciri.', 'found', 'open', 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=400'],
    [$userId2, 5, 4, 'Kunci Motor Honda Beat', 'Kunci motor Honda Beat dengan gantungan boneka karakter Rilakkuma. Hilang di area kantin terpadu sekitar jam 12 siang.', 'lost', 'open', 'https://images.unsplash.com/photo-1582139329536-e7284fece509?w=400'],
    [$userId, 7, 3, 'Buku Catatan Biru', 'Ditemukan buku catatan berwarna biru di perpustakaan. Berisi catatan mata kuliah Kalkulus. Ada nama inisial "RD" di cover.', 'found', 'open', 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=400'],
    [$userId2, 4, 13, 'Kacamata Hitam', 'Kacamata hitam merk Ray-Ban hilang di Masjid Al-Wasi\'i setelah sholat Dzuhur. Frame hitam dengan lensa polarized.', 'lost', 'process', 'https://images.unsplash.com/photo-1511499767150-a48a237f0083?w=400'],
    [$userId, 1, 2, 'Smartwatch Apple Watch', 'Ditemukan Apple Watch Series 7 warna silver di GSG setelah acara seminar. Kondisi mati baterai, ada wallpaper foto keluarga.', 'found', 'process', 'https://images.unsplash.com/photo-1579586337278-3befd40fd17a?w=400'],
    [$userId2, 6, 6, 'Tas Ransel Hitam', 'Tas ransel merk Eiger warna hitam hilang di gedung fakultas teknik lantai 3. Berisi laptop, buku, dan alat tulis. Sangat penting!', 'lost', 'open', 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=400'],
    [$userId, 3, 10, 'Jaket Almamater Unila', 'Ditemukan jaket almamater Unila ukuran L di FKIP. Ada name tag dengan nama "Ahmad" di bagian dalam. Warna biru khas Unila.', 'found', 'closed', 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=400'],
    [$userId2, 1, 1, 'Charger Laptop Lenovo', 'Charger laptop merk Lenovo 65W hilang di gedung rektorat ruang tunggu. Kabel warna hitam dengan adaptor kotak.', 'lost', 'closed', 'https://images.unsplash.com/photo-1625948515291-69613efd103f?w=400'],
];

$stmt = $pdo->prepare("INSERT INTO items (user_id, category_id, location_id, title, description, type, status, image_path, incident_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

foreach ($items as $item) {
    $stmt->execute([$item[0], $item[1], $item[2], $item[3], $item[4], $item[5], $item[6], $item[7]]);
}

echo "✅ Item dummy dibuat (10 items dengan gambar dari Unsplash).\n";
echo "🎉 SEEDING SELESAI! Silakan cek Dashboard Admin.\n";
?>