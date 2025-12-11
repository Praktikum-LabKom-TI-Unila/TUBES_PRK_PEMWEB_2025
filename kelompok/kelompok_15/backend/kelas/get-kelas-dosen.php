<?php
/**
 * FITUR 2: MANAJEMEN KELAS - GET KELAS DOSEN
 * API Endpoint untuk fetch semua kelas milik dosen
 * 
 * Deskripsi: Get semua kelas yang dibuat dosen
 * - Query kelas berdasarkan id_dosen
 * - Include counts: mahasiswa, materi, tugas
 * - Return JSON array kelas terurut
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../auth/session-check.php';
require_once __DIR__ . '/../auth/session-helper.php';

$response = ['success' => false, 'message' => '', 'data' => null];

try {
    // 1. Validate dosen role
    requireDosen();
    
    $dosenId = getUserId();

    // 2. Query kelas milik dosen dengan counts
    $query = "
        SELECT 
            k.id_kelas,
            k.nama_matakuliah,
            k.kode_matakuliah,
            k.deskripsi,
            k.created_at,
            COUNT(DISTINCT km.id_mahasiswa) as jumlah_mahasiswa,
            COUNT(DISTINCT m.id_materi) as jumlah_materi,
            COUNT(DISTINCT t.id_tugas) as jumlah_tugas
        FROM kelas k
        LEFT JOIN kelas_mahasiswa km ON k.id_kelas = km.id_kelas
        LEFT JOIN materi m ON k.id_kelas = m.id_kelas
        LEFT JOIN tugas t ON k.id_kelas = t.id_kelas
        WHERE k.id_dosen = ?
        GROUP BY k.id_kelas, k.nama_matakuliah, k.kode_matakuliah, k.deskripsi, k.created_at
        ORDER BY k.created_at DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$dosenId]);
    $kelas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response['success'] = true;
    $response['message'] = 'Kelas berhasil diambil';
    $response['data'] = $kelas;

    http_response_code(200);
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    http_response_code($e->getCode() === 403 ? 403 : 500);
}

echo json_encode($response);
?>
