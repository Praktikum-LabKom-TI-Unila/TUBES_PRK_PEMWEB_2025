<?php
include 'config.php';

if (isset($_POST['register'])) {
    $name = $_POST['name']; $email = $_POST['email']; $role = $_POST['role']; $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    if(mysqli_num_rows(mysqli_query($conn, "SELECT email FROM users WHERE email='$email'")) > 0) $err = "Email sudah terdaftar.";
    else {
        mysqli_query($conn, "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$pass', '$role')");
        echo "<script>alert('Berhasil! Silakan login.'); window.location='index.php';</script>";
    }
}
echo getHeader("Register");
?>
<body class="flex items-center justify-center min-h-screen bg-gray-100 p-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 animate-fade-in border border-gray-100">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Buat Akun Baru</h1>
            <p class="text-gray-500 text-sm">Bergabung dengan Codevis</p>
        </div>
        <?php if(isset($err)) echo "<div class='bg-red-50 text-red-600 p-3 rounded mb-4 text-sm text-center'>$err</div>"; ?>
        <form method="POST" class="space-y-4">
            <input type="text" name="name" placeholder="Nama Lengkap" required class="w-full px-4 py-3 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-primary outline-none">
            <input type="email" name="email" placeholder="Email" required class="w-full px-4 py-3 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-primary outline-none">
            <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-3 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-primary outline-none">
            <div class="relative">
                <select name="role" class="w-full px-4 py-3 bg-gray-50 border rounded-lg focus:ring-2 focus:ring-primary outline-none appearance-none cursor-pointer">
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                </select>
                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-gray-500">▼</div>
            </div>
            <button type="submit" name="register" class="w-full py-3 bg-primary text-white font-bold rounded-lg hover:bg-primaryDark transition shadow-lg">Daftar Sekarang</button>
        </form>
        <div class="mt-6 pt-4 border-t text-center"><a href="index.php" class="text-sm text-primary font-bold hover:underline">Kembali ke Login</a></div>
    </div>
</body></html>