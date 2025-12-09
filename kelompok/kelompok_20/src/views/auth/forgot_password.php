<?php
$error_message = flash('message', 'error');

$email = $email ?? $_GET['email'] ?? '';
$token = $token ?? $_GET['token'] ?? '';
?>

<div class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white dark:bg-slate-800 p-10 rounded-xl shadow-2xl border border-slate-200/50 dark:border-slate-700/50">
        
        <div class="text-center">
            <svg class="w-16 h-16 mx-auto text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <h2 class="mt-4 text-3xl font-extrabold text-slate-900 dark:text-white">
                Atur Ulang Password
            </h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                Memperbarui password untuk **<?= htmlspecialchars($email) ?>**
            </p>
        </div>

        <?php if ($error_message): ?>
            <div class="bg-rose-100 border border-rose-400 text-rose-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">⚠️ <?= htmlspecialchars($error_message) ?></span>
            </div>
        <?php endif; ?>

        <form 
            class="mt-8 space-y-6" 
            action="<?= base_url('index.php?page=auth&action=resetPassword') ?>" 
            method="POST"
        >
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="space-y-4">
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Password Baru</label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        required 
                        minlength="6"
                        placeholder="Minimal 6 karakter"
                        class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 placeholder-slate-500 text-slate-900 dark:text-white focus:outline-none focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm transition-all"
                    >
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Konfirmasi Password Baru</label>
                    <input 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        type="password" 
                        required 
                        minlength="6"
                        placeholder="Ulangi password baru"
                        class="appearance-none rounded-lg relative block w-full px-3 py-3 border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 placeholder-slate-500 text-slate-900 dark:text-white focus:outline-none focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm transition-all"
                    >
                </div>
            </div>

            <div>
                <button 
                    type="submit" 
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-bold rounded-lg text-white bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-all shadow-md shadow-cyan-500/30"
                >
                    Update Password
                </button>
            </div>
            
            <div class="text-center">
                <a href="<?= base_url('index.php?page=auth&action=login') ?>" class="text-sm font-medium text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300">
                    Batal dan Kembali
                </a>
            </div>
        </form>
    </div>
</div>