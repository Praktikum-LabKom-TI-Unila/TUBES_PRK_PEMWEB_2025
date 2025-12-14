<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$admin = require_admin();

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

if (!in_array($ticket['status'], ['menunggu_validasi_admin', 'selesai'], true)) {
    response_error(409, 'Bukti belum dapat dilihat pada status saat ini.', [
        'reason' => 'proof_not_ready',
    ]);
}

$proofStmt = $pdo->prepare(
    'SELECT cp.id, cp.photo_after, cp.notes, cp.created_at,
            o.id AS officer_id, ou.full_name AS officer_name
     FROM completion_proofs cp
     JOIN officers o ON o.id = cp.officer_id
     JOIN users ou ON ou.id = o.user_id
     WHERE cp.complaint_id = :id
     ORDER BY cp.created_at DESC'
);
$proofStmt->execute([':id' => $ticketId]);
$records = [];
foreach ($proofStmt->fetchAll() as $row) {
    $records[] = [
        'id' => (int) $row['id'],
        'photo' => $row['photo_after'],
        'notes' => $row['notes'],
        'submitted_at' => $row['created_at'],
        'officer' => [
            'id' => (int) $row['officer_id'],
            'name' => $row['officer_name'],
        ],
    ];
}

if (!$records) {
    response_error(404, 'Bukti penyelesaian belum diunggah.', [
        'reason' => 'proof_not_found',
    ]);
}

response_success(200, 'Bukti penyelesaian berhasil diambil.', [
    'ticket_id' => $ticketId,
    'records' => $records,
]);

/*
Contoh Request:
GET /api/admin/tickets/42/proof

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Bukti penyelesaian berhasil diambil.",
  "data": {
    "ticket_id": 42,
    "records": [
      {
        "id": 3,
        "photo": "uploads/proofs/upload_abc.jpg",
        "notes": "Pekerjaan selesai, foto setelah perbaikan",
        "submitted_at": "2025-01-15 08:00:00",
        "officer": {
          "id": 5,
          "name": "Arif Nugraha"
        }
      }
    ]
  },
  "errors": []
}

Contoh Response Error (status belum validasi):
{
  "status": "error",
  "code": 409,
  "message": "Bukti belum dapat dilihat pada status saat ini.",
  "data": [],
  "errors": [
    {
      "reason": "proof_not_ready"
    }
  ]
}
*/
