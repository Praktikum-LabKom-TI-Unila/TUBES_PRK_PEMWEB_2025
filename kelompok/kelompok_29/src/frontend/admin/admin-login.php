<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Pengaduan</title>
    <link rel="stylesheet" href="css/style.css"> 
    <link rel="stylesheet" href="css/admin-login.css">
</head>
<body>
    <div class="login-page-wrapper">
        <div class="login-container">
            <div class="text-center header-login">
                <div class="logo-wrapper">
                    🛡️
                </div>
                <h1 class="system-title">Sistem Pengaduan Infrastruktur</h1>
                <p class="portal-subtitle">Portal Admin</p>
            </div>

            <div class="card login-card">
                <h2 class="card-title">Masuk sebagai Admin</h2>
                
                <form id="loginForm" action="admin-process_login.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email Admin</label>
                        <div class="input-with-icon">
                            <span class="icon">✉️</span>
                            <input type="email" id="email" name="email" placeholder="admin@system.go.id" required value="admin@system.go.id">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-with-icon">
                            <span class="icon">🔒</span>
                            <input type="password" id="password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full-width">
                        Masuk ke Dashboard
                    </button>
                </form>
            </div>

            <div class="alert alert-warning-login">
                <p>⚠️ Halaman ini hanya untuk admin sistem</p>
            </div>
        </div>
    </div>
    <script src="js/admin-login.js"></script>
</body>
</html>