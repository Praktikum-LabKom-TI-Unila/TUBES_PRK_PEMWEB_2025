<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/validation.php';
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

$data = list_officer_tasks_with_status($pdo, $officer['officer_id'], ['ditugaskan_ke_petugas', 'dalam_proses'], $page, $limit);

response_success(200, 'Daftar tugas aktif berhasil diambil.', $data);

/*
Contoh Request:
GET /api/officer/tasks/active?page=1&limit=10
Authorization: Bearer <token_petugas>

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Daftar tugas aktif berhasil diambil.",
  "data": {
    "page": 1,
    "limit": 10,
    "total_data": 2,
    "total_page": 1,
    "records": [
      {
        "task_id": 12,
        "complaint_id": 44,
        "title": "Perbaiki lampu jalan",
        "category": "Penerangan_Jalan",
        "status": "ditugaskan_ke_petugas",
        "address": "Jl. Kenanga 3",
        "started_at": null,
        "finished_at": null,
        "created_at": "2025-01-12 09:20:00",
        "updated_at": "2025-01-12 09:20:00"
      }
    ]
  },
  "errors": []
}
*/
