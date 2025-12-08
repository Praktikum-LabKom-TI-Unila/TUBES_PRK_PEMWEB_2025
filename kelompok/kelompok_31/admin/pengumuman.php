<?php
/**
 * CRUD Pengumuman
 * Dikerjakan oleh: Anggota 4
 */

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$page_title = "Manajemen Pengumuman";
include '../components/header.php';
include '../components/navbar.php';
?>

<div class="container mt-4">
    <h2>Manajemen Pengumuman</h2>
    <!-- TODO Anggota 4: Implementasi CRUD pengumuman -->
</div>

<?php include '../components/footer.php'; ?>
