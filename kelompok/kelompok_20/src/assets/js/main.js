// Consolidated site JS moved from inline scripts
(function () {
    'use strict';

    // Global functions (exposed on window)
    function showSection(sectionId) {
        const sections = ['dashboard', 'items', 'users', 'master'];
        sections.forEach(id => {
            const el = document.getElementById('section-' + id);
            const nav = document.getElementById('nav-' + id);
            if (el) el.classList.add('hidden');
            if (nav) {
                nav.classList.remove('active-nav', 'bg-gray-800');
                nav.classList.remove('text-cyan-400');
            }
        });

        const target = document.getElementById('section-' + sectionId);
        if (target) target.classList.remove('hidden');
        const nav = document.getElementById('nav-' + sectionId);
        if (nav) nav.classList.add('active-nav', 'bg-gray-800');

        const titles = {
            'dashboard': 'Dashboard Overview',
            'items': 'Kelola Postingan Barang',
            'users': 'Manajemen Pengguna',
            'master': 'Master Data'
        };
        const pageTitle = document.getElementById('page-title');
        if (pageTitle && titles[sectionId]) pageTitle.innerText = titles[sectionId];
    }

    function deleteItem(button) {
        if (!button) return;
        if (confirm('Hapus postingan ini?')) {
            const row = button.closest('tr');
            if (!row) return;
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 500);
        }
    }

    function toggleBan(button, badgeId) {
        const badge = document.getElementById(badgeId);
        if (!button || !badge) return;
        const isBanning = button.innerHTML.includes('Ban');

        if (isBanning) {
            if (confirm('Yakin ingin memblokir user ini?')) {
                badge.className = 'px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-600';
                badge.innerText = 'Banned';

                button.className = 'text-green-600 hover:text-white hover:bg-green-600 border border-green-200 px-3 py-1.5 rounded-md transition text-xs font-medium shadow-sm w-24';
                button.innerHTML = '<i class="fa-solid fa-check mr-1"></i> Unban';
            }
        } else {
            if (confirm('Aktifkan kembali user ini?')) {
                badge.className = 'px-2.5 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700';
                badge.innerText = 'Active';

                button.className = 'text-amber-600 hover:text-white hover:bg-amber-500 border border-amber-200 px-3 py-1.5 rounded-md transition text-xs font-medium shadow-sm w-24';
                button.innerHTML = '<i class="fa-solid fa-ban mr-1"></i> Ban User';
            }
        }
    }

    // Expose functions
    window.showSection = showSection;
    window.deleteItem = deleteItem;
    window.toggleBan = toggleBan;

    // DOM ready behaviors
    document.addEventListener('DOMContentLoaded', function () {
        // Mobile menu toggle (navbar)
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', function () {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Theme toggle (main layout)
        const themeToggle = document.getElementById('theme-toggle');
        const iconLight = document.getElementById('theme-icon-light');
        const iconDark = document.getElementById('theme-icon-dark');

        function updateIcon() {
            if (!iconLight || !iconDark) return;
            const isDark = document.documentElement.classList.contains('dark');
            iconLight.classList.toggle('hidden', !isDark);
            iconDark.classList.toggle('hidden', isDark);
        }
        updateIcon();

        if (themeToggle) {
            themeToggle.addEventListener('click', function () {
                const isDark = document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateIcon();
            });
        }

        // Auto hide flash alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    });

})();
