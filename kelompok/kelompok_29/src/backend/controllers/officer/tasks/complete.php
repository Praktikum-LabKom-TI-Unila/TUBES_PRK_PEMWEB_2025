<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/validation.php';
require_once __DIR__ . '/../../../helpers/officer.php';
require_once __DIR__ . '/../../../helpers/officer_tasks.php';
require_once __DIR__ . '/../../../helpers/upload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_error(405, 'Method tidak diperbolehkan.');
}

hydrate_streamed_multipart_if_needed();

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

if (stripos($contentType, 'application/json') !== false) {
    $decoded = json_decode($rawBody, true);
    if ($rawBody !== '' && json_last_error() !== JSON_ERROR_NONE) {
        response_error(400, 'Payload harus berupa JSON valid.', [
            'reason' => 'invalid_json',
        ]);
    }
    $payload = is_array($decoded) ? $decoded : [];
} else {
    $payload = $_POST;
}

$note = isset($payload['note']) ? trim((string) $payload['note']) : '';
$photoPath = null;

if (!empty($payload['photo_base64'])) {
    $photoPath = save_base64_image($payload['photo_base64'], 'complaints');
} else {
    $uploadedFile = find_uploaded_file($_FILES, ['photo_after', 'photo', 'proof']);
    if ($uploadedFile) {
        $photoPath = save_uploaded_file($uploadedFile, 'complaints');
    }
}

if ($photoPath === null) {
    response_error(422, 'Bukti foto sesudah wajib diunggah.', [
        [
            'field' => 'photo_base64',
            'reason' => 'photo_missing',
        ],
    ]);
}

$officer = require_officer();
$pdo = get_pdo();
$task = get_officer_task_or_404($taskId, $officer['officer_id']);

if ($task['status'] !== 'dalam_proses') {
    response_error(409, 'Hanya tugas dalam proses yang bisa mengunggah bukti.', [
        'reason' => 'invalid_status_transition',
        'status' => $task['status'],
    ]);
}

try {
    $pdo->beginTransaction();

    $existingProofStmt = $pdo->prepare('SELECT id, photo_after FROM completion_proofs WHERE complaint_id = :complaint_id AND officer_id = :officer_id LIMIT 1');
    $existingProofStmt->execute([
        ':complaint_id' => $task['complaint_id'],
        ':officer_id' => $officer['officer_id'],
    ]);
    $existingProof = $existingProofStmt->fetch();

    if ($existingProof) {
        $updateProof = $pdo->prepare(
            'UPDATE completion_proofs
             SET photo_after = :photo, notes = :notes, updated_at = NOW()
             WHERE id = :id'
        );
        $updateProof->execute([
            ':photo' => $photoPath,
            ':notes' => $note !== '' ? $note : null,
            ':id' => $existingProof['id'],
        ]);
    } else {
        $insertProof = $pdo->prepare(
            'INSERT INTO completion_proofs (complaint_id, officer_id, photo_after, notes, created_at)
             VALUES (:complaint_id, :officer_id, :photo, :notes, NOW())'
        );
        $insertProof->execute([
            ':complaint_id' => $task['complaint_id'],
            ':officer_id' => $officer['officer_id'],
            ':photo' => $photoPath,
            ':notes' => $note !== '' ? $note : null,
        ]);
    }

    $updateComplaint = $pdo->prepare('UPDATE complaints SET status = :status, updated_at = NOW() WHERE id = :id');
    $updateComplaint->execute([
        ':status' => 'menunggu_validasi_admin',
        ':id' => $task['complaint_id'],
    ]);

    $insertProgress = $pdo->prepare(
        'INSERT INTO complaint_progress (complaint_id, status, note, created_by, created_at)
         VALUES (:complaint_id, :status, :note, :created_by, NOW())'
    );
    $insertProgress->execute([
        ':complaint_id' => $task['complaint_id'],
        ':status' => 'menunggu_validasi_admin',
        ':note' => $note !== '' ? $note : 'Bukti penyelesaian diunggah petugas.',
        ':created_by' => $officer['user_id'],
    ]);

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    response_error(500, 'Gagal mengunggah bukti penyelesaian.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

recalc_officer_status($pdo, $officer['officer_id']);

response_success(200, 'Bukti penyelesaian terkirim, menunggu validasi admin.', [
    'task_id' => (int) $task['task_id'],
    'complaint_id' => (int) $task['complaint_id'],
    'status' => 'menunggu_validasi_admin',
    'photo_after' => $photoPath,
    'note' => $note,
]);

/*
Contoh Request (multipart):
POST /api/officer/tasks/12/complete
Authorization: Bearer <token_petugas>
Content-Type: multipart/form-data
- note=Pekerjaan selesai pukul 14.00
- photo_after=<file>

Contoh Response:
{
  "status": "success",
  "code": 200,
  "message": "Bukti penyelesaian terkirim, menunggu validasi admin.",
  "data": {
    "task_id": 12,
    "complaint_id": 44,
    "status": "menunggu_validasi_admin",
    "photo_after": "uploads/complaints/upload_xyz.jpg",
    "note": "Pekerjaan selesai pukul 14.00"
  },
  "errors": []
}
*/
