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

$note = isset($payload['note']) ? trim((string) $payload['note']) : '';

$officer = require_officer();
$pdo = get_pdo();
$task = get_officer_task_or_404($taskId, $officer['officer_id']);

if ($task['status'] === 'dalam_proses') {
    response_error(409, 'Tugas sudah dalam proses pengerjaan.', [
        'reason' => 'already_in_progress',
    ]);
}

if ($task['status'] !== 'ditugaskan_ke_petugas') {
    response_error(409, 'Status tugas tidak dapat dimulai.', [
        'reason' => 'invalid_status_transition',
        'status' => $task['status'],
    ]);
}

$progressNote = $note !== '' ? $note : 'Petugas memulai pengerjaan di lapangan.';

try {
    $pdo->beginTransaction();

    $updateComplaint = $pdo->prepare('UPDATE complaints SET status = :status, updated_at = NOW() WHERE id = :id');
    $updateComplaint->execute([
        ':status' => 'dalam_proses',
        ':id' => $task['complaint_id'],
    ]);

    $updateTask = $pdo->prepare('UPDATE officer_tasks SET started_at = COALESCE(started_at, NOW()) WHERE id = :task_id');
    $updateTask->execute([':task_id' => $task['task_id']]);

    $insertProgress = $pdo->prepare(
        'INSERT INTO complaint_progress (complaint_id, status, note, created_by, created_at)
         VALUES (:complaint_id, :status, :note, :created_by, NOW())'
    );
    $insertProgress->execute([
        ':complaint_id' => $task['complaint_id'],
        ':status' => 'dalam_proses',
        ':note' => $progressNote,
        ':created_by' => $officer['user_id'],
    ]);

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    response_error(500, 'Gagal memulai pengerjaan tugas.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

recalc_officer_status($pdo, $officer['officer_id']);

response_success(200, 'Tugas berhasil ditandai dalam proses.', [
    'task_id' => (int) $task['task_id'],
    'complaint_id' => (int) $task['complaint_id'],
    'status' => 'dalam_proses',
    'note' => $progressNote,
]);

/*
Contoh Request:
POST /api/officer/tasks/12/start
Authorization: Bearer <token_petugas>
Body:
{
  "note": "Menuju lokasi perbaikan."
}

Contoh Response:
{
  "status": "success",
  "code": 200,
  "message": "Tugas berhasil ditandai dalam proses.",
  "data": {
    "task_id": 12,
    "complaint_id": 44,
    "status": "dalam_proses",
    "note": "Menuju lokasi perbaikan."
  },
  "errors": []
}
*/
