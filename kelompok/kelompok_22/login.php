<?php
require_once 'includes/auth.php';

// Redirect cerdas jika user sudah login
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? 'warga';
    if ($role === 'admin') header('Location: admin.php');
    else if ($role === 'petugas') header('Location: petugas.php');
    else header('Location: pengguna.php');
    exit;
}

$error = '';

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Panggil fungsi dari auth.php
    $result = loginUser($username, $password);
    
    if ($result['success']) {
        // Redirect berdasarkan role
        switch ($result['role']) {
            case 'admin':
                header('Location: admin.php');
                break;
            case 'petugas':
                header('Location: petugas.php');
                break;
            default: // warga
                header('Location: pengguna.php');
                break;
        }
        exit;
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SiPaMaLi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-8 text-center text-white">
            <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-lg border border-white/30">
                <i class="fa-solid fa-leaf"></i>
            </div>
            <h1 class="text-2xl font-bold">SiPaMaLi</h1>
            <p class="text-emerald-100 text-sm">Satu Akun untuk Semua Layanan</p>
        </div>

        <!-- Form -->
        <div class="p-8">
            <h2 class="text-xl font-bold text-slate-800 mb-6 text-center">Selamat Datang Kembali!</h2>

            <?php if ($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 text-sm rounded-r flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Username</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-4 top-3.5 text-slate-400"></i>
                        <input type="text" name="username" required 
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition text-sm placeholder:text-slate-400"
                            placeholder="Masukkan username Anda">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-4 top-3.5 text-slate-400"></i>
                        <input type="password" name="password" required 
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition text-sm placeholder:text-slate-400"
                            placeholder="Masukkan kata sandi">
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl shadow-lg transition transform active:scale-95">
                    Masuk Sekarang
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-500">
                    Belum punya akun warga? 
                    <a href="registrasi.php" class="text-emerald-600 font-bold hover:underline">Daftar disini</a>
                </p>
            </div>
            
            <div class="mt-4 text-center">
                <a href="index.php" class="text-xs text-slate-400 hover:text-slate-600 flex items-center justify-center gap-1">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

</body>
</html>