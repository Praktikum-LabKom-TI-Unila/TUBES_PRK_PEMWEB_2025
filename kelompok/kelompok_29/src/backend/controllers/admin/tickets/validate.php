<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/validation.php';
require_once __DIR__ . '/../../../helpers/admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$admin = require_admin();
$adminId = $admin['id'];

$ticketId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($ticketId <= 0) {
    response_error(400, 'ID tiket tidak valid.', [
        'field' => 'id',
        'reason' => 'invalid_id',
    ]);
}

$rawBody = file_get_contents('php://input');
$payload = json_decode($rawBody, true);
if ($rawBody !== '' && !is_array($payload)) {
    response_error(400, 'Payload harus berupa JSON.', [
        'reason' => 'invalid_json',
    ]);
}

$note = null;
if (is_array($payload) && array_key_exists('note', $payload)) {
    $note = trim((string) $payload['note']);
}

$pdo = get_pdo();
$ticketStmt = $pdo->prepare('SELECT id, status FROM complaints WHERE id = :id LIMIT 1');
$ticketStmt->execute([':id' => $ticketId]);
$ticket = $ticketStmt->fetch();

if (!$ticket) {
    response_error(404, 'Tiket tidak ditemukan.');
}

if ($ticket['status'] !== 'menunggu_validasi_admin') {
    response_error(409, 'Tiket belum memasuki tahap validasi.', [
        'reason' => 'invalid_status_transition',
    ]);
}

try {
    $pdo->beginTransaction();

    $update = $pdo->prepare('UPDATE complaints SET status = :status, updated_at = NOW() WHERE id = :id');
    $update->execute([
        ':status' => 'selesai',
        ':id' => $ticketId,
    ]);

    $taskUpdate = $pdo->prepare('UPDATE officer_tasks SET finished_at = NOW() WHERE complaint_id = :complaint_id AND finished_at IS NULL');
    $taskUpdate->execute([':complaint_id' => $ticketId]);

    $log = $pdo->prepare(
        'INSERT INTO complaint_progress (complaint_id, status, note, created_by, created_at)
         VALUES (:complaint_id, :status, :note, :created_by, NOW())'
    );
    $log->execute([
        ':complaint_id' => $ticketId,
        ':status' => 'selesai',
        ':note' => $note ?: 'Validasi admin: pekerjaan selesai.',
        ':created_by' => $adminId,
    ]);

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    response_error(500, 'Gagal memvalidasi tiket.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

response_success(200, 'Tiket berhasil divalidasi sebagai selesai.', [
    'ticket_id' => $ticketId,
    'status' => 'selesai',
    'note' => $note,
]);

/*
Contoh Request:
POST /api/admin/tickets/42/validate
Body:
{
  "note": "Validasi lapangan selesai."
}

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Tiket berhasil divalidasi sebagai selesai.",
  "data": {
    "ticket_id": 42,
    "status": "selesai",
    "note": "Validasi lapangan selesai."
  },
  "errors": []
}

Contoh Response Error (status belum siap):
{
  "status": "error",
  "code": 409,
  "message": "Tiket belum memasuki tahap validasi.",
  "data": [],
  "errors": [
    {
      "reason": "invalid_status_transition"
    }
  ]
}
*/
