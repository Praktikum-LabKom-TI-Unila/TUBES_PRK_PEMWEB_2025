<?php
require_once '../config/database.php';
require_once '../config/auth.php';

checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = sanitize($_POST['judul_laporan']);
    $isi = sanitize($_POST['isi_laporan']);
    $user_id = $_SESSION['user_id'];

    if (empty($judul) || empty($isi)) {
        // Redirect with error (simplified for PHP Native)
        echo "<script>alert('Judul dan Isi Laporan harus diisi!'); window.history.back();</script>";
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO laporan (user_id, judul_laporan, isi_laporan, status) VALUES (?, ?, ?, 'received')");
        if ($stmt->execute([$user_id, $judul, $isi])) {
            // Redirect to My Reports on success
            header("Location: ../user/laporan_saya.php");
            exit;
        } else {
            echo "<script>alert('Gagal mengirim laporan.'); window.history.back();</script>";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    // If accessed directly, redirect back
    header("Location: ../user/tambah_laporan.php");
    exit;
}
