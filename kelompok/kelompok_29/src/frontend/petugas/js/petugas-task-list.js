const DOM = {
    statsContainer: document.getElementById('stats-cards-container'),
    statsLoader: document.getElementById('statsLoader'),
    activeList: document.getElementById('active-tasks-list'),
    activeLoader: document.getElementById('activeTasksLoader'),
    completedList: document.getElementById('completed-tasks-list'),
    completedLoader: document.getElementById('completedTasksLoader'),
    noActive: document.getElementById('no-active-tasks'),
    noCompleted: document.getElementById('no-completed-tasks'),
    activePagination: document.getElementById('activePagination'),
    completedPagination: document.getElementById('completedPagination'),
    alert: document.getElementById('dashboardAlert'),
    headerName: document.getElementById('dashboardName'),
    headerDept: document.getElementById('dashboardDepartment'),
    logoutButton: document.getElementById('logoutButton')
};

const paginationState = {
    active: { page: 1, limit: 5, total_page: 1 },
    completed: { page: 1, limit: 5, total_page: 1 }
};

const placeholderImage = 'https://placehold.co/160x160?text=Foto';

function showAlert(message, type = 'error') {
    if (!DOM.alert) return;
    DOM.alert.textContent = message;
    DOM.alert.classList.remove('hidden', 'text-red-700', 'bg-red-50', 'border-red-200', 'text-green-700', 'bg-green-50', 'border-green-200');
    if (type === 'success') {
        DOM.alert.classList.add('text-green-700', 'bg-green-50', 'border', 'border-green-200');
    } else {
        DOM.alert.classList.add('text-red-700', 'bg-red-50', 'border', 'border-red-200');
    }
}

function clearAlert() {
    if (!DOM.alert) return;
    DOM.alert.classList.add('hidden');
    DOM.alert.textContent = '';
}

function formatStatus(status = '') {
    return status.replace(/_/g, ' ');
}

function getStatusBadgeHtml(status = '') {
    const normalized = status.toLowerCase();
    let classes = 'px-3 py-1 rounded-full text-xs font-semibold ';
    if (normalized.includes('proses') || normalized.includes('ditugaskan')) classes += 'bg-yellow-100 text-yellow-700';
    else if (normalized.includes('selesai')) classes += 'bg-green-100 text-green-700';
    else if (normalized.includes('validasi')) classes += 'bg-orange-100 text-orange-700';
    else classes += 'bg-blue-100 text-blue-700';
    return `<span class="${classes}">${formatStatus(status)}</span>`;
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
}

function renderStats(stats = {}) {
    if (!DOM.statsContainer) return;
    DOM.statsLoader?.classList.add('hidden');
    DOM.statsContainer.innerHTML = `
        <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
            <div class="text-gray-900 font-bold mb-1 text-2xl">${stats.total_assignments ?? 0}</div>
            <p class="text-gray-600 text-sm">Total Tugas</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-yellow-200 text-center">
            <div class="text-yellow-600 font-bold mb-1 text-2xl">${stats.active_tasks ?? 0}</div>
            <p class="text-gray-600 text-sm">Aktif</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-green-200 text-center">
            <div class="text-green-600 font-bold mb-1 text-2xl">${stats.completed_tasks ?? 0}</div>
            <p class="text-gray-600 text-sm">Selesai</p>
        </div>
    `;
}

function renderTasks(records, container, emptyContainer, loader, type) {
    if (!container) return;
    loader?.classList.add('hidden');
    container.innerHTML = '';

    if (!records || records.length === 0) {
        emptyContainer?.classList.remove('hidden');
        return;
    }
    emptyContainer?.classList.add('hidden');

    records.forEach(record => {
        const id = record.task_id ?? record.complaint_id ?? record.id;
        const badge = getStatusBadgeHtml(record.status || '');
        const cover = PetugasAuth.resolveFileUrl(record.photos?.before || record.cover_photo || '') || placeholderImage;
        const location = record.address || record.location || 'Lokasi belum tersedia';

        const card = document.createElement('div');
        card.className = `bg-white rounded-xl p-4 border border-gray-200 ${type === 'completed' ? 'cursor-pointer hover:shadow-md transition-shadow opacity-90' : ''}`;
        if (type === 'completed') {
            card.addEventListener('click', () => {
                window.location.href = `petugas-task-detail.php?id=${id}`;
            });
        }

        card.innerHTML = `
            <div class="flex items-start gap-4">
                <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                    <img src="${cover}" alt="${record.title || 'Tugas Infrastruktur'}" class="w-full h-full object-cover" onerror="this.src='${placeholderImage}'"/>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div>
                            <div class="text-gray-500 text-sm mb-1">#${id || '-'}</div>
                            <p class="text-gray-900 font-medium truncate">${record.title || 'Tugas Infrastruktur'}</p>
                        </div>
                        ${badge}
                    </div>
                    <div class="space-y-2 text-gray-600 text-sm">
                        <div class="flex items-center gap-2 truncate">
                            <i class="material-icons text-base">place</i>
                            <span class="truncate">${location}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-500">
                            <i class="material-icons text-base">calendar_today</i>
                            <span>Ditugaskan: ${formatDate(record.created_at)}</span>
                        </div>
                    </div>
                    ${type === 'completed'
                        ? '<div class="mt-3 text-right text-sm text-blue-600">Klik kartu untuk detail</div>'
                        : `<div class="mt-3 pt-3 border-t border-gray-200">
                            <a href="petugas-task-detail.php?id=${id}" class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center font-semibold">
                                Lihat Detail & Aksi
                            </a>
                        </div>`}
                </div>
            </div>
        `;

        container.appendChild(card);
    });
}

