<?php 
// Asumsi: helper flash() dan base_url() tersedia
$flash = flash('message'); 
$is_success = $flash && $flash['type'] === 'success';

// Ambil nilai email dari session/flash jika ada (untuk mengisi ulang form saat ada error)
$email_value = $_SESSION['old_input']['email'] ?? ''; 
if (isset($_SESSION['old_input'])) {
    unset($_SESSION['old_input']); 
}
?>

<main class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-900/90">
    <div class="max-w-md w-full space-y-8">
        
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 gradient-primary rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-white">
                Memulihkan Akun
            </h2>
            <p class="mt-2 text-sm text-gray-400">
                Langkah 1: Kirim Link Verifikasi
            </p>
        </div>

        
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-2xl p-8 border border-gray-700/50">
            
            <?php if ($is_success): ?>
                <div class="text-center space-y-6 py-4">
                    <svg class="w-20 h-20 mx-auto text-green-500 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <h3 class="text-2xl font-bold text-white">Token Verifikasi Terkirim!</h3>
                    <p class="text-gray-400">
                        Kami telah mengirimkan **link reset password** yang berisi token unik ke email Anda. 
                        Silakan cek kotak masuk **Gmail** Anda (atau Mailpit/Mailhog) untuk melanjutkan proses reset.
                    </p>
                    
                    <a href="<?= base_url('index.php?page=auth&action=login') ?>" class="inline-block px-6 py-3 text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 transition shadow-md shadow-green-500/30">
                        Kembali ke Login
                    </a>
                </div>
            
            <?php else: ?>
                <?php if ($flash && $flash['type'] === 'error'): ?>
                    <div class="p-3 mb-4 rounded-lg text-sm bg-red-800 text-red-200">
                        ⚠️ <?= $flash['message'] ?>
                    </div>
                <?php endif; ?>

                <form class="space-y-6" action="<?= base_url('index.php?page=auth&action=sendResetLink') ?>" method="POST">
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                autocomplete="email" 
                                required 
                                value="<?= htmlspecialchars($email_value) ?>"
                                class="block w-full pl-10 pr-3 py-3 border border-gray-600 rounded-lg bg-slate-900 text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200"
                                placeholder="Masukkan email terdaftar Anda"
                            >
                        </div>
                    </div>
                    
                    <div>
                        <button 
                            type="submit" 
                            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 shadow-md shadow-primary-500/30"
                        >
                            Kirim Link Verifikasi
                        </button>
                    </div>
                </form>
            <?php endif; ?>

            <div class="text-center mt-6">
                <p class="text-sm text-gray-400">
                    <a href="<?= base_url('index.php?page=auth&action=login') ?>" class="font-medium text-primary-400 hover:text-primary-300 transition-colors">
                        ← Kembali ke Halaman Login
                    </a>
                </p>
            </div>
        </div>
    </div>
</main>