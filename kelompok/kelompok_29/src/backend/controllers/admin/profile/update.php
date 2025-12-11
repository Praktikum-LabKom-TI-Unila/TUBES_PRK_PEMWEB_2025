<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/validation.php';
require_once __DIR__ . '/../../../helpers/admin.php';
require_once __DIR__ . '/../../../helpers/upload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    response_error(405, 'Method tidak diperbolehkan.');
}


hydrate_streamed_multipart_if_needed();

$admin = require_admin();
$adminId = $admin['id'];

$contentType = $_SERVER['HTTP_CONTENT_TYPE'] ?? $_SERVER['CONTENT_TYPE'] ?? '';
$rawBody = file_get_contents('php://input');
$payload = [];

if (stripos($contentType, 'application/json') !== false) {
    $payload = json_decode($rawBody, true);
    if (!is_array($payload)) {
        response_error(400, 'Payload harus berupa JSON valid.', [
            'reason' => 'invalid_json',
        ]);
    }
} else {
    $payload = $_POST;
}

$payload = is_array($payload) ? $payload : [];
$payload = array_map(function ($value) {
    return is_string($value) ? trim($value) : $value;
}, $payload);

$requiredErrors = require_fields($payload, ['full_name', 'email', 'phone']);
if ($requiredErrors) {
    response_error(422, 'Field wajib belum lengkap.', $requiredErrors);
}

if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
    response_error(422, 'Format email tidak valid.', [
        [
            'field' => 'email',
            'reason' => 'invalid_email',
        ],
    ]);
}

$phoneDigits = preg_replace('/[^0-9]/', '', $payload['phone']);
if (strlen($phoneDigits) < 9) {
    response_error(422, 'Nomor telepon minimal 9 digit.', [
        [
            'field' => 'phone',
            'reason' => 'phone_invalid',
        ],
    ]);
}

$password = $payload['password'] ?? '';
if ($password !== '' && strlen($password) < 8) {
    response_error(422, 'Password minimal 8 karakter.', [
        [
            'field' => 'password',
            'reason' => 'password_too_short',
        ],
    ]);
}

if ($password !== '') {
    $confirm = $payload['confirm_password'] ?? '';
    if ($confirm !== $password) {
        response_error(422, 'Konfirmasi password tidak cocok.', [
            [
                'field' => 'confirm_password',
                'reason' => 'password_not_match',
            ],
        ]);
    }
}

$pdo = get_pdo();

$currentStmt = $pdo->prepare('SELECT email, phone, address, profile_photo FROM users WHERE id = :id LIMIT 1');
$currentStmt->execute([':id' => $adminId]);
$current = $currentStmt->fetch();

if (!$current) {
    response_error(404, 'Data admin tidak ditemukan.');
}

$dupEmail = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
$dupEmail->execute([':email' => $payload['email'], ':id' => $adminId]);
if ($dupEmail->fetch()) {
    response_error(409, 'Email sudah digunakan.', [
        [
            'field' => 'email',
            'reason' => 'email_taken',
        ],
    ]);
}

$dupPhone = $pdo->prepare('SELECT id FROM users WHERE phone = :phone AND id <> :id LIMIT 1');
$dupPhone->execute([':phone' => $payload['phone'], ':id' => $adminId]);
if ($dupPhone->fetch()) {
    response_error(409, 'Nomor telepon sudah digunakan.', [
        [
            'field' => 'phone',
            'reason' => 'phone_taken',
        ],
    ]);
}

$newPhotoPath = null;
$photoDirectory = 'profile_photos';
if (!empty($payload['photo_base64'])) {
    $newPhotoPath = save_base64_image($payload['photo_base64'], $photoDirectory);
} else {
    $uploadedFile = find_uploaded_file($_FILES, ['photo', 'profile_photo']);
    if ($uploadedFile) {
        $newPhotoPath = save_uploaded_file($uploadedFile, $photoDirectory);
    }
}

$updates = [];
$params = [
    ':id' => $adminId,
    ':full_name' => $payload['full_name'],
    ':email' => $payload['email'],
    ':phone' => $payload['phone'],
    ':address' => $payload['address'] ?? $current['address'],
];

$updates[] = 'full_name = :full_name';
$updates[] = 'email = :email';
$updates[] = 'phone = :phone';
$updates[] = 'address = :address';

if ($password !== '') {
    $updates[] = 'password = :password';
    $params[':password'] = password_hash($password, PASSWORD_BCRYPT);
}

if ($newPhotoPath !== null) {
    $updates[] = 'profile_photo = :profile_photo';
    $params[':profile_photo'] = $newPhotoPath;
}

if (count($updates) === 0) {
    response_error(400, 'Tidak ada perubahan yang dikirim.');
}

$updates[] = 'updated_at = NOW()';
$sql = 'UPDATE users SET ' . implode(', ', $updates) . ' WHERE id = :id';

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
} catch (PDOException $e) {
    response_error(500, 'Gagal memperbarui profil admin.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

if ($newPhotoPath && !empty($current['profile_photo']) && $current['profile_photo'] !== $newPhotoPath) {
    $baseDir = dirname(dirname(dirname(__DIR__)));
    $previousPath = $baseDir . '/' . $current['profile_photo'];
    if (is_file($previousPath)) {
        @unlink($previousPath);
    }
}

$refreshStmt = $pdo->prepare('SELECT id, full_name, email, phone, address, profile_photo FROM users WHERE id = :id LIMIT 1');
$refreshStmt->execute([':id' => $adminId]);
$latest = $refreshStmt->fetch();

response_success(200, 'Profil admin berhasil diperbarui.', [
    'id' => (int) $latest['id'],
    'full_name' => $latest['full_name'],
    'email' => $latest['email'],
    'phone' => $latest['phone'],
    'address' => $latest['address'],
    'profile_photo' => $latest['profile_photo'],
]);

/*
Contoh Request (JSON + base64 foto opsional):
PUT /api/admin/profile/update
Authorization: Bearer <token_admin>
Content-Type: application/json
{
  "full_name": "Admin Baru",
  "email": "admin.baru@sipinda.go.id",
  "phone": "081234567890",
  "address": "Jl. Jendral Sudirman No. 1",
  "password": "RahasiaBaru123",
  "confirm_password": "RahasiaBaru123",
  "photo_base64": "data:image/png;base64,iVBORw0KGgo..."
}

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Profil admin berhasil diperbarui.",
  "data": {
    "id": 1,
    "full_name": "Admin Baru",
    "email": "admin.baru@sipinda.go.id",
    "phone": "081234567890",
    "address": "Jl. Jendral Sudirman No. 1",
    "profile_photo": "uploads/admin/upload_abcd.png"
  },
  "errors": []
}

Contoh Response Error (email bentrok):
{
  "status": "error",
  "code": 409,
  "message": "Email sudah digunakan.",
  "data": [],
  "errors": [
    {
      "field": "email",
      "reason": "email_taken"
    }
  ]
}
*/
