<?php
session_start();
require_once _DIR_ . "/config.php";

error_log("=== LOGIN REQUEST ===");
error_log("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
error_log("POST data: " . print_r($_POST, true));

$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? 'user';

if (empty($email) || empty($password)) {
    error_log("Validation failed - empty fields");
    echo "<script>alert('Email dan password wajib diisi!'); window.history.back();</script>";
    exit;
}


if ($role === 'admin') {
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    
    if (!$admin) {
        echo "<script>alert('Admin tidak ditemukan!'); window.history.back();</script>";
        exit;
    }
    
    $passwordValid = password_verify($password, $admin['password']) || $password === $admin['password'];
    
    if (!$passwordValid) {
        error_log("Admin password verification failed for: $email");
        echo "<script>alert('Password admin salah!'); window.history.back();</script>";
        exit;
    }
    
    $_SESSION['admin_status'] = "login";
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['role'] = "admin";
    
    error_log("Admin login successful: $email");
    header("Location: ../dashboard/admin/dashboard.php");
    exit;
    
} else {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR nim = ? LIMIT 1");
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if (!$user) {
        echo "<script>alert('Akun tidak ditemukan. Silakan daftar dulu.'); window.history.back();</script>";
        exit;
    }
    
    if (!password_verify($password, $user['password'])) {
        error_log("User password verification failed for: $email");
        echo "<script>alert('Password salah!'); window.history.back();</script>";
        exit;
    }
    
    $_SESSION['status'] = "login";
    $_SESSION['user_id']= $user['id'];
    $_SESSION['nim']    = $user['nim'];
    $_SESSION['nama']   = $user['first_name'];
    $_SESSION['role']   = "user";
    $_SESSION['has_voted'] = $user['has_voted'];
    
    error_log("User login successful: $email");
    header("Location: ../dashboard/user/dashboard.php");
    exit;
}
?>