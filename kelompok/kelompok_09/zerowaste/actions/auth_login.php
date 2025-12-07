<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Validasi input kosong
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Username dan password harus diisi!';
        header('Location: ../login.php');
        exit();
    }
    
    // Query dengan prepared statement (MENCEGAH SQL INJECTION)
    $stmt = mysqli_prepare($conn, "SELECT id, username, password, nama_lengkap, role, is_active, deleted_at 
                                    FROM users 
                                    WHERE username = ? AND deleted_at IS NULL");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // Cek apakah user aktif (BUKAN BANNED)
        if ($row['is_active'] == 0) {
            $_SESSION['error'] = 'Akun Anda telah dinonaktifkan. Hubungi admin.';
            header('Location: ../login.php');
            exit();
        }
        
        // Verifikasi password
        if (password_verify($password, $row['password'])) {
            // Set session (SESUAI KOLOM DB)
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama_lengkap'] = $row['nama_lengkap']; // BUKAN 'nama'
            $_SESSION['role'] = $row['role'];
            
            // Log activity
            logActivity($conn, $row['id'], 'LOGIN', 'User berhasil login');
            
            // Redirect berdasarkan role
            switch ($row['role']) {
                case 'admin':
                    header('Location: ../admin/dashboard.php');
                    break;
                case 'donatur':
                    header('Location: ../donatur/dashboard.php');
                    break;
                case 'mahasiswa':
                    header('Location: ../mahasiswa/dashboard.php');
                    break;
                default:
                    header('Location: ../index.php');
            }
            exit();
        } else {
            $_SESSION['error'] = 'Username atau password salah!';
            header('Location: ../login.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'Username atau password salah!';
        header('Location: ../login.php');
        exit();
    }
    
    mysqli_stmt_close($stmt);
} else {
    header('Location: ../login.php');
    exit();
}
?>