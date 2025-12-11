<?php
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'POST') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$headers = getallheaders();
$authorization = $headers['Authorization'] ?? $headers['authorization'] ?? '';

if (!$authorization || stripos($authorization, 'Bearer ') !== 0) {
    response_error(401, 'Token tidak ditemukan.', [
        'reason' => 'missing_token',
    ]);
}

$token = trim(substr($authorization, 7));

if ($token === '') {
    response_error(401, 'Token tidak valid.', [
        'reason' => 'empty_token',
    ]);
}

$pdo = get_pdo();

try {
    $stmt = $pdo->prepare('SELECT id FROM auth_tokens WHERE token = :token LIMIT 1');
    $stmt->execute([':token' => $token]);
    $row = $stmt->fetch();

    if (!$row) {
        response_error(403, 'Token tidak dikenali.', [
            'reason' => 'invalid_token',
    ]);
    }

    $deleteStmt = $pdo->prepare('DELETE FROM auth_tokens WHERE id = :id');
    $deleteStmt->execute([':id' => $row['id']]);

    response_success(200, 'Logout berhasil.', [
        'revoked_token' => $token,
    ]);
} catch (PDOException $e) {
    response_error(500, 'Terjadi kesalahan pada server.', [
        'reason' => 'database_error',
        'detail' => $e->getMessage(),
    ]);
}
