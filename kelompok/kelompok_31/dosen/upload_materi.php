<?php
/**
 * Upload Materi
 * Dikerjakan oleh: Anggota 3
 */

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../login.php");
    exit();
}

$page_title = "Upload Materi";
include '../components/header.php';
include '../components/navbar.php';
?>

<div class="container mt-4">
    <h2>Upload Materi Pembelajaran</h2>
    <!-- TODO Anggota 3: Form upload materi -->
</div>

<?php include '../components/footer.php'; ?>
