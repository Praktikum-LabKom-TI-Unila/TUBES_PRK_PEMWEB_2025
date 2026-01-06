'use strict';

(function () {
  const DOM = {
    pageAlert: document.getElementById('pageAlert'),
    skeleton: document.getElementById('detailSkeleton'),
    shell: document.getElementById('detailShell'),
    name: document.getElementById('officerName'),
    department: document.getElementById('officerDepartment'),
    employeeId: document.getElementById('officerEmployeeId'),
    statusBadge: document.getElementById('officerStatusBadge'),
    email: document.getElementById('officerEmail'),
    phone: document.getElementById('officerPhone'),
    address: document.getElementById('officerAddress'),
    specialization: document.getElementById('officerSpecialization'),
    createdAt: document.getElementById('officerCreatedAt'),
    updatedAt: document.getElementById('officerUpdatedAt'),
    statActive: document.getElementById('statActiveTasks'),
    statFinished: document.getElementById('statFinishedTasks'),
    statTotal: document.getElementById('statTotalTasks'),
    tasksContainer: document.getElementById('tasksContainer'),
  };

  const state = {
    officerId: '',
    detail: null,
    tasks: [],
  };

  document.addEventListener('DOMContentLoaded', () => {
    state.officerId = document.body.dataset.officerId || new URLSearchParams(window.location.search).get('id');
    if (!state.officerId) {
      AdminAPI.showInlineAlert(DOM.pageAlert, 'ID petugas tidak ditemukan pada URL.', 'error');
      toggleShell(false);
      return;
    }
    loadDetail();
  });

  const toggleShell = (show) => {
    if (DOM.shell) {
      DOM.shell.classList.toggle('hidden', !show);
    }
    if (DOM.skeleton) {
      DOM.skeleton.classList.toggle('hidden', show);
    }
  };

  const loadDetail = async () => {
    toggleShell(false);
    AdminAPI.showInlineAlert(DOM.pageAlert, 'Memuat profil petugas...', 'info');
    try {
      const data = await AdminAPI.request(`/api/admin/officers/${state.officerId}`);
      state.detail = data || {};
      renderDetail();
      await loadTasks();
      AdminAPI.showInlineAlert(DOM.pageAlert);
      toggleShell(true);
    } catch (error) {
      console.error('[AdminOfficerDetail] load detail error', error);
      AdminAPI.showInlineAlert(DOM.pageAlert, error?.message || 'Gagal memuat detail petugas.');
    }
  };

  const loadTasks = async () => {
    try {
      const params = new URLSearchParams({ page: 1, limit: 10 });
      const data = await AdminAPI.request(`/api/admin/officers/${state.officerId}/tasks?${params.toString()}`);
      state.tasks = Array.isArray(data?.records) ? data.records : [];
      renderTasks();
    } catch (error) {
      console.warn('[AdminOfficerDetail] gagal memuat tugas', error);
      if (DOM.tasksContainer) {
        DOM.tasksContainer.innerHTML = '<p class="text-sm text-red-600">Tugas aktif gagal dimuat. Coba muat ulang halaman.</p>';
      }
    }
  };

  const renderDetail = () => {
    const officer = state.detail?.officer || {};
    const statistics = state.detail?.statistics || {};

    document.title = officer?.name ? `${officer.name} | Detail Petugas` : 'Detail Petugas';

    DOM.name.textContent = officer.name || '-';
    DOM.department.textContent = officer.department || '-';
    DOM.employeeId.textContent = officer.employee_id ? `ID Pegawai: ${officer.employee_id}` : 'ID Pegawai: -';
    DOM.email.href = officer.email ? `mailto:${officer.email}` : '#';
    DOM.email.querySelector('span').textContent = officer.email || '-';
    DOM.phone.href = officer.phone ? `tel:${officer.phone}` : '#';
    DOM.phone.querySelector('span').textContent = officer.phone || '-';
    DOM.address.textContent = officer.address || '-';
    DOM.specialization.textContent = formatSpecialization(officer.specialization);
    DOM.createdAt.textContent = AdminAPI.formatDateTime(officer.created_at);
    DOM.updatedAt.textContent = AdminAPI.formatDateTime(officer.updated_at);

    renderStatusBadge(officer.status);
    renderStats(statistics);
  };

  const renderStatusBadge = (status) => {
    const meta = {
      tersedia: { text: 'Tersedia', classes: 'bg-green-100 text-green-700' },
      sibuk: { text: 'Sedang bertugas', classes: 'bg-amber-100 text-amber-700' },
    };
    const info = meta[status] || { text: 'Status tidak diketahui', classes: 'bg-gray-100 text-gray-600' };
    DOM.statusBadge.className = `px-3 py-1 rounded-full text-sm font-semibold ${info.classes}`;
    DOM.statusBadge.textContent = info.text;
  };

  const renderStats = (stats) => {
    DOM.statActive.textContent = stats?.active_tasks ?? 0;
    DOM.statFinished.textContent = stats?.finished_tasks ?? 0;
    DOM.statTotal.textContent = stats?.total_tasks ?? 0;
  };

  const renderTasks = () => {
    if (!DOM.tasksContainer) return;
    if (!state.tasks.length) {
      DOM.tasksContainer.innerHTML = '<p class="text-sm text-gray-500">Belum ada tugas aktif untuk petugas ini.</p>';
      return;
    }

    DOM.tasksContainer.innerHTML = state.tasks
      .map(
        (task) => `
                <div class="border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">${task.title}</p>
                        <p class="text-sm text-gray-500">Pengaduan #${task.complaint_id} • ${task.category || '-'} </p>
                        <p class="text-xs text-gray-400">Mulai ${AdminAPI.formatDateTime(task.started_at)}</p>
                    </div>
                    <div class="flex flex-col items-start gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 capitalize">${task.status?.replace(
                          /_/g,
                          ' '
                        )}</span>
                        <a href="admin-detail-tiket.php?id=${task.complaint_id}" class="text-sm text-blue-600 hover:underline">Lihat Tiket</a>
                    </div>
                </div>
            `
      )
      .join('');
  };

  const formatSpecialization = (value) => {
    if (!value) return '-';
    return value
      .split('_')
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
      .join(' ');
  };
})();
