<?php 
$email = $data['email'] ?? '';
$token = $data['token'] ?? '';
$flash = flash('message');
?>

<main class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 gradient-mesh">
    <div class="max-w-md w-full space-y-8">
        
        <div class="text-center">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 gradient-primary rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v-2m0 0V8m0 4h.01" />
                    </svg>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                Atur Ulang Password
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                Masukkan password baru Anda untuk email **<?= htmlspecialchars($email) ?>**.
            </p>
        </div>

        
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl p-8 border border-gray-200/50 dark:border-gray-700/50">
            
            <?php if ($flash): ?>
                <div class="p-3 mb-4 rounded-lg text-sm <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                    <?= $flash['message'] ?>
                </div>
            <?php endif; ?>

            <form class="space-y-6" action="<?= base_url('index.php?page=auth&action=resetPassword') ?>" method="POST">
                
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Password Baru (min 6 karakter)
                    </label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        required 
                        class="block w-full py-3 px-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200"
                        placeholder="Password baru Anda"
                    >
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Konfirmasi Password Baru
                    </label>
                    <input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        required 
                        class="block w-full py-3 px-4 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-slate-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all duration-200"
                        placeholder="Ketik ulang password baru"
                    >
                </div>

                
                <div>
                    <button 
                        type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white gradient-primary hover:shadow-lg hover:shadow-primary-500/30 hover:scale-[1.02] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200"
                    >
                        Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>