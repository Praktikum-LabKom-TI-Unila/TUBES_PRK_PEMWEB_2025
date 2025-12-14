<?php
require_once __DIR__ . '/../../../../helpers/response.php';
require_once __DIR__ . '/../../../../helpers/officer.php';
require_once __DIR__ . '/../../../../helpers/officer_tasks.php';
require_once __DIR__ . '/../../../../helpers/database.php';
require_once __DIR__ . '/../../../../helpers/upload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
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

if ($photoPath === null && $note === '') {
    response_error(422, 'Isi minimal salah satu: foto setelah atau catatan.', [
        [
            'field' => 'photo_after',
            'reason' => 'missing_photo',
        ],
        [
            'field' => 'note',
            'reason' => 'missing_note',
        ],
    ]);
}

$officer = require_officer();
$pdo = get_pdo();
$task = get_officer_task_or_404($taskId, $officer['officer_id']);

if ($task['status'] !== 'menunggu_validasi_admin') {
    response_error(409, 'Hanya tugas yang menunggu validasi admin yang dapat diperbarui.', [
        'reason' => 'invalid_status',
        'status' => $task['status'],
    ]);
}

$proofStmt = $pdo->prepare(
    'SELECT id, photo_after, notes FROM completion_proofs WHERE complaint_id = :complaint_id AND officer_id = :officer_id LIMIT 1'
);
$proofStmt->execute([
    ':complaint_id' => $task['complaint_id'],
    ':officer_id' => $officer['officer_id'],
]);
$existingProof = $proofStmt->fetch();

if (!$existingProof) {
    response_error(404, 'Bukti penyelesaian belum dibuat. Gunakan endpoint complete terlebih dahulu.', [
        'reason' => 'proof_not_found',
    ]);
}

try {
    $pdo->beginTransaction();

    $updateParts = [];
    $params = [
        ':id' => $existingProof['id'],
    ];

    if ($photoPath !== null) {
        $updateParts[] = 'photo_after = :photo_after';
        $params[':photo_after'] = $photoPath;
    }

    if ($note !== '') {
        $updateParts[] = 'notes = :notes';
        $params[':notes'] = $note;
    }

    if (!$updateParts) {
        $pdo->rollBack();
        response_error(422, 'Tidak ada perubahan yang dikirimkan.', [
            'reason' => 'no_changes',
        ]);
    }

    $updateSql = 'UPDATE completion_proofs SET ' . implode(', ', $updateParts) . ', updated_at = NOW() WHERE id = :id';
    $updateProof = $pdo->prepare($updateSql);
    $updateProof->execute($params);

    $touchComplaint = $pdo->prepare('UPDATE complaints SET updated_at = NOW() WHERE id = :id');
    $touchComplaint->execute([':id' => $task['complaint_id']]);

    $logNote = $note !== '' ? $note : 'Petugas memperbarui bukti penyelesaian.';
    $log = $pdo->prepare(
        'INSERT INTO complaint_progress (complaint_id, status, note, created_by, created_at)
         VALUES (:complaint_id, :status, :note, :created_by, NOW())'
    );
    $log->execute([
        ':complaint_id' => $task['complaint_id'],
        ':status' => 'menunggu_validasi_admin',
        ':note' => $logNote,
        ':created_by' => $officer['user_id'],
    ]);

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    response_error(500, 'Gagal memperbarui bukti penyelesaian.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

$refresh = $pdo->prepare('SELECT id, photo_after, notes, created_at, updated_at FROM completion_proofs WHERE id = :id LIMIT 1');
$refresh->execute([':id' => $existingProof['id']]);
$latest = $refresh->fetch();

response_success(200, 'Bukti penyelesaian berhasil diperbarui.', [
    'task_id' => (int) $task['task_id'],
    'complaint_id' => (int) $task['complaint_id'],
    'proof' => [
        'id' => (int) $latest['id'],
        'photo_after' => $latest['photo_after'],
        'notes' => $latest['notes'],
        'created_at' => $latest['created_at'],
        'updated_at' => $latest['updated_at'],
    ],
]);

/*
Contoh Request (multipart):
PUT /api/officer/tasks/12/completion-proof
Authorization: Bearer <token_petugas>
Content-Type: multipart/form-data
- note=Update dokumentasi siang hari
- photo_after=<file>

Contoh Response:
{
  "status": "success",
  "code": 200,
  "message": "Bukti penyelesaian berhasil diperbarui.",
  "data": {
    "task_id": 12,
    "complaint_id": 44,
    "proof": {
      "id": 5,
      "photo_after": "uploads/complaints/upload_new.jpg",
      "notes": "Update dokumentasi siang hari",
      "created_at": "2025-01-12 14:05:00",
      "updated_at": "2025-01-12 16:20:00"
    }
  },
  "errors": []
}
*/
