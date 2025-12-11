<?php 
session_start();
if(isset($_SESSION['status']) && $_SESSION['status'] == "login"){
    header("location:../dashboard/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - NPC System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { npcGreen: '#10B981', npcDark: '#0F172A' }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans">

    <div class="min-h-screen flex">
        
        <div class="hidden lg:block w-1/2 relative bg-npcDark">
            <img src="../../img/gambar_cetak.jpg" class="absolute inset-0 w-full h-full object-cover opacity-60">
            <div class="absolute inset-0 bg-gradient-to-t from-npcDark via-transparent to-transparent"></div>
            
            <div class="absolute bottom-0 left-0 p-16 text-white z-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="bg-npcGreen p-2 rounded-lg"><i class="fa-solid fa-print text-2xl"></i></div>
                    <span class="font-bold text-2xl tracking-tight">NPC System</span>
                </div>
                <h2 class="text-4xl font-extrabold mb-4 leading-tight">Kelola Dokumen Anda<br>Lebih Efisien.</h2>
                <p class="text-gray-300 text-lg">Bergabunglah dengan ribuan pelanggan yang mempercayakan kebutuhan cetak mereka kepada Nagoya Print & Copy.</p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-white">
            <div class="w-full max-w-md space-y-8">
                
                <div class="lg:hidden text-center mb-8">
                    <a href="../../index.php" class="inline-flex items-center gap-2">
                        <div class="bg-npcGreen text-white px-2 py-1 rounded"><i class="fa-solid fa-print"></i></div>
                        <span class="font-bold text-xl text-npcDark">NPC System</span>
                    </a>
                </div>

                <div class="text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-gray-900">Selamat Datang Kembali</h2>
                    <p class="mt-2 text-sm text-gray-600">Silakan masuk menggunakan akun yang terdaftar</p>
                </div>

                <?php if(isset($_GET['pesan'])): ?>
                    <?php if($_GET['pesan'] == "gagal"): ?>
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm flex items-start gap-3">
                            <i class="fa-solid fa-circle-exclamation text-red-500 mt-1"></i>
                            <div>
                                <h3 class="text-sm font-bold text-red-800">Login Gagal</h3>
                                <p class="text-xs text-red-600">Username atau password yang Anda masukkan salah.</p>
                            </div>
                        </div>
                    <?php elseif($_GET['pesan'] == "registered"): ?>
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm flex items-start gap-3">
                            <i class="fa-solid fa-check-circle text-green-500 mt-1"></i>
                            <div>
                                <h3 class="text-sm font-bold text-green-800">Registrasi Berhasil</h3>
                                <p class="text-xs text-green-600">Silakan login dengan akun baru Anda.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <form class="mt-8 space-y-6" action="../../backend/login/cek_login.php" method="POST">
                    <div class="space-y-5">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-regular fa-user"></i>
                                </div>
                                <input id="username" name="username" type="text" required class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-npcGreen focus:border-transparent transition sm:text-sm" placeholder="Masukkan username">
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                                <a href="../lupa_password/lupa_password.php" class="text-sm font-bold text-npcGreen hover:text-green-600">Lupa password?</a>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-lock"></i>
                                </div>
                                <input id="password" name="password" type="password" required class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-npcGreen focus:border-transparent transition sm:text-sm" placeholder="••••••••">
                            </div>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-npcDark hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-npcGreen transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            MASUK SEKARANG
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Belum punya akun? 
                        <a href="../register/register.php" class="font-bold text-npcGreen hover:text-green-600">Daftar Member Baru</a>
                    </p>
                </div>
                
                <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                    <a href="../../index.php" class="text-xs text-gray-400 hover:text-gray-600 flex items-center justify-center gap-1">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>