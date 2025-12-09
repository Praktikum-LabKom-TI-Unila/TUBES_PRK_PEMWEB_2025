<?php
/**
 * Dashboard Mahasiswa
 * Dikerjakan oleh: Anggota 2
 */

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$page_title = "Dashboard Mahasiswa";
include '../components/header.php';
include '../components/navbar.php';
?>

<div class="container mt-4">
    <h2>Dashboard Mahasiswa</h2>
    <p>Selamat datang, <?php echo $_SESSION['username']; ?>!</p>
    
    <!-- TODO Anggota 2: Tambahkan daftar mata kuliah yang diambil -->
</div>

<?php include '../components/footer.php'; ?>
