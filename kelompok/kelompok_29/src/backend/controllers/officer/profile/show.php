<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/officer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$session = require_officer();
$pdo = get_pdo();

$stmt = $pdo->prepare(
    'SELECT u.id AS user_id, u.full_name, u.email, u.phone, u.address, u.profile_photo, u.role,
            o.id AS officer_id, o.employee_id, o.department, o.specialization, o.officer_status,
            o.created_at AS joined_at
     FROM users u
     JOIN officers o ON o.user_id = u.id
     WHERE u.id = :user_id
     LIMIT 1'
);
$stmt->execute([':user_id' => $session['user_id']]);
$profile = $stmt->fetch();

if (!$profile) {
    response_error(404, 'Profil petugas tidak ditemukan.');
}

$data = [
    'user_id' => (int) $profile['user_id'],
    'officer_id' => (int) $profile['officer_id'],
    'full_name' => $profile['full_name'],
    'email' => $profile['email'],
    'phone' => $profile['phone'],
    'department' => $profile['department'],
    'employee_id' => $profile['employee_id'],
    'specialization' => $profile['specialization'],
    'role' => $profile['role'],
    'joined_at' => $profile['joined_at'],
    'address' => $profile['address'],
    'profile_photo' => $profile['profile_photo'],
    'status' => $profile['officer_status'],
];

response_success(200, 'Profil petugas berhasil diambil.', $data);

/*
Contoh Request:
GET /api/officer/profile
Authorization: Bearer <token_petugas>

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Profil petugas berhasil diambil.",
  "data": {
    "user_id": 12,
    "officer_id": 7,
    "full_name": "Yudha Rahman",
    "email": "petugas@sipinda.go.id",
    "phone": "08123456712",
    "department": "Dinas PUPR",
    "employee_id": "OFF-0007",
    "specialization": "Jalan_Raya",
    "role": "petugas",
    "joined_at": "2024-06-01 08:00:00",
    "address": "Jl. Mawar",
    "profile_photo": "uploads/profile_photos/upload_xxx.jpg",
    "status": "tersedia"
  },
  "errors": []
}
*/
