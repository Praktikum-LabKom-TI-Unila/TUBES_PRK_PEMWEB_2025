<?php
session_start();
require_once _DIR_ . "/config.php";

error_log("=== REGISTER REQUEST ===");
error_log("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
error_log("POST data: " . print_r($_POST, true));

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

if (strpos($contentType, 'application/json') !== false) {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    error_log("JSON data: " . $json);
    
    $firstName = $data['firstName'] ?? '';
    $lastName  = $data['lastName'] ?? '';
    $nim       = $data['nim'] ?? '';
    $email     = $data['email'] ?? '';
    $password  = $data['password'] ?? '';
} else {
    $firstName = $_POST['firstName'] ?? $_GET['firstName'] ?? '';
    $lastName  = $_POST['lastName'] ?? $_GET['lastName'] ?? '';
    $nim       = $_POST['nim'] ?? $_GET['nim'] ?? '';
    $email     = $_POST['email'] ?? $_GET['email'] ?? '';
    $password  = $_POST['password'] ?? $_GET['password'] ?? '';
}

if (empty($firstName) || empty($lastName) || empty($nim) || empty($email) || empty($password)) {
    error_log("Validation failed - empty fields");
    echo "<script>alert('Semua field wajib diisi.'); window.history.back();</script>";
    exit;
}

$stmtCheck = $conn->prepare("SELECT id FROM users WHERE nim = ? OR email = ?");
$stmtCheck->bind_param("ss", $nim, $email);
$stmtCheck->execute();
if ($stmtCheck->get_result()->num_rows > 0) {
    echo "<script>alert('NIM atau Email sudah terdaftar!'); window.history.back();</script>";
    exit;
}

$hashedPass = password_hash($password, PASSWORD_BCRYPT);

$stmtInsert = $conn->prepare("INSERT INTO users (first_name, last_name, nim, email, password, has_voted) VALUES (?, ?, ?, ?, ?, 0)");
$stmtInsert->bind_param("sssss", $firstName, $lastName, $nim, $email, $hashedPass);

if ($stmtInsert->execute()) {
    error_log("Registration successful for: $email");
    echo "<script>
            alert('Registrasi Berhasil! Silakan Login.'); 
            window.location='../login.php';
          </script>";
} else {
    error_log("Registration failed: " . $conn->error);
    echo "<script>alert('Gagal daftar: " . $conn->error . "'); window.history.back();</script>";
}
?>