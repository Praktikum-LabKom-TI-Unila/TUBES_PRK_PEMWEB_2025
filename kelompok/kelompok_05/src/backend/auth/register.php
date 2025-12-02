<?php
require 'config.php';

if (isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'warga'; 

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$hashed_password', '$role')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location='login.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

?>