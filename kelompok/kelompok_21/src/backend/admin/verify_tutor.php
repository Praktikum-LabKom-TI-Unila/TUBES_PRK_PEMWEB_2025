<?php
session_start();
require_once '../../config/database.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../frontend/pages/auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = intval($_POST['user_id']);
    $action  = $_POST['action'];

    if ($user_id > 0) {
        if ($action == 'approve') {
            // Ubah status jadi active
            $query = "UPDATE users SET status = 'active' WHERE id = $user_id";
            $msg   = "Tutor berhasil diverifikasi dan diaktifkan.";
        } elseif ($action == 'reject') {
            // Kalau ditolak, kita hapus saja datanya (atau bisa set status 'banned')
            $query = "DELETE FROM users WHERE id = $user_id";
            $msg   = "Permintaan tutor ditolak dan data dihapus.";
        } else {
            // Action tidak valid
            header("Location: ../../frontend/pages/admin/dashboard.php");
            exit();
        }

        // Eksekusi Query
        if (mysqli_query($conn, $query)) {
            header("Location: ../../frontend/pages/admin/dashboard.php?msg=" . urlencode($msg));
        } else {
            header("Location: ../../frontend/pages/admin/dashboard.php?msg=Terjadi_kesalahan_database");
        }
    }
} else {
    header("Location: ../../frontend/pages/admin/dashboard.php");
    exit();
}
?>