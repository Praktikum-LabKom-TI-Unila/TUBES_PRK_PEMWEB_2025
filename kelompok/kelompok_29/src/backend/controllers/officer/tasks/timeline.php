<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/officer.php';
require_once __DIR__ . '/../../../helpers/officer_tasks.php';

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
$pdo = get_pdo();
$task = get_officer_task_or_404($taskId, $officer['officer_id']);
$timeline = fetch_task_timeline($pdo, (int) $task['complaint_id']);

response_success(200, 'Timeline tugas petugas.', [
    'task_id' => (int) $task['task_id'],
    'complaint_id' => (int) $task['complaint_id'],
    'records' => $timeline,
]);

/*
Contoh Request:
GET /api/officer/tasks/12/timeline
Authorization: Bearer <token_petugas>

Contoh Response:
{
  "status": "success",
  "code": 200,
  "message": "Timeline tugas petugas.",
  "data": {
    "task_id": 12,
    "complaint_id": 44,
    "records": [
      {
        "id": 153,
        "status": "ditugaskan_ke_petugas",
        "note": "Tiket ditugaskan ke officer #7",
        "created_at": "2025-01-12 09:30:00",
        "created_by": {
          "id": 1,
          "name": "Admin Kota"
        }
      },
      {
        "id": 156,
        "status": "dalam_proses",
        "note": "Petugas menuju lokasi.",
        "created_at": "2025-01-12 10:00:00",
        "created_by": {
          "id": 15,
          "name": "Arif Nugraha"
        }
      }
    ]
  },
  "errors": []
}
*/
