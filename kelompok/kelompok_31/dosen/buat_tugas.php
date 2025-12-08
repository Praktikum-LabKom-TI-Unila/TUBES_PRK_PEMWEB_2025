<?php
/**
 * Buat Tugas
 * Dikerjakan oleh: Anggota 3
 */

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../login.php");
    exit();
}

$page_title = "Buat Tugas";
include '../components/header.php';
include '../components/navbar.php';
?>

<div class="container mt-4">
    <h2>Buat Tugas Baru</h2>
    <!-- TODO Anggota 3: Form buat tugas -->
</div>

<?php include '../components/footer.php'; ?>
