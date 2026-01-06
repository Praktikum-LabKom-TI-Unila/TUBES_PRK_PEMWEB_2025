const HISTORY_STATUS = [
    { value: '', label: 'Semua' },
    { value: 'diajukan', label: 'Diajukan' },
    { value: 'diverifikasi_admin', label: 'Diverifikasi Admin' },
    { value: 'ditugaskan_ke_petugas', label: 'Ditugaskan' },
    { value: 'dalam_proses', label: 'Dalam Proses' },
    { value: 'menunggu_validasi_admin', label: 'Menunggu Validasi' },
    { value: 'selesai', label: 'Selesai' }
];

const HistoryDOM = {
    alert: document.getElementById('historyAlert'),
    filterContainer: document.getElementById('filter-buttons-container'),
    statsContainer: document.getElementById('history-stats-container'),
    listContainer: document.getElementById('complaints-history-list'),
    emptyState: document.getElementById('no-filtered-complaints'),
    pagination: document.getElementById('historyPagination')
};

const paginationState = {
    page: 1,
    totalPage: 1,
    limit: 5,
    status: ''
};

function setHistoryAlert(message = '', type = 'error') {
    if (!HistoryDOM.alert) return;
    if (!message) {
        HistoryDOM.alert.classList.add('hidden');
        HistoryDOM.alert.textContent = '';
        return;
    }
    const palette =
        type === 'success'
            ? 'bg-green-50 border border-green-200 text-green-700'
            : 'bg-red-50 border border-red-200 text-red-700';
    HistoryDOM.alert.className = `mb-4 p-3 rounded-lg text-sm ${palette}`;
    HistoryDOM.alert.textContent = message;
    HistoryDOM.alert.classList.remove('hidden');
}

function renderFilterButtons() {
    if (!HistoryDOM.filterContainer) return;
    HistoryDOM.filterContainer.innerHTML = '';
    HISTORY_STATUS.forEach((status) => {
        const button = document.createElement('button');
        const isActive = paginationState.status === status.value;
        button.type = 'button';
        button.className = `px-4 py-2 rounded-lg border text-sm transition-colors ${
            isActive ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600'
        }`;
        button.textContent = status.label;
        button.addEventListener('click', () => {
            paginationState.status = status.value;
            paginationState.page = 1;
            loadComplaints();
        });
        HistoryDOM.filterContainer.appendChild(button);
    });
}

function renderStats(data = {}) {
    if (!HistoryDOM.statsContainer) return;
    const total = data.total_laporan ?? 0;
    const diproses = data.total_diproses ?? 0;
    const selesai = data.total_selesai ?? 0;
    const diterima = Math.max(total - diproses - selesai, 0);
    HistoryDOM.statsContainer.innerHTML = `
        <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
            <div class="text-gray-900 font-bold mb-1">${total}</div>
            <p class="text-gray-600 text-sm">Total</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-blue-200 text-center">
            <div class="text-blue-600 font-bold mb-1">${diterima}</div>
            <p class="text-gray-600 text-sm">Diterima</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-yellow-200 text-center">
            <div class="text-yellow-600 font-bold mb-1">${diproses}</div>
            <p class="text-gray-600 text-sm">Diproses</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-green-200 text-center">
            <div class="text-green-600 font-bold mb-1">${selesai}</div>
            <p class="text-gray-600 text-sm">Selesai</p>
        </div>
    `;
}

function statusBadge(status = '') {
    const normalized = status.replace(/_/g, '-');
    let classes = 'inline-block px-3 py-1 text-xs font-medium rounded-lg ';
    if (normalized.includes('selesai')) classes += 'bg-green-100 text-green-700';
    else if (normalized.includes('proses') || normalized.includes('tugaskan') || normalized.includes('validasi'))
        classes += 'bg-yellow-100 text-yellow-700';
    else if (normalized.includes('diajukan') || normalized.includes('verifikasi')) classes += 'bg-blue-100 text-blue-700';
    else classes += 'bg-gray-100 text-gray-700';
    return `<span class="${classes}">${normalized.replace(/-/g, ' ')}</span>`;
}

function renderComplaints(records = []) {
    if (!HistoryDOM.listContainer) return;
    HistoryDOM.listContainer.innerHTML = '';
    if (records.length === 0) {
        HistoryDOM.emptyState?.classList.remove('hidden');
        return;
    }
    HistoryDOM.emptyState?.classList.add('hidden');

    records.forEach((record) => {
        HistoryDOM.listContainer.innerHTML += `
            <a href="warga-detail-pengaduan.php?id=${record.id}" class="bg-white rounded-xl p-4 border border-gray-200 block hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div>
                        <div class="text-gray-500 text-xs">#${record.id}</div>
                        <p class="text-gray-900 font-medium">${record.title || 'Pengaduan Infrastruktur'}</p>
                        <p class="text-sm text-gray-500">${record.category || '-'}</p>
                    </div>
                    ${statusBadge(record.status || '')}
                </div>
                <p class="text-sm text-gray-600 flex items-center gap-1">
                    <i class="material-icons text-base">place</i>${record.address || '-'}
                </p>
                <p class="text-xs text-gray-400 mt-1">${new Date(record.created_at).toLocaleString('id-ID')}</p>
            </a>
        `;
    });
}

function renderPagination() {
    if (!HistoryDOM.pagination) return;
    if (paginationState.totalPage <= 1) {
        HistoryDOM.pagination.classList.add('hidden');
        HistoryDOM.pagination.innerHTML = '';
        return;
    }
    HistoryDOM.pagination.classList.remove('hidden');
    HistoryDOM.pagination.innerHTML = `
        <button type="button" id="historyPrevBtn" class="px-3 py-1 border rounded-lg ${
            paginationState.page <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'
        }">Sebelumnya</button>
        <span>Halaman ${paginationState.page} dari ${paginationState.totalPage}</span>
        <button type="button" id="historyNextBtn" class="px-3 py-1 border rounded-lg ${
            paginationState.page >= paginationState.totalPage ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'
        }">Berikutnya</button>
    `;
    document.getElementById('historyPrevBtn')?.addEventListener('click', () => {
        if (paginationState.page > 1) {
            paginationState.page -= 1;
            loadComplaints();
        }
    });
    document.getElementById('historyNextBtn')?.addEventListener('click', () => {
        if (paginationState.page < paginationState.totalPage) {
            paginationState.page += 1;
            loadComplaints();
        }
    });
}

async function loadStats() {
    try {
        const payload = await PelaporAPI.request('/pelapor/dashboard/stats');
        renderStats(payload?.data || {});
    } catch (error) {
        console.error('[History] stats error', error);
        setHistoryAlert(error.message || 'Gagal memuat statistik.');
    }
}

async function loadComplaints() {
    HistoryDOM.listContainer.innerHTML =
        '<div class="bg-white rounded-xl p-4 border border-gray-200 text-center text-sm text-gray-500">Memuat data...</div>';
    try {
        const payload = await PelaporAPI.request('/pelapor/complaints', {
            query: {
                page: paginationState.page,
                limit: paginationState.limit,
                status: paginationState.status || undefined
            }
        });
        const data = payload?.data || {};
        paginationState.totalPage = data.total_page || 1;
        renderComplaints(data.records || []);
        renderPagination();
    } catch (error) {
        console.error('[History] list error', error);
        setHistoryAlert(error.message || 'Gagal memuat riwayat pengaduan.');
        HistoryDOM.listContainer.innerHTML =
            '<div class="bg-white rounded-xl p-4 border border-gray-200 text-center text-sm text-red-600">Tidak dapat memuat data.</div>';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    PelaporAuth.requirePelapor();
    renderFilterButtons();
    loadStats();
    loadComplaints();
});