function renderPagination(container, state, type) {
    if (!container) return;
    if (!state || (state.total_page ?? 1) <= 1) {
        container.classList.add('hidden');
        container.innerHTML = '';
        return;
    }
    container.classList.remove('hidden');
    container.innerHTML = `
        <button class="px-3 py-1 border rounded-lg ${state.page <= 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'}" data-type="${type}" data-direction="prev">Sebelumnya</button>
        <div>Halaman ${state.page} dari ${state.total_page}</div>
        <button class="px-3 py-1 border rounded-lg ${state.page >= state.total_page ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-100'}" data-type="${type}" data-direction="next">Berikutnya</button>
    `;
}

async function loadStats() {
    try {
        clearAlert();
        const payload = await PetugasAPI.request('/officer/dashboard/stats');
        renderStats(payload.data || {});
    } catch (error) {
        console.error('[Dashboard] stats error', error);
        DOM.statsLoader?.classList.add('hidden');
        showAlert(error.message || 'Gagal memuat statistik.');
    }
}

async function loadActiveTasks() {
    try {
        clearAlert();
        DOM.activeLoader?.classList.remove('hidden');
        const payload = await PetugasAPI.request('/officer/tasks/active', {
            query: {
                page: paginationState.active.page,
                limit: paginationState.active.limit
            }
        });
        const data = payload.data || {};
        paginationState.active.total_page = data.total_page || 1;
        renderTasks(data.records || [], DOM.activeList, DOM.noActive, DOM.activeLoader, 'active');
        renderPagination(DOM.activePagination, paginationState.active, 'active');
    } catch (error) {
        console.error('[Dashboard] active tasks error', error);
        DOM.activeLoader?.classList.add('hidden');
        showAlert(error.message || 'Gagal memuat tugas aktif.');
    }
}

async function loadCompletedTasks() {
    try {
        clearAlert();
        DOM.completedLoader?.classList.remove('hidden');
        const payload = await PetugasAPI.request('/officer/tasks/completed', {
            query: {
                page: paginationState.completed.page,
                limit: paginationState.completed.limit
            }
        });
        const data = payload.data || {};
        paginationState.completed.total_page = data.total_page || 1;
        renderTasks(data.records || [], DOM.completedList, DOM.noCompleted, DOM.completedLoader, 'completed');
        renderPagination(DOM.completedPagination, paginationState.completed, 'completed');
    } catch (error) {
        console.error('[Dashboard] completed tasks error', error);
        DOM.completedLoader?.classList.add('hidden');
        showAlert(error.message || 'Gagal memuat riwayat tugas.');
    }
}

function handlePaginationClick(event) {
    const button = event.target.closest('button[data-type]');
    if (!button) return;
    const type = button.dataset.type;
    const direction = button.dataset.direction;
    const state = paginationState[type];
    if (!state) return;
    if (direction === 'prev' && state.page > 1) {
        state.page -= 1;
    } else if (direction === 'next' && state.page < state.total_page) {
        state.page += 1;
    } else {
        return;
    }
    if (type === 'active') loadActiveTasks();
    else loadCompletedTasks();
}

function hydrateHeader() {
    const user = PetugasAuth.getUser();
    if (DOM.headerName) {
        DOM.headerName.textContent = user?.full_name || user?.name || '-';
    }
    if (DOM.headerDept) {
        DOM.headerDept.textContent = user?.department || 'Petugas Lapangan';
    }
    DOM.logoutButton?.addEventListener('click', () => PetugasAuth.handleLogout());
}

document.addEventListener('DOMContentLoaded', () => {
    PetugasAuth.requireOfficer();
    hydrateHeader();
    DOM.activePagination?.addEventListener('click', handlePaginationClick);
    DOM.completedPagination?.addEventListener('click', handlePaginationClick);
    loadStats();
    loadActiveTasks();
    loadCompletedTasks();
});
