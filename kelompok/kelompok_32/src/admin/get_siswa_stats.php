<?php
session_start();
include '../config.php';

// hanya admin atau siswa yang meminta data dirinya sendiri
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin','siswa'])) {
    die(json_encode(['error' => 'Unauthorized']));
}

$siswa_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$filter_by_kelas = isset($_GET['filter_by_kelas']) && $_GET['filter_by_kelas'] == '1';

// jika siswa meminta, batasi hanya untuk dirinya sendiri
if ($_SESSION['role'] === 'siswa' && $siswa_id !== intval($_SESSION['user_id'])) {
    die(json_encode(['error' => 'Unauthorized']));
}

// ambil kelas siswa (dipakai jika filter_by_kelas=1)
$student_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT kelas FROM users WHERE id = $siswa_id"));
$kelas_id = $student_row ? intval($student_row['kelas']) : null;

$where_filter = "WHERE ru.user_id = $siswa_id";
if ($filter_by_kelas && !is_null($kelas_id)) {
    $where_filter .= " AND u.kelas = $kelas_id";
}

$query = "SELECT 
    COUNT(*) as total_ujian,
    IFNULL(AVG(ru.skor), 0) as rata_rata,
    IFNULL(MAX(ru.skor), 0) as tertinggi,
    IFNULL(MIN(ru.skor), 0) as terendah,
    COALESCE(k.nama, u.kelas) as kelas_nama
    FROM riwayat_ujian ru
    JOIN ujian u ON ru.ujian_id = u.id
    LEFT JOIN kelas k ON u.kelas = k.id
    $where_filter";

$result = mysqli_query($conn, $query);
$stats = mysqli_fetch_assoc($result);

header('Content-Type: application/json');
echo json_encode([
    'total_ujian' => (int)$stats['total_ujian'],
    'rata_rata' => number_format($stats['rata_rata'], 1),
    'tertinggi' => (int)$stats['tertinggi'],
    'terendah' => (int)$stats['terendah'],
    'kelas_nama' => $stats['kelas_nama']
]);
?>