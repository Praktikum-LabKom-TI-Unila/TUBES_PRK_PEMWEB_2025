<?php
session_start();
include '../config/koneksi.php';

$aksi = $_GET['aksi'];

// --- 1. PROSES REGISTER ---
if ($aksi == 'register') {
    $nama     = $_POST['nama_lengkap'];
    $toko     = $_POST['nama_toko'];
    $email    = $_POST['email'];
    $password = $_POST['password']; 
    $alamat   = $_POST['alamat'];

    // Cek apakah email sudah terdaftar?
    $cek_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $_SESSION['error'] = "Email sudah digunakan! Gunakan email lain.";
        header("Location: register.php");
        exit;
    }

    // Masukkan data ke Database
    $query = "INSERT INTO users (nama_lengkap, nama_toko, email, password, alamat_toko, role) 
              VALUES ('$nama', '$toko', '$email', '$password', '$alamat', 'umkm')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
        header("Location: login.php");
    } else {
        $_SESSION['error'] = "Gagal daftar: " . mysqli_error($koneksi);
        header("Location: register.php");
    }
}

// --- 2. PROSES LOGIN ---
elseif ($aksi == 'login') {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Cari user berdasarkan email
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        // Cek Password
        if ($password == $data['password']) {
            // SET SESSION (Penting!)
            $_SESSION['status']   = 'login';
            $_SESSION['user_id']  = $data['id'];
            $_SESSION['nama']     = $data['nama_lengkap'];
            $_SESSION['toko']     = $data['nama_toko'];
            $_SESSION['role']     = $data['role'];

            // Redirect ke Dashboard Produk
            header("Location: ../produk/index.php");
        } else {
            $_SESSION['error'] = "Password salah!";
            header("Location: login.php");
        }
    } else {
        $_SESSION['error'] = "Email tidak ditemukan!";
        header("Location: login.php");
    }
}

// --- 3. PROSES LOGOUT ---
elseif ($aksi == 'logout') {
    session_destroy();
    header("Location: ../index.php");
}
?>