<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/validation.php';
require_once __DIR__ . '/../../../helpers/officer.php';
require_once __DIR__ . '/../../../helpers/upload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    response_error(405, 'Method tidak diperbolehkan.');
}

hydrate_streamed_multipart_if_needed();

$session = require_officer();
$userId = $session['user_id'];
$officerId = $session['officer_id'];

$contentType = $_SERVER['HTTP_CONTENT_TYPE'] ?? $_SERVER['CONTENT_TYPE'] ?? '';
$rawBody = file_get_contents('php://input');
$payload = [];

if (stripos($contentType, 'application/json') !== false) {
    $payload = json_decode($rawBody, true);
    if (!is_array($payload)) {
        response_error(400, 'Payload harus JSON valid.', [
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

$requiredErrors = require_fields($payload, ['full_name', 'email', 'phone', 'department', 'specialization']);
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

$allowedSpecializations = [
    'Jalan_Raya', 'Penerangan_Jalan', 'Drainase', 'Trotoar', 'Taman',
    'Jembatan', 'Rambu_Lalu_Lintas', 'Fasilitas_Umum', 'Lainnya',
];

if (!in_array($payload['specialization'], $allowedSpecializations, true)) {
    response_error(422, 'Spesialisasi tidak dikenali.', [
        [
            'field' => 'specialization',
            'reason' => 'invalid_specialization',
        ],
    ]);
}

$employeeId = isset($payload['employee_id']) ? trim((string) $payload['employee_id']) : null;

$pdo = get_pdo();

$currentStmt = $pdo->prepare(
    'SELECT u.email, u.phone, u.address, u.profile_photo,
            o.employee_id
     FROM users u
     JOIN officers o ON o.user_id = u.id
     WHERE u.id = :id
     LIMIT 1'
);
$currentStmt->execute([':id' => $userId]);
$current = $currentStmt->fetch();

if (!$current) {
    response_error(404, 'Data petugas tidak ditemukan.');
}

$dupEmail = $pdo->prepare('SELECT id FROM users WHERE email = :email AND id <> :id LIMIT 1');
$dupEmail->execute([':email' => $payload['email'], ':id' => $userId]);
if ($dupEmail->fetch()) {
    response_error(409, 'Email sudah digunakan.', [
        [
            'field' => 'email',
            'reason' => 'email_taken',
        ],
    ]);
}

$dupPhone = $pdo->prepare('SELECT id FROM users WHERE phone = :phone AND id <> :id LIMIT 1');
$dupPhone->execute([':phone' => $payload['phone'], ':id' => $userId]);
if ($dupPhone->fetch()) {
    response_error(409, 'Nomor telepon sudah digunakan.', [
        [
            'field' => 'phone',
            'reason' => 'phone_taken',
        ],
    ]);
}

if ($employeeId !== null && $employeeId !== '' && $employeeId !== $current['employee_id']) {
    $dupEmployee = $pdo->prepare('SELECT id FROM officers WHERE employee_id = :employee_id AND id <> :id LIMIT 1');
    $dupEmployee->execute([
        ':employee_id' => $employeeId,
        ':id' => $officerId,
    ]);
    if ($dupEmployee->fetch()) {
        response_error(409, 'Kode pegawai sudah digunakan.', [
            [
                'field' => 'employee_id',
                'reason' => 'employee_taken',
            ],
        ]);
    }
}

$newPhotoPath = null;
$photoDir = 'profile_photos';
if (!empty($payload['photo_base64'])) {
    $newPhotoPath = save_base64_image($payload['photo_base64'], $photoDir);
} else {
    $uploadedFile = find_uploaded_file($_FILES, ['photo', 'profile_photo']);
    if ($uploadedFile) {
        $newPhotoPath = save_uploaded_file($uploadedFile, $photoDir);
    }
}

$finalEmployeeId = $employeeId !== null && $employeeId !== '' ? $employeeId : $current['employee_id'];
$newAddress = $payload['address'] ?? $current['address'];
$previousPhotoPath = $current['profile_photo'] ?? null;

$pdo->beginTransaction();

try {
    $updateUserSql = 'UPDATE users SET full_name = :full_name, email = :email, phone = :phone, address = :address';
    $params = [
        ':full_name' => $payload['full_name'],
        ':email' => $payload['email'],
        ':phone' => $payload['phone'],
        ':address' => $newAddress,
        ':id' => $userId,
    ];

    if ($newPhotoPath !== null) {
        $updateUserSql .= ', profile_photo = :photo';
        $params[':photo'] = $newPhotoPath;
    }

    $updateUserSql .= ', updated_at = NOW() WHERE id = :id';
    $stmt = $pdo->prepare($updateUserSql);
    $stmt->execute($params);

    $officerSql = 'UPDATE officers SET department = :department, specialization = :specialization';
    $officerParams = [
        ':department' => $payload['department'],
        ':specialization' => $payload['specialization'],
        ':id' => $officerId,
    ];

    if ($finalEmployeeId !== $current['employee_id']) {
        $officerSql .= ', employee_id = :employee_id';
        $officerParams[':employee_id'] = $finalEmployeeId;
    }

    $officerSql .= ', updated_at = NOW() WHERE id = :id';
    $updateOfficer = $pdo->prepare($officerSql);
    $updateOfficer->execute($officerParams);

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    response_error(500, 'Gagal memperbarui profil petugas.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

if ($newPhotoPath && $previousPhotoPath && $previousPhotoPath !== $newPhotoPath) {
    $baseDir = dirname(dirname(dirname(__DIR__)));
    $previousFullPath = $baseDir . '/' . $previousPhotoPath;
    if (is_file($previousFullPath)) {
        @unlink($previousFullPath);
    }
}

$refresh = $pdo->prepare(
    'SELECT u.id AS user_id, u.full_name, u.email, u.phone, u.address, u.profile_photo,
            o.id AS officer_id, o.employee_id, o.department, o.specialization,
            o.officer_status, o.created_at AS joined_at
     FROM users u
     JOIN officers o ON o.user_id = u.id
     WHERE u.id = :id
     LIMIT 1'
);
$refresh->execute([':id' => $userId]);
$latest = $refresh->fetch();

response_success(200, 'Profil petugas berhasil diperbarui.', [
    'user_id' => (int) $latest['user_id'],
    'officer_id' => (int) $latest['officer_id'],
    'full_name' => $latest['full_name'],
    'email' => $latest['email'],
    'phone' => $latest['phone'],
    'department' => $latest['department'],
    'employee_id' => $latest['employee_id'],
    'specialization' => $latest['specialization'],
    'address' => $latest['address'],
    'profile_photo' => $latest['profile_photo'],
    'status' => $latest['officer_status'],
    'joined_at' => $latest['joined_at'],
]);

/*
Contoh Request (multipart):
PUT /api/officer/profile/update
Authorization: Bearer <token_petugas>
Content-Type: multipart/form-data
- full_name=Yudha Rahman
- email=petugas2@sipinda.go.id
- phone=0812333444
- department=Dinas PUPR
- specialization=Drainase
- employee_id=OFF-0100
- address=Jl. Melati No. 12
- photo=<file>

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Profil petugas berhasil diperbarui.",
  "data": {
    "user_id": 12,
    "officer_id": 7,
    "full_name": "Yudha Rahman",
    "email": "petugas2@sipinda.go.id",
    "phone": "0812333444",
    "department": "Dinas PUPR",
    "employee_id": "OFF-0100",
    "specialization": "Drainase",
    "address": "Jl. Melati No. 12",
    "profile_photo": "uploads/profile_photos/upload_xxx.jpg",
    "status": "tersedia",
    "joined_at": "2024-06-01 08:00:00"
  },
  "errors": []
}
*/
