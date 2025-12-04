<?php
session_start();
require_once '../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        header("Location: ../../frontend/pages/auth/register.php?error=empty_fields");
        exit();
    }

    $check_query = "SELECT id FROM users WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        header("Location: ../../frontend/pages/auth/register.php?error=email_taken");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', '$role')";

    if (mysqli_query($conn, $query)) {
        header("Location: ../../frontend/pages/auth/login.php?success=registered");
    } else {
        header("Location: ../../frontend/pages/auth/register.php?error=db_error");
    }
} else {
    header("Location: ../../frontend/pages/auth/register.php");
    exit();
}
?>