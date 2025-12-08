<?php
/**
 * Daftar Tugas
 * Dikerjakan oleh: Anggota 3
 */

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$page_title = "Tugas Kuliah";
include '../components/header.php';
include '../components/navbar.php';
?>

<div class="container mt-4">
    <h2>Daftar Tugas</h2>
    <!-- TODO Anggota 3: Daftar tugas & submit -->
</div>

<?php include '../components/footer.php'; ?>
