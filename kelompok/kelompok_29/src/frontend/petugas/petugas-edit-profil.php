<?php
$departments = [
    "Dinas Pekerjaan Umum",
    "Dinas Perhubungan",
    "Dinas Lingkungan Hidup",
    "Dinas Tata Kota",
    "Dinas Kebersihan"
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .input-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
        .max-w-4xl { max-width: 896px; }
    </style>
</head>
<body class="bg-gray-50">
    <main class="py-8">
        <div class="container max-w-4xl mx-auto p-4">
            
            <header class="page-header flex items-center gap-4 mb-6 sticky top-0 z-10 bg-gray-50 pt-4">
                <a href="petugas-task-list.php" class="text-xl text-gray-700 hover:text-blue-600 font-semibold">&larr;</a>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Edit Profile Petugas</h1>
                    <p class="text-gray-600 text-sm">Perbarui informasi akun petugas</p>
                </div>
            </header>
            
            <div id="profilAlert" class="hidden mb-4 p-3 rounded-lg text-sm"></div>
            <div id="profilLoading" class="mb-4 bg-white rounded-xl border border-gray-200 p-4 text-gray-500 text-sm">Memuat data profil...</div>

            <form id="formEditProfilePetugas" class="space-y-4">
                
                <section class="bg-white rounded-xl p-6 border border-gray-200">
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 text-green-600 text-4xl overflow-hidden relative">
                            <img id="fotoPreview" src="" alt="Foto Profil" class="w-full h-full object-cover hidden" />
                            <i id="fotoIconPlaceholder" class="material-icons">person</i>
                        </div>
                        <div>
                            <h3 id="profilNamaText" class="text-lg font-semibold text-gray-900">Petugas Lapangan</h3>
                            <p id="profilDepartemenText" class="text-gray-600">-</p>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded-xl p-6 border border-gray-200 space-y-4">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Informasi Pribadi</h3>
                    
                    <div>
                        <label for="nama" class="block text-gray-700 mb-2 text-sm">Nama Lengkap <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="material-icons input-icon">person</i>
                            <input type="text" id="nama" name="full_name" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-gray-700 mb-2 text-sm">Email <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="material-icons input-icon">mail</i>
                            <input type="email" id="email" name="email" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                        </div>
                    </div>
                    
                    <div>
                        <label for="telepon" class="block text-gray-700 mb-2 text-sm">No. Telepon <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="material-icons input-icon">phone</i>
                            <input type="tel" id="telepon" name="phone" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                        </div>
                    </div>

                    <div>
                        <label for="fotoProfilInput" class="block text-gray-700 mb-2 text-sm">Foto Profil</label>
                        <input type="file" id="fotoProfilInput" accept="image/*" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <p class="text-xs text-gray-500 mt-2">Format JPG/PNG maksimal 5MB.</p>
                    </div>
                </section>

                <section class="bg-white rounded-xl p-6 border border-gray-200 space-y-4">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Informasi Kepegawaian</h3>
                    
                    <div>
                        <label for="employeeId" class="block text-gray-700 mb-2 text-sm">Employee ID</label>
                        <div class="relative">
                            <i class="material-icons input-icon">work</i>
                            <input type="text" id="employeeId" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg bg-gray-100" disabled>
                        </div>
                        <p class="text-gray-500 mt-1 text-xs">Employee ID tidak dapat diubah</p>
                    </div>

                    <div>
                        <label for="departemen" class="block text-gray-700 mb-2 text-sm">Departemen <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="material-icons input-icon">apartment</i>
                            <select id="departemen" name="department" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 appearance-none" required>
                                <option value="">Pilih Departemen</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo htmlspecialchars($dept); ?>">
                                        <?php echo htmlspecialchars($dept); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <h3 class="text-xl font-semibold mt-6 pt-4 border-t text-gray-800">Informasi Akun</h3>
                    <div class="space-y-2 text-gray-600 text-sm">
                        <p>User ID: <span id="profilUserId" class="text-gray-900 font-semibold">-</span></p>
                        <p>Role: <span id="profilRole" class="text-green-600 font-semibold">-</span></p>
                        <p>Tanggal Bergabung: <span id="profilJoinDate" class="text-gray-900 font-semibold">-</span></p>
                    </div>
                </section>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="window.location.href='petugas-task-list.php'" class="flex-1 bg-white border border-gray-300 text-gray-700 py-4 rounded-lg hover:bg-gray-50 font-semibold">
                        Batal
                    </button>
                    <button id="btnSimpanProfil" type="submit" class="flex-1 bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 font-semibold">
                        <i class="material-icons text-xl">save</i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </main>
    <script src="js/petugas-auth-guard.js"></script>
    <script>
        PetugasAuth.requireOfficer();
    </script>
    <script src="js/petugas-edit-profil.js"></script>
</body>
</html>
