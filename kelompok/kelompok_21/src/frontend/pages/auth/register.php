<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - ScholarBridge</title>
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
                <a href="login.php" class="auth-tab">Masuk</a>
                <button class="auth-tab active">Daftar</button>
            </div>

            <!-- Error Message -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <?php
                        if ($_GET['error'] == 'email_taken') echo "Email sudah terdaftar.";
                        else if ($_GET['error'] == 'db_error') echo "Terjadi kesalahan sistem.";
                        else echo "Harap isi semua data.";
                    ?>
                </div>
            <?php endif; ?>

            <!-- Register Form -->
            <form action="../../../backend/auth/register_process.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-input" placeholder="Masukkan nama lengkap" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" placeholder="nama@email.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="password-toggle">
                        <input type="password" name="password" id="password" class="form-input" placeholder="Minimal 8 karakter" required minlength="8">
                        <span class="toggle-icon" onclick="togglePassword()">👁️</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Daftar Sebagai</label>
                    <div class="role-options">
                        <div class="role-option">
                            <input type="radio" name="role" id="role-learner" value="learner" required>
                            <label for="role-learner" class="role-label">
                                <div class="role-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M12,4A4,4 0 0,1 16,8A4,4 0 0,1 12,12A4,4 0 0,1 8,8A4,4 0 0,1 12,4M12,14C16.42,14 20,15.79 20,18V20H4V18C4,15.79 7.58,14 12,14Z"/>
                                    </svg>
                                </div>
                                <span class="role-name">Siswa</span>
                            </label>
                        </div>
                        <div class="role-option">
                            <input type="radio" name="role" id="role-tutor" value="tutor" required>
                            <label for="role-tutor" class="role-label">
                                <div class="role-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M12,3L1,9L12,15L21,10.09V17H23V9M5,13.18V17.18L12,21L19,17.18V13.18L12,17L5,13.18Z"/>
                                    </svg>
                                </div>
                                <span class="role-name">Tutor</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="terms" required>
                    <label for="terms" class="checkbox-label">
                        Saya setuju dengan <a href="#">Syarat & Ketentuan</a>
                    </label>
                </div>

                <button type="submit" class="btn-primary">Daftar Sekarang</button>
            </form>

            <div class="auth-footer">
                Sudah punya akun? <a href="login.php">Masuk</a>
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