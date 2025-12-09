<?php
/**
 * DOWNLOAD MATERI - Secure file download
 * Tanggung Jawab: SURYA (Backend Developer)
 * 
 * Security Features:
 * - Verify user is enrolled in class or is owner dosen
 * - Prevent direct URL access to uploads folder
 * - Stream file with proper headers
 * - Log download activity
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../auth/session-check.php';

// Check login
if (!isLoggedIn()) {
    http_response_code(401);
    header('Location: /TUGASAKHIR/kelompok/kelompok_15/pages/login.html');
    exit;
}

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo 'Missing material ID';
    exit;
}

$id_materi = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

try {
    // Get materi info
    $stmt = $pdo->prepare(
        "SELECT m.id_materi, m.file_path, m.tipe, m.judul, k.id_kelas, k.id_dosen 
         FROM materi m 
         JOIN kelas k ON m.id_kelas = k.id_kelas 
         WHERE m.id_materi = ? AND m.tipe = 'pdf'"
    );
    $stmt->execute([$id_materi]);
    $materi = $stmt->fetch();

    if (!$materi) {
        http_response_code(404);
        echo 'Material not found';
        exit;
    }

    // Verify access
    $has_access = false;

    if ($user_role === 'dosen' && $materi['id_dosen'] == $user_id) {
        // Dosen owner
        $has_access = true;
    } elseif ($user_role === 'mahasiswa') {
        // Check if mahasiswa enrolled in this class
        $stmt = $pdo->prepare(
            "SELECT id FROM kelas_mahasiswa 
             WHERE id_kelas = ? AND id_mahasiswa = ?"
        );
        $stmt->execute([$materi['id_kelas'], $user_id]);
        if ($stmt->fetch()) {
            $has_access = true;
        }
    }

    if (!$has_access) {
        http_response_code(403);
        echo 'Access denied';
        exit;
    }

    // Build full file path
    $file_path = __DIR__ . '/../../' . $materi['file_path'];

    // Verify file exists and is in uploads directory (security)
    $real_path = realpath($file_path);
    $upload_dir = realpath(__DIR__ . '/../../uploads/');

    if (!$real_path || strpos($real_path, $upload_dir) !== 0 || !file_exists($real_path)) {
        http_response_code(404);
        echo 'File not found';
        exit;
    }

    // Set headers for download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . urlencode($materi['judul'] . '.pdf') . '"');
    header('Content-Length: ' . filesize($real_path));
    header('Cache-Control: private, max-age=0');
    header('Pragma: public');

    // Stream file
    readfile($real_path);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo 'Database error';
}
?>
