<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/auth.php';
require_once __DIR__ . '/../../../helpers/schema.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$pelapor = require_pelapor();
$pdo = get_pdo();

$fields = 'id, full_name, email, phone, nik, address, role, created_at, updated_at';
$hasProfilePhoto = table_has_column($pdo, 'users', 'profile_photo');
if ($hasProfilePhoto) {
    $fields .= ', profile_photo';
}

$stmt = $pdo->prepare("SELECT {$fields} FROM users WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $pelapor['id']]);
$profile = $stmt->fetch();

if (!$profile) {
    response_error(404, 'Profil tidak ditemukan.');
}

$data = [
    'id' => (int) $profile['id'],
    'full_name' => $profile['full_name'],
    'email' => $profile['email'],
    'phone' => $profile['phone'],
    'nik' => $profile['nik'],
    'address' => $profile['address'],
    'role' => $profile['role'],
    'created_at' => $profile['created_at'],
    'updated_at' => $profile['updated_at'],
    'profile_photo' => $hasProfilePhoto ? ($profile['profile_photo'] ?? null) : null,
];

response_success(200, 'Profil pelapor.', $data);
