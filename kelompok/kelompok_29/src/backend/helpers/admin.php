<?php
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/database.php';

function require_admin(): array
{
    $headers = function_exists('getallheaders') ? getallheaders() : [];
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
    $stmt = $pdo->prepare(
        'SELECT at.user_id, at.expires_at, u.full_name, u.email, u.role
         FROM auth_tokens at
         JOIN users u ON u.id = at.user_id
         WHERE at.token = :token
         LIMIT 1'
    );
    $stmt->execute([':token' => $token]);
    $record = $stmt->fetch();

    if (!$record) {
        response_error(401, 'Token tidak dikenali.', [
            'reason' => 'invalid_token',
        ]);
    }

    if (!empty($record['expires_at']) && strtotime($record['expires_at']) <= time()) {
        response_error(401, 'Token kedaluwarsa.', [
            'reason' => 'expired_token',
        ]);
    }

    if ($record['role'] !== 'admin') {
        response_error(403, 'Akses hanya untuk admin.', [
            'reason' => 'forbidden_role',
        ]);
    }

    return [
        'id' => (int) $record['user_id'],
        'token' => $token,
        'full_name' => $record['full_name'],
        'email' => $record['email'],
    ];
}
