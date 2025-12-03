<?php
session_start();
include '../config/koneksi.php';

$aksi = $_GET['aksi'];

// --- 1. PROSES LOGIN ---
if ($aksi == 'login') {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        // Cek Password (Plain text sesuai request awal, bisa di-upgrade ke password_verify)
        if ($password == $data['password']) {
            $_SESSION['status'] = 'login';
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['nama'] = $data['nama_lengkap'];
            $_SESSION['role'] = $data['role'];

            // Redirect Admin vs UMKM
            if ($data['role'] == 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../produk/index.php");
            }
        } else {
            $_SESSION['error'] = "Password salah!";
            header("Location: login.php");
        }
    } else {
        $_SESSION['error'] = "Email tidak ditemukan!";
        header("Location: login.php");
    }
}

// --- 2. PROSES REGISTER (UPDATED) ---
elseif ($aksi == 'register') {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $toko     = mysqli_real_escape_string($koneksi, $_POST['nama_toko']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password']; 
    $alamat   = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    // Tangkap Kategori Bisnis
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori_bisnis']);

    // Cek Email
    $cek_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $_SESSION['error'] = "Email sudah digunakan!";
        header("Location: register.php");
        exit;
    }

    // Insert dengan kategori_bisnis
    $query = "INSERT INTO users (nama_lengkap, nama_toko, kategori_bisnis, email, password, alamat_toko, role) 
              VALUES ('$nama', '$toko', '$kategori', '$email', '$password', '$alamat', 'umkm')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
        header("Location: login.php");
    } else {
        $_SESSION['error'] = "Gagal daftar: " . mysqli_error($koneksi);
        header("Location: register.php");
    }
}

// --- 3. PROSES UPDATE PROFIL (UPDATED) ---
elseif ($aksi == 'update_profil') {
    $id_user    = $_SESSION['user_id'];
    $nama_toko  = mysqli_real_escape_string($koneksi, $_POST['nama_toko']);
    $alamat     = mysqli_real_escape_string($koneksi, $_POST['alamat_toko']);
    
    // Data Baru
    $kategori   = mysqli_real_escape_string($koneksi, $_POST['kategori_bisnis']);
    $deskripsi  = mysqli_real_escape_string($koneksi, $_POST['deskripsi_toko']);
    $no_hp      = mysqli_real_escape_string($koneksi, $_POST['no_hp']);

    $query = "UPDATE users SET 
              nama_toko='$nama_toko', 
              kategori_bisnis='$kategori',
              alamat_toko='$alamat',
              deskripsi_toko='$deskripsi',
              no_hp='$no_hp'
              WHERE id='$id_user'";

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Profil toko berhasil diperbarui!";
        header("Location: profil.php");
    } else {
        $_SESSION['error'] = "Gagal update: " . mysqli_error($koneksi);
        header("Location: profil.php");
    }
}

// --- 4. LOGOUT ---
elseif ($aksi == 'logout') {
    session_destroy();
    header("Location: ../index.php");
}
?>