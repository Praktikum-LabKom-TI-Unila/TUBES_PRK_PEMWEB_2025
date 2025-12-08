<?php
/**
 * Input Nilai
 * Dikerjakan oleh: Anggota 4
 */

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../login.php");
    exit();
}

$page_title = "Input Nilai";
include '../components/header.php';
include '../components/navbar.php';
?>

<div class="container mt-4">
    <h2>Input Nilai Tugas</h2>
    <!-- TODO Anggota 4: Form input nilai -->
</div>

<?php include '../components/footer.php'; ?>
