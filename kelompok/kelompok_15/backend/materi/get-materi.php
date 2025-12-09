<?php
/**
 * GET MATERI - List materi untuk dosen
 * Tanggung Jawab: SURYA (Backend Developer)
 * 
 * Fitur:
 * - Get all materi for dosen's classes
 * - Group by pertemuan
 * - Return JSON
 */

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../auth/session-check.php';

requireDosen();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_GET['id_kelas'])) {
    echo json_encode(['success' => false, 'message' => 'Missing id_kelas']);
    exit;
}

$id_kelas = intval($_GET['id_kelas']);
$id_dosen = $_SESSION['user_id'];

try {
    // Verify kelas ownership
    $stmt = $pdo->prepare("SELECT id_kelas FROM kelas WHERE id_kelas = ? AND id_dosen = ?");
    $stmt->execute([$id_kelas, $id_dosen]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    // Get all materi grouped by pertemuan
    $stmt = $pdo->prepare(
        "SELECT id_materi, judul, deskripsi, tipe, file_path, video_url, pertemuan_ke, uploaded_at 
         FROM materi 
         WHERE id_kelas = ? 
         ORDER BY pertemuan_ke ASC, uploaded_at DESC"
    );
    $stmt->execute([$id_kelas]);
    $materis = $stmt->fetchAll();

    // Group by pertemuan
    $grouped = [];
    foreach ($materis as $materi) {
        $pertemuan = $materi['pertemuan_ke'];
        if (!isset($grouped[$pertemuan])) {
            $grouped[$pertemuan] = [];
        }
        $grouped[$pertemuan][] = $materi;
    }

    echo json_encode([
        'success' => true,
        'data' => $grouped,
        'total' => count($materis)
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
