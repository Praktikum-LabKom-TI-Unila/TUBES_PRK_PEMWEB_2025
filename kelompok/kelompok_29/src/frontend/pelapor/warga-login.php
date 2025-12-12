<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Warga</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .login-page-wrapper { min-height: 100vh; background-color: #f4f6f8; }
        .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
    </style>
</head>
<body>
    <div class="login-page-wrapper flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4 text-white text-3xl">
                    <i class="material-icons">person</i>
                </div>
                <h1 class="text-xl font-semibold mb-2 text-gray-800">Sistem Pengaduan Infrastruktur</h1>
                <p class="text-gray-600">Portal Warga</p>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200">
                <h2 class="text-xl font-semibold mb-6 text-gray-800">Masuk ke Akun Anda</h2>
                
                <form id="wargaLoginForm" novalidate>
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2">Email</label>
                        <div class="relative">
                            <i class="material-icons input-icon">mail</i>
                            <input type="email" id="email" name="email" placeholder="nama@email.com" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required/>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <i class="material-icons input-icon">lock</i>
                            <input type="password" id="password" name="password" placeholder="••••••••" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required/>
                        </div>
                    </div>

                    <button id="loginButton" type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold mb-4">
                        Masuk
                    </button>
                    <p id="loginFeedback" class="text-center text-sm text-red-600 min-h-[1.5rem]"></p>

                    <div class="text-center">
                      <button type="button" onclick="window.location.href='warga-register.php'" class="text-blue-600 hover:underline font-medium">
                        Belum punya akun? Daftar di sini
                      </button>
                    </div>
                </form>
            </div>

            <p class="text-center text-gray-500 mt-6 text-sm">
                Layanan Pengaduan Infrastruktur Publik
            </p>
        </div>
    </div>
    <script src="js/warga-auth.js"></script>
    <script src="js/warga-login.js"></script>
</body>
</html>
