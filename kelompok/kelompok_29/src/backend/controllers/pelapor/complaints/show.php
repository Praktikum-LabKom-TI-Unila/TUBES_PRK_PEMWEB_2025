<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/auth.php';
require_once __DIR__ . '/../../../helpers/complaints.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$complaintId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($complaintId <= 0) {
    response_error(400, 'Parameter id tidak valid.');
}

$pelapor = require_pelapor();
$pdo = get_pdo();

$stmt = $pdo->prepare(
    'SELECT c.*, o.id AS officer_internal_id, o.employee_id, o.department, o.specialization, o.officer_status,
            uo.full_name AS officer_name
     FROM complaints c
     LEFT JOIN officers o ON c.assigned_officer_id = o.id
     LEFT JOIN users uo ON uo.id = o.user_id
     WHERE c.id = :id AND c.reporter_id = :reporter_id
     LIMIT 1'
);
$stmt->execute([
    ':id' => $complaintId,
    ':reporter_id' => $pelapor['id'],
]);
$complaint = $stmt->fetch();

if (!$complaint) {
    response_error(404, 'Pengaduan tidak ditemukan.');
}

$timeline = fetch_complaint_timeline($pdo, $complaintId);

$proofStmt = $pdo->prepare(
    'SELECT id, photo_after, notes, created_at
     FROM completion_proofs
     WHERE complaint_id = :id
     ORDER BY created_at DESC'
);
$proofStmt->execute([':id' => $complaintId]);
$proofs = array_map(function ($row) {
    return [
        'id' => (int) $row['id'],
        'photo_after' => $row['photo_after'],
        'notes' => $row['notes'],
        'created_at' => $row['created_at'],
    ];
}, $proofStmt->fetchAll());

$officer = null;
if (!empty($complaint['officer_internal_id'])) {
    $officer = [
        'officer_id' => (int) $complaint['officer_internal_id'],
        'employee_id' => $complaint['employee_id'],
        'name' => $complaint['officer_name'],
        'department' => $complaint['department'],
        'specialization' => $complaint['specialization'],
        'status' => $complaint['officer_status'],
    ];
}

response_success(200, 'Detail pengaduan.', [
    'id' => (int) $complaint['id'],
    'title' => $complaint['title'],
    'category' => $complaint['category'],
    'status' => $complaint['status'],
    'description' => $complaint['description'],
    'address' => $complaint['address'],
    'photo_before' => $complaint['photo_before'],
    'location' => [
        'latitude' => $complaint['latitude'],
        'longitude' => $complaint['longitude'],
    ],
    'assigned_officer' => $officer,
    'created_at' => $complaint['created_at'],
    'updated_at' => $complaint['updated_at'],
    'timeline' => $timeline,
    'completion_proofs' => $proofs,
]);
