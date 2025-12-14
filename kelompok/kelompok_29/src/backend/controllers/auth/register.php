<?php
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/validation.php';
require_once __DIR__ . '/../../helpers/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$rawInput = file_get_contents('php://input');
$payload = json_decode($rawInput, true) ?? [];
$payload = array_map(fn($value) => is_string($value) ? trim($value) : $value, $payload);

$errors = require_fields($payload, ['full_name', 'email', 'password', 'confirm_password', 'phone', 'address', 'nik']);

if (!isset($payload['email']) || !filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = ['field' => 'email', 'message' => 'Format email tidak valid.'];
}

if (!isset($payload['password']) || strlen($payload['password']) < 8) {
    $errors[] = ['field' => 'password', 'message' => 'Password minimal 8 karakter.'];
}

if (isset($payload['password'], $payload['confirm_password']) && $payload['password'] !== $payload['confirm_password']) {
    $errors[] = ['field' => 'confirm_password', 'message' => 'Konfirmasi password harus sama.'];
}

if (!isset($payload['nik']) || !preg_match('/^\d{16}$/', $payload['nik'])) {
    $errors[] = ['field' => 'nik', 'message' => 'NIK harus terdiri dari 16 digit angka.'];
}

if ($errors) {
    response_error(422, 'Validasi gagal.', $errors);
}

$pdo = get_pdo();

try {
    $checkStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $checkStmt->execute([':email' => $payload['email']]);
    $existingUser = $checkStmt->fetch();

    if ($existingUser) {
        response_error(409, 'Email sudah terdaftar.', [
            'field' => 'email',
            'message' => 'Gunakan email lain atau lakukan login.',
        ]);
    }

    $existingNikStmt = $pdo->prepare('SELECT id FROM users WHERE nik = :nik LIMIT 1');
    $existingNikStmt->execute([':nik' => $payload['nik']]);
    $existingNik = $existingNikStmt->fetch();

    if ($existingNik) {
        response_error(409, 'NIK sudah terdaftar.', [
            'field' => 'nik',
            'message' => 'Gunakan NIK lain atau hubungi admin.',
        ]);
    }

    $hash = password_hash($payload['password'], PASSWORD_BCRYPT);

    $insertStmt = $pdo->prepare(
        'INSERT INTO users (full_name, email, password, phone, nik, address, role, created_at, updated_at)
         VALUES (:full_name, :email, :password, :phone, :nik, :address, :role, NOW(), NOW())'
    );

    $insertStmt->execute([
        ':full_name' => $payload['full_name'],
        ':email'     => $payload['email'],
        ':password'  => $hash,
        ':phone'     => $payload['phone'],
        ':nik'       => $payload['nik'],
        ':address'   => $payload['address'],
        ':role'      => 'warga',
    ]);

    $userId = $pdo->lastInsertId();

    response_success(201, 'Pelapor berhasil terdaftar.', [
        'user_id' => $userId,
        'role' => 'warga',
        'nik' => $payload['nik'],
    ]);
} catch (PDOException $e) {
    response_error(500, 'Terjadi kesalahan pada server.', [
        'reason' => 'database_error',
        'detail' => $e->getMessage(),
    ]);
}