<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/complaints.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

// Pseudo-autentikasi admin:
// $admin = require_admin();
// if ($admin['role'] !== 'admin') {
//     response_error(403, 'Akses hanya untuk admin.');
// }

$pdo = get_pdo();

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 15;
$limit = max(1, min($limit, 50));
$statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? trim($_GET['status']) : null;
$searchQuery = array_key_exists('search', $_GET) ? trim((string) $_GET['search']) : null;

$allowedStatuses = complaint_statuses();
if ($statusFilter !== null && !in_array($statusFilter, $allowedStatuses, true)) {
    response_error(422, 'Status tidak valid.', [
        [
            'field' => 'status',
            'reason' => 'invalid_status',
            'allowed' => $allowedStatuses,
        ],
    ]);
}

if ($searchQuery !== null && $searchQuery === '') {
  response_error(422, 'Parameter pencarian tidak boleh kosong.', [
    [
      'field' => 'search',
      'reason' => 'empty_search',
    ],
  ]);
}

$conditions = [];
$params = [];
if ($statusFilter !== null) {
    $conditions[] = 'c.status = :status';
    $params[':status'] = $statusFilter;
}

if ($searchQuery !== null) {
  $searchConditions = [
    'c.title LIKE :search_term',
    'u.full_name LIKE :search_term',
    'u.email LIKE :search_term',
  ];
  $params[':search_term'] = '%' . $searchQuery . '%';

  if (ctype_digit($searchQuery)) {
    $searchConditions[] = 'c.id = :search_exact_id';
    $params[':search_exact_id'] = (int) $searchQuery;
  }

  $conditions[] = '(' . implode(' OR ', $searchConditions) . ')';
}

$whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

try {
  $countStmt = $pdo->prepare(
    "SELECT COUNT(*) AS total
     FROM complaints c
     JOIN users u ON u.id = c.reporter_id
     {$whereClause}"
  );
    $countStmt->execute($params);
    $totalData = (int) $countStmt->fetchColumn();

  $totalPage = $totalData > 0 ? (int) ceil($totalData / $limit) : 0;
    $offset = ($page - 1) * $limit;

    $listStmt = $pdo->prepare(
        "SELECT 
            c.id,
            c.title,
            c.status,
            c.category,
            c.address,
            c.created_at,
            c.updated_at,
            u.id AS reporter_id,
            u.full_name AS reporter_name,
            u.email AS reporter_email
         FROM complaints c
         JOIN users u ON u.id = c.reporter_id
         {$whereClause}
         ORDER BY c.created_at DESC
         LIMIT :limit OFFSET :offset"
    );

    foreach ($params as $key => $value) {
        $listStmt->bindValue($key, $value);
    }
    $listStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $listStmt->execute();

    $records = [];
    foreach ($listStmt->fetchAll() as $row) {
        $records[] = [
            'id' => (int) $row['id'],
            'title' => $row['title'],
            'status' => $row['status'],
            'category' => $row['category'],
            'address' => $row['address'],
            'reporter' => [
                'id' => (int) $row['reporter_id'],
                'name' => $row['reporter_name'],
                'email' => $row['reporter_email'],
            ],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
        ];
    }

  if ($searchQuery !== null && $totalData === 0) {
    response_error(404, 'Tiket tidak ditemukan.', [
      [
        'field' => 'search',
        'reason' => 'no_match',
      ],
    ]);
  }

  response_success(200, 'Daftar tiket berhasil dimuat.', [
        'page' => $page,
        'limit' => $limit,
        'total_data' => $totalData,
        'total_page' => $totalPage,
        'filters' => [
            'status' => $statusFilter,
      'search' => $searchQuery,
        ],
        'records' => $records,
    ]);
} catch (PDOException $e) {
    response_error(500, 'Gagal memuat daftar tiket.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

/*
Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Daftar tiket berhasil dimuat.",
  "data": {
    "page": 1,
    "limit": 15,
    "total_data": 120,
    "total_page": 8,
    "filters": {
      "status": "dalam_proses",
      "search": "lampu"
    },
    "records": [
      {
        "id": 32,
        "title": "Jalan berlubang besar",
        "status": "dalam_proses",
        "category": "Jalan_Raya",
        "address": "Jl. Kenanga No. 2",
        "reporter": {
          "id": 7,
          "name": "Riska Dewi",
          "email": "riska@mail.com"
        },
        "created_at": "2025-01-10 07:30:00",
        "updated_at": "2025-01-12 14:11:00"
      }
    ]
  },
  "errors": []
}

Contoh Response Error (status tidak valid):
{
  "status": "error",
  "code": 422,
  "message": "Status tidak valid.",
  "data": [],
  "errors": [
    {
      "field": "status",
      "reason": "invalid_status"
    }
  ]
}

Contoh Response Error (search tanpa hasil):
{
  "status": "error",
  "code": 404,
  "message": "Tiket tidak ditemukan.",
  "data": [],
  "errors": [
    {
      "field": "search",
      "reason": "no_match"
    }
  ]
}
*/
