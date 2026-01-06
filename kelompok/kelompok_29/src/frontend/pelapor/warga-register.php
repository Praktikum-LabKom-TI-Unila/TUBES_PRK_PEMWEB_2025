<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
        .input-icon-top { position: absolute; left: 12px; top: 12px; color: #9ca3af; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-4">
      <div class="w-full max-w-2xl">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
          
          <div class="bg-blue-600 text-white p-6">
            <button onclick="window.location.href='warga-login.php'" class="flex items-center gap-2 text-blue-100 hover:text-white mb-4 transition-colors text-sm">
              <i class="material-icons text-base">arrow_back</i>
              Kembali ke Login
            </button>
            <div class="flex items-center gap-3 mb-2">
              <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl">
                <i class="material-icons">person_add</i>
              </div>
              <div>
                <h1 class="text-xl font-semibold text-white">Daftar Akun Baru</h1>
                <p class="text-blue-100 text-sm">Sistem Pengaduan Infrastruktur Publik</p>
              </div>
            </div>
          </div>

          <form id="registerForm" class="p-6 space-y-4" novalidate>
            <div id="registerFeedback" class="hidden p-3 rounded-lg text-sm"></div>
            <div>
              <label class="block text-gray-700 mb-2 text-sm">Nama Lengkap <span class="text-red-500">*</span></label>
              <div class="relative">
                <i class="material-icons input-icon">person</i>
                <input type="text" name="name" placeholder="Masukkan nama lengkap" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required/>
              </div>
            </div>

            <div>
              <label class="block text-gray-700 mb-2 text-sm">Email <span class="text-red-500">*</span></label>
              <div class="relative">
                <i class="material-icons input-icon">mail</i>
                <input type="email" name="email" placeholder="nama@email.com" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required/>
              </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-700 mb-2 text-sm">No. Telepon <span class="text-red-500">*</span></label>
                <div class="relative">
                  <i class="material-icons input-icon">phone</i>
                  <input type="tel" name="phone" placeholder="081234567890" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required/>
                </div>
              </div>
              <div>
                <label class="block text-gray-700 mb-2 text-sm">NIK <span class="text-red-500">*</span></label>
                <div class="relative">
                  <i class="material-icons input-icon">credit_card</i>
                  <input type="text" name="nik" placeholder="16 digit NIK" maxlength="16" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required/>
                </div>
              </div>
            </div>

            <div>
              <label class="block text-gray-700 mb-2 text-sm">Alamat <span class="text-red-500">*</span></label>
              <div class="relative">
                <i class="material-icons input-icon-top">place</i>
                <textarea name="address" placeholder="Alamat lengkap" rows="3" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 resize-none" required></textarea>
              </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-gray-700 mb-2 text-sm">Password <span class="text-red-500">*</span></label>
                <div class="relative">
                  <i class="material-icons input-icon">lock</i>
                  <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required/>
                </div>
              </div>
              <div>
                <label class="block text-gray-700 mb-2 text-sm">Konfirmasi Password <span class="text-red-500">*</span></label>
                <div class="relative">
                  <i class="material-icons input-icon">lock</i>
                  <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Ulangi password" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required/>
                </div>
              </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-900">
              <p>Dengan mendaftar, Anda menyetujui untuk menggunakan sistem ini sesuai dengan ketentuan yang berlaku.</p>
            </div>

            <button id="registerButton" type="submit" class="w-full bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 font-semibold">
              <i class="material-icons text-xl">person_add</i> Daftar Sekarang
            </button>

            <p class="text-center text-gray-600 pt-2 text-sm">
              Sudah punya akun? 
              <button type="button" onclick="window.location.href='warga-login.php'" class="text-blue-600 hover:text-blue-700 font-medium">
                Login di sini
              </button>
            </p>
          </form>
        </div>
      </div>
    </div>
    <script src="js/warga-auth.js"></script>
    <script src="js/warga-register.js"></script>
</body>
</html>
