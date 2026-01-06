<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/validation.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/auth.php';
require_once __DIR__ . '/../../../helpers/upload.php';
require_once __DIR__ . '/../../../helpers/schema.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$isMultipart = stripos($contentType, 'multipart/form-data') !== false;

if ($isMultipart) {
    $payload = $_POST ?? [];
} else {
    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true) ?? [];
}

unset($payload['_method']);

$payload = array_map(function ($value) {
    return is_string($value) ? trim($value) : $value;
}, $payload);

$errors = require_fields($payload, ['full_name', 'email', 'phone', 'nik', 'address']);

if (!empty($payload['email']) && !filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = ['field' => 'email', 'message' => 'Format email tidak valid.'];
}

if (!empty($payload['nik']) && !preg_match('/^\d{16}$/', $payload['nik'])) {
    $errors[] = ['field' => 'nik', 'message' => 'NIK harus terdiri dari 16 digit angka.'];
}

if ($errors) {
    response_error(422, 'Validasi gagal.', $errors);
}

$pelapor = require_pelapor();
$pdo = get_pdo();

$pdo->beginTransaction();

try {
    $duplicateEmailStmt = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id != :id LIMIT 1');
    $duplicateEmailStmt->execute([
        ':email' => $payload['email'],
        ':id' => $pelapor['id'],
    ]);
    if ($duplicateEmailStmt->fetch()) {
        $pdo->rollBack();
        response_error(409, 'Email sudah digunakan pengguna lain.', [
            'field' => 'email',
            'reason' => 'duplicate',
        ]);
    }

    $duplicateNikStmt = $pdo->prepare('SELECT id FROM users WHERE nik = :nik AND id != :id LIMIT 1');
    $duplicateNikStmt->execute([
        ':nik' => $payload['nik'],
        ':id' => $pelapor['id'],
    ]);
    if ($duplicateNikStmt->fetch()) {
        $pdo->rollBack();
        response_error(409, 'NIK sudah digunakan pengguna lain.', [
            'field' => 'nik',
            'reason' => 'duplicate',
        ]);
    }

    $photoPath = null;
    $currentPhotoPath = null;
    $hasProfilePhotoColumn = table_has_column($pdo, 'users', 'profile_photo');

    if ($isMultipart && isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $photoPath = save_uploaded_file($_FILES['photo'], 'profile_photos');
    } elseif (!empty($payload['photo_base64'])) {
        $photoPath = save_base64_image($payload['photo_base64'], 'profile_photos');
    }

    if ($hasProfilePhotoColumn) {
        $currentPhotoStmt = $pdo->prepare('SELECT profile_photo FROM users WHERE id = :id');
        $currentPhotoStmt->execute([':id' => $pelapor['id']]);
        $current = $currentPhotoStmt->fetch();
        $currentPhotoPath = $current['profile_photo'] ?? null;

        if ($photoPath && $currentPhotoPath) {
            $existingPath = dirname(__DIR__, 3) . '/' . $currentPhotoPath;
            if (is_file($existingPath)) {
                @unlink($existingPath);
            }
        }
    }

    $fields = [
        'full_name = :full_name',
        'email = :email',
        'phone = :phone',
        'nik = :nik',
        'address = :address',
        'updated_at = NOW()',
    ];

    $params = [
        ':full_name' => $payload['full_name'],
        ':email' => $payload['email'],
        ':phone' => $payload['phone'],
        ':nik' => $payload['nik'],
        ':address' => $payload['address'],
        ':id' => $pelapor['id'],
    ];

    if ($hasProfilePhotoColumn) {
        $fields[] = 'profile_photo = :profile_photo';
        $params[':profile_photo'] = $photoPath ?: $currentPhotoPath;
    }

    $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
    $updateStmt = $pdo->prepare($sql);
    $updateStmt->execute($params);

    $pdo->commit();

    response_success(200, 'Profil berhasil diperbarui.', [
        'id' => $pelapor['id'],
        'full_name' => $payload['full_name'],
        'email' => $payload['email'],
        'phone' => $payload['phone'],
        'nik' => $payload['nik'],
        'address' => $payload['address'],
        'profile_photo' => $photoPath ?: $currentPhotoPath,
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    response_error(500, 'Terjadi kesalahan pada server.', [
        'reason' => 'database_error',
        'detail' => $e->getMessage(),
    ]);
}

