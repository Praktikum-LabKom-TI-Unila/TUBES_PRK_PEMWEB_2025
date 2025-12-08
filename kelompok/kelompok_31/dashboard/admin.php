<?php
/**
 * Dashboard Admin
 * Dikerjakan oleh: Anggota 2
 */

session_start();

// Check if logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$page_title = "Dashboard Admin";
include '../components/header.php';
include '../components/navbar.php';
?>

<div class="container mt-4">
    <h2>Dashboard Admin</h2>
    <p>Selamat datang, <?php echo $_SESSION['username']; ?>!</p>
    
    <!-- TODO Anggota 2: Tambahkan statistik & quick actions -->
</div>

<?php include '../components/footer.php'; ?>
