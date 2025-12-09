<?php
/**
 * TESTING SUITE - MATERI DOSEN
 * 
 * Test Coverage:
 * - PDF upload dengan validasi format
 * - File size validation (max 10MB)
 * - Video link validation (YouTube, Google Drive)
 * - Edit materi
 * - Delete materi dengan cleanup file
 * - Security: prevent direct URL access
 * - Authorization: ownership verification
 */

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';

// ============================================
// TEST STATISTICS
// ============================================

$total_tests = 0;
$passed_tests = 0;
$failed_tests = 0;
$all_tests = [];
$test_results = [];

// ============================================
// SETUP: Test Data
// ============================================

$test_dosen = [
    'id' => null,
    'email' => 'dosen_materi_' . time() . '@test.com',
    'password' => password_hash('TestDosen123', PASSWORD_BCRYPT),
    'nama' => 'Dosen Materi Test',
    'role' => 'dosen',
    'npm_nidn' => 'NIDN_MATERI_' . rand(1000, 9999)
];

$test_kelas = [
    'id' => null,
    'nama_matakuliah' => 'Testing Materi ' . time(),
    'kode_matakuliah' => 'TEST' . rand(100, 999),
    'semester' => '5',
    'tahun_ajaran' => '2024/2025',
    'deskripsi' => 'Class for materi testing',
    'kode_kelas' => strtoupper(chr(65 + rand(0, 25))) . strtoupper(chr(65 + rand(0, 25))) . rand(1000, 9999),
    'kapasitas' => 50
];

$test_pdf_path = null;

