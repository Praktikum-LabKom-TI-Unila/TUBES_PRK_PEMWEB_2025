<?php
// seed_admin.php - Auto create admin user
// Run from CLI: php seed_admin.php
require __DIR__ . '/helpers.php';

if (php_sapi_name() !== 'cli') {
    echo "This script must be run from the command line.\n";
    exit(1);
}

// Data admin default
$nama = 'Admin CleanSpot';
$email = 'admin@cleanspot.com';
$password = 'admin123'; // Ganti password ini di production!
$telepon = '081234567890';
$alamat = 'Kantor CleanSpot Bandar Lampung';

echo "=== Membuat Admin User ===\n";
echo "Nama  : $nama\n";
echo "Email : $email\n";
echo "Pass  : $password\n\n";

try {
    $pdo = db();
    
    // Cek apakah admin sudah ada
    $stmt = $pdo->prepare("SELECT id FROM pengguna WHERE email = :email");
    $stmt->execute(['email' => $email]);
    
    if ($stmt->fetch()) {
        echo "❌ Admin dengan email $email sudah ada!\n";
        echo "Hapus dulu dari database jika ingin buat ulang.\n";
        exit(1);
    }
    
    // Hash password
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert admin
    $ins = $pdo->prepare("
        INSERT INTO pengguna (nama, email, password_hash, role, telepon, alamat, created_at, updated_at) 
        VALUES (:nama, :email, :hash, 'admin', :telepon, :alamat, NOW(), NOW())
    ");
    
    $ins->execute([
        'nama' => $nama,
        'email' => $email,
        'hash' => $hash,
        'telepon' => $telepon,
        'alamat' => $alamat
    ]);
    
    $admin_id = $pdo->lastInsertId();
    
    echo "✅ Admin berhasil dibuat!\n";
    echo "ID: $admin_id\n";
    echo "\n=== Login Info ===\n";
    echo "Email    : $email\n";
    echo "Password : $password\n";
    echo "\n⚠️  PENTING: Ganti password setelah login pertama!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

