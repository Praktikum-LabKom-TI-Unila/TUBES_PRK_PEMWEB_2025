<?php
/**
 * Daftar Nilai
 * Dikerjakan oleh: Anggota 4
 */

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$page_title = "Nilai Saya";
include '../components/header.php';
include '../components/navbar.php';
?>

<div class="container mt-4">
    <h2>Nilai Saya</h2>
    <!-- TODO Anggota 4: Daftar nilai -->
</div>

<?php include '../components/footer.php'; ?>
