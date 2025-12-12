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
$task = get_officer_task_or_404($taskId, $officer['officer_id']);

$payload = [
    'task_id' => (int) $task['task_id'],
    'complaint_id' => (int) $task['complaint_id'],
    'title' => $task['title'],
    'category' => $task['category'],
    'status' => $task['status'],
    'description' => $task['description'],
    'address' => $task['address'],
    'location' => [
        'latitude' => $task['latitude'] !== null ? (float) $task['latitude'] : null,
        'longitude' => $task['longitude'] !== null ? (float) $task['longitude'] : null,
    ],
    'photos' => [
        'before' => $task['photo_before'],
    'after' => $task['completion_photo_after'],
    ],
    'reporter' => [
        'id' => (int) $task['reporter_id'],
        'name' => $task['reporter_name'],
        'email' => $task['reporter_email'],
        'phone' => $task['reporter_phone'],
        'address' => $task['reporter_address'],
        'photo' => $task['reporter_photo'],
    ],
    'timestamps' => [
        'assigned_at' => $task['started_at'],
        'finished_at' => $task['finished_at'],
        'complaint_created_at' => $task['complaint_created_at'],
        'complaint_updated_at' => $task['complaint_updated_at'],
    ],
];

response_success(200, 'Detail tugas petugas.', $payload);

/*
Contoh Request:
GET /api/officer/tasks/12
Authorization: Bearer <token_petugas>

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Detail tugas petugas.",
  "data": {
    "task_id": 12,
    "complaint_id": 44,
    "title": "Perbaikan penerangan jalan",
    "category": "Penerangan_Jalan",
    "status": "ditugaskan_ke_petugas",
    "description": "Lampu jalan utama padam sejak minggu lalu.",
    "address": "Jl. Kenanga No. 3",
    "location": {
      "latitude": -5.377,
      "longitude": 105.257
    },
    "photos": {
      "before": "uploads/complaints/upload_before.jpg",
      "after": null
    },
    "reporter": {
      "id": 8,
      "name": "Fajar Santoso",
      "email": "fajar@sipinda.id",
      "phone": "08123456789",
      "address": "Jl. Bunga No. 1",
      "photo": "uploads/profile_photos/upload_abc.jpg"
    },
    "timestamps": {
      "assigned_at": "2025-01-12 09:21:00",
      "finished_at": null,
      "complaint_created_at": "2025-01-10 08:00:00",
      "complaint_updated_at": "2025-01-12 09:21:00"
    }
  },
  "errors": []
}
*/
