<?php
require_once __DIR__ . '/response.php';
require_once __DIR__ . '/database.php';

function require_officer(): array
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
        'SELECT at.user_id, at.expires_at,
                u.full_name, u.email, u.phone, u.role, u.address, u.profile_photo,
                o.id AS officer_id, o.employee_id, o.department, o.specialization, o.officer_status, o.created_at
         FROM auth_tokens at
         JOIN users u ON u.id = at.user_id
         JOIN officers o ON o.user_id = u.id
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

    if ($record['role'] !== 'petugas') {
        response_error(403, 'Akses hanya untuk petugas.', [
            'reason' => 'forbidden_role',
        ]);
    }

    return [
        'user_id' => (int) $record['user_id'],
        'officer_id' => (int) $record['officer_id'],
        'token' => $token,
        'full_name' => $record['full_name'],
        'email' => $record['email'],
        'phone' => $record['phone'],
        'address' => $record['address'],
        'profile_photo' => $record['profile_photo'],
        'employee_id' => $record['employee_id'],
        'department' => $record['department'],
        'specialization' => $record['specialization'],
        'status' => $record['officer_status'],
        'joined_at' => $record['created_at'],
    ];
}
