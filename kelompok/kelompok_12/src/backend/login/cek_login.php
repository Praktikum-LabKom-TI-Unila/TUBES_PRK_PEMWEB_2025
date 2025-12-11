<?php
session_start();
require_once '../../koneksi/database.php';

$username = isset($_POST['username']) ? bersihkan_input($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if(empty($username) || empty($password)) {
    header("location:../../frontend/login/login.php?pesan=gagal");
    exit;
}

$username_safe = mysqli_real_escape_string($conn, $username);
$query = "SELECT * FROM users WHERE username = '$username_safe'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0){
    $data = mysqli_fetch_assoc($result);

    if($data['is_active'] == 0) {
        header("location:../../frontend/login/login.php?pesan=nonaktif"); 
        exit;
    }

    if($password == $data['password']){
        
        $_SESSION['user'] = [
            'id' => $data['id'],
            'username' => $data['username'],
            'full_name' => $data['full_name'],
            'role' => $data['role'],
            'email' => $data['email']
        ];
        $_SESSION['status'] = "login";
        
        header("location:../../frontend/dashboard/dashboard.php");
        
    } else {
        header("location:../../frontend/login/login.php?pesan=gagal");
    }
} else {
    header("location:../../frontend/login/login.php?pesan=gagal");
}
?>