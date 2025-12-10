<?php
// auth/login_session.php - Login handler dengan session
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../fungsi_helper.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }
    
    $input = json_decode(file_get_contents('php://input'), true);
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    
    if (!$email || !$password) {
        throw new Exception('Email dan password harus diisi');
    }
    
    // Cari user (support both password and password_hash columns)
    $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception('Email atau password salah');
    }
    
    // Verifikasi password (check both column names)
    $password_hash = $user['password_hash'] ?? $user['password'] ?? '';
    if (!password_verify($password, $password_hash)) {
        throw new Exception('Email atau password salah');
    }
    
    // Set session
    $_SESSION['logged_in'] = true;
    $_SESSION['pengguna_id'] = $user['id'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    
    // Log aktivitas
    catat_log('login', 'pengguna', $user['id'], 'Login ke sistem');
    
    // Redirect berdasarkan role
    $redirect = '';
    switch ($user['role']) {
        case 'admin':
            $redirect = 'admin/beranda_admin.php';
            break;
        case 'petugas':
            $redirect = 'petugas/beranda_petugas.php';
            break;
        case 'warga':
            $redirect = 'warga/beranda_warga.php';
            break;
    }
    
    json_response([
        'success' => true,
        'message' => 'Login berhasil',
        'data' => [
            'nama' => $user['nama'],
            'role' => $user['role'],
            'redirect' => $redirect
        ]
    ]);
    
} catch (Exception $e) {
    json_response([
        'success' => false,
        'message' => $e->getMessage()
    ], 400);
}
