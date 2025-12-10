<?php
session_set_cookie_params(0, '/'); 
session_start();

$dbPath = dirname(__DIR__, 3) . '/config/database.php';

if (file_exists($dbPath)) {
    require_once $dbPath;
} else {
    die("System Error: Database configuration not found at: " . $dbPath);
}

if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header("Location: ../admin/dashboard.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === 'admin' && $password === '123') {
        
        $_SESSION['is_logged_in'] = true;
        $_SESSION['user_role'] = 'admin'; 
        $_SESSION['user_name'] = $username;

        header("Location: ../admin/dashboard.php");
        exit;
    } else {
        $error = "Username atau Password yang Anda masukkan salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - ScholarBridge</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <!-- Logo -->
            <div class="auth-logo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M12 3L1 9L5 11.18V17.18L12 21L19 17.18V11.18L21 10.09V17H23V9L12 3M18.82 9L12 12.72L5.18 9L12 5.28L18.82 9M17 16L12 18.72L7 16V12.27L12 15L17 12.27V16Z"/>
                </svg>
            </div>

            <h1 class="auth-title">ScholarBridge</h1>
            <p class="auth-subtitle">Platform Bimbingan Belajar Terpercaya</p>

            <!-- Tabs -->
            <div class="auth-tabs">
                <button class="auth-tab active">Masuk</button>
                <a href="register.php" class="auth-tab">Daftar</a>
            </div>

            <!-- Error Message -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-input" id="username" name="username" placeholder="Masukkan username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-toggle">
                        <input type="password" class="form-input" id="password" name="password" placeholder="Masukkan password" required>
                        <span class="toggle-icon" onclick="togglePassword()">👁️</span>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary">Masuk</button>
            </form>

            <div class="auth-footer">
                Belum punya akun? <a href="register.php">Daftar</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        }
    </script>
</body>
</html>