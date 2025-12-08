<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Sistem Manajemen Kehilangan dan Penemuan Barang Universitas Lampung">
    <title><?= $pageTitle ?? 'myUnila Lost & Found' ?></title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="<?= base_url('favicon.svg') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/iconlost&found.png') ?>">
    <link rel="apple-touch-icon" href="<?= base_url('assets/images/iconlost&found.png') ?>">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#ecfeff',
                            100: '#cffafe',
                            200: '#a5f3fc',
                            300: '#67e8f9',
                            400: '#22d3ee',
                            500: '#06b6d4',
                            600: '#0891b2',
                            700: '#0e7490',
                            800: '#155e75',
                            900: '#164e63',
                            950: '#083344',
                        }
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'glow': 'glow 2s ease-in-out infinite alternate',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        glow: {
                            '0%': { boxShadow: '0 0 20px rgba(6, 182, 212, 0.3)' },
                            '100%': { boxShadow: '0 0 30px rgba(6, 182, 212, 0.6)' },
                        }
                    }
                }
            }
        }
    </script>
    
    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (theme === 'dark' || (!theme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        .dark .glass { background: rgba(30, 41, 59, 0.8); }
        .glass-card { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); }
        .dark .glass-card { background: rgba(51, 65, 85, 0.5); border: 1px solid rgba(255, 255, 255, 0.1); }
        .gradient-text { background: linear-gradient(135deg, #06b6d4 0%, #0ea5e9 50%, #3b82f6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; display: inline-block; padding-bottom: 0.1em; }
        .gradient-primary { background: linear-gradient(135deg, #0891b2 0%, #06b6d4 50%, #22d3ee 100%); }
        .gradient-mesh { background: radial-gradient(at 40% 20%, rgba(6, 182, 212, 0.15) 0px, transparent 50%), radial-gradient(at 80% 0%, rgba(14, 165, 233, 0.1) 0px, transparent 50%), radial-gradient(at 0% 50%, rgba(6, 182, 212, 0.1) 0px, transparent 50%); }
        .dark .gradient-mesh { background: radial-gradient(at 40% 20%, rgba(6, 182, 212, 0.2) 0px, transparent 50%), radial-gradient(at 80% 0%, rgba(14, 165, 233, 0.15) 0px, transparent 50%); }
        .bento-shadow { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 20px 40px rgba(0, 0, 0, 0.05); }
        .dark .bento-shadow { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2), 0 20px 40px rgba(0, 0, 0, 0.3); }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 min-h-screen flex flex-col antialiased transition-colors duration-300">
    
    <?php require_once __DIR__ . '/navbar.php'; ?>
    
    <?php if ($flash = flash('message')): ?>
        <div class="container mx-auto px-4 mt-6">
            <div class="<?= $flash['type'] === 'success' 
                ? 'bg-emerald-50 dark:bg-emerald-900/30 border-l-4 border-emerald-500 text-emerald-800 dark:text-emerald-200' 
                : 'bg-red-50 dark:bg-red-900/30 border-l-4 border-red-500 text-red-800 dark:text-red-200' 
            ?> p-4 rounded-xl flex items-start justify-between" role="alert">
                <div class="flex items-center gap-3">
                    <?php if ($flash['type'] === 'success'): ?>
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    <?php else: ?>
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    <?php endif; ?>
                    <span class="font-medium"><?= $flash['message'] ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-current opacity-70 hover:opacity-100 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    <?php endif; ?>
    
    <main class="flex-grow">
        <?= $content ?? '' ?>
    </main>
    
    <footer class="bg-white dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-700/50 mt-auto">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="<?= base_url('assets/images/iconlost&found.png') ?>" alt="Logo" class="w-12 h-12 rounded-xl shadow-lg">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900 dark:text-white">myUnila Lost & Found</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Universitas Lampung</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed max-w-md">
                        Platform digital untuk membantu civitas akademika Universitas Lampung dalam melaporkan dan menemukan barang hilang.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-semibold text-slate-900 dark:text-white mb-4">Menu</h4>
                    <ul class="space-y-2 text-sm">
                        <li><a href="<?= base_url('index.php') ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition">Beranda</a></li>
                        <li><a href="<?= base_url('index.php?page=items&type=lost') ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition">Barang Hilang</a></li>
                        <li><a href="<?= base_url('index.php?page=items&type=found') ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition">Barang Temuan</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-semibold text-slate-900 dark:text-white mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-400">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            lostfound@unila.ac.id
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            Universitas Lampung
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-200 dark:border-slate-700/50 mt-8 pt-8 text-center">
                <p class="text-sm text-slate-500 dark:text-slate-400">&copy; <?= date('Y') ?> myUnila Lost & Found — Kelompok 20. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <button id="theme-toggle" class="fixed bottom-6 right-6 z-50 w-14 h-14 rounded-2xl gradient-primary text-white shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 hover:scale-105 active:scale-95 transition-all duration-300 flex items-center justify-center group">
        <svg id="theme-icon-light" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <svg id="theme-icon-dark" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
        </svg>
        <span class="absolute -top-10 right-0 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-xs px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap font-medium">Toggle Theme</span>
    </button>
    
    <script>
        const themeToggle = document.getElementById('theme-toggle');
        const iconLight = document.getElementById('theme-icon-light');
        const iconDark = document.getElementById('theme-icon-dark');
        
        function updateIcon() {
            const isDark = document.documentElement.classList.contains('dark');
            iconLight.classList.toggle('hidden', !isDark);
            iconDark.classList.toggle('hidden', isDark);
        }
        updateIcon();
        
        themeToggle.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            updateIcon();
        });
        
        setTimeout(() => {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
