<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: admin/dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | ZeroWaste</title>
</head>
<body>

<h2>Login</h2>

<form action="actions/auth_login.php" method="POST">
    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Masuk</button>
</form>

<p>Belum punya akun? <a href="register.php">Daftar</a></p>

</body>
</html>
