<?php
require "../config/database.php";

$nama = $_POST['nama_lengkap'];
$username = $_POST['username'];
$role = $_POST['role'];
$no_hp = $_POST['no_hp'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Cek username sudah dipakai atau belum
$cek = mysqli_query($conn, "SELECT id FROM users WHERE username='$username'");
if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Username sudah digunakan'); window.location='../register.php';</script>";
    exit;
}

// Insert user baru
$query = "INSERT INTO users (username, password, nama_lengkap, role, no_hp)
          VALUES ('$username', '$password', '$nama', '$role', '$no_hp')";

if (mysqli_query($conn, $query)) {
    echo "<script>alert('Registrasi Berhasil! Silakan login.'); window.location='../login.php';</script>";
} else {
    echo "<script>alert('Registrasi Gagal!'); window.location='../register.php';</script>";
}
