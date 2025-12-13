<?php
require_once '../config/database.php';
require_once '../config/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Semua field harus diisi.";
    } elseif ($password !== $confirm_password) {
        $error = "Password konfirmasi tidak cocok.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email sudah terdaftar.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $success = "Registrasi berhasil! Silakan login.";
            } else {
                $error = "Terjadi kesalahan sistem.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - SILATIUM</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h1>Buat Akun Baru</h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Bergabung dengan SILATIUM</p>
            </div>

            <?php if ($error): ?>
                <div
                    style="background-color: var(--danger-bg); color: var(--danger-text); padding: 0.75rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; text-align: center; font-size: 0.875rem;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div
                    style="background-color: var(--success-bg); color: var(--success-text); padding: 0.75rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; text-align: center; font-size: 0.875rem;">
                    <?= $success ?> <br>
                    <a href="login.php"
                        style="color: var(--success-text); text-decoration: underline; font-weight: 600;">Klik disini untuk
                        Login</a>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" required placeholder="John Doe">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="name@example.com">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Buat password aman">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required
                        placeholder="Ulangi password">
                </div>
                <button type="submit" class="btn btn-primary"
                    style="width: 100%; justify-content: center; padding: 0.75rem;">
                    Daftar Sekarang
                </button>
            </form>

            <div class="text-center mt-4" style="font-size: 0.875rem; color: var(--text-muted);">
                <p>Sudah punya akun? <a href="login.php" style="color: var(--primary-color); font-weight: 600;">Login
                        disini</a></p>
            </div>
        </div>
    </div>
</body>

</html>