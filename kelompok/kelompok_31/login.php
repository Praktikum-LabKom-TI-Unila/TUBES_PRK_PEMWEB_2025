<?php
/**
 * Halaman Login
 * Dikerjakan oleh: Anggota 1 (Ketua)
 * 
 * TODO:
 * - Buat form login (username & password)
 * - Implementasi AJAX untuk login
 * - Redirect ke dashboard sesuai role
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
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-center mb-4">EduPortal Login</h3>
                        <form id="loginForm">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        <div id="errorMessage" class="alert alert-danger mt-3 d-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        // TODO Anggota 1: Implementasi AJAX login di sini
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            // Implementasi AJAX ke api/auth/login.php
            alert('TODO: Implementasi AJAX login');
        });
    </script>
</body>
</html>