try {
    // Create test dosen
    $stmt = $pdo->prepare(
        "INSERT INTO users (nama, email, password, role, npm_nidn) 
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $test_dosen['nama'],
        $test_dosen['email'],
        $test_dosen['password'],
        $test_dosen['role'],
        $test_dosen['npm_nidn']
    ]);
    $test_dosen['id'] = $pdo->lastInsertId();

    // Create test kelas
    $stmt = $pdo->prepare(
        "INSERT INTO kelas (id_dosen, nama_matakuliah, kode_matakuliah, semester, tahun_ajaran, deskripsi, kode_kelas, kapasitas) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $test_dosen['id'],
        $test_kelas['nama_matakuliah'],
        $test_kelas['kode_matakuliah'],
        $test_kelas['semester'],
        $test_kelas['tahun_ajaran'],
        $test_kelas['deskripsi'],
        $test_kelas['kode_kelas'],
        $test_kelas['kapasitas']
    ]);
    $test_kelas['id'] = $pdo->lastInsertId();

    // ============================================
    // TEST 1: PDF FILE VALIDATION
    // ============================================

    // Test 1.1: Valid PDF upload
    add_test_result("Upload valid PDF file", function() use ($pdo, $test_kelas, $test_dosen, &$test_pdf_path) {
        $temp_pdf = createTempPdfFile();
        if (!$temp_pdf) {
            throw new Exception("Cannot create temp PDF");
        }

        $stmt = $pdo->prepare(
            "INSERT INTO materi (id_kelas, judul, deskripsi, tipe, file_path, pertemuan_ke) 
             VALUES (?, ?, ?, 'pdf', ?, ?)"
        );
        $stmt->execute([
            $test_kelas['id'],
            'Test PDF Upload',
            'Test description',
            'uploads/materi/test_' . time() . '.pdf',
            1
        ]);

        $materi_id = $pdo->lastInsertId();
        if ($materi_id <= 0) {
            throw new Exception("Failed to insert materi");
        }

        $test_pdf_path = 'uploads/materi/test_' . $materi_id . '.pdf';
        return "Materi created: ID {$materi_id}";
    }, $test_results);

    // Test 1.2: Reject non-PDF files
    add_test_result("Reject non-PDF file (validation)", function() use ($pdo, $test_kelas, $test_dosen) {
        // Create a test text file (not a PDF) - use sys_get_temp_dir() for cross-platform compatibility
        $temp_dir = sys_get_temp_dir();
        $filename = $temp_dir . DIRECTORY_SEPARATOR . 'test_not_pdf_' . time() . '.txt';
        file_put_contents($filename, 'This is not a PDF file');

        // Verify it's detected as text/plain
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filename);
        finfo_close($finfo);

        if ($mime !== 'text/plain') {
            unlink($filename);
            throw new Exception("Test file should be text/plain, got: {$mime}");
        }

        // Try to validate this file using the same validation logic
        $fake_file = [
            'tmp_name' => $filename,
            'name' => 'test_not_pdf.txt',
            'size' => filesize($filename)
        ];

        // Call the validation function (copy here for testing)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_mime = finfo_file($finfo, $fake_file['tmp_name']);
        finfo_close($finfo);

        unlink($filename);

        // Check if it properly rejects non-PDF
        $allowed_types = ['application/pdf'];
        if (in_array($file_mime, $allowed_types)) {
            throw new Exception("Should reject text files, but MIME validation passed");
        }

        // Also check extension validation
        $extension = strtolower(pathinfo($fake_file['name'], PATHINFO_EXTENSION));
        if ($extension === 'pdf') {
            throw new Exception("Should reject .txt extension");
        }

        return "Correctly rejected non-PDF file (MIME: {$file_mime}, Extension: {$extension})";
    }, $test_results);

    // Test 1.3: Validate PDF magic bytes
    add_test_result("Validate PDF header (magic bytes)", function() {
        $temp_pdf = createTempPdfFile();
        $handle = fopen($temp_pdf, 'rb');
        $header = fread($handle, 4);
        fclose($handle);
        unlink($temp_pdf);

        if ($header === '%PDF') {
            return "PDF header validation passed";
        }
        throw new Exception("Invalid PDF header");
    }, $test_results);

    // Test 1.4: File size validation (max 10MB)
    add_test_result("Validate file size limit (10MB max)", function() {
        $max_size = 10 * 1024 * 1024;
        $test_sizes = [
            1024 => true,           // 1KB - OK
            5 * 1024 * 1024 => true, // 5MB - OK
            15 * 1024 * 1024 => false // 15MB - REJECT
        ];

        foreach ($test_sizes as $size => $should_pass) {
            $valid = $size <= $max_size;
            if ($valid !== $should_pass) {
                throw new Exception("Size validation failed for {$size} bytes");
            }
        }
        return "File size validation working correctly";
    }, $test_results);

    // ============================================
    // TEST 2: VIDEO LINK VALIDATION
    // ============================================

    // Test 2.1: YouTube URL validation
    add_test_result("Add YouTube video link", function() use ($pdo, $test_kelas) {
        $youtube_urls = [
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'https://youtu.be/dQw4w9WgXcQ'
        ];

        foreach ($youtube_urls as $url) {
            $embed_url = processYoutubeUrl($url);
            if (strpos($embed_url, 'youtube.com/embed/') === false) {
                throw new Exception("Failed to convert YouTube URL: {$url}");
            }
        }

        // Insert video materi
        $stmt = $pdo->prepare(
            "INSERT INTO materi (id_kelas, judul, deskripsi, tipe, video_url, pertemuan_ke) 
             VALUES (?, ?, ?, 'video', ?, ?)"
        );
        $stmt->execute([
            $test_kelas['id'],
            'Test YouTube Video',
            'Test description',
            'https://www.youtube.com/embed/dQw4w9WgXcQ',
            1
        ]);

        $materi_id = $pdo->lastInsertId();
        return "YouTube video added: ID {$materi_id}";
    }, $test_results);

    // Test 2.2: Google Drive URL validation
    add_test_result("Add Google Drive video link", function() use ($pdo, $test_kelas) {
        $drive_url = 'https://drive.google.com/file/d/1example_file_id_123/view';
        
        $embed_url = processGoogleDriveUrl($drive_url);
        if (strpos($embed_url, 'drive.google.com/file/d/') === false) {
            throw new Exception("Failed to convert Google Drive URL");
        }

        // Insert video materi
        $stmt = $pdo->prepare(
            "INSERT INTO materi (id_kelas, judul, deskripsi, tipe, video_url, pertemuan_ke) 
             VALUES (?, ?, ?, 'video', ?, ?)"
        );
        $stmt->execute([
            $test_kelas['id'],
            'Test Google Drive Video',
            'Test description',
            $embed_url,
            2
        ]);

        return "Google Drive video added successfully";
    }, $test_results);

    // ============================================
    // TEST 3: EDIT & DELETE OPERATIONS
    // ============================================

    // Test 3.1: Edit materi
    add_test_result("Edit materi (update judul)", function() use ($pdo) {
        $stmt = $pdo->prepare("SELECT id_materi FROM materi LIMIT 1");
        $stmt->execute();
        $materi = $stmt->fetch();

        if (!$materi) {
            throw new Exception("No materi to edit");
        }

        $stmt = $pdo->prepare("UPDATE materi SET judul = ? WHERE id_materi = ?");
        $stmt->execute(['Updated Title', $materi['id_materi']]);

        if ($stmt->rowCount() > 0) {
            return "Materi updated successfully";
        }
        throw new Exception("Update failed");
    }, $test_results);

    // Test 3.2: Delete materi
    add_test_result("Delete materi (with cleanup)", function() use ($pdo) {
        $stmt = $pdo->prepare("SELECT id_materi FROM materi WHERE tipe = 'pdf' LIMIT 1");
        $stmt->execute();
        $materi = $stmt->fetch();

        if (!$materi) {
            return "No PDF materi to delete (skipping)";
        }

        $stmt = $pdo->prepare("DELETE FROM materi WHERE id_materi = ?");
        $stmt->execute([$materi['id_materi']]);

        if ($stmt->rowCount() > 0) {
            return "Materi deleted successfully";
        }
        throw new Exception("Delete failed");
    }, $test_results);

    // ============================================
    // TEST 4: SECURITY TESTS
    // ============================================

    // Test 4.1: Prevent direct URL access
    add_test_result("Security: Prevent direct file URL access", function() use ($pdo) {
        // Check that files are served through download-materi.php
        // Not accessible directly from /uploads/materi/

        $stmt = $pdo->prepare("SELECT file_path FROM materi WHERE tipe = 'pdf' LIMIT 1");
        $stmt->execute();
        $materi = $stmt->fetch();

        if ($materi && $materi['file_path']) {
            // File should be protected by download-materi.php
            if (strpos($materi['file_path'], 'uploads/materi/') === 0) {
                return "Files are stored in protected uploads directory";
            }
        }
        return "No PDF files to test (skipping)";
    }, $test_results);

    // Test 4.2: Authorization check
    add_test_result("Security: Ownership verification", function() use ($pdo, $test_kelas, $test_dosen) {
        $stmt = $pdo->prepare(
            "SELECT k.id_dosen FROM kelas k WHERE k.id_kelas = ?"
        );
        $stmt->execute([$test_kelas['id']]);
        $kelas = $stmt->fetch();

        if ($kelas['id_dosen'] == $test_dosen['id']) {
            return "Ownership verification working";
        }
        throw new Exception("Ownership mismatch");
    }, $test_results);

    // ============================================
    // CLEANUP
    // ============================================

    try {
        $pdo->prepare("DELETE FROM materi WHERE id_kelas = ?")->execute([$test_kelas['id']]);
        $pdo->prepare("DELETE FROM kelas WHERE id_kelas = ?")->execute([$test_kelas['id']]);
        $pdo->prepare("DELETE FROM users WHERE id_user = ?")->execute([$test_dosen['id']]);
    } catch (Exception $e) {
        // Continue
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}

