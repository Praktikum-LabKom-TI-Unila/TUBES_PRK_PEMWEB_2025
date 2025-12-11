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

$checkStmt = $pdo->prepare('SELECT id FROM complaints WHERE id = :id AND reporter_id = :reporter_id LIMIT 1');
$checkStmt->execute([
    ':id' => $complaintId,
    ':reporter_id' => $pelapor['id'],
]);

if (!$checkStmt->fetch()) {
    response_error(404, 'Pengaduan tidak ditemukan.');
}

$timeline = fetch_complaint_timeline($pdo, $complaintId);

response_success(200, 'Timeline pengaduan.', [
    'complaint_id' => $complaintId,
    'records' => $timeline,
]);
