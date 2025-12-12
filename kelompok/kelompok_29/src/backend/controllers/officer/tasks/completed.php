<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/officer.php';
require_once __DIR__ . '/../../../helpers/officer_tasks.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$officer = require_officer();
$pdo = get_pdo();

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
if ($limit < 1) {
    $limit = 10;
}
$limit = min($limit, 50);

$data = list_officer_tasks_with_status($pdo, $officer['officer_id'], ['selesai'], $page, $limit);

response_success(200, 'Daftar tugas selesai berhasil diambil.', $data);

/*
Contoh Request:
GET /api/officer/tasks/completed?page=1&limit=5
Authorization: Bearer <token_petugas>

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Daftar tugas selesai berhasil diambil.",
  "data": {
    "page": 1,
    "limit": 5,
    "total_data": 8,
    "total_page": 2,
    "records": [
      {
        "task_id": 3,
        "complaint_id": 9,
        "title": "Perbaikan drainase",
        "category": "Drainase",
        "status": "selesai",
        "address": "Jl. Merpati",
        "started_at": "2025-01-10 08:00:00",
        "finished_at": "2025-01-12 16:00:00",
        "created_at": "2025-01-09 14:11:00",
        "updated_at": "2025-01-12 16:00:00"
      }
    ]
  },
  "errors": []
}
*/
