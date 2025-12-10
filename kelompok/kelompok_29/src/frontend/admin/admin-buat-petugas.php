<?php
// Departemen untuk opsi SELECT
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
    <title>Buat Akun Petugas</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <main class="py-8">
        <div class="container max-w-4xl mx-auto p-4">
            
            <header class="page-header flex items-center gap-4 mb-6">
                <a href="admin-dashboard.php" class="text-xl text-gray-700 hover:text-blue-600 font-semibold">&larr;</a>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Buat Akun Petugas Lapangan</h1>
                    <p class="text-gray-600 text-sm">Tambahkan petugas baru ke sistem</p>
                </div>
            </header>
            
            <form id="formPetugasBaru" class="space-y-4">
                
                <section class="card bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Informasi Pribadi</h3>
                    <div class="space-y-4">
                        
                        <div class="form-group">
                            <label for="nama" class="block text-gray-700 mb-2 text-sm">Nama Lengkap <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="material-icons absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">person</i>
                                <input type="text" id="nama" name="name" placeholder="Masukkan nama lengkap petugas" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="block text-gray-700 mb-2 text-sm">Email <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="material-icons absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">email</i>
                                <input type="email" id="email" name="email" placeholder="petugas@infra.go.id" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="telepon" class="block text-gray-700 mb-2 text-sm">No. Telepon <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="material-icons absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">phone</i>
                                <input type="tel" id="telepon" name="phone" placeholder="081234567890" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Informasi Kepegawaian</h3>
                    <div class="space-y-4">
                        
                        <div class="form-group">
                            <label for="employeeId" class="block text-gray-700 mb-2 text-sm">Employee ID <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="material-icons absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">work</i>
                                <input type="text" id="employeeId" name="employeeId" placeholder="EMP-2024-001" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="departemen" class="block text-gray-700 mb-2 text-sm">Departemen <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="material-icons absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">apartment</i>
                                <select id="departemen" name="department" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 appearance-none" required>
                                    <option value="">Pilih Departemen</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo htmlspecialchars($dept); ?>"><?php echo htmlspecialchars($dept); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </section>
                
                <section class="card bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-xl font-semibold mb-4 text-gray-800">Keamanan Akun</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        
                        <div class="form-group">
                            <label for="password" class="block text-gray-700 mb-2 text-sm">Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="material-icons absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">lock</i>
                                <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="konfirmasiPassword" class="block text-gray-700 mb-2 text-sm">Konfirmasi Password <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="material-icons absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">lock</i>
                                <input type="password" id="konfirmasiPassword" name="confirmPassword" placeholder="Ulangi password" class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" required>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-900">
                    Kredensial akun akan dikirimkan ke email petugas. Pastikan data yang dimasukkan sudah benar.
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="window.location.href='admin-kelola-petugas.php'" class="flex-1 bg-white border border-gray-300 text-gray-700 py-4 rounded-lg hover:bg-gray-50 font-semibold">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-4 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 font-semibold">
                        <i class="material-icons text-xl">person_add</i> Buat Akun Petugas
                    </button>
                </div>
            </form>
        </div>
    </main>
    <script src="js/admin-buat-petugas.js"></script>
</body>
</html>