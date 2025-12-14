const DashboardDOM = {
    name: document.getElementById('dashboardUserName'),
    logoutButton: document.getElementById('dashboardLogoutButton'),
    statsContainer: document.getElementById('warga-stats-container'),
    recentList: document.getElementById('recent-complaints-list'),
    emptyState: document.getElementById('no-complaints-state'),
    alert: document.getElementById('dashboardAlert'),
    apiInfo: document.getElementById('dashboardApiInfo')
};
const dashboardApiLogs = [];

function appendApiInfoLog(section, payload = {}) {
    if (!DashboardDOM.apiInfo) return;
    const now = new Date().toLocaleTimeString('id-ID');
    const status = payload?.status || 'tidak diketahui';
    const code = payload?.code != null ? ` (${payload.code})` : '';
    const message = payload?.message || 'Respon tidak memiliki pesan.';
    const entry = `[${now}] ${section}: ${status}${code} - ${message}`;
    dashboardApiLogs.unshift(entry);
    DashboardDOM.apiInfo.textContent = dashboardApiLogs.slice(0, 4).join(' | ');
}

function setDashboardAlert(message = '', type = 'error') {
    if (!DashboardDOM.alert) return;
    if (!message) {
        DashboardDOM.alert.classList.add('hidden');
        DashboardDOM.alert.textContent = '';
        return;
    }
    const palette =
        type === 'success'
            ? 'bg-green-50 border border-green-200 text-green-700'
            : 'bg-red-50 border border-red-200 text-red-700';
    DashboardDOM.alert.className = `mb-4 p-3 rounded-lg text-sm ${palette}`;
    DashboardDOM.alert.textContent = message;
    DashboardDOM.alert.classList.remove('hidden');
}

function hydrateDashboardHeader() {
    const user = PelaporAuth.getUser();
    if (DashboardDOM.name) {
        DashboardDOM.name.textContent = user?.full_name || user?.name || 'Pelapor';
    }
    DashboardDOM.logoutButton?.addEventListener('click', () => PelaporAuth.handleLogout());
}

function renderStats(data = {}) {
    if (!DashboardDOM.statsContainer) return;
    const total = data.total_laporan ?? data.total ?? data.total_tasks ?? 0;
    const diproses = data.total_diproses ?? data.in_progress ?? data.active ?? data.on_progress ?? 0;
    const selesai = data.total_selesai ?? data.finished ?? data.completed ?? 0;
    DashboardDOM.statsContainer.innerHTML = `
        <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
            <div class="text-gray-900 font-bold mb-1 text-xl">${total}</div>
            <p class="text-gray-600 text-sm">Total</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-yellow-200 text-center">
            <div class="text-yellow-600 font-bold mb-1 text-xl">${diproses}</div>
            <p class="text-gray-600 text-sm">Diproses</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-green-200 text-center">
            <div class="text-green-600 font-bold mb-1 text-xl">${selesai}</div>
            <p class="text-gray-600 text-sm">Selesai</p>
        </div>
    `;
}

function getStatusBadge(status = '') {
    const normalized = status.replace(/_/g, '-');
    let classes = 'inline-block px-3 py-1 text-xs font-medium rounded-lg ';
    if (normalized.includes('selesai')) classes += 'bg-green-100 text-green-700';
    else if (normalized.includes('proses') || normalized.includes('tugaskan') || normalized.includes('validasi'))
        classes += 'bg-yellow-100 text-yellow-700';
    else classes += 'bg-gray-100 text-gray-700';
    return `<span class="${classes}">${normalized.replace(/-/g, ' ')}</span>`;
}

function renderRecent(records = []) {
    if (!DashboardDOM.recentList) return;
    DashboardDOM.recentList.innerHTML = '';
    if (records.length === 0) {
        DashboardDOM.emptyState?.classList.remove('hidden');
        return;
    }
    DashboardDOM.emptyState?.classList.add('hidden');

    records.forEach((record) => {
        const badge = getStatusBadge(record.status || '');
        DashboardDOM.recentList.innerHTML += `
            <a href="warga-detail-pengaduan.php?id=${record.id}" class="bg-white rounded-xl p-4 border border-gray-200 hover:shadow-md transition-shadow block">
                <div class="flex items-center justify-between gap-3 mb-1">
                    <p class="text-gray-900 font-medium truncate">${record.title || 'Pengaduan Infrastruktur'}</p>
                    ${badge}
                </div>
                <p class="text-sm text-gray-500">#${record.id} • ${record.category || '-'}</p>
                <p class="flex items-center gap-1 text-sm text-gray-600 mt-1">
                    <i class="material-icons text-base">place</i>${record.address || '-'}
                </p>
                <p class="text-xs text-gray-400 mt-1">${new Date(record.created_at).toLocaleString('id-ID')}</p>
            </a>
        `;
    });
}

async function loadStats() {
    DashboardDOM.statsContainer.innerHTML =
        '<div class="col-span-3 text-center text-sm text-gray-500">Memuat statistik...</div>';
    try {
        const payload = await PelaporAPI.request('/pelapor/dashboard/stats');
        console.info('[WargaDashboard] stats payload', payload);
        renderStats(payload?.data || {});
        appendApiInfoLog('Statistik', payload);
    } catch (error) {
        console.error('[PelaporDashboard] stats error', error);
        setDashboardAlert(error.message || 'Gagal memuat statistik.');
        DashboardDOM.statsContainer.innerHTML =
            '<div class="col-span-3 text-center text-sm text-red-600">Gagal memuat statistik.</div>';
        appendApiInfoLog('Statistik', { status: 'error', message: error.message });
    }
}

async function loadRecent() {
    DashboardDOM.recentList.innerHTML =
        '<div class="bg-white rounded-xl p-4 border border-gray-200 text-center text-sm text-gray-500">Memuat data...</div>';
    try {
        const payload = await PelaporAPI.request('/pelapor/complaints/recent', { query: { limit: 5 } });
        console.info('[WargaDashboard] recent payload', payload);
        renderRecent(payload?.data?.records || []);
        appendApiInfoLog('Pengaduan terbaru', payload);
    } catch (error) {
        console.error('[PelaporDashboard] recent error', error);
        setDashboardAlert(error.message || 'Gagal memuat pengaduan terbaru.');
        DashboardDOM.recentList.innerHTML =
            '<div class="bg-white rounded-xl p-4 border border-gray-200 text-center text-sm text-red-600">Tidak dapat memuat data.</div>';
        appendApiInfoLog('Pengaduan terbaru', { status: 'error', message: error.message });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    PelaporAuth.requirePelapor();
    hydrateDashboardHeader();
    loadStats();
    loadRecent();
});
