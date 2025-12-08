<nav class="glass sticky top-0 z-40 border-b border-slate-200/50 dark:border-slate-700/50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <a href="<?= base_url('index.php') ?>" class="flex items-center gap-3 group">
                <img src="<?= base_url('assets/images/iconlost&found.png') ?>" alt="Logo" class="w-10 h-10 rounded-xl shadow-lg shadow-primary-500/20 group-hover:shadow-primary-500/40 group-hover:scale-105 transition-all">
                <div>
                    <span class="font-bold text-lg text-slate-900 dark:text-white">myUnila</span>
                    <span class="font-semibold text-lg gradient-text ml-1">Lost & Found</span>
                </div>
            </a>
            
            <div class="hidden md:flex items-center gap-1">
                <a href="<?= base_url('index.php') ?>" class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary-600 dark:hover:text-primary-400 transition-all font-medium text-sm">
                    Beranda
                </a>
                <a href="<?= base_url('index.php?page=items&type=lost') ?>" class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary-600 dark:hover:text-primary-400 transition-all font-medium text-sm">
                    Barang Hilang
                </a>
                <a href="<?= base_url('index.php?page=items&type=found') ?>" class="px-4 py-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary-600 dark:hover:text-primary-400 transition-all font-medium text-sm">
                    Barang Temuan
                </a>
                
                <?php if (isLoggedIn()): ?>
                    <?php
                    require_once __DIR__ . '/../../models/Notification.php';
                    $notifModel = new Notification();
                    $unread_count = $notifModel->countUnread((int)$_SESSION['user']['id']);
                    ?>
                    <a href="<?= base_url('index.php?page=notifications') ?>" class="relative p-2 ml-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-primary-600 dark:hover:text-primary-400 transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <?php if ($unread_count > 0): ?>
                            <span class="absolute top-1 right-1 w-5 h-5 bg-gradient-to-r from-rose-500 to-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center shadow-lg shadow-rose-500/50 animate-pulse">
                                <?= $unread_count > 9 ? '9+' : $unread_count ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    
                    <div class="relative group ml-2">
                        <button class="flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                            <div class="w-8 h-8 gradient-primary rounded-lg flex items-center justify-center">
                                <span class="text-white font-semibold text-sm"><?= strtoupper(substr(currentUser()['name'], 0, 1)) ?></span>
                            </div>
                            <span class="font-medium text-sm text-slate-700 dark:text-slate-200"><?= currentUser()['name'] ?></span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 py-2 hidden group-hover:block">
                            <a href="<?= base_url('index.php?page=profile') ?>" class="flex items-center gap-3 px-4 py-2.5 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profil Saya
                            </a>
                            <a href="<?= base_url('index.php?page=items&action=my') ?>" class="flex items-center gap-3 px-4 py-2.5 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Laporan Saya
                            </a>
                            <?php if (isAdmin()): ?>
                                <a href="<?= base_url('index.php?page=admin') ?>" class="flex items-center gap-3 px-4 py-2.5 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    Dashboard Admin
                                </a>
                            <?php endif; ?>
                            <hr class="my-2 border-slate-200 dark:border-slate-700">
                            <a href="<?= base_url('index.php?page=auth&action=logout') ?>" class="flex items-center gap-3 px-4 py-2.5 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= base_url('index.php?page=auth&action=login') ?>" class="px-4 py-2 text-slate-600 dark:text-slate-300 hover:text-primary-600 dark:hover:text-primary-400 transition font-medium text-sm ml-2">
                        Login
                    </a>
                    <a href="<?= base_url('index.php?page=auth&action=register') ?>" class="px-5 py-2.5 gradient-primary text-white rounded-xl hover:shadow-lg hover:shadow-primary-500/30 hover:-translate-y-0.5 transition-all font-semibold text-sm">
                        Daftar
                    </a>
                <?php endif; ?>
            </div>
            
            <button id="mobile-menu-btn" class="md:hidden p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                <svg class="w-6 h-6 text-slate-700 dark:text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
        
        <div id="mobile-menu" class="md:hidden hidden pb-4 border-t border-slate-200 dark:border-slate-700 mt-2 pt-4">
            <div class="flex flex-col gap-1">
                <a href="<?= base_url('index.php') ?>" class="px-4 py-2.5 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">Beranda</a>
                <a href="<?= base_url('index.php?page=items&type=lost') ?>" class="px-4 py-2.5 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">Barang Hilang</a>
                <a href="<?= base_url('index.php?page=items&type=found') ?>" class="px-4 py-2.5 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">Barang Temuan</a>
                
                <?php if (isLoggedIn()): ?>
                    <a href="<?= base_url('index.php?page=notifications') ?>" class="px-4 py-2.5 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium flex items-center justify-between">
                        <span>Notifikasi</span>
                        <?php if (isset($unread_count) && $unread_count > 0): ?>
                            <span class="ml-2 px-2 py-0.5 bg-rose-500 text-white text-xs font-bold rounded-full">
                                <?= $unread_count > 9 ? '9+' : $unread_count ?>
                            </span>
                        <?php endif; ?>
                    </a>
                    <hr class="my-2 border-slate-200 dark:border-slate-700">
                    <a href="<?= base_url('index.php?page=profile') ?>" class="px-4 py-2.5 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">Profil</a>
                    <a href="<?= base_url('index.php?page=auth&action=logout') ?>" class="px-4 py-2.5 rounded-xl text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition font-medium">Logout</a>
                <?php else: ?>
                    <hr class="my-2 border-slate-200 dark:border-slate-700">
                    <a href="<?= base_url('index.php?page=auth&action=login') ?>" class="px-4 py-2.5 rounded-xl text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition font-medium">Login</a>
                    <a href="<?= base_url('index.php?page=auth&action=register') ?>" class="px-4 py-2.5 gradient-primary text-white rounded-xl text-center font-semibold">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
document.getElementById('mobile-menu-btn').addEventListener('click', function() {
    document.getElementById('mobile-menu').classList.toggle('hidden');
});
</script>