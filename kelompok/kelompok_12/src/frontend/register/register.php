<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - NPC System</title>
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
            <img src="../../img/gambar_dokumen.jpg" class="absolute inset-0 w-full h-full object-cover opacity-50">
            <div class="absolute inset-0 bg-gradient-to-t from-npcDark via-transparent to-transparent"></div>
            
            <div class="absolute bottom-0 left-0 p-16 text-white z-10">
                <h2 class="text-4xl font-extrabold mb-4 leading-tight">Mulai Perjalanan<br>Digital Anda.</h2>
                <p class="text-gray-300 text-lg">Buat akun untuk melacak pesanan, menyimpan riwayat transaksi, dan mendapatkan penawaran eksklusif.</p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-12 bg-white overflow-y-auto">
            <div class="w-full max-w-md space-y-6">
                
                <div class="text-center lg:text-left">
                    <h2 class="text-3xl font-bold text-gray-900">Buat Akun Baru</h2>
                    <p class="mt-2 text-sm text-gray-600">Lengkapi data diri Anda untuk mendaftar</p>
                </div>

                <form class="mt-8 space-y-4" action="../../backend/register/proses_register.php" method="POST">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input name="full_name" type="text" required class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-npcGreen focus:border-transparent outline-none transition" placeholder="Contoh: Budi Santoso">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input name="email" type="email" required class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-npcGreen outline-none transition" placeholder="budi@email.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp</label>
                            <input name="phone" type="text" required class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-npcGreen outline-none transition" placeholder="0812...">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input name="username" type="text" required class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-npcGreen outline-none transition" placeholder="Buat username unik">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input name="password" type="password" required class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-npcGreen outline-none transition" placeholder="Minimal 6 karakter">
                    </div>

                    <input type="hidden" name="role" value="customer">

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 text-npcGreen border-gray-300 rounded focus:ring-npcGreen">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-medium text-gray-700">Saya menyetujui <a href="../syarat/terms.php" class="text-npcGreen hover:underline">Syarat & Ketentuan</a></label>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 px-4 bg-npcGreen text-white font-bold rounded-lg shadow-lg hover:bg-green-600 transition transform hover:-translate-y-0.5">
                        DAFTAR SEKARANG
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun? 
                        <a href="../login/login.php" class="font-bold text-npcDark hover:underline">Masuk disini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>