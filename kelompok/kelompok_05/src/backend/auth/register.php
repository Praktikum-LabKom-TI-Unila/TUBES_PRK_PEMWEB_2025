<?php
session_start();
require '../config.php'; 
$success = "";
$error = "";

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = 'warga'; 

    $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        $error = "Email sudah terdaftar!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$hashed_password', '$role')";

        if (mysqli_query($conn, $query)) {
            $success = "Registrasi Berhasil! Silakan Login.";
        } else {
            $error = "Gagal mendaftar: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - SigerHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../../frontend/css/style.css">
</head>

<body class="bg-register">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card card-login p-4 bg-white">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-success">Daftar Warga</h3>
                            <p class="text-muted">Bergabung dengan SigerHub</p>
                        </div>

                        <?php if($success): ?>
                            <div class="alert alert-success text-center">
                                <?php echo $success; ?> <br>
                                <a href="login.php" class="fw-bold">Klik disini untuk Login</a>
                            </div>
                        <?php endif; ?>

                        <?php if($error): ?>
                            <div class="alert alert-danger text-center">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <?php if(!$success): ?>
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" minlength="6" required>
                                <div class="form-text">Minimal 6 karakter</div>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" name="register" class="btn btn-success btn-lg">DAFTAR AKUN</button>
                            </div>
                        </form>
                        <?php endif; ?>

                        <div class="text-center mt-3">
                            <span class="text-muted">Sudah punya akun?</span>
                            <a href="login.php" class="text-decoration-none fw-bold text-success">Login disini</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>