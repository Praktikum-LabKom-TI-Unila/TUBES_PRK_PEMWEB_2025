<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/validation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

// Pseudo-autentikasi admin:
// $admin = require_admin();
// if (!$admin || $admin['role'] !== 'admin') {
//     response_error(401, 'Butuh token admin yang valid.');
// }

$errors = require_fields($_GET, ['q']);
if ($errors) {
    response_error(422, 'Parameter pencarian wajib diisi.', $errors);
}

$query = trim((string) $_GET['q']);
if ($query === '') {
    response_error(422, 'Parameter pencarian tidak boleh kosong.', [
        [
            'field' => 'q',
            'reason' => 'empty_keyword',
        ],
    ]);
}

$pdo = get_pdo();
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 20;
$limit = max(1, min($limit, 30));

$likeTerm = '%' . $query . '%';
$params = [':term' => $likeTerm];
$orConditions = [
    'c.title LIKE :term',
    'u.full_name LIKE :term',
    'u.email LIKE :term',
];

if (ctype_digit($query)) {
    $params[':exact_id'] = (int) $query;
    $orConditions[] = 'c.id = :exact_id';
}

$whereClause = implode(' OR ', $orConditions);

// Jika server mendukung FULLTEXT INDEX:
// Ganti klausa WHERE dengan MATCH(...) AGAINST(:term IN NATURAL LANGUAGE MODE)
// untuk performa lebih baik. LIKE dipakai sebagai fallback universal.

try {
    $stmt = $pdo->prepare(
        "SELECT 
            c.id,
            c.title,
            c.status,
            c.category,
            c.address,
            c.created_at,
            u.full_name AS reporter_name,
            u.email AS reporter_email
         FROM complaints c
         JOIN users u ON u.id = c.reporter_id
         WHERE {$whereClause}
         ORDER BY c.created_at DESC
         LIMIT :limit"
    );

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $records = [];
    foreach ($stmt->fetchAll() as $row) {
        $records[] = [
            'id' => (int) $row['id'],
            'title' => $row['title'],
            'status' => $row['status'],
            'category' => $row['category'],
            'address' => $row['address'],
            'reporter' => [
                'name' => $row['reporter_name'],
                'email' => $row['reporter_email'],
            ],
            'created_at' => $row['created_at'],
        ];
    }

    if (!$records) {
        response_error(404, 'Tiket tidak ditemukan.', [
            [
                'field' => 'q',
                'reason' => 'no_match',
            ],
        ]);
    }

    response_success(200, 'Hasil pencarian tiket.', [
        'keyword' => $query,
        'limit' => $limit,
        'records' => $records,
    ]);
} catch (PDOException $e) {
    response_error(500, 'Gagal melakukan pencarian.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

/*
Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Hasil pencarian tiket.",
  "data": {
    "keyword": "lampu",
    "limit": 10,
    "records": [
      {
        "id": 45,
        "title": "Lampu taman mati",
        "status": "diverifikasi_admin",
        "category": "Penerangan_Jalan",
        "address": "Taman Melati",
        "reporter": {
          "name": "Iman Wahyu",
          "email": "iman@mail.com"
        },
        "created_at": "2025-02-05 18:11:00"
      }
    ]
  },
  "errors": []
}

Contoh Response Error (tidak ada hasil):
{
  "status": "error",
  "code": 404,
  "message": "Tiket tidak ditemukan.",
  "data": [],
  "errors": [
    {
      "field": "q",
      "reason": "no_match"
    }
  ]
}
*/
