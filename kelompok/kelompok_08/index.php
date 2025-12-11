<?php
session_start(); include 'config.php';
if (isset($_POST['login'])) {
    $email = $_POST['email']; $password = $_POST['password'];
    $q = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($q) > 0) {
        $d = mysqli_fetch_assoc($q);
        if (password_verify($password, $d['password'])) {
            $_SESSION['user'] = $d;
            header("Location: " . ($d['role']=='dosen' ? 'dosen/dashboard.php' : 'mahasiswa/dashboard.php')); exit;
        }
    }
    $err = "Email atau password salah.";
}
echo getHeader("Login - EduStruct");
?>
<body class="flex items-center justify-center min-h-screen bg-gray-100 p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 animate-fade-in">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-primary">Codevis</h1>
            <p class="text-gray-500 text-sm mt-1">Masuk untuk mulai belajar</p>
        </div>
        <?php if(isset($err)) echo "<div class='bg-red-50 text-red-600 p-3 rounded mb-4 text-sm text-center'>$err</div>"; ?>
        <form method="POST" class="space-y-4">
            <input type="email" name="email" placeholder="Email Address" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary outline-none">
            <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary outline-none">
            <button type="submit" name="login" class="w-full py-3 bg-primary text-white font-bold rounded-lg hover:bg-primaryDark transition shadow-lg">Masuk</button>
        </form>
        <p class="text-center text-sm text-gray-600 mt-6">Belum punya akun? <a href="register.php" class="text-primary font-bold hover:underline">Daftar</a></p>
    </div>
</body></html>