<?php
/**
 * DELETE MATERI - Remove materi & file
 * Tanggung Jawab: SURYA (Backend Developer)
 * 
 * Fitur:
 * - Delete from database
 * - Remove physical files
 * - Ownership verification
 */

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../auth/session-check.php';

requireDosen();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

if (!isset($_POST['id_materi'])) {
    echo json_encode(['success' => false, 'message' => 'Missing id_materi']);
    exit;
}

$id_materi = intval($_POST['id_materi']);
$id_dosen = $_SESSION['user_id'];

try {
    // Get materi info & verify ownership
    $stmt = $pdo->prepare(
        "SELECT m.id_materi, m.file_path, m.tipe, k.id_dosen 
         FROM materi m 
         JOIN kelas k ON m.id_kelas = k.id_kelas 
         WHERE m.id_materi = ?"
    );
    $stmt->execute([$id_materi]);
    $materi = $stmt->fetch();

    if (!$materi) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Materi not found']);
        exit;
    }

    if ($materi['id_dosen'] != $id_dosen) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    // Delete physical file if exists
    if ($materi['tipe'] === 'pdf' && $materi['file_path']) {
        $file_path = __DIR__ . '/../../' . $materi['file_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM materi WHERE id_materi = ?");
    $stmt->execute([$id_materi]);

    echo json_encode([
        'success' => true,
        'message' => 'Materi deleted successfully'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
