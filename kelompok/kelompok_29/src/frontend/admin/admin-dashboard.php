<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <script src="js/admin-auth-guard.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    
    <header class="bg-blue-600 text-white p-6">
        <div class="max-w-6xl mx-auto">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-200 mb-1 text-sm">Dashboard Admin</p>
                    <h1 class="text-2xl font-semibold">Manajemen Pengaduan</h1>
                </div>
                <div class="flex gap-2">
                    <!-- Profile button temporarily hidden -->
                    <button
                        type="button"
                        class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center hover:bg-blue-400 text-xl"
                        title="Logout"
                        data-admin-logout
                        data-default-text="Logout"
                        data-loading-text="Keluar..."
                    >
                        <i class="material-icons">logout</i>
                        <span class="sr-only">Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <main class="bg-gray-50 pb-8">
        <div class="max-w-6xl mx-auto p-4">
            
            <div class="grid md:grid-cols-4 gap-4 mb-6" id="stats-cards-container"></div>

            <div class="bg-white rounded-xl p-6 border border-gray-200 mb-6">
                <h2 class="text-xl font-semibold mb-4">Menu Utama</h2>
                <div class="grid md:grid-cols-3 gap-4" id="menu-utama-container">
                    <a href="admin-manajemen-tiket.php" class="p-6 border-2 border-gray-200 rounded-xl hover:border-blue-600 hover:bg-blue-50 transition-all text-left block">
                        <i class="material-icons text-blue-600 text-3xl mb-3">receipt_long</i>
                        <div class="text-gray-900 font-semibold mb-1">Kelola Tiket</div>
                        <p class="text-gray-600 text-sm">Manajemen pengaduan masyarakat</p>
                    </a>
                    <a href="admin-buat-petugas.php" class="p-6 border-2 border-gray-200 rounded-xl hover:border-blue-600 hover:bg-blue-50 transition-all text-left block">
                        <i class="material-icons text-blue-600 text-3xl mb-3">person_add</i>
                        <div class="text-gray-900 font-semibold mb-1">Buat Akun Petugas</div>
                        <p class="text-gray-600 text-sm">Tambahkan petugas lapangan baru</p>
                    </a>
                    <a href="admin-kelola-petugas.php" class="p-6 border-2 border-gray-200 rounded-xl hover:border-blue-600 hover:bg-blue-50 transition-all text-left block">
                        <i class="material-icons text-blue-600 text-3xl mb-3">group</i>
                        <div class="text-gray-900 font-semibold mb-1">Kelola Petugas</div>
                        <p class="text-gray-600 text-sm">Pantau kinerja petugas aktif</p>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 border border-gray-200">
                <h2 class="text-xl font-semibold mb-4">Aktivitas Terbaru</h2>
                <div class="space-y-4" id="aktivitas-terbaru-container"></div>
            </div>
        </div>
    </main>
    
    <script src="js/admin-logout.js"></script>
    <script>
        'use strict';

        (function () {
            const statsContainer = document.getElementById('stats-cards-container');
            const activitiesContainer = document.getElementById('aktivitas-terbaru-container');

            const STAT_CARD_META = [
                { key: 'total', label: 'Total', subtitle: 'Jumlah seluruh pengaduan', icon: 'description', accent: 'slate' },
                { key: 'baru', label: 'Baru', subtitle: 'Baru diajukan', icon: 'fiber_new', accent: 'blue' },
                { key: 'in_progress', label: 'Proses', subtitle: 'Sedang ditangani', icon: 'autorenew', accent: 'amber' },
                { key: 'finished', label: 'Selesai', subtitle: 'Tuntas diproses', icon: 'check_circle', accent: 'emerald' },
            ];

            const ACTIVITY_STATUS_META = {
                diajukan: { label: 'Diajukan', classes: 'bg-blue-100 text-blue-700' },
                diverifikasi_admin: { label: 'Diverifikasi', classes: 'bg-indigo-100 text-indigo-700' },
                ditugaskan_ke_petugas: { label: 'Ditugaskan', classes: 'bg-orange-100 text-orange-700' },
                dalam_proses: { label: 'Dalam Proses', classes: 'bg-amber-100 text-amber-700' },
                menunggu_validasi_admin: { label: 'Menunggu Validasi', classes: 'bg-purple-100 text-purple-700' },
                ditolak_admin: { label: 'Ditolak', classes: 'bg-rose-100 text-rose-700' },
                selesai: { label: 'Selesai', classes: 'bg-green-100 text-green-700' },
            };

            document.addEventListener('DOMContentLoaded', () => {
                const token = getAdminToken();
                if (!token) {
                    setStatsError('Sesi admin tidak ditemukan. Silakan login ulang.');
                    setActivitiesError('Sesi admin tidak ditemukan.');
                    return;
                }

                loadStats(token);
                loadActivities(token);
            });

            // --- BAGIAN DASHBOARD: Statistik ---
            async function loadStats(token) {
                setStatsLoading();
                try {
                    const data = await requestWithAuth('/api/admin/dashboard/stats', token);
                    renderStatsCards(data || {});
                } catch (error) {
                    console.error('[AdminDashboard] stats error', error);
                    setStatsError(error?.message || 'Gagal memuat statistik.');
                }
            }

            function renderStatsCards(data) {
                const formatter = new Intl.NumberFormat('id-ID');
                const accentClass = {
                    slate: { border: 'border-slate-200', text: 'text-slate-600', bg: 'bg-slate-100' },
                    blue: { border: 'border-blue-200', text: 'text-blue-600', bg: 'bg-blue-100' },
                    amber: { border: 'border-amber-200', text: 'text-amber-600', bg: 'bg-amber-100' },
                    emerald: { border: 'border-emerald-200', text: 'text-emerald-600', bg: 'bg-emerald-100' },
                };

                const cards = STAT_CARD_META.map((item) => {
                    const value = data?.[item.key] ?? 0;
                    const classes = accentClass[item.accent];
                    return `
                        <div class="bg-white rounded-xl p-6 border ${classes.border}">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 ${classes.bg} rounded-lg flex items-center justify-center ${classes.text} text-2xl">
                                    <i class="material-icons">${item.icon}</i>
                                </div>
                                <span class="${classes.text} text-sm font-semibold">${item.label}</span>
                            </div>
                            <div class="${classes.text} text-3xl font-bold mb-1">${formatter.format(value)}</div>
                            <p class="text-gray-600 text-sm">${item.subtitle}</p>
                        </div>
                    `;
                }).join('');

                statsContainer.innerHTML = cards;
            }

            function setStatsLoading() {
                statsContainer.innerHTML = '<div class="md:col-span-4 col-span-1 text-center text-gray-500 py-8">Memuat statistik...</div>';
            }

            function setStatsError(message) {
                statsContainer.innerHTML = `<div class="md:col-span-4 col-span-1 text-center text-red-600 bg-red-50 border border-red-200 rounded-lg py-6">${message}</div>`;
            }

            // --- BAGIAN DASHBOARD: Aktivitas Terbaru ---
            async function loadActivities(token) {
                setActivitiesLoading();
                try {
                    const data = await requestWithAuth('/api/admin/dashboard/recent-activities', token);
                    const records = data?.records || [];
                    if (!records.length) {
                        activitiesContainer.innerHTML = '<div class="text-center text-gray-500 py-6">Belum ada aktivitas terbaru.</div>';
                        return;
                    }
                    activitiesContainer.innerHTML = records.map(renderActivityItem).join('');
                } catch (error) {
                    console.error('[AdminDashboard] activities error', error);
                    setActivitiesError(error?.message || 'Gagal memuat aktivitas terbaru.');
                }
            }

            function renderActivityItem(item) {
                const reporterName = item?.reporter?.name || 'Anonym';
                const reporterEmail = item?.reporter?.email || '';
                const statusMeta = ACTIVITY_STATUS_META[item?.status] || { label: 'Status Tidak Dikenal', classes: 'bg-gray-100 text-gray-600' };
                const dateLabel = formatDateTime(item?.activity_time);
                const complaintId = item?.complaint_id ? `#${item.complaint_id}` : '#-';

                return `
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <div class="flex-1 min-w-0">
                            <p class="text-gray-900 font-semibold truncate">${item?.title || 'Tanpa judul'}</p>
                            <p class="text-gray-500 text-sm">${complaintId} • ${reporterName}${reporterEmail ? ` (${reporterEmail})` : ''}</p>
                            <p class="text-gray-400 text-xs mt-1">${dateLabel}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusMeta.classes}">${statusMeta.label}</span>
                    </div>
                `;
            }

            function setActivitiesLoading() {
                activitiesContainer.innerHTML = '<div class="text-center text-gray-500 py-6">Memuat aktivitas...</div>';
            }

            function setActivitiesError(message) {
                activitiesContainer.innerHTML = `<div class="text-center text-red-600 bg-red-50 border border-red-200 rounded-lg py-6">${message}</div>`;
            }

            // --- UTILITAS ---
            function getAdminToken() {
                return localStorage.getItem('sipinda_admin_token') || sessionStorage.getItem('sipinda_admin_token');
            }

            function resolveBackendBaseUrl() {
                const metaOverride = document.querySelector('meta[name="sipinda-backend-url"]');
                if (metaOverride?.content) {
                    return metaOverride.content.replace(/\/+$/, '');
                }

                const globalOverride = window.SIPINDA_BACKEND_URL || window.__SIPINDA_BACKEND_URL__;
                if (typeof globalOverride === 'string' && globalOverride.trim()) {
                    return globalOverride.trim().replace(/\/+$/, '');
                }

                const protocol = window.location.protocol === 'https:' ? 'https:' : 'http:';
                const hostname = window.location.hostname || 'localhost';
                const backendPort = hostname === 'localhost' ? 9090 : (window.location.port || 80);
                return `${protocol}//${hostname}:${backendPort}`;
            }

            async function requestWithAuth(endpoint, token) {
                const response = await fetch(`${resolveBackendBaseUrl()}${endpoint}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        Authorization: `Bearer ${token}`,
                    },
                    mode: 'cors',
                });

                const payload = await safeParseJson(response);

                if (!response.ok || payload?.status === 'error') {
                    throw new Error(buildApiErrorMessage(payload, response));
                }

                return payload?.data;
            }

            async function safeParseJson(response) {
                try {
                    const text = await response.text();
                    return text ? JSON.parse(text) : {};
                } catch (error) {
                    console.warn('[AdminDashboard] gagal parse JSON', error);
                    return {};
                }
            }

            function buildApiErrorMessage(payload, response) {
                const code = payload?.code || response?.status;
                const baseMessage = payload?.message || 'Permintaan gagal diproses.';
                const errors = Array.isArray(payload?.errors) ? payload.errors.map((err) => err?.message).filter(Boolean) : [];
                const detail = errors.length ? ` Detail: ${errors.join(', ')}` : '';
                return `${baseMessage} (kode ${code}).${detail}`;
            }

            function formatDateTime(value) {
                if (!value) return 'Waktu tidak diketahui';
                const date = new Date(value);
                if (Number.isNaN(date.getTime())) return 'Waktu tidak diketahui';
                return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(date);
            }
        })();
    </script>
</body>
</html>
