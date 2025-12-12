<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$pelapor = require_pelapor();
$pdo = get_pdo();

$stmt = $pdo->prepare(
    'SELECT 
        COUNT(*) AS total_all,
        SUM(CASE WHEN status IN ("diverifikasi_admin", "ditugaskan_ke_petugas", "dalam_proses", "menunggu_validasi_admin") THEN 1 ELSE 0 END) AS total_in_process,
        SUM(CASE WHEN status = "selesai" THEN 1 ELSE 0 END) AS total_completed
     FROM complaints
     WHERE reporter_id = :reporter_id'
);
$stmt->execute([':reporter_id' => $pelapor['id']]);
$stats = $stmt->fetch();

response_success(200, 'Ringkasan pelapor berhasil diambil.', [
    'total_laporan' => (int) ($stats['total_all'] ?? 0),
    'total_diproses' => (int) ($stats['total_in_process'] ?? 0),
    'total_selesai' => (int) ($stats['total_completed'] ?? 0),
]);