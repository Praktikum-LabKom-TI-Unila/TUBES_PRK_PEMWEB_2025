<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/validation.php';
require_once __DIR__ . '/../../../helpers/officer.php';
require_once __DIR__ . '/../../../helpers/officer_tasks.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$taskId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($taskId <= 0) {
    response_error(400, 'Parameter id tugas tidak valid.', [
        'field' => 'id',
        'reason' => 'invalid_task_id',
    ]);
}

$contentType = $_SERVER['HTTP_CONTENT_TYPE'] ?? $_SERVER['CONTENT_TYPE'] ?? '';
$rawBody = file_get_contents('php://input');
$payload = [];

$shouldParseJson = stripos($contentType, 'application/json') !== false || ($contentType === '' && $rawBody !== '');
if ($shouldParseJson) {
    $decoded = json_decode($rawBody, true);
    if ($rawBody !== '' && json_last_error() !== JSON_ERROR_NONE) {
        response_error(400, 'Payload harus berupa JSON valid.', [
            'reason' => 'invalid_json',
        ]);
    }
    $payload = is_array($decoded) ? $decoded : [];
} elseif (!empty($_POST)) {
    $payload = $_POST;
}

$errors = require_fields($payload, ['reason']);
if ($errors) {
    response_error(422, 'Alasan pembatalan wajib diisi.', $errors);
}

$reason = trim((string) ($payload['reason'] ?? ''));
if ($reason === '') {
    response_error(422, 'Alasan pembatalan tidak boleh kosong.', [
        [
            'field' => 'reason',
            'reason' => 'empty_reason',
        ],
    ]);
}

$officer = require_officer();
$pdo = get_pdo();
$task = get_officer_task_or_404($taskId, $officer['officer_id']);

if (!in_array($task['status'], ['ditugaskan_ke_petugas', 'dalam_proses'], true)) {
    response_error(409, 'Tugas tidak dapat dibatalkan pada status saat ini.', [
        'reason' => 'invalid_status_transition',
        'status' => $task['status'],
    ]);
}

try {
    $pdo->beginTransaction();

    $updateComplaint = $pdo->prepare(
        'UPDATE complaints
         SET status = :status, assigned_officer_id = NULL, updated_at = NOW()
         WHERE id = :id'
    );
    $updateComplaint->execute([
        ':status' => 'diverifikasi_admin',
        ':id' => $task['complaint_id'],
    ]);

    $updateTask = $pdo->prepare('UPDATE officer_tasks SET finished_at = NOW() WHERE id = :task_id AND finished_at IS NULL');
    $updateTask->execute([':task_id' => $task['task_id']]);

    $insertProgress = $pdo->prepare(
        'INSERT INTO complaint_progress (complaint_id, status, note, created_by, created_at)
         VALUES (:complaint_id, :status, :note, :created_by, NOW())'
    );
    $insertProgress->execute([
        ':complaint_id' => $task['complaint_id'],
        ':status' => 'diverifikasi_admin',
        ':note' => 'Petugas mengembalikan ke admin: ' . $reason,
        ':created_by' => $officer['user_id'],
    ]);

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    response_error(500, 'Gagal membatalkan tugas.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

recalc_officer_status($pdo, $officer['officer_id']);

response_success(200, 'Tugas berhasil dikembalikan ke admin.', [
    'task_id' => (int) $task['task_id'],
    'complaint_id' => (int) $task['complaint_id'],
    'status' => 'diverifikasi_admin',
    'reason' => $reason,
]);

/*
Contoh Request:
POST /api/officer/tasks/12/cancel
Authorization: Bearer <token_petugas>
Body:
{
  "reason": "Perlu alat berat, mohon dialihkan"
}

Contoh Response:
{
  "status": "success",
  "code": 200,
  "message": "Tugas berhasil dikembalikan ke admin.",
  "data": {
    "task_id": 12,
    "complaint_id": 44,
    "status": "diverifikasi_admin",
    "reason": "Perlu alat berat, mohon dialihkan"
  },
  "errors": []
}
*/
