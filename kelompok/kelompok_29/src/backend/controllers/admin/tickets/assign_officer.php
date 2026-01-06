<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/validation.php';
require_once __DIR__ . '/../../../helpers/admin.php';
require_once __DIR__ . '/../../../helpers/officer_tasks.php';

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
if (!is_array($payload)) {
    response_error(400, 'Payload harus berupa JSON.', [
        'reason' => 'invalid_json',
    ]);
}

$errors = require_fields($payload, ['officer_id']);
if ($errors) {
    response_error(422, 'officer_id wajib diisi.', $errors);
}

$officerId = (int) $payload['officer_id'];
if ($officerId <= 0) {
    response_error(422, 'officer_id tidak valid.', [
        [
            'field' => 'officer_id',
            'reason' => 'invalid_officer_id',
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

if ($ticket['status'] !== 'diverifikasi_admin') {
    response_error(409, 'Tiket belum siap ditugaskan.', [
        'reason' => 'invalid_status_transition',
    ]);
}

$maxActiveTickets = 3;
$officerStmt = $pdo->prepare(
    'SELECT o.id, o.employee_id, o.department, o.specialization,
            COALESCE(SUM(CASE WHEN ot.finished_at IS NULL THEN 1 ELSE 0 END), 0) AS active_tasks
     FROM officers o
     LEFT JOIN officer_tasks ot ON ot.officer_id = o.id
     WHERE o.id = :id AND o.officer_status = :status
     GROUP BY o.id, o.employee_id, o.department, o.specialization'
);
$officerStmt->execute([
    ':id' => $officerId,
    ':status' => 'tersedia',
]);
$officer = $officerStmt->fetch();

if (!$officer) {
    response_error(404, 'Petugas tidak ditemukan atau tidak tersedia.');
}

if ((int) $officer['active_tasks'] >= $maxActiveTickets) {
    response_error(409, 'Petugas sudah mencapai batas penugasan.', [
        'reason' => 'officer_overloaded',
    ]);
}

try {
    $pdo->beginTransaction();

    $update = $pdo->prepare('UPDATE complaints SET status = :status, assigned_officer_id = :officer_id, updated_at = NOW() WHERE id = :id');
    $update->execute([
        ':status' => 'ditugaskan_ke_petugas',
        ':officer_id' => $officerId,
        ':id' => $ticketId,
    ]);

    $task = $pdo->prepare('INSERT INTO officer_tasks (officer_id, complaint_id, started_at) VALUES (:officer_id, :complaint_id, NOW())');
    $task->execute([
        ':officer_id' => $officerId,
        ':complaint_id' => $ticketId,
    ]);

    $log = $pdo->prepare(
        'INSERT INTO complaint_progress (complaint_id, status, note, created_by, created_at)
         VALUES (:complaint_id, :status, :note, :created_by, NOW())'
    );
    $log->execute([
        ':complaint_id' => $ticketId,
        ':status' => 'ditugaskan_ke_petugas',
        ':note' => 'Tiket ditugaskan ke officer #' . $officerId,
        ':created_by' => $adminId,
    ]);

    $pdo->commit();
} catch (PDOException $e) {
    $pdo->rollBack();
    response_error(500, 'Gagal menugaskan petugas.', [
        'reason' => 'db_error',
        'detail' => $e->getMessage(),
    ]);
}

recalc_officer_status($pdo, $officerId);

response_success(200, 'Petugas berhasil ditugaskan ke tiket.', [
    'ticket_id' => $ticketId,
    'officer' => [
        'id' => $officerId,
        'employee_id' => $officer['employee_id'],
        'department' => $officer['department'],
        'specialization' => $officer['specialization'],
    ],
    'status' => 'ditugaskan_ke_petugas',
]);

/*
Contoh Request:
POST /api/admin/tickets/42/assign-officer
Body:
{
  "officer_id": 5
}

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Petugas berhasil ditugaskan ke tiket.",
  "data": {
    "ticket_id": 42,
    "officer": {
      "id": 5,
      "employee_id": "OFF-005",
      "department": "PUPR",
      "specialization": "Jalan_Raya"
    },
    "status": "ditugaskan_ke_petugas"
  },
  "errors": []
}

Contoh Response Error (petugas penuh):
{
  "status": "error",
  "code": 409,
  "message": "Petugas sudah mencapai batas penugasan.",
  "data": [],
  "errors": [
    {
      "reason": "officer_overloaded"
    }
  ]
}
*/
