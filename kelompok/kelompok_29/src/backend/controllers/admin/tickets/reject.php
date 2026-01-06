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

$contentType = $_SERVER['HTTP_CONTENT_TYPE'] ?? $_SERVER['CONTENT_TYPE'] ?? '';
$rawBody = file_get_contents('php://input');
$payload = [];

$shouldParseJson = stripos($contentType, 'application/json') !== false
    || ($contentType === '' && trim($rawBody) !== '');

if ($shouldParseJson) {
    $decoded = json_decode($rawBody, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        response_error(400, 'Payload harus berupa JSON valid.', [
            'reason' => 'invalid_json',
        ]);
    }
    $payload = is_array($decoded) ? $decoded : [];
} elseif (!empty($_POST)) {
    $payload = $_POST;
}

$requiredErrors = require_fields($payload, ['reason']);
if ($requiredErrors) {
    response_error(422, 'Alasan penolakan wajib diisi.', $requiredErrors);
}

$reason = trim((string) ($payload['reason'] ?? ''));
if ($reason === '') {
    response_error(422, 'Alasan penolakan tidak boleh kosong.', [
        [
            'field' => 'reason',
            'reason' => 'empty_reason',
        ],
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
    response_error(409, 'Tiket tidak dapat ditolak pada status saat ini.', [
        'reason' => 'invalid_status_transition',
    ]);
}

try {
    $pdo->beginTransaction();

    $update = $pdo->prepare('UPDATE complaints SET status = :status, updated_at = NOW() WHERE id = :id');
    $update->execute([
        ':status' => 'ditolak_admin',
        ':id' => $ticketId,
    ]);

    $log = $pdo->prepare(
        'INSERT INTO complaint_progress (complaint_id, status, note, created_by, created_at)
         VALUES (:complaint_id, :status, :note, :created_by, NOW())'
    );
    $log->execute([
        ':complaint_id' => $ticketId,
        ':status' => 'ditolak_admin',
        ':note' => $reason,
        ':created_by' => $adminId,
    ]);

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    response_error(500, 'Gagal menolak tiket.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

response_success(200, 'Tiket berhasil ditolak.', [
    'ticket_id' => $ticketId,
    'status' => 'ditolak_admin',
    'reason' => $reason,
]);

/*
Contoh Request:
POST /api/admin/tickets/42/reject
Body:
{
  "reason": "Laporan tidak lengkap (foto kabur)."
}

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Tiket berhasil ditolak.",
  "data": {
    "ticket_id": 42,
    "status": "ditolak_admin",
    "reason": "Laporan tidak lengkap (foto kabur)."
  },
  "errors": []
}

Contoh Response Error (tanpa alasan):
{
  "status": "error",
  "code": 422,
  "message": "Alasan penolakan wajib diisi.",
  "data": [],
  "errors": [
    {
      "field": "reason",
      "message": "Field wajib diisi."
    }
  ]
}
*/
