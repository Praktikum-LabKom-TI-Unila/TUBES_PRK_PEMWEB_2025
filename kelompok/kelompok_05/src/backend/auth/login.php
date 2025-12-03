<?php
session_start();
require '../config.php';

if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../dashboard_admin.php");
    } else {
        header("Location: ../dashboard_warga.php");
    }
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'admin') {
                header("Location: ../dashboard_admin.php");
            } else {
                header("Location: ../dashboard_warga.php");
            }
            exit;
        }
    }
    $error = "Email atau Password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LampungSmart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../../frontend/css/style.css">
</head>

<body class="d-flex align-items-center justify-content-center">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card card-login p-4 bg-white">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-primary">SigerHub</h3>
                            <p class="text-muted text-small">Masuk untuk melanjutkan</p>
                        </div>

                        <?php if($error): ?>
                            <div class="alert alert-danger py-2 text-center text-small">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="******" required>
                            </div>

                            <div class="d-grid mb-3">
                                <button type="submit" name="login" class="btn btn-primary btn-block">MASUK SEKARANG</button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <span class="text-muted">Belum punya akun?</span>
                            <a href="register.php" class="text-decoration-none fw-bold">Daftar Warga</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>