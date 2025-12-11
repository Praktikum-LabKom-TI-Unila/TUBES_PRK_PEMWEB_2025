<?php
require_once '../config/database.php';
require_once '../config/auth.php';

if (isset($_SESSION['user_id'])) {
    redirectBasedOnRole($_SESSION['role']);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Email dan password wajib diisi.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            redirectBasedOnRole($user['role']);
        } else {
            $error = "Email atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SILATIUM</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2">
                        </path>
                        <circle cx="7" cy="17" r="2"></circle>
                        <path d="M9 17h6"></path>
                        <circle cx="17" cy="17" r="2"></circle>
                    </svg>
                </div>
                <h1>SILATIUM</h1>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Sistem Informasi Layanan Transportasi Umum
                    (Smart City)</p>
            </div>

            <?php if ($error): ?>
                <div
                    style="background-color: var(--danger-bg); color: var(--danger-text); padding: 0.75rem; border-radius: var(--radius-md); margin-bottom: 1.5rem; text-align: center; font-size: 0.875rem;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="name@example.com">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
                <button type="submit" class="btn btn-primary"
                    style="width: 100%; justify-content: center; padding: 0.75rem;">
                    Masuk ke Sistem
                </button>
            </form>

            <div class="text-center mt-4" style="font-size: 0.875rem; color: var(--text-muted);">
                <p>Belum punya akun? <a href="register.php"
                        style="color: var(--primary-color); font-weight: 600;">Daftar sekarang</a></p>
            </div>
        </div>
    </div>
</body>

</html>