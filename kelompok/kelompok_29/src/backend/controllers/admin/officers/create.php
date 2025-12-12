<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/validation.php';
require_once __DIR__ . '/../../../helpers/admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$admin = require_admin(); // Validasi Bearer token admin telah dilakukan di middleware/helper

$contentType = $_SERVER['HTTP_CONTENT_TYPE'] ?? $_SERVER['CONTENT_TYPE'] ?? '';
$rawBody = file_get_contents('php://input');
$payload = null;

if (stripos($contentType, 'application/json') !== false) {
    $payload = json_decode($rawBody, true);
    if (!is_array($payload)) {
        response_error(400, 'Payload harus berupa JSON valid.', [
            'reason' => 'invalid_json',
        ]);
    }
} elseif (!empty($_POST)) {
    $payload = $_POST;
} else {
    $payload = json_decode($rawBody, true);
    if (!is_array($payload)) {
        response_error(400, 'Payload wajib dikirim dalam JSON atau form-data.', [
            'reason' => 'invalid_payload_format',
        ]);
    }
}

$payload = array_map(function ($value) {
    return is_string($value) ? trim($value) : $value;
}, $payload);

$requiredErrors = require_fields($payload, [
    'full_name',
    'email',
    'password',
    'phone',
    'address',
    'specialization',
]);

if ($requiredErrors) {
    response_error(422, 'Field wajib belum lengkap.', $requiredErrors);
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

$allowedStatuses = ['tersedia', 'sibuk'];
$officerStatus = $payload['officer_status'] ?? 'tersedia';
if (!in_array($officerStatus, $allowedStatuses, true)) {
    response_error(422, 'Status petugas hanya boleh tersedia/sibuk.', [
        [
            'field' => 'officer_status',
            'reason' => 'invalid_status',
        ],
    ]);
}

if (!filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
    response_error(422, 'Format email tidak valid.', [
        [
            'field' => 'email',
            'reason' => 'invalid_email',
        ],
    ]);
}

if (strlen($payload['password']) < 8) {
    response_error(422, 'Password minimal 8 karakter.', [
        [
            'field' => 'password',
            'reason' => 'password_too_short',
        ],
    ]);
}

$department = trim((string) ($payload['department'] ?? 'Operasional'));
if ($department === '') {
    $department = 'Operasional';
}

$pdo = get_pdo();

$dupEmail = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
$dupEmail->execute([':email' => $payload['email']]);
if ($dupEmail->fetch()) {
    response_error(409, 'Email sudah digunakan.', [
        [
            'field' => 'email',
            'reason' => 'email_taken',
        ],
    ]);
}

$dupPhone = $pdo->prepare('SELECT id FROM users WHERE phone = :phone LIMIT 1');
$dupPhone->execute([':phone' => $payload['phone']]);
if ($dupPhone->fetch()) {
    response_error(409, 'Nomor telepon sudah digunakan.', [
        [
            'field' => 'phone',
            'reason' => 'phone_taken',
        ],
    ]);
}

try {
    $pdo->beginTransaction();

    $userStmt = $pdo->prepare(
        'INSERT INTO users (full_name, email, password, phone, address, role, created_at, updated_at)
         VALUES (:full_name, :email, :password, :phone, :address, :role, NOW(), NOW())'
    );
    $userStmt->execute([
        ':full_name' => $payload['full_name'],
        ':email' => $payload['email'],
        ':password' => password_hash($payload['password'], PASSWORD_BCRYPT),
        ':phone' => $payload['phone'],
        ':address' => $payload['address'],
        ':role' => 'petugas',
    ]);

    $userId = (int) $pdo->lastInsertId();
    $employeeId = $payload['employee_id'] ?? ('OFF-' . str_pad((string) $userId, 4, '0', STR_PAD_LEFT));

    $officerStmt = $pdo->prepare(
        'INSERT INTO officers (user_id, employee_id, department, specialization, officer_status, created_at, updated_at)
         VALUES (:user_id, :employee_id, :department, :specialization, :status, NOW(), NOW())'
    );
    $officerStmt->execute([
        ':user_id' => $userId,
        ':employee_id' => $employeeId,
        ':department' => $department,
        ':specialization' => $payload['specialization'],
        ':status' => $officerStatus,
    ]);

    $officerId = (int) $pdo->lastInsertId();

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    response_error(500, 'Gagal membuat akun petugas.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

response_success(201, 'Petugas baru berhasil dibuat.', [
    'user_id' => $userId,
    'officer_id' => $officerId,
    'employee_id' => $employeeId,
    'specialization' => $payload['specialization'],
    'status' => $officerStatus,
]);

/*
Contoh Request:
POST /api/admin/officers/create
Authorization: Bearer <token_admin>
Content-Type: application/json
{
  "full_name": "Siti Andini",
  "email": "siti.andini@pemkot.go.id",
  "password": "Rahasia123",
  "phone": "081234567890",
  "address": "Jl. Raya 123",
  "specialization": "Jalan_Raya",
  "department": "Dinas PUPR",
  "officer_status": "tersedia"
}

Contoh Response Sukses:
{
  "status": "success",
  "code": 201,
  "message": "Petugas baru berhasil dibuat.",
  "data": {
    "user_id": 21,
    "officer_id": 9,
    "employee_id": "OFF-0021",
    "specialization": "Jalan_Raya",
    "status": "tersedia"
  },
  "errors": []
}

Contoh Response Error (email duplikat):
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
