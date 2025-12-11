<?php
require_once __DIR__ . '/../../../../helpers/response.php';
require_once __DIR__ . '/../../../../helpers/officer.php';
require_once __DIR__ . '/../../../../helpers/officer_tasks.php';
require_once __DIR__ . '/../../../../helpers/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$taskId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($taskId <= 0) {
    response_error(400, 'Parameter id tugas tidak valid.', [
        'field' => 'id',
        'reason' => 'invalid_task_id',
    ]);
}

$officer = require_officer();
$task = get_officer_task_or_404($taskId, $officer['officer_id']);

if ($task['status'] !== 'menunggu_validasi_admin') {
    response_error(409, 'Bukti hanya dapat diakses saat menunggu validasi admin.', [
        'reason' => 'invalid_status',
        'status' => $task['status'],
    ]);
}

$pdo = get_pdo();
$stmt = $pdo->prepare(
    'SELECT id, photo_after, notes, created_at, updated_at
     FROM completion_proofs
     WHERE complaint_id = :complaint_id AND officer_id = :officer_id
     ORDER BY updated_at DESC, created_at DESC
     LIMIT 1'
);
$stmt->execute([
    ':complaint_id' => $task['complaint_id'],
    ':officer_id' => $officer['officer_id'],
]);
$record = $stmt->fetch();

if (!$record) {
    response_error(404, 'Bukti penyelesaian belum ditemukan untuk tugas ini.', [
        'reason' => 'proof_not_found',
    ]);
}

response_success(200, 'Bukti penyelesaian terkini.', [
    'task_id' => (int) $task['task_id'],
    'complaint_id' => (int) $task['complaint_id'],
    'proof' => [
        'id' => (int) $record['id'],
        'photo_after' => $record['photo_after'],
        'notes' => $record['notes'],
        'created_at' => $record['created_at'],
        'updated_at' => $record['updated_at'],
    ],
]);

/*
Contoh Request:
GET /api/officer/tasks/12/completion-proof
Authorization: Bearer <token_petugas>

Contoh Response:
{
  "status": "success",
  "code": 200,
  "message": "Bukti penyelesaian terkini.",
  "data": {
    "task_id": 12,
    "complaint_id": 44,
    "proof": {
      "id": 5,
      "photo_after": "uploads/complaints/upload_xyz.jpg",
      "notes": "Pekerjaan selesai pukul 14.00",
      "created_at": "2025-01-12 14:05:00",
      "updated_at": "2025-01-12 14:05:00"
    }
  },
  "errors": []
}
*/
