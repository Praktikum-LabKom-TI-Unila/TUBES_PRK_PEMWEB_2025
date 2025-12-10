<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$admin = require_admin(); // Token admin diverifikasi sebelum akses

$officerId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($officerId <= 0) {
    response_error(400, 'ID petugas tidak valid.', [
        [
            'field' => 'id',
            'reason' => 'invalid_id',
        ],
    ]);
}

$pdo = get_pdo();

$stmt = $pdo->prepare(
    'SELECT o.id, o.employee_id, o.department, o.specialization, o.officer_status,
            o.created_at, o.updated_at,
            u.id AS user_id, u.full_name, u.email, u.phone, u.address, u.profile_photo
     FROM officers o
     JOIN users u ON u.id = o.user_id
     WHERE o.id = :id
     LIMIT 1'
);
$stmt->execute([':id' => $officerId]);
$officer = $stmt->fetch();

if (!$officer) {
    response_error(404, 'Petugas tidak ditemukan.');
}

$statsStmt = $pdo->prepare(
    'SELECT 
        COUNT(*) AS total_tasks,
        SUM(CASE WHEN finished_at IS NULL THEN 1 ELSE 0 END) AS active_tasks,
        SUM(CASE WHEN finished_at IS NOT NULL THEN 1 ELSE 0 END) AS finished_tasks
     FROM officer_tasks
     WHERE officer_id = :id'
);
$statsStmt->execute([':id' => $officerId]);
$stats = $statsStmt->fetch();

$responseData = [
    'officer' => [
        'id' => (int) $officer['id'],
        'employee_id' => $officer['employee_id'],
        'name' => $officer['full_name'],
        'email' => $officer['email'],
        'phone' => $officer['phone'],
        'address' => $officer['address'],
        'department' => $officer['department'],
        'specialization' => $officer['specialization'],
        'status' => $officer['officer_status'],
        'profile_photo' => $officer['profile_photo'],
        'created_at' => $officer['created_at'],
        'updated_at' => $officer['updated_at'],
    ],
    'statistics' => [
        'total_tasks' => (int) ($stats['total_tasks'] ?? 0),
        'active_tasks' => (int) ($stats['active_tasks'] ?? 0),
        'finished_tasks' => (int) ($stats['finished_tasks'] ?? 0),
    ],
];

response_success(200, 'Detail petugas berhasil diambil.', $responseData);

/*
Contoh Request:
GET /api/admin/officers/5
Authorization: Bearer <token_admin>

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Detail petugas berhasil diambil.",
  "data": {
    "officer": {
      "id": 5,
      "employee_id": "OFF-0005",
      "name": "Siti Anggraeni",
      "email": "siti@pemkot.go.id",
      "phone": "0812121212",
      "address": "Jl. Mawar 10",
      "department": "Dinas PUPR",
      "specialization": "Drainase",
      "status": "tersedia",
      "profile_photo": "uploads/profile_photos/upload_abc.jpg",
      "created_at": "2025-01-02 09:10:00",
      "updated_at": "2025-01-08 14:02:00"
    },
    "statistics": {
      "total_tasks": 12,
      "active_tasks": 2,
      "finished_tasks": 10
    }
  },
  "errors": []
}

Contoh Response Error:
{
  "status": "error",
  "code": 404,
  "message": "Petugas tidak ditemukan.",
  "data": [],
  "errors": []
}
*/
