<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

// Pseudo-autentikasi admin:
// $admin = require_admin(); // Validasi token Bearer + role admin via middleware

$pdo = get_pdo();
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$limit = max(1, min($limit, 25));

try {
    $stmt = $pdo->prepare(
        'SELECT 
            c.id,
            c.title,
            c.status,
            u.full_name AS reporter_name,
            u.email AS reporter_email,
            COALESCE(cp.status, c.status) AS latest_status,
            COALESCE(cp.created_at, c.updated_at, c.created_at) AS activity_time
         FROM complaints c
         JOIN users u ON u.id = c.reporter_id
         LEFT JOIN complaint_progress cp ON cp.id = (
             SELECT cp2.id
             FROM complaint_progress cp2
             WHERE cp2.complaint_id = c.id
             ORDER BY cp2.created_at DESC, cp2.id DESC
             LIMIT 1
         )
         ORDER BY activity_time DESC
         LIMIT :limit'
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    $activities = [];
    foreach ($stmt->fetchAll() as $row) {
        $activities[] = [
            'complaint_id' => (int) $row['id'],
            'title' => $row['title'],
            'status' => $row['latest_status'],
            'reporter' => [
                'name' => $row['reporter_name'],
                'email' => $row['reporter_email'],
            ],
            'activity_time' => $row['activity_time'],
        ];
    }

    response_success(200, 'Aktivitas terbaru berhasil diambil.', [
        'limit' => $limit,
        'records' => $activities,
    ]);
} catch (PDOException $e) {
    response_error(500, 'Gagal memuat aktivitas terbaru.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

/*
Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Aktivitas terbaru berhasil diambil.",
  "data": {
    "limit": 10,
    "records": [
      {
        "complaint_id": 87,
        "title": "Lampu jalan padam",
        "status": "dalam_proses",
        "reporter": {
          "name": "Adi Pranata",
          "email": "adi@mail.com"
        },
        "activity_time": "2025-01-12 09:21:00"
      }
    ]
  },
  "errors": []
}

Contoh Response Error:
{
  "status": "error",
  "code": 500,
  "message": "Gagal memuat aktivitas terbaru.",
  "data": [],
  "errors": [
    {
      "reason": "db_error"
    }
  ]
}
*/
