<?php
/**
 * Daftar Materi
 * Dikerjakan oleh: Anggota 3
 */

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$page_title = "Materi Pembelajaran";
include '../components/header.php';
include '../components/navbar.php';
?>

<div class="container mt-4">
    <h2>Materi Pembelajaran</h2>
    <!-- TODO Anggota 3: Daftar materi & download -->
</div>

<?php include '../components/footer.php'; ?>
