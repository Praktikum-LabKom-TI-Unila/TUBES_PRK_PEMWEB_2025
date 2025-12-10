<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$config = [
    'db' => [
        'host' => 'localhost',
        'dbname' => 'cleanspot_db',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4',
    ],
    'jwt_secret' => 'ganti_dengan_secret_panjang_dan_random_2025!',
    'admin_code' => '123'
];
try {
    $pdo = new PDO(
        "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']};charset={$config['db']['charset']}", 
        $config['db']['user'], 
        $config['db']['pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8mb4");
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
function is_admin(): bool {
    return isset($_SESSION['user_id']) && (($_SESSION['role'] ?? '') === 'admin');
}