<?php
/**
 * UPLOAD MATERI - PDF dengan Progress Indicator
 * Tanggung Jawab: SURYA (Backend Developer)
 * 
 * Fitur:
 * - Validasi format PDF (strict)
 * - Max 10MB
 * - Unique filename
 * - Sanitized input
 * - Return JSON dengan file info
 */

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Include database & auth
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../auth/session-check.php';

// Check authentication
requireDosen();

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validate required fields
if (!isset($_POST['id_kelas']) || !isset($_POST['judul']) || !isset($_POST['pertemuan_ke'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Validate file upload
if (!isset($_FILES['file'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    exit;
}

$id_kelas = intval($_POST['id_kelas']);
$judul = trim($_POST['judul']);
$deskripsi = trim($_POST['deskripsi'] ?? '');
$pertemuan_ke = intval($_POST['pertemuan_ke']);
$file = $_FILES['file'];
$id_dosen = $_SESSION['user_id'];

// Validate materi input
if (empty($judul) || $pertemuan_ke < 1) {
    echo json_encode(['success' => false, 'message' => 'Invalid judul or pertemuan_ke']);
    exit;
}

try {
    // 1. Check kelas ownership
    $stmt = $pdo->prepare("SELECT id_kelas FROM kelas WHERE id_kelas = ? AND id_dosen = ?");
    $stmt->execute([$id_kelas, $id_dosen]);
    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized: Kelas not found']);
        exit;
    }

    // 2. Validate file properties
    $file_error = validatePdfFile($file);
    if ($file_error) {
        echo json_encode(['success' => false, 'message' => $file_error]);
        exit;
    }

    // 3. Generate unique filename
    $original_name = pathinfo($file['name'], PATHINFO_FILENAME);
    $timestamp = time();
    $unique_filename = "materi_{$id_kelas}_{$timestamp}.pdf";
    $upload_dir = __DIR__ . '/../../uploads/materi/';
    
    // Create directory if not exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $upload_path = $upload_dir . $unique_filename;

    // 4. Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save file']);
        exit;
    }

    // 5. Insert into database
    $stmt = $pdo->prepare(
        "INSERT INTO materi (id_kelas, judul, deskripsi, tipe, file_path, pertemuan_ke) 
         VALUES (?, ?, ?, 'pdf', ?, ?)"
    );
    
    $stmt->execute([
        $id_kelas,
        $judul,
        $deskripsi,
        "uploads/materi/{$unique_filename}",
        $pertemuan_ke
    ]);

    $id_materi = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'File uploaded successfully',
        'id_materi' => $id_materi,
        'file_name' => $unique_filename,
        'file_size' => $file['size'],
        'original_name' => $original_name
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

/**
 * Validate PDF file
 */
function validatePdfFile($file) {
    // Check file size (max 10MB)
    $max_size = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $max_size) {
        return 'File size exceeds 10MB limit';
    }

    if ($file['size'] === 0) {
        return 'File is empty';
    }

    // Check file type using MIME detection
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    // Validate MIME type
    $allowed_types = ['application/pdf'];
    if (!in_array($mime, $allowed_types)) {
        return 'Invalid file type. Only PDF files are allowed';
    }

    // Check file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($extension !== 'pdf') {
        return 'File must be a PDF';
    }

    // Check PDF header magic bytes
    $handle = fopen($file['tmp_name'], 'rb');
    $header = fread($handle, 4);
    fclose($handle);
    
    if ($header !== '%PDF') {
        return 'File is not a valid PDF';
    }

    return null; // Valid
}
?>
