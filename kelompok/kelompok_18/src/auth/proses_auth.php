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
        
        if (password_verify($password, $data['password'])) {
            $_SESSION['status'] = 'login';
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['nama'] = $data['nama_lengkap'];
            $_SESSION['toko'] = $data['nama_toko']; 
            // Simpan foto profil ke session
            $_SESSION['foto_profil'] = $data['foto_profil']; 
            $_SESSION['role'] = $data['role'];

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

// --- 2. PROSES REGISTER (Tanpa Upload Foto) ---
elseif ($aksi == 'register') {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $toko     = mysqli_real_escape_string($koneksi, $_POST['nama_toko']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password']; 
    $alamat   = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori_bisnis']);

    $cek_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $_SESSION['error'] = "Email sudah digunakan!";
        header("Location: register.php");
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    // Foto profil default diset di sini
    $query = "INSERT INTO users (nama_lengkap, nama_toko, kategori_bisnis, email, password, alamat_toko, role, foto_profil) 
              VALUES ('$nama', '$toko', '$kategori', '$email', '$password_hash', '$alamat', 'umkm', 'default.jpg')";
    
    if (mysqli_query($koneksi, $query)) {
        $_SESSION['success'] = "Pendaftaran berhasil! Silakan login.";
        header("Location: login.php");
    } else {
        $_SESSION['error'] = "Gagal daftar: " . mysqli_error($koneksi);
        header("Location: register.php");
    }
}

// --- 3. UPDATE PROFIL (Hanya di sini bisa upload foto) ---
elseif ($aksi == 'update_profil') {
    $id_user    = $_SESSION['user_id'];
    $nama_toko  = mysqli_real_escape_string($koneksi, $_POST['nama_toko']);
    $alamat     = mysqli_real_escape_string($koneksi, $_POST['alamat_toko']);
    $kategori   = mysqli_real_escape_string($koneksi, $_POST['kategori_bisnis']);
    $deskripsi  = mysqli_real_escape_string($koneksi, $_POST['deskripsi_toko']);
    $no_hp      = mysqli_real_escape_string($koneksi, $_POST['no_hp']);

    // LOGIKA UPLOAD FOTO
    $query_update_foto = "";
    
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
        $target_dir = "../assets/uploads/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }

        $filename    = $_FILES['foto_profil']['name'];
        $filesize    = $_FILES['foto_profil']['size'];
        $filetmp     = $_FILES['foto_profil']['tmp_name'];
        $fileext     = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        $valid_ext   = ['jpg', 'jpeg', 'png', 'webp'];

        // Validasi Ekstensi & Ukuran (Max 2MB)
        if (in_array($fileext, $valid_ext) && $filesize <= 2000000) {
            // Nama file unik: profil_ID_TIMESTAMP.ext
            $new_filename = "profil_" . $id_user . "_" . time() . "." . $fileext;
            
            if (move_uploaded_file($filetmp, $target_dir . $new_filename)) {
                $_SESSION['foto_profil'] = $new_filename; // Update session
                $query_update_foto = ", foto_profil='$new_filename'";
            }
        } else {
            $_SESSION['error'] = "Gagal Upload: Format harus JPG/PNG/WEBP dan maksimal 2MB.";
            header("Location: profil.php");
            exit;
        }
    }

    $query = "UPDATE users SET 
              nama_toko='$nama_toko', 
              kategori_bisnis='$kategori',
              alamat_toko='$alamat',
              deskripsi_toko='$deskripsi',
              no_hp='$no_hp'
              $query_update_foto
              WHERE id='$id_user'";

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['toko'] = $nama_toko;
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