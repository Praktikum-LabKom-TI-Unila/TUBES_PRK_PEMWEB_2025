<?php
/**
 * Export Data Admin
 * Dikerjakan oleh: Anggota 2
 * 
 * Export data sistem ke CSV
 */

session_start();

// Check if logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$pdo = $database->getConnection();

if (!$pdo) {
    die('Error: Koneksi database gagal');
}

// Get export type
$type = isset($_GET['type']) ? $_GET['type'] : 'all';

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="eduportal_export_' . $type . '_' . date('Y-m-d_His') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

try {
    switch ($type) {
        case 'mata_kuliah':
            // Export Mata Kuliah
            fputcsv($output, ['Kode', 'Nama', 'SKS', 'Dosen', 'Created At'], ';');
            $stmt = $pdo->query("
                SELECT mk.kode, mk.nama, mk.sks, u.nama as dosen_name, mk.created_at
                FROM mata_kuliah mk
                LEFT JOIN users u ON mk.dosen_id = u.id
                ORDER BY mk.kode
            ");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['kode'],
                    $row['nama'],
                    $row['sks'],
                    $row['dosen_name'] ?? 'Belum ditentukan',
                    $row['created_at']
                ], ';');
            }
            break;
            
        case 'users':
            // Export Users
            fputcsv($output, ['Username', 'Nama', 'Role', 'Created At'], ';');
            $stmt = $pdo->query("
                SELECT username, nama, role, created_at
                FROM users
                ORDER BY role, nama
            ");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['username'],
                    $row['nama'],
                    $row['role'],
                    $row['created_at']
                ], ';');
            }
            break;
            
        case 'pengumuman':
            // Export Pengumuman
            fputcsv($output, ['Judul', 'Isi', 'Author', 'Created At'], ';');
            $stmt = $pdo->query("
                SELECT p.judul, p.isi, u.nama as author_name, p.created_at
                FROM pengumuman p
                JOIN users u ON p.created_by = u.id
                ORDER BY p.created_at DESC
            ");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['judul'],
                    strip_tags($row['isi']),
                    $row['author_name'],
                    $row['created_at']
                ], ';');
            }
            break;
            
        case 'nilai':
            // Export Nilai
            fputcsv($output, ['Mata Kuliah', 'Mahasiswa', 'Nilai', 'Feedback', 'Dosen', 'Created At'], ';');
            $stmt = $pdo->query("
                SELECT mk.nama as mata_kuliah, u.nama as mahasiswa, n.nilai, n.feedback, d.nama as dosen, n.created_at
                FROM nilai n
                JOIN mata_kuliah mk ON n.mata_kuliah_id = mk.id
                JOIN users u ON n.mahasiswa_id = u.id
                JOIN users d ON n.created_by = d.id
                ORDER BY mk.nama, u.nama
            ");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['mata_kuliah'],
                    $row['mahasiswa'],
                    $row['nilai'],
                    $row['feedback'] ?? '',
                    $row['dosen'],
                    $row['created_at']
                ], ';');
            }
            break;
            
        default:
            // Export All (multiple sections in CSV)
            // Mata Kuliah
            fputcsv($output, ['=== MATA KULIAH ==='], ';');
            fputcsv($output, ['Kode', 'Nama', 'SKS', 'Dosen', 'Created At'], ';');
            $stmt = $pdo->query("
                SELECT mk.kode, mk.nama, mk.sks, u.nama as dosen_name, mk.created_at
                FROM mata_kuliah mk
                LEFT JOIN users u ON mk.dosen_id = u.id
                ORDER BY mk.kode
            ");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['kode'],
                    $row['nama'],
                    $row['sks'],
                    $row['dosen_name'] ?? 'Belum ditentukan',
                    $row['created_at']
                ], ';');
            }
            
            fputcsv($output, [], ';'); // Empty line
            
            // Users
            fputcsv($output, ['=== USERS ==='], ';');
            fputcsv($output, ['Username', 'Nama', 'Role', 'Created At'], ';');
            $stmt = $pdo->query("
                SELECT username, nama, role, created_at
                FROM users
                ORDER BY role, nama
            ");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['username'],
                    $row['nama'],
                    $row['role'],
                    $row['created_at']
                ], ';');
            }
            
            fputcsv($output, [], ';'); // Empty line
            
            // Pengumuman
            fputcsv($output, ['=== PENGUMUMAN ==='], ';');
            fputcsv($output, ['Judul', 'Isi', 'Author', 'Created At'], ';');
            $stmt = $pdo->query("
                SELECT p.judul, p.isi, u.nama as author_name, p.created_at
                FROM pengumuman p
                JOIN users u ON p.created_by = u.id
                ORDER BY p.created_at DESC
            ");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['judul'],
                    strip_tags($row['isi']),
                    $row['author_name'],
                    $row['created_at']
                ], ';');
            }
            
            fputcsv($output, [], ';'); // Empty line
            
            // Nilai
            fputcsv($output, ['=== NILAI ==='], ';');
            fputcsv($output, ['Mata Kuliah', 'Mahasiswa', 'Nilai', 'Feedback', 'Dosen', 'Created At'], ';');
            $stmt = $pdo->query("
                SELECT mk.nama as mata_kuliah, u.nama as mahasiswa, n.nilai, n.feedback, d.nama as dosen, n.created_at
                FROM nilai n
                JOIN mata_kuliah mk ON n.mata_kuliah_id = mk.id
                JOIN users u ON n.mahasiswa_id = u.id
                JOIN users d ON n.created_by = d.id
                ORDER BY mk.nama, u.nama
            ");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['mata_kuliah'],
                    $row['mahasiswa'],
                    $row['nilai'],
                    $row['feedback'] ?? '',
                    $row['dosen'],
                    $row['created_at']
                ], ';');
            }
            break;
    }
} catch (PDOException $e) {
    error_log("Export Error: " . $e->getMessage());
    fputcsv($output, ['Error: ' . $e->getMessage()], ';');
}

fclose($output);
exit();
?>
