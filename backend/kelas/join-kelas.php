<?php
/**
 * FITUR 5: JOIN KELAS - JOIN
 * Tanggung Jawab: ELISA (Database Engineer & Backend)
 * 
 * Deskripsi: Mahasiswa join kelas dengan kode
 * - Validasi kode kelas exists
 * - Cek mahasiswa belum join (check duplicate)
 * - Cek kapasitas kelas belum penuh
 * - Insert ke tabel kelas_mahasiswa
 */

header('Content-Type: application/json; charset=utf-8');

session_start();

// Simple session check: pastikan user login dan role adalah mahasiswa
if (!isset($_SESSION['id_user']) || ($_SESSION['role'] ?? '') !== 'mahasiswa') {
	http_response_code(401);
	echo json_encode(['success' => false, 'message' => 'Unauthorized. Mohon login sebagai mahasiswa.']);
	exit;
}

$id_mahasiswa = (int) $_SESSION['id_user'];

// Ambil input
$kode_kelas = isset($_POST['kode_kelas']) ? trim($_POST['kode_kelas']) : '';
if ($kode_kelas === '') {
	http_response_code(400);
	echo json_encode(['success' => false, 'message' => 'Kode kelas diperlukan.']);
	exit;
}

require_once __DIR__ . '/../../config/database.php';

try {
	// 1) Cari kelas berdasarkan kode
	$stmt = $pdo->prepare('SELECT id_kelas, kapasitas FROM kelas WHERE kode_kelas = :kode LIMIT 1');
	$stmt->execute([':kode' => $kode_kelas]);
	$kelas = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!$kelas) {
		http_response_code(404);
		echo json_encode(['success' => false, 'message' => 'Kelas tidak ditemukan.']);
		exit;
	}

	$id_kelas = (int) $kelas['id_kelas'];
	$kapasitas = (int) $kelas['kapasitas'];

	// 2) Cek duplicate enrollment
	$stmt = $pdo->prepare('SELECT COUNT(*) AS cnt FROM kelas_mahasiswa WHERE id_kelas = :id_kelas AND id_mahasiswa = :id_mahasiswa');
	$stmt->execute([':id_kelas' => $id_kelas, ':id_mahasiswa' => $id_mahasiswa]);
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	if ($row && (int) $row['cnt'] > 0) {
		echo json_encode(['success' => false, 'message' => 'Anda sudah tergabung di kelas ini.']);
		exit;
	}

	// 3) Cek kapasitas
	$stmt = $pdo->prepare('SELECT COUNT(*) AS jumlah FROM kelas_mahasiswa WHERE id_kelas = :id_kelas');
	$stmt->execute([':id_kelas' => $id_kelas]);
	$countRow = $stmt->fetch(PDO::FETCH_ASSOC);
	$jumlah = $countRow ? (int) $countRow['jumlah'] : 0;
	if ($jumlah >= $kapasitas) {
		echo json_encode(['success' => false, 'message' => 'Kapasitas kelas sudah penuh.']);
		exit;
	}

	// 4) Insert enrollment
	$stmt = $pdo->prepare('INSERT INTO kelas_mahasiswa (id_kelas, id_mahasiswa) VALUES (:id_kelas, :id_mahasiswa)');
	$stmt->execute([':id_kelas' => $id_kelas, ':id_mahasiswa' => $id_mahasiswa]);

	echo json_encode(['success' => true, 'message' => 'Berhasil bergabung ke kelas.']);
	exit;

} catch (PDOException $e) {
	http_response_code(500);
	echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan server: ' . $e->getMessage()]);
	exit;
}

?>
