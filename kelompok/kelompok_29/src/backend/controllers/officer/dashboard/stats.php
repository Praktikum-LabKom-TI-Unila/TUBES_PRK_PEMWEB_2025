<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/officer.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$officer = require_officer();
$pdo = get_pdo();

$stmt = $pdo->prepare(
    'SELECT 
        COUNT(*) AS total_tasks,
        SUM(c.status IN (\'ditugaskan_ke_petugas\', \'dalam_proses\')) AS active_tasks,
        SUM(c.status = \'selesai\') AS finished_tasks
     FROM officer_tasks ot
     JOIN complaints c ON c.id = ot.complaint_id
     WHERE ot.officer_id = :officer_id'
);
$stmt->execute([':officer_id' => $officer['officer_id']]);
$result = $stmt->fetch() ?: [
    'total_tasks' => 0,
    'active_tasks' => 0,
    'finished_tasks' => 0,
];

$payload = [
    'total_tasks' => (int) $result['total_tasks'],
    'active_tasks' => (int) $result['active_tasks'],
    'finished_tasks' => (int) $result['finished_tasks'],
];

response_success(200, 'Ringkasan dashboard petugas berhasil diambil.', $payload);

/*
Contoh Request:
GET /api/officer/dashboard/stats
Authorization: Bearer <token_petugas>

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Ringkasan dashboard petugas berhasil diambil.",
  "data": {
    "total_tasks": 12,
    "active_tasks": 4,
    "finished_tasks": 6
  },
  "errors": []
}
*/
