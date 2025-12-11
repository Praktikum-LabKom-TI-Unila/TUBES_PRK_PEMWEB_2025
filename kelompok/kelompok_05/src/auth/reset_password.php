<?php
session_start();
require '../config/config.php';

$success = "";
$error = "";
$valid_token = false;
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header("Location: forgot_password.php");
    exit;
}

$check_table = $conn->query("SHOW TABLES LIKE 'password_resets'");
if ($check_table->num_rows === 0) {
    $error = "Token tidak valid atau sudah kadaluarsa!";
} else {
    $stmt = $conn->prepare("SELECT pr.*, u.nama, u.email FROM password_resets pr JOIN users u ON pr.user_id = u.id WHERE pr.token = ? AND pr.used = 0 AND pr.expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $valid_token = true;
        $reset_data = $result->fetch_assoc();
    } else {
        $error = "Token tidak valid atau sudah kadaluarsa!";
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($password) || empty($confirm_password)) {
        $error = "Semua field harus diisi!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $reset_data['user_id']);
        
        if ($stmt->execute()) {
            $conn->query("UPDATE password_resets SET used = 1 WHERE token = '" . $conn->real_escape_string($token) . "'");
            $success = "Password berhasil direset! Silakan login dengan password baru Anda.";
            $valid_token = false;
        } else {
            $error = "Gagal mereset password. Silakan coba lagi.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - LampungSmart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/logo-navbar.css">
    <style>
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .password-wrapper {
            position: relative;
        }
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 8px;
            transition: all 0.3s ease;
        }
        .strength-weak { background-color: #dc3545; width: 33%; }
        .strength-medium { background-color: #ffc107; width: 66%; }
        .strength-strong { background-color: #28a745; width: 100%; }
    </style>
</head>
<body class="bg-auth">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card card-login p-4 p-md-5 bg-white">
                    <div class="card-body px-0">
                        <div class="text-center mb-4">
                            <img src="../assets/images/logo-lampung.png" alt="Logo Lampung" class="logo-lampung-sidebar">
                            <h3 class="fw-bold text-brand-primary">Reset Password</h3>
                            <?php if ($valid_token): ?>
                                <p class="text-muted">Buat password baru untuk <strong><?php echo htmlspecialchars($reset_data['nama']); ?></strong></p>
                            <?php endif; ?>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger py-2 text-center mb-4">
                                <small><i class="fas fa-exclamation-circle me-1"></i> <?php echo $error; ?></small>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success py-3 text-center mb-4">
                                <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                                <span><?php echo $success; ?></span>
                            </div>
                            <div class="d-grid">
                                <a href="login.php" class="btn btn-brand-yellow btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i> Login Sekarang
                                </a>
                            </div>
                        <?php elseif ($valid_token): ?>
                            <form action="" method="POST">
                                <div class="mb-4">
                                    <label class="form-label">Password Baru</label>
                                    <div class="password-wrapper">
                                        <input type="password" name="password" id="password" class="form-control fw-medium" placeholder="Minimal 6 karakter" required minlength="6">
                                        <i class="fas fa-eye password-toggle" onclick="togglePassword('password', this)"></i>
                                    </div>
                                    <div class="password-strength" id="passwordStrength"></div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <div class="password-wrapper">
                                        <input type="password" name="confirm_password" id="confirm_password" class="form-control fw-medium" placeholder="Ulangi password baru" required>
                                        <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password', this)"></i>
                                    </div>
                                    <small id="matchStatus" class="text-muted"></small>
                                </div>

                                <div class="d-grid mb-4">
                                    <button type="submit" class="btn btn-brand-yellow btn-lg">
                                        <i class="fas fa-key me-2"></i> Reset Password
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="text-center">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <p class="text-muted">Link reset password tidak valid atau sudah kadaluarsa. Silakan minta link reset baru.</p>
                                <a href="forgot_password.php" class="btn btn-outline-primary">
                                    <i class="fas fa-redo me-1"></i> Minta Link Baru
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mt-4">
                            <a href="login.php" class="text-decoration-none text-muted">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke halaman login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        document.getElementById('password')?.addEventListener('input', function() {
            const strength = document.getElementById('passwordStrength');
            const password = this.value;
            
            strength.className = 'password-strength';
            
            if (password.length === 0) {
                strength.style.width = '0';
            } else if (password.length < 6) {
                strength.classList.add('strength-weak');
            } else if (password.length < 10 || !/[A-Z]/.test(password) || !/[0-9]/.test(password)) {
                strength.classList.add('strength-medium');
            } else {
                strength.classList.add('strength-strong');
            }
        });

        document.getElementById('confirm_password')?.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const matchStatus = document.getElementById('matchStatus');
            
            if (this.value === '') {
                matchStatus.textContent = '';
            } else if (this.value === password) {
                matchStatus.innerHTML = '<i class="fas fa-check text-success me-1"></i> Password cocok';
                matchStatus.className = 'text-success small';
            } else {
                matchStatus.innerHTML = '<i class="fas fa-times text-danger me-1"></i> Password tidak cocok';
                matchStatus.className = 'text-danger small';
            }
        });
    </script>
</body>
</html>
