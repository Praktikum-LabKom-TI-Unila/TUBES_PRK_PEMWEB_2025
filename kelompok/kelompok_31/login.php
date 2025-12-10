<?php
/**
 * Halaman Login
 * Dikerjakan oleh: Anggota 1 (Ketua)
 * 
 * Desain modern dengan split-screen layout dan glassmorphism effect
 */

session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard/" . $_SESSION['role'] . ".php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EduPortal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
    <!-- Login Container - Split Screen -->
    <div class="login-container">
        <!-- Left Panel - Branding -->
        <div class="login-left-panel">
            <!-- Decorative Shapes -->
            <div class="decorative-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>
            
            <!-- Branding Content -->
            <div class="branding-content">
                <div class="branding-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h1 class="branding-title">EduPortal</h1>
                <p class="branding-welcome">Selamat Datang</p>
                <p class="branding-tagline">Sistem Manajemen Pembelajaran Akademik</p>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="login-right-panel">
            <div class="login-card-wrapper">
                <div class="login-card">
                    <!-- Login Header -->
                    <div class="login-header">
                        <h2 class="login-title">Login</h2>
                        <p class="login-subtitle">Masuk ke akun Anda</p>
                    </div>

                    <!-- Login Form -->
                    <form id="loginForm" class="login-form">
                        <!-- Username Field -->
                        <div class="form-group">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="username" 
                                    placeholder="Masukkan username"
                                    required
                                    autocomplete="username"
                                >
                            </div>
                        </div>

                        <!-- Password Field -->
                        <div class="form-group">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input 
                                    type="password" 
                                    class="form-control" 
                                    id="password" 
                                    placeholder="Masukkan password"
                                    required
                                    autocomplete="current-password"
                                >
                                <button 
                                    type="button" 
                                    class="password-toggle" 
                                    id="passwordToggle"
                                    aria-label="Toggle password visibility"
                                >
                                    <i class="fas fa-eye" id="passwordToggleIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Error Message -->
                        <div id="errorMessage" class="alert alert-danger d-none" role="alert"></div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-login w-100" id="loginBtn">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // Password Toggle Functionality
        $('#passwordToggle').on('click', function() {
            const passwordInput = $('#password');
            const passwordIcon = $('#passwordToggleIcon');
            
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                passwordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordInput.attr('type', 'password');
                passwordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // AJAX Login Implementation
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const username = $('#username').val().trim();
            const password = $('#password').val();
            
            // Validation
            if (!username || !password) {
                $('#errorMessage').removeClass('d-none').text('Username dan password wajib diisi');
                return;
            }
            
            // Hide previous error message
            $('#errorMessage').addClass('d-none').text('');
            
            // Disable submit button and show loading
            const $submitBtn = $('#loginBtn');
            const originalText = $submitBtn.html();
            $submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Memproses...');
            
            // AJAX request
            $.ajax({
                url: 'api/auth/login.php',
                method: 'POST',
                data: {
                    username: username,
                    password: password
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Login berhasil, redirect ke dashboard sesuai role
                        const role = response.role;
                        window.location.href = 'dashboard/' + role + '.php';
                    } else {
                        // Login gagal, tampilkan error
                        $('#errorMessage').removeClass('d-none').text(response.message || 'Login gagal. Periksa kembali username dan password Anda.');
                        $submitBtn.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    // Handle AJAX error
                    console.error('Login Error:', error);
                    let errorMsg = 'Terjadi kesalahan. Silakan coba lagi.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    $('#errorMessage').removeClass('d-none').text(errorMsg);
                    $submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    </script>
</body>
</html>
