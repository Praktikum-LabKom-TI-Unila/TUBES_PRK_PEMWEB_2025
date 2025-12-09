<?php
/**
 * UPDATE MATERI - Edit info & file
 * Tanggung Jawab: SURYA (Backend Developer)
 * 
 * Fitur:
 * - Edit judul, deskripsi
 * - Replace PDF file jika ada upload baru
 * - Update video URL
 * - Cleanup old files
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
$judul = isset($_POST['judul']) ? trim($_POST['judul']) : null;
$deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : null;
$pertemuan_ke = isset($_POST['pertemuan_ke']) ? intval($_POST['pertemuan_ke']) : null;
$id_dosen = $_SESSION['user_id'];

try {
    // Check ownership
    $stmt = $pdo->prepare(
        "SELECT m.id_materi, m.file_path, m.tipe, k.id_dosen 
         FROM materi m 
         JOIN kelas k ON m.id_kelas = k.id_kelas 
         WHERE m.id_materi = ?"
    );
    $stmt->execute([$id_materi]);
    $materi = $stmt->fetch();

    if (!$materi || $materi['id_dosen'] != $id_dosen) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    // Build update query
    $update_fields = [];
    $update_values = [];

    if ($judul !== null) {
        $update_fields[] = 'judul = ?';
        $update_values[] = $judul;
    }

    if ($deskripsi !== null) {
        $update_fields[] = 'deskripsi = ?';
        $update_values[] = $deskripsi;
    }

    if ($pertemuan_ke !== null) {
        $update_fields[] = 'pertemuan_ke = ?';
        $update_values[] = $pertemuan_ke;
    }

    // Handle new file upload
    if (isset($_FILES['file']) && $_FILES['file']['size'] > 0) {
        $file_error = validatePdfFile($_FILES['file']);
        if ($file_error) {
            echo json_encode(['success' => false, 'message' => $file_error]);
            exit;
        }

        // Delete old file
        if ($materi['file_path'] && file_exists(__DIR__ . '/../../' . $materi['file_path'])) {
            unlink(__DIR__ . '/../../' . $materi['file_path']);
        }

        // Upload new file
        $timestamp = time();
        $unique_filename = "materi_{$materi['id_materi']}_{$timestamp}.pdf";
        $upload_dir = __DIR__ . '/../../uploads/materi/';
        $upload_path = $upload_dir . $unique_filename;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $upload_path)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save file']);
            exit;
        }

        $update_fields[] = 'file_path = ?';
        $update_values[] = "uploads/materi/{$unique_filename}";
    }

    // Handle video URL update
    if (isset($_POST['video_url'])) {
        $video_url = trim($_POST['video_url']);
        if (!empty($video_url)) {
            $processed_url = processVideoUrl($video_url);
            if (!$processed_url) {
                echo json_encode(['success' => false, 'message' => 'Invalid video URL']);
                exit;
            }
            $update_fields[] = 'video_url = ?';
            $update_values[] = $processed_url;
        }
    }

    if (empty($update_fields)) {
        echo json_encode(['success' => false, 'message' => 'No fields to update']);
        exit;
    }

    $update_values[] = $id_materi;

    $stmt = $pdo->prepare(
        "UPDATE materi SET " . implode(', ', $update_fields) . " WHERE id_materi = ?"
    );
    
    $stmt->execute($update_values);

    echo json_encode([
        'success' => true,
        'message' => 'Materi updated successfully',
        'id_materi' => $id_materi
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
}

function validatePdfFile($file) {
    $max_size = 10 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return 'File size exceeds 10MB limit';
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if ($mime !== 'application/pdf') {
        return 'Invalid file type. Only PDF files are allowed';
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($extension !== 'pdf') {
        return 'File must be a PDF';
    }

    $handle = fopen($file['tmp_name'], 'rb');
    $header = fread($handle, 4);
    fclose($handle);
    
    if ($header !== '%PDF') {
        return 'File is not a valid PDF';
    }

    return null;
}

function processVideoUrl($url) {
    if (preg_match('%youtube\.com/watch\?v=([a-zA-Z0-9_-]+)%', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    if (preg_match('%youtu\.be/([a-zA-Z0-9_-]+)%', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    if (preg_match('%drive\.google\.com/file/d/([a-zA-Z0-9_-]+)%', $url, $m)) {
        return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
    }
    if (strpos($url, 'youtube.com/embed/') !== false || strpos($url, 'drive.google.com/file/d/') !== false) {
        return $url;
    }
    return null;
}
?>
