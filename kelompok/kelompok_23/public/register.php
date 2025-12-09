<?php
session_start();
require_once __DIR__ . '/../database/db.php';

$errors = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($password !== $confirm) {
        $errors = 'Konfirmasi password tidak cocok.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors = 'Email sudah terdaftar.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?,?,?, 'anggota')");
            $stmt->execute([$name, $email, $hash]);
            $success = 'Registrasi berhasil, silakan login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - EventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .animate-gradient { 
            background: linear-gradient(270deg, #1e40af, #1e3a8a, #3b82f6, #1e40af);
            background-size: 800% 800%;
            animation: gradient 15s ease infinite;
        }

        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(30, 64, 175, 0.15);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s;
        }

        .btn-gradient:hover::before {
            left: 100%;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(30, 64, 175, 0.4);
        }

        .gradient-text {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .gradient-border {
            position: relative;
            background: white;
            border-radius: 1.5rem;
        }

        .gradient-border::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 1.5rem;
            padding: 2px;
            background: linear-gradient(135deg, #1e40af, #1e3a8a, #3b82f6);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
        }

        /* Smaller Scale */
        .w-full {
            width: 100%;
        }

        .w-full, .px-4, .py-2 {
            padding: 0.5rem;
        }

        .py-2, .px-4 {
            padding: 0.5rem;
        }

        .btn-gradient {
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
        }

        .input-focus:focus {
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 hero-pattern relative overflow-hidden">

<!-- Floating Background Elements -->
<div class="absolute inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-20 left-20 w-96 h-96 bg-gradient-to-r from-blue-300/15 to-indigo-300/15 rounded-full blur-3xl animate-float"></div>
    <div class="absolute bottom-20 right-20 w-[500px] h-[500px] bg-gradient-to-r from-blue-400/15 to-blue-600/15 rounded-full blur-3xl animate-float" style="animation-delay:1.5s"></div>
</div>

<!-- Back to Home Button -->
<a href="index.php" class="absolute top-8 left-8 z-10 glass-effect px-4 py-2 rounded-xl text-gray-700 font-semibold hover:shadow-xl transition duration-300 flex items-center gap-2 animate-fadeInUp">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
    </svg>
    Kembali
</a>

<div class="w-full max-w-md mx-4 relative z-10">
    <!-- Registration Card -->
    <div class="glass-effect rounded-2xl shadow-2xl overflow-hidden gradient-border animate-fadeInUp delay-200">
        
        <!-- Header Section -->
        <div class="relative bg-gradient-to-br from-blue-800 via-blue-900 to-blue-950 p-8 text-center overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-0 w-32 h-32 bg-white rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-48 h-48 bg-white rounded-full translate-x-1/3 translate-y-1/3"></div>
            </div>
            
            <div class="relative z-10">
                <div class="inline-flex items-center justify-center gap-3 mb-2">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-xl">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-3xl font-black text-white mb-1">EventHub</h1>
                <p class="text-blue-200 text-xs font-medium">Platform Manajemen Event Modern</p>
            </div>
        </div>

        <!-- Form Section -->
        <div class="p-8">
            <div class="text-center mb-6 animate-slideInRight delay-300">
                <h2 class="text-2xl font-black text-gray-900 mb-1">Selamat Datang! 👋</h2>
                <p class="text-gray-600 text-sm">Daftarkan akun Anda untuk bergabung dengan platform EventHub</p>
            </div>

            <?php if ($errors): ?>
                <div class="mb-5 text-sm text-red-700 bg-gradient-to-r from-red-50 to-pink-50 border-2 border-red-200 px-4 py-3 rounded-xl flex items-center gap-3 shadow-lg animate-fadeInUp">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold"><?= htmlspecialchars($errors) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="mb-5 text-sm text-emerald-600 bg-emerald-50 border-2 border-emerald-200 px-4 py-3 rounded-xl flex items-center gap-3 shadow-lg animate-fadeInUp">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold"><?= htmlspecialchars($success) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div class="animate-slideInRight delay-400">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap</label>
                    <input type="text" name="name" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition duration-300 input-focus bg-gray-50 font-medium text-sm">
                </div>

                <div class="animate-slideInRight delay-500">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                    <input type="email" name="email" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition duration-300 input-focus bg-gray-50 font-medium text-sm">
                </div>

                <div class="animate-slideInRight delay-600">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition duration-300 input-focus bg-gray-50 font-medium text-sm">
                </div>

                <div class="animate-slideInRight delay-700">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password</label>
                    <input type="password" name="confirm" required
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-100 focus:border-blue-400 transition duration-300 input-focus bg-gray-50 font-medium text-sm">
                </div>

                <button type="submit"
                        class="w-full btn-gradient text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-2xl transition duration-300 flex items-center justify-center gap-2 animate-slideInRight delay-800">
                    Daftar Sekarang
                </button>
            </form>

            <div class="mt-6 text-center text-sm animate-fadeInUp delay-900">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-xs">
                        <span class="px-3 bg-white text-gray-500 font-medium">atau</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center text-sm animate-fadeInUp delay-1000">
                <span class="text-gray-600">Sudah punya akun?</span>
                <a href="login.php" class="gradient-text font-bold hover:underline ml-1">
                    Login disini →
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