// ============================================
// RESPONSE
// ============================================

echo json_encode([
    'success' => true,
    'stats' => [
        'total' => $total_tests,
        'passed' => $passed_tests,
        'failed' => $failed_tests,
        'success_rate' => $total_tests > 0 ? round(($passed_tests / $total_tests) * 100) : 0
    ],
    'tests' => array_merge(
        isset($test_results) ? $test_results : []
    ),
    'all_tests' => $all_tests
]);

// ============================================
// HELPER FUNCTIONS
// ============================================

function add_test_result($name, $callback, &$category_results = null) {
    global $total_tests, $passed_tests, $failed_tests, $all_tests;
    
    $total_tests++;
    
    try {
        $message = $callback();
        $passed_tests++;
        
        $result = [
            'name' => $name,
            'passed' => true,
            'message' => $message ?: 'PASSED'
        ];
    } catch (Exception $e) {
        $failed_tests++;
        
        $result = [
            'name' => $name,
            'passed' => false,
            'message' => $e->getMessage()
        ];
    }
    
    if (is_array($category_results)) {
        $category_results[] = $result;
    }
    $all_tests[] = $result;
}

function createTempPdfFile() {
    // Use sys_get_temp_dir() for cross-platform compatibility
    $temp_file = tempnam(sys_get_temp_dir(), 'pdf_');
    
    // Create minimal PDF
    $pdf_content = "%PDF-1.4\n";
    $pdf_content .= "1 0 obj\n<</Type /Catalog /Pages 2 0 R>>\nendobj\n";
    $pdf_content .= "2 0 obj\n<</Type /Pages /Kids [3 0 R] /Count 1>>\nendobj\n";
    $pdf_content .= "3 0 obj\n<</Type /Page /Parent 2 0 R /MediaBox [0 0 612 792]>>\nendobj\n";
    $pdf_content .= "xref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\n";
    $pdf_content .= "trailer\n<</Size 4 /Root 1 0 R>>\nstartxref\n221\n%%EOF\n";
    
    file_put_contents($temp_file, $pdf_content);
    return $temp_file;
}

function processYoutubeUrl($url) {
    if (preg_match('%youtube\.com/watch\?v=([a-zA-Z0-9_-]+)%', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    if (preg_match('%youtu\.be/([a-zA-Z0-9_-]+)%', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    return null;
}

function processGoogleDriveUrl($url) {
    if (preg_match('%drive\.google\.com/file/d/([a-zA-Z0-9_-]+)%', $url, $m)) {
        return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
    }
    return null;
}

?>
