<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_error(405, 'Method tidak diperbolehkan.');
}

// Pseudo-autentikasi admin:
$admin = require_admin(); // Middleware JWT memastikan role admin
$adminId = $admin['id'];

$ticketId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($ticketId <= 0) {
    response_error(400, 'ID tiket tidak valid.', [
        'field' => 'id',
        'reason' => 'invalid_id',
    ]);
}

$pdo = get_pdo();

$ticketStmt = $pdo->prepare('SELECT id, status FROM complaints WHERE id = :id LIMIT 1');
$ticketStmt->execute([':id' => $ticketId]);
$ticket = $ticketStmt->fetch();

if (!$ticket) {
    response_error(404, 'Tiket tidak ditemukan.');
}

if ($ticket['status'] !== 'diajukan') {
    response_error(409, 'Tiket tidak dalam status diajukan.', [
        'reason' => 'invalid_status_transition',
    ]);
}

try {
    $pdo->beginTransaction();

    $update = $pdo->prepare('UPDATE complaints SET status = :status, updated_at = NOW() WHERE id = :id');
    $update->execute([
        ':status' => 'diverifikasi_admin',
        ':id' => $ticketId,
    ]);

    $log = $pdo->prepare(
        'INSERT INTO complaint_progress (complaint_id, status, note, created_by, created_at)
         VALUES (:complaint_id, :status, :note, :created_by, NOW())'
    );
    $log->execute([
        ':complaint_id' => $ticketId,
        ':status' => 'diverifikasi_admin',
        ':note' => 'Tiket diverifikasi oleh admin.',
        ':created_by' => $adminId,
    ]);

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    response_error(500, 'Gagal memverifikasi tiket.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

response_success(200, 'Status tiket diperbarui menjadi diverifikasi.', [
    'ticket_id' => $ticketId,
    'status' => 'diverifikasi_admin',
]);

/*
Contoh Request:
POST /api/admin/tickets/42/verify
Header: Authorization: Bearer <token_admin>

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Status tiket diperbarui menjadi diverifikasi.",
  "data": {
    "ticket_id": 42,
    "status": "diverifikasi_admin"
  },
  "errors": []
}

Contoh Response Error (status tidak valid):
{
  "status": "error",
  "code": 409,
  "message": "Tiket tidak dalam status diajukan.",
  "data": [],
  "errors": [
    {
      "reason": "invalid_status_transition"
    }
  ]
}
*/
