<?php
/**
 * ADD VIDEO LINK - YouTube & Google Drive
 * Tanggung Jawab: SURYA (Backend Developer)
 * 
 * Fitur:
 * - Add video link (YouTube, Google Drive)
 * - Validasi URL format
 * - Extract video ID
 * - Embed-ready URL
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

if (!isset($_POST['id_kelas']) || !isset($_POST['judul']) || !isset($_POST['video_url'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$id_kelas = intval($_POST['id_kelas']);
$judul = trim($_POST['judul']);
$video_url = trim($_POST['video_url']);
$deskripsi = trim($_POST['deskripsi'] ?? '');
$pertemuan_ke = intval($_POST['pertemuan_ke'] ?? 1);
$id_dosen = $_SESSION['user_id'];

if (empty($judul) || empty($video_url)) {
    echo json_encode(['success' => false, 'message' => 'Judul and URL are required']);
    exit;
}

try {
    // Check kelas ownership
    $stmt = $pdo->prepare("SELECT id_kelas FROM kelas WHERE id_kelas = ? AND id_dosen = ?");
    $stmt->execute([$id_kelas, $id_dosen]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    // Validate and process video URL
    $processed_url = processVideoUrl($video_url);
    if (!$processed_url) {
        echo json_encode(['success' => false, 'message' => 'Invalid video URL format']);
        exit;
    }

    // Insert into database
    $stmt = $pdo->prepare(
        "INSERT INTO materi (id_kelas, judul, deskripsi, tipe, video_url, pertemuan_ke) 
         VALUES (?, ?, ?, 'video', ?, ?)"
    );
    
    $stmt->execute([
        $id_kelas,
        $judul,
        $deskripsi,
        $processed_url,
        $pertemuan_ke
    ]);

    $id_materi = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'Video added successfully',
        'id_materi' => $id_materi,
        'video_url' => $processed_url
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

/**
 * Process video URL - extract from various formats
 */
function processVideoUrl($url) {
    // YouTube
    if (preg_match('%youtube\.com/watch\?v=([a-zA-Z0-9_-]+)%', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    if (preg_match('%youtu\.be/([a-zA-Z0-9_-]+)%', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    
    // Google Drive
    if (preg_match('%drive\.google\.com/file/d/([a-zA-Z0-9_-]+)%', $url, $m)) {
        return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
    }
    
    // Already embed URL
    if (strpos($url, 'youtube.com/embed/') !== false || strpos($url, 'drive.google.com/file/d/') !== false) {
        return $url;
    }

    return null;
}
?>
