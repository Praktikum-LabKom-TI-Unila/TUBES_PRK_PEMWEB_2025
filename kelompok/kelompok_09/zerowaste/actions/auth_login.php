<?php
session_start();
require "../config/database.php";

// Ambil input
$username = $_POST['username'];
$password = $_POST['password'];

// Cek username
$sql = "SELECT * FROM users WHERE username='$username' AND deleted_at IS NULL";
$q = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($q);

// Validasi login
if ($user && password_verify($password, $user['password'])) {

    $_SESSION['user_id']   = $user['id'];
    $_SESSION['nama']      = $user['nama_lengkap'];
    $_SESSION['role']      = $user['role'];

    // Catat Activity Log
    mysqli_query($conn, "
        INSERT INTO activity_logs(user_id, action, description, ip_address)
        VALUES ({$user['id']}, 'LOGIN', 'User berhasil login', '{$_SERVER['REMOTE_ADDR']}')
    ");

    // Redirect berdasarkan role
    if ($user['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
    } elseif ($user['role'] == 'donatur') {
        header("Location: ../donatur/dashboard.php");
    } else {
        header("Location: ../mahasiswa/dashboard.php");
    }
    exit;

} else {
    echo "<script>alert('Username atau Password salah'); window.location='../login.php';</script>";
}
