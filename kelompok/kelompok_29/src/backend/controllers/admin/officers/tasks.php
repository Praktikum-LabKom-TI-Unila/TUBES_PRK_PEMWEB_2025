<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$admin = require_admin(); // Token Bearer admin divalidasi sebelum akses

$officerId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($officerId <= 0) {
    response_error(400, 'ID petugas tidak valid.', [
        [
            'field' => 'id',
            'reason' => 'invalid_id',
        ],
    ]);
}

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
if ($limit < 1) {
    $limit = 10;
}
$limit = min($limit, 50);
$offset = ($page - 1) * $limit;

$pdo = get_pdo();

$existsStmt = $pdo->prepare('SELECT id FROM officers WHERE id = :id LIMIT 1');
$existsStmt->execute([':id' => $officerId]);
if (!$existsStmt->fetch()) {
    response_error(404, 'Petugas tidak ditemukan.');
}

$countStmt = $pdo->prepare('SELECT COUNT(*) FROM officer_tasks WHERE officer_id = :id AND finished_at IS NULL');
$countStmt->execute([':id' => $officerId]);
$totalData = (int) $countStmt->fetchColumn();
$totalPage = $totalData > 0 ? (int) ceil($totalData / $limit) : 0;

$dataStmt = $pdo->prepare(
    'SELECT ot.id, ot.complaint_id, ot.started_at, ot.finished_at,
            c.title, c.status, c.category, c.address
     FROM officer_tasks ot
     JOIN complaints c ON c.id = ot.complaint_id
     WHERE ot.officer_id = :id AND ot.finished_at IS NULL
     ORDER BY ot.started_at DESC
     LIMIT :limit OFFSET :offset'
);
$dataStmt->bindValue(':id', $officerId, PDO::PARAM_INT);
$dataStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$dataStmt->execute();

$records = [];
foreach ($dataStmt->fetchAll() as $row) {
    $records[] = [
        'task_id' => (int) $row['id'],
        'complaint_id' => (int) $row['complaint_id'],
        'title' => $row['title'],
        'status' => $row['status'],
        'category' => $row['category'],
        'address' => $row['address'],
        'started_at' => $row['started_at'],
    ];
}

response_success(200, 'Daftar tugas aktif berhasil diambil.', [
    'page' => $page,
    'limit' => $limit,
    'total_data' => $totalData,
    'total_page' => $totalPage,
    'records' => $records,
]);

/*
Contoh Request:
GET /api/admin/officers/5/tasks?page=1&limit=5
Authorization: Bearer <token_admin>

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Daftar tugas aktif berhasil diambil.",
  "data": {
    "page": 1,
    "limit": 5,
    "total_data": 1,
    "total_page": 1,
    "records": [
      {
        "task_id": 11,
        "complaint_id": 102,
        "title": "Perbaikan lampu jalan",
        "status": "ditugaskan_ke_petugas",
        "category": "Penerangan_Jalan",
        "address": "Jl. Dahlia",
        "started_at": "2025-02-10 09:00:00"
      }
    ]
  },
  "errors": []
}

Contoh Response Error (petugas tidak ada):
{
  "status": "error",
  "code": 404,
  "message": "Petugas tidak ditemukan.",
  "data": [],
  "errors": []
}
*/
