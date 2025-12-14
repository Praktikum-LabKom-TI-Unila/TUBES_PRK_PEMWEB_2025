<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

// Pseudo-autentikasi admin:
// $admin = require_admin(); // Validasi Bearer token + pastikan role == 'admin'
// if (!$admin) {
//     response_error(401, 'Silakan login sebagai admin.');
// }

$pdo = get_pdo();

try {
    $stmt = $pdo->query(
        'SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN status = "diajukan" THEN 1 ELSE 0 END) AS baru,
            SUM(CASE WHEN status IN ("diverifikasi_admin", "ditugaskan_ke_petugas", "dalam_proses", "menunggu_validasi_admin") THEN 1 ELSE 0 END) AS in_progress,
            SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) AS finished
         FROM complaints'
    );
    $stats = $stmt->fetch() ?: [];

    response_success(200, 'Statistik dashboard admin berhasil diambil.', [
        'total' => (int) ($stats['total'] ?? 0),
        'baru' => (int) ($stats['baru'] ?? 0),
        'in_progress' => (int) ($stats['in_progress'] ?? 0),
        'finished' => (int) ($stats['finished'] ?? 0),
    ]);
} catch (PDOException $e) {
    response_error(500, 'Gagal memuat statistik dashboard.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

/*
Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Statistik dashboard admin berhasil diambil.",
  "data": {
    "total": 120,
    "baru": 15,
    "in_progress": 42,
    "finished": 63
  },
  "errors": []
}

Contoh Response Error:
{
  "status": "error",
  "code": 500,
  "message": "Gagal memuat statistik dashboard.",
  "data": [],
  "errors": [
    {
      "reason": "db_error"
    }
  ]
}
*/
