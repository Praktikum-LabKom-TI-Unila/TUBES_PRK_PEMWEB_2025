<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/complaints.php';
require_once __DIR__ . '/../../../helpers/admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

// Pseudo-autentikasi admin:
// $admin = require_admin(); // Validasi Bearer token & pastikan role admin
$admin = require_admin();

$ticketId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($ticketId <= 0) {
    response_error(400, 'ID tiket tidak valid.', [
        'field' => 'id',
        'reason' => 'invalid_id',
    ]);
}

$pdo = get_pdo();

$stmt = $pdo->prepare(
    'SELECT 
        c.id,
        c.title,
        c.description,
        c.category,
        c.status,
        c.address,
        c.latitude,
        c.longitude,
        c.photo_before,
        c.created_at,
        c.updated_at,
        u.id AS reporter_id,
        u.full_name AS reporter_name,
        u.email AS reporter_email,
        u.phone AS reporter_phone,
        u.address AS reporter_address,
        o.id AS officer_id,
        o.employee_id,
        o.department,
        o.specialization,
        ou.full_name AS officer_name,
        ou.email AS officer_email
     FROM complaints c
     JOIN users u ON u.id = c.reporter_id
     LEFT JOIN officers o ON o.id = c.assigned_officer_id
     LEFT JOIN users ou ON ou.id = o.user_id
     WHERE c.id = :id
     LIMIT 1'
);
$stmt->execute([':id' => $ticketId]);
$ticket = $stmt->fetch();

if (!$ticket) {
    response_error(404, 'Tiket tidak ditemukan.');
}

$timeline = fetch_complaint_timeline($pdo, $ticketId); // complaint_progress berperan sebagai complaint_status_history

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
$proofs = [];
foreach ($proofStmt->fetchAll() as $proof) {
    $proofs[] = [
        'id' => (int) $proof['id'],
        'photo' => $proof['photo_after'],
        'notes' => $proof['notes'],
        'submitted_at' => $proof['created_at'],
        'officer' => [
            'id' => (int) $proof['officer_id'],
            'name' => $proof['officer_name'],
        ],
    ];
}

// Ambil foto after terbaru dari bukti penyelesaian jika ada
$latestProofPhoto = null;
if (!empty($proofs)) {
  $latestProofPhoto = $proofs[0]['photo'];
}

response_success(200, 'Detail tiket berhasil diambil.', [
    'ticket' => [
        'id' => (int) $ticket['id'],
        'title' => $ticket['title'],
        'description' => $ticket['description'],
        'category' => $ticket['category'],
        'status' => $ticket['status'],
        'address' => $ticket['address'],
        'photo_before' => $ticket['photo_before'],
    'photo_after' => $latestProofPhoto,
        'location' => [
            'latitude' => $ticket['latitude'] !== null ? (float) $ticket['latitude'] : null,
            'longitude' => $ticket['longitude'] !== null ? (float) $ticket['longitude'] : null,
        ],
        'created_at' => $ticket['created_at'],
        'updated_at' => $ticket['updated_at'],
    ],
    'reporter' => [
        'id' => (int) $ticket['reporter_id'],
        'name' => $ticket['reporter_name'],
        'email' => $ticket['reporter_email'],
        'phone' => $ticket['reporter_phone'],
        'address' => $ticket['reporter_address'],
    ],
    'officer' => $ticket['officer_id'] ? [
        'id' => (int) $ticket['officer_id'],
        'employee_id' => $ticket['employee_id'],
        'name' => $ticket['officer_name'],
        'email' => $ticket['officer_email'],
        'department' => $ticket['department'],
        'specialization' => $ticket['specialization'],
    ] : null,
    'timeline' => $timeline,
    'completion_proofs' => $proofs,
]);

/*
Contoh Request: GET /api/admin/tickets/42 dengan header Authorization: Bearer <token_admin>

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Detail tiket berhasil diambil.",
  "data": {
    "ticket": {
      "id": 42,
      "title": "Lampu jalan padam",
      "description": "Lampu utama kawasan rusak",
      "category": "Penerangan_Jalan",
      "status": "menunggu_validasi_admin",
      "address": "Jl. Kenanga No. 2",
      "photo_before": "uploads/complaints/photo.jpg",
      "photo_after": null,
      "location": {
        "latitude": -6.2,
        "longitude": 106.8
      },
      "created_at": "2025-01-10 09:00:00",
      "updated_at": "2025-01-15 11:00:00"
    },
    "reporter": {
      "id": 7,
      "name": "Riska Dewi",
      "email": "riska@mail.com",
      "phone": "08123456789",
      "address": "Jl. Anggrek No. 5"
    },
    "officer": {
      "id": 3,
      "employee_id": "OFF-003",
      "name": "Dedi Pratama",
      "email": "dedi@pemkot.go.id",
      "department": "PUPR",
      "specialization": "Penerangan_Jalan"
    },
    "timeline": [
      { "id": 1, "status": "diajukan", "note": "Tiket dibuat", ... }
    ],
    "completion_proofs": []
  },
  "errors": []
}

Contoh Response Error:
{
  "status": "error",
  "code": 404,
  "message": "Tiket tidak ditemukan.",
  "data": [],
  "errors": []
}
*/
