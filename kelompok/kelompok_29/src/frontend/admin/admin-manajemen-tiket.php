<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Tiket</title>
    <script src="https://cdn.tailwindcss.com"></script> 
    <script src="js/admin-auth-guard.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .badge-kategori { font-size: 12px; }
        .data-table th, .data-table td { padding: 16px; }
    </style>
</head>
<body class="bg-gray-50">
    <main class="content-wrapper py-8">
        <div class="max-w-7xl mx-auto p-4">
            
            <header class="page-header flex items-center gap-4 mb-4 pt-4">
                <a href="admin-dashboard.php" class="text-xl text-gray-700 hover:text-blue-600 font-semibold">&larr;</a>
                <h1 class="text-2xl font-semibold text-gray-900 mr-auto">Manajemen Tiket</h1>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 bg-red-50 text-red-600 px-4 py-2 rounded-lg border border-red-100 text-sm font-semibold hover:bg-red-100"
                    data-admin-logout
                    data-default-text="Logout"
                    data-loading-text="Keluar..."
                >
                    <i class="material-icons text-base">logout</i>
                    <span>Logout</span>
                </button>
            </header>
            
            <section class="bg-white rounded-xl shadow-sm p-4 border border-gray-200 mb-4">
                <form id="ticketFilterForm" class="grid gap-3 md:grid-cols-4 mb-4">
                    <div class="md:col-span-2 relative">
                        <i class="material-icons absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400">search</i>
                        <label for="searchInput" class="sr-only">Kata kunci</label>
                        <input type="text" id="searchInput" name="search" placeholder="Cari tiket, pelapor, atau ID..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600" />
                    </div>
                    <div>
                        <label for="statusFilter" class="text-sm text-gray-600 mb-1 block">Status</label>
                        <select id="statusFilter" name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            <option value="all">Semua Status</option>
                            <option value="diajukan">Diajukan</option>
                            <option value="diverifikasi_admin">Diverifikasi Admin</option>
                            <option value="ditugaskan_ke_petugas">Ditugaskan ke Petugas</option>
                            <option value="dalam_proses">Dalam Proses</option>
                            <option value="menunggu_validasi_admin">Menunggu Validasi</option>
                            <option value="ditolak_admin">Ditolak Admin</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div>
                        <label for="limitFilter" class="text-sm text-gray-600 mb-1 block">Limit</label>
                        <select id="limitFilter" name="limit" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600">
                            <option value="5">5 data / halaman</option>
                            <option value="10" selected>10 data / halaman</option>
                            <option value="20">20 data / halaman</option>
                            <option value="50">50 data / halaman</option>
                        </select>
                    </div>
                </form>
                <p id="filterSummary" class="text-sm text-gray-500">Gunakan filter di atas untuk menyesuaikan data tiket.</p>
            </section>

            <section class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="data-table w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-gray-700 font-semibold text-sm uppercase">ID Tiket</th>
                                <th class="px-4 py-3 text-gray-700 font-semibold text-sm uppercase">Pelapor</th>
                                <th class="px-4 py-3 text-gray-700 font-semibold text-sm uppercase">Judul & Lokasi</th>
                                <th class="px-4 py-3 text-gray-700 font-semibold text-sm uppercase">Status</th>
                                <th class="px-4 py-3 text-gray-700 font-semibold text-sm uppercase">Tanggal Dibuat</th>
                                <th class="px-4 py-3 text-gray-700 font-semibold text-sm uppercase">Petugas</th>
                                <th class="px-4 py-3 text-center text-gray-700 font-semibold text-sm uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-body-pengaduan" class="divide-y divide-gray-100">
                            </tbody>
                    </table>
                </div>
            </section>

            <div id="ticketsMessage" class="hidden mt-4 p-4 rounded-lg border text-sm"></div>

            <div id="empty-state" class="hidden bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center mt-4">
                <i class="material-icons text-6xl text-gray-300 mb-4">search_off</i>
                <p class="text-gray-500">Tidak ada tiket yang sesuai filter</p>
            </div>
        </div>
    </main>

    <script src="js/admin-logout.js"></script>
    <script src="js/admin-select-officer.js"></script>
    <script>
        'use strict';

        (function () {
            const state = {
                token: '',
                filters: {
                    search: '',
                    status: 'all',
                    limit: 10,
                    page: 1,
                },
            };

            const elements = {
                form: document.getElementById('ticketFilterForm'),
                searchInput: document.getElementById('searchInput'),
                statusSelect: document.getElementById('statusFilter'),
                limitSelect: document.getElementById('limitFilter'),
                summary: document.getElementById('filterSummary'),
                messageBox: document.getElementById('ticketsMessage'),
                tableBody: document.getElementById('tabel-body-pengaduan'),
                emptyState: document.getElementById('empty-state'),
            };

            const STATUS_META = {
                diajukan: { label: 'Diajukan', classes: 'bg-blue-100 text-blue-700' },
                diverifikasi_admin: { label: 'Diverifikasi Admin', classes: 'bg-indigo-100 text-indigo-700' },
                ditugaskan_ke_petugas: { label: 'Ditugaskan', classes: 'bg-orange-100 text-orange-700' },
                dalam_proses: { label: 'Dalam Proses', classes: 'bg-amber-100 text-amber-700' },
                menunggu_validasi_admin: { label: 'Menunggu Validasi Admin', classes: 'bg-purple-100 text-purple-700' },
                ditolak_admin: { label: 'Ditolak Admin', classes: 'bg-rose-100 text-rose-700' },
                selesai: { label: 'Selesai', classes: 'bg-green-100 text-green-700' },
            };

            document.addEventListener('DOMContentLoaded', () => {
                state.token = getAdminToken();
                if (!state.token) {
                    showMessage('Sesi admin berakhir. Silakan login ulang.', 'error');
                    showTableError('Tidak bisa memuat data tanpa token.');
                    return;
                }

                bindFilterEvents();
                fetchTickets();
            });

            // --- EVENT HANDLER FILTER ---
            function bindFilterEvents() {
                elements.form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    syncFilters();
                    fetchTickets();
                });

                elements.statusSelect.addEventListener('change', () => {
                    syncFilters();
                    fetchTickets();
                });

                elements.limitSelect.addEventListener('change', () => {
                    syncFilters();
                    fetchTickets();
                });

                const debouncedSearch = debounce(() => {
                    state.filters.search = elements.searchInput.value.trim();
                    fetchTickets();
                }, 400);

                elements.searchInput.addEventListener('input', debouncedSearch);
            }

            function syncFilters() {
                state.filters.search = elements.searchInput.value.trim();
                state.filters.status = elements.statusSelect.value;
                state.filters.limit = Number(elements.limitSelect.value) || 10;
            }

            // --- FETCH DATA TIKET ---
            async function fetchTickets() {
                setTableLoading();
                showMessage('Memuat data tiket...', 'info');
                elements.emptyState.classList.add('hidden');

                const query = new URLSearchParams();
                query.set('page', state.filters.page);
                query.set('limit', state.filters.limit);
                if (state.filters.status && state.filters.status !== 'all') {
                    query.set('status', state.filters.status);
                }
                if (state.filters.search) {
                    query.set('search', state.filters.search);
                }

                try {
                    const data = await requestWithAuth(`/api/admin/tickets?${query.toString()}`, state.token);
                    const records = data?.records || [];
                    renderTickets(records);
                    updateSummary(data, query);
                    if (!records.length) {
                        elements.emptyState.classList.remove('hidden');
                        elements.tableBody.innerHTML = '<tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Tidak ada data tiket.</td></tr>';
                    } else {
                        elements.emptyState.classList.add('hidden');
                    }
                    showMessage('Data tiket berhasil diperbarui.', 'success');
                } catch (error) {
                    console.error('[AdminTickets] fetch error', error);
                    showTableError(error?.message || 'Gagal memuat tiket.');
                    showMessage(error?.message || 'Gagal memuat tiket.', 'error');
                    elements.summary.textContent = 'Gagal memuat data tiket. Silakan coba lagi.';
                }
            }

            function renderTickets(records) {
                const rows = records.map((ticket) => {
                    const statusMeta = STATUS_META[ticket?.status] || { label: formatStatus(ticket?.status), classes: 'bg-gray-100 text-gray-600' };
                    const reporterName = ticket?.reporter?.name || 'Tidak diketahui';
                    const reporterEmail = ticket?.reporter?.email || '';
                    const location = ticket?.address || '-';
                    const officerName = ticket?.officer?.name || 'Belum ada petugas';
                    const createdAt = formatDateTime(ticket?.created_at);
                    const category = ticket?.category ? `<span class="badge-kategori inline-block px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">${ticket.category}</span>` : '';
                    const ticketId = ticket?.id || '-';

                    return `
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3"><span class="text-blue-600 font-semibold">#${ticketId}</span></td>
                            <td class="px-4 py-3">
                                <p class="text-gray-900 font-medium">${reporterName}</p>
                                <p class="text-gray-500 text-sm">${reporterEmail}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-gray-900 font-medium mb-1">${ticket?.title || 'Tanpa judul'}</p>
                                ${category}
                                <p class="text-gray-500 text-sm mt-1">${location}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full ${statusMeta.classes}">${statusMeta.label}</span>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-gray-900">${createdAt}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="text-gray-900">${officerName}</p>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="admin-detail-tiket.php?id=${ticketId}" class="inline-flex items-center justify-center w-10 h-10 text-blue-600 hover:bg-blue-50 rounded-lg" title="Lihat Detail">
                                    <i class="material-icons text-xl">visibility</i>
                                </a>
                            </td>
                        </tr>
                    `;
                }).join('');

                if (rows) {
                    elements.tableBody.innerHTML = rows;
                }
            }

            function updateSummary(data, query) {
                const paramsText = query.toString() ? `?${query.toString()}` : '-';
                const totalData = data?.total_data ?? 0;
                const limit = data?.limit ?? state.filters.limit;
                const page = data?.page ?? state.filters.page;
                elements.summary.textContent = `Menampilkan ${data?.records?.length || 0} tiket dari total ${totalData}.`;
            }

            function setTableLoading() {
                elements.tableBody.innerHTML = '<tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">Memuat data tiket...</td></tr>';
            }

            function showTableError(message) {
                elements.tableBody.innerHTML = `<tr><td colspan="7" class="px-4 py-6 text-center text-red-600">${message}</td></tr>`;
            }

            function showMessage(message, type) {
                if (!message) {
                    elements.messageBox.classList.add('hidden');
                    elements.messageBox.textContent = '';
                    return;
                }

                const palette = type === 'success'
                    ? 'bg-green-50 border-green-200 text-green-700'
                    : type === 'error'
                        ? 'bg-red-50 border-red-200 text-red-700'
                        : 'bg-blue-50 border-blue-200 text-blue-700';

                elements.messageBox.className = `mt-4 p-4 rounded-lg border text-sm ${palette}`;
                elements.messageBox.textContent = message;
                elements.messageBox.classList.remove('hidden');
            }

            function debounce(fn, delay) {
                let timer;
                return (...args) => {
                    clearTimeout(timer);
                    timer = setTimeout(() => fn.apply(null, args), delay);
                };
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
                    console.warn('[AdminTickets] gagal parse JSON', error);
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

            function formatStatus(status) {
                if (!status) return 'Tidak diketahui';
                return status.replace(/_/g, ' ').replace(/\b\w/g, (char) => char.toUpperCase());
            }

            function formatDateTime(value) {
                if (!value) return '-';
                const date = new Date(value);
                if (Number.isNaN(date.getTime())) return '-';
                return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(date);
            }
        })();
    </script>
</body>
</html>