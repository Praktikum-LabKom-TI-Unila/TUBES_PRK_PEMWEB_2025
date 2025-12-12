<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$admin = require_admin(); // Mengandalkan header Authorization Bearer
$adminId = $admin['id'];

$pdo = get_pdo();
$stmt = $pdo->prepare('SELECT id, full_name, email, phone, address, role, profile_photo FROM users WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $adminId]);
$user = $stmt->fetch();

if (!$user) {
    response_error(404, 'Data admin tidak ditemukan.');
}

response_success(200, 'Profil admin berhasil diambil.', [
    'id' => (int) $user['id'],
    'full_name' => $user['full_name'],
    'email' => $user['email'],
    'role' => $user['role'],
    'phone' => $user['phone'],
    'address' => $user['address'],
    'profile_photo' => $user['profile_photo'],
]);

/*
Contoh Request:
GET /api/admin/profile
Authorization: Bearer <token_admin>

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Profil admin berhasil diambil.",
  "data": {
    "id": 1,
    "full_name": "Admin Utama",
    "email": "admin@sipinda.go.id",
    "role": "admin",
    "phone": "0811111111",
    "address": "Jl. Gubernur No. 15",
    "profile_photo": "uploads/admin/upload_abc.jpg"
  },
  "errors": []
}

Contoh Response Error:
{
  "status": "error",
  "code": 401,
  "message": "Token tidak dikenali.",
  "data": [],
  "errors": [
    {
      "reason": "invalid_token"
    }
  ]
}
*/
