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

$errors = require_fields($payload, ['identifier', 'password']);
if ($errors) {
    response_error(422, 'Validasi gagal.', $errors);
}

$pdo = get_pdo();

try {
    $stmt = $pdo->prepare(
        'SELECT id, full_name, email, password AS password_hash, role
         FROM users
         WHERE email = :identifier OR phone = :identifier OR nik = :identifier
         LIMIT 1'
    );
    $stmt->execute([':identifier' => $payload['identifier']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($payload['password'], $user['password_hash'])) {
        response_error(401, 'Kredensial tidak valid.', [
            'reason' => 'invalid_credentials',
        ]);
    }

    $token = bin2hex(random_bytes(32));

    $tokenStmt = $pdo->prepare(
        'INSERT INTO auth_tokens (id, user_id, token, created_at, expires_at)
         VALUES (UUID(), :user_id, :token, NOW(), DATE_ADD(NOW(), INTERVAL 7 DAY))'
    );
    $tokenStmt->execute([
        ':user_id' => $user['id'],
        ':token'   => $token,
    ]);

    response_success(200, 'Login berhasil.', [
        'token' => $token,
        'user' => [
            'id' => $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ],
    ]);
} catch (PDOException $e) {
    response_error(500, 'Terjadi kesalahan pada server.', [
        'reason' => 'database_error',
        'detail' => $e->getMessage(),
    ]);
}