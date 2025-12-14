
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-8">
    <div class="container mx-auto px-4">
        
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                <span class="gradient-text">Profil Saya</span>
            </h1>
            <p class="text-slate-600 dark:text-slate-400">
                Kelola informasi profil dan pengaturan akun Anda
            </p>
        </div>

        
        <?php if ($success): ?>
            <div class="mb-6 px-6 py-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-r-xl">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-green-700 dark:text-green-300 font-medium"><?= htmlspecialchars($success['message']) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="mb-6 px-6 py-4 bg-rose-50 dark:bg-rose-950/30 border-l-4 border-rose-500 rounded-r-xl">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-rose-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-rose-700 dark:text-rose-300"><?= $error['message'] ?></div>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-1">
                <div class="glass-card rounded-2xl p-6 sticky top-6">
                    
                    <div class="text-center mb-6">
                        <div class="relative inline-block">
                            <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-primary-500 dark:border-primary-400 shadow-xl mx-auto">
                                <?php if (!empty($user['avatar']) && $user['avatar'] !== 'default.jpg'): ?>
                                    <img 
                                        src="<?= base_url('assets/uploads/profiles/' . $user['avatar']) ?>" 
                                        alt="<?= htmlspecialchars($user['name']) ?>"
                                        class="w-full h-full object-cover"
                                        onerror="this.src='<?= base_url('assets/images/default-avatar.png') ?>'"
                                    >
                                <?php else: ?>
                                    <div class="w-full h-full bg-gradient-primary flex items-center justify-center text-white text-4xl font-bold">
                                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="absolute bottom-0 right-0 bg-primary-600 rounded-full p-2 shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">
                            <?= htmlspecialchars($user['name']) ?>
                        </h2>
                        
                        
                        <?php if ($user['role'] === 'admin'): ?>
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-100 dark:bg-purple-950/50 text-purple-700 dark:text-purple-400 rounded-full text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Administrator
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary-100 dark:bg-primary-950/50 text-primary-700 dark:text-primary-400 rounded-full text-sm font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Pengguna
                            </span>
                        <?php endif; ?>
                    </div>

                    
                    <div class="space-y-4 mb-6">
                        
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">
                                NOMOR IDENTITAS (NPM/NIP)
                            </label>
                            <div class="px-4 py-3 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400 font-mono">
                                <?= htmlspecialchars($user['identity_number']) ?>
                            </div>
                        </div>

                        
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">
                                EMAIL
                            </label>
                            <div class="px-4 py-3 bg-slate-100 dark:bg-slate-800 rounded-lg text-slate-600 dark:text-slate-400">
                                <?= htmlspecialchars($user['email']) ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="border-t border-slate-200 dark:border-slate-700 pt-6">
                        <div class="grid grid-cols-2 gap-4">
                            
                            <div class="text-center">
                                <div class="text-3xl font-bold gradient-text mb-1">
                                    <?= $stats['total_items'] ?>
                                </div>
                                <div class="text-xs text-slate-600 dark:text-slate-400 font-medium">
                                    Total Laporan
                                </div>
                            </div>

                            
                            <div class="text-center">
                                <div class="text-3xl font-bold text-amber-600 dark:text-amber-400 mb-1">
                                    <?= $stats['total_claims'] ?>
                                </div>
                                <div class="text-xs text-slate-600 dark:text-slate-400 font-medium">
                                    Total Klaim
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="lg:col-span-2">
                <form action="<?= base_url('index.php?page=profile&action=update') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                    
                    <div class="glass-card rounded-2xl p-6">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Informasi Dasar
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="text" 
                                        id="name" 
                                        name="name" 
                                        required
                                        value="<?= htmlspecialchars($user['name']) ?>"
                                        class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                                        placeholder="Masukkan nama lengkap"
                                    >
                                </div>
                            </div>

                            
                            <div class="md:col-span-2">
                                <label for="phone" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    Nomor Telepon
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="tel" 
                                        id="phone" 
                                        name="phone" 
                                        value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                                        pattern="[0-9]{10,15}"
                                        class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                                        placeholder="08123456789"
                                    >
                                </div>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    Format: 10-15 digit angka
                                </p>
                            </div>

                            
                            <div class="md:col-span-2">
                                <label for="avatar" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    Foto Profil
                                </label>
                                <input 
                                    type="file" 
                                    id="avatar" 
                                    name="avatar" 
                                    accept="image/jpeg,image/png,image/gif"
                                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-600 file:text-white hover:file:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                                >
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    Format: JPG, PNG, GIF. Maksimal 5MB
                                </p>
                            </div>
                        </div>
                    </div>

                    
                    <div class="glass-card rounded-2xl p-6">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
                            <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Ubah Password
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                            Kosongkan jika tidak ingin mengubah password
                        </p>

                        <div class="space-y-4">
                            
                            <div>
                                <label for="old_password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    Password Lama
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="password" 
                                        id="old_password" 
                                        name="old_password" 
                                        class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                                        placeholder="Masukkan password lama"
                                    >
                                </div>
                            </div>

                            
                            <div>
                                <label for="new_password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    Password Baru
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="password" 
                                        id="new_password" 
                                        name="new_password" 
                                        minlength="8"
                                        class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                                        placeholder="Minimal 8 karakter"
                                    >
                                </div>
                            </div>

                            
                            <div>
                                <label for="confirm_password" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                    Konfirmasi Password Baru
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="password" 
                                        id="confirm_password" 
                                        name="confirm_password" 
                                        class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                                        placeholder="Ulangi password baru"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="flex justify-end gap-4">
                        <a 
                            href="<?= base_url('index.php') ?>"
                            class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-semibold hover:bg-slate-300 dark:hover:bg-slate-600 transition-all"
                        >
                            Batal
                        </a>
                        <button 
                            type="submit"
                            class="px-8 py-3 gradient-primary text-white rounded-xl font-semibold shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 transition-all flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
