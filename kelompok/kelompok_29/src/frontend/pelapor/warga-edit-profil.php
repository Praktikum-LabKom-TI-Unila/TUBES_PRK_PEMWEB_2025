<?php
// Dummy data untuk simulasi Warga yang sedang login (berdasarkan structure di EditProfile.tsx)
$currentUser = [
    'id' => 'C001',
    'name' => 'Ahmad Wijaya',
    'email' => 'ahmad.wijaya@email.com',
    'phone' => '081234567890',
    'nik' => '3201012345678901',
    'address' => 'Jl. Mawar Indah No. 5, Jakarta Selatan',
    'role' => 'citizen',
    'createdAt' => '2024-01-15',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile Pelapor</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
        .input-icon-top { position: absolute; left: 12px; top: 12px; color: #9ca3af; }
        .max-w-4xl { max-width: 896px; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen bg-gray-50 pb-8">
        
        <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
            <div class="max-w-4xl mx-auto p-4 flex items-center gap-3">
              <a href="warga-dashboard.php" class="w-10 h-10 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-700 text-xl transition-colors">←</a>
              <div>
                <h1 class="text-xl font-semibold text-gray-900">Edit Profile</h1>
                <p class="text-gray-600 text-sm">Perbarui informasi akun Anda</p>
              </div>
            </div>
        </div>

        <div class="max-w-4xl mx-auto p-4">
            <form id="formEditProfileWarga" class="space-y-4">
                
                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <div class="flex items-center gap-4">
                      <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="material-icons text-4xl text-blue-600">person</i>
                      </div>
                      <div>
                        <h3 class="text-lg font-semibold text-gray-900">Foto Profile</h3>
                        <p class="text-gray-600 text-sm">JPG atau PNG, max 2MB</p>
                      </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-xl font-semibold mb-6 text-gray-800">Informasi Pribadi</h3>
                    
                    <div class="space-y-4">
                      
                      <div>
                        <label class="block text-gray-700 mb-2 text-sm">Nama Lengkap <span class="text-red-500">*</span></label>
                        <div class="relative">
                          <i class="material-icons input-icon">person</i>
                          <input type="text" name="name" value="<?php echo htmlspecialchars($currentUser['name']); ?>" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required/>
                        </div>
                      </div>

                      <div>
                        <label class="block text-gray-700 mb-2 text-sm">Email <span class="text-red-500">*</span></label>
                        <div class="relative">
                          <i class="material-icons input-icon">mail</i>
                          <input type="email" name="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required/>
                        </div>
                      </div>

                      <div class="grid md:grid-cols-2 gap-4">
                        <div>
                          <label class="block text-gray-700 mb-2 text-sm">No. Telepon <span class="text-red-500">*</span></label>
                          <div class="relative">
                            <i class="material-icons input-icon">phone</i>
                            <input type="tel" name="phone" value="<?php echo htmlspecialchars($currentUser['phone']); ?>" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required/>
                          </div>
                        </div>
                        <div>
                          <label class="block text-gray-700 mb-2 text-sm">NIK <span class="text-red-500">*</span></label>
                          <div class="relative">
                            <i class="material-icons input-icon">credit_card</i>
                            <input type="text" name="nik" value="<?php echo htmlspecialchars($currentUser['nik']); ?>" maxlength="16" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required/>
                          </div>
                        </div>
                      </div>

                      <div>
                        <label class="block text-gray-700 mb-2 text-sm">Alamat <span class="text-red-500">*</span></label>
                        <div class="relative">
                          <i class="material-icons input-icon-top">place</i>
                          <textarea name="address" rows="3" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 resize-none" required><?php echo htmlspecialchars($currentUser['address']); ?></textarea>
                        </div>
                      </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Informasi Akun</h3>
                    <div class="space-y-2 text-gray-600 text-sm">
                      <p>User ID: <span class="text-gray-900 font-medium"><?php echo htmlspecialchars($currentUser['id']); ?></span></p>
                      <p>Tanggal Bergabung: <span class="text-gray-900 font-medium"><?php echo htmlspecialchars($currentUser['createdAt']); ?></span></p>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="window.location.href='warga-dashboard.php'" class="flex-1 bg-white border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 font-semibold">
                        <i class="material-icons text-xl">save</i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="js/warga-edit-profil.js"></script>
</body>
</html>