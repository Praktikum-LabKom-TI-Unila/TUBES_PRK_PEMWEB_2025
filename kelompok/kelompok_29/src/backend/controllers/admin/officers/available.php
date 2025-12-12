<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$admin = require_admin(); // Validasi token admin, hanya dokumentasi

$pdo = get_pdo();
$maxActiveTickets = 3; // Batas maksimum tiket aktif per petugas

$stmt = $pdo->prepare(
    'SELECT 
        o.id,
        o.employee_id,
        o.department,
        o.specialization,
        o.officer_status,
        u.full_name,
        u.email,
        COALESCE(SUM(CASE WHEN ot.finished_at IS NULL THEN 1 ELSE 0 END), 0) AS active_tasks
     FROM officers o
     JOIN users u ON u.id = o.user_id
     LEFT JOIN officer_tasks ot ON ot.officer_id = o.id
     WHERE o.officer_status = :status
     GROUP BY o.id, o.employee_id, o.department, o.specialization, o.officer_status, u.full_name, u.email
     HAVING active_tasks < :max_active
     ORDER BY active_tasks ASC, o.id ASC'
);
$stmt->bindValue(':status', 'tersedia');
$stmt->bindValue(':max_active', $maxActiveTickets, PDO::PARAM_INT);
$stmt->execute();

$records = [];
foreach ($stmt->fetchAll() as $row) {
    $records[] = [
        'id' => (int) $row['id'],
        'employee_id' => $row['employee_id'],
        'name' => $row['full_name'],
        'email' => $row['email'],
        'department' => $row['department'],
        'specialization' => $row['specialization'],
        'active_tasks' => (int) $row['active_tasks'],
        'capacity_left' => max(0, $maxActiveTickets - (int) $row['active_tasks']),
    ];
}

response_success(200, 'Daftar petugas tersedia berhasil diambil.', [
    'max_active_tasks' => $maxActiveTickets,
    'records' => $records,
]);

/*
Contoh Request:
GET /api/admin/officers/available

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Daftar petugas tersedia berhasil diambil.",
  "data": {
    "max_active_tasks": 3,
    "records": [
      {
        "id": 5,
        "employee_id": "OFF-005",
        "name": "Arif Nugraha",
        "email": "arif@pemkot.go.id",
        "department": "PUPR",
        "specialization": "Jalan_Raya",
        "active_tasks": 1,
        "capacity_left": 2
      }
    ]
  },
  "errors": []
}

Contoh Response Error (token invalid):
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
