'use strict';

(function () {
  const state = {
    page: 1,
    limit: 10,
    totalPage: 1,
    totalData: 0,
    status: 'all',
    search: '',
    records: [],
  };

  const DOM = {
    searchInput: document.getElementById('searchPetugas'),
    statusFilter: document.getElementById('statusFilter'),
    tableBody: document.getElementById('tabel-body-petugas'),
    statsContainer: document.getElementById('ringkasan-petugas-container'),
    showingInfo: document.getElementById('showing-info'),
    emptyState: document.getElementById('empty-state'),
    pageAlert: document.getElementById('pageAlert'),
    tableSkeleton: document.getElementById('tableSkeleton'),
    prevBtn: document.getElementById('prevPageBtn'),
    nextBtn: document.getElementById('nextPageBtn'),
    paginationInfo: document.getElementById('paginationInfo'),
    detailModalContainer: document.getElementById('detail-modal-container'),
  };

  const detailState = {
    officerId: null,
    detail: null,
    tasks: [],
    tasksPage: 1,
    tasksLimit: 5,
    tasksTotalPage: 1,
    tasksTotalData: 0,
  };

  const detailRefs = {};

  let searchDebounce = null;

  document.addEventListener('DOMContentLoaded', () => {
    if (!DOM.tableBody || !DOM.statsContainer) return;
    bindEvents();
    fetchOfficers();
  });

  document.addEventListener('keydown', handleDocumentKeydown);

  const bindEvents = () => {
    if (DOM.searchInput) {
      DOM.searchInput.addEventListener('input', (event) => {
        state.search = event.target.value.trim();
        state.page = 1;
        if (searchDebounce) clearTimeout(searchDebounce);
        searchDebounce = setTimeout(fetchOfficers, 400);
      });
    }

    if (DOM.statusFilter) {
      DOM.statusFilter.addEventListener('change', (event) => {
        state.status = event.target.value;
        state.page = 1;
        fetchOfficers();
      });
    }

    if (DOM.prevBtn) {
      DOM.prevBtn.addEventListener('click', () => {
        if (state.page <= 1) return;
        state.page -= 1;
        fetchOfficers();
      });
    }

    if (DOM.nextBtn) {
      DOM.nextBtn.addEventListener('click', () => {
        if (state.page >= state.totalPage) return;
        state.page += 1;
        fetchOfficers();
      });
    }

    if (DOM.tableBody) {
      DOM.tableBody.addEventListener('click', handleTableActions);
    }
  };

  const handleTableActions = (event) => {
    const button = event.target.closest('[data-action="open-officer-detail"]');
    if (!button) return;
    event.preventDefault();
    const officerId = button.dataset.officerId;
    if (officerId) {
      showDetailModal(officerId);
    }
  };

  const toggleLoading = (show) => {
    if (!DOM.tableSkeleton) return;
    DOM.tableSkeleton.classList.toggle('hidden', !show);
    const table = DOM.tableBody?.parentElement;
    if (table) {
      table.classList.toggle('opacity-30', show);
    }
  };

  const fetchOfficers = async () => {
    const params = new URLSearchParams({
      page: Math.max(state.page, 1),
      limit: state.limit,
    });
    if (state.status && state.status !== 'all') {
      params.append('status', state.status);
    }
    if (state.search) {
      params.append('q', state.search);
    }

    toggleLoading(true);
    AdminAPI.showInlineAlert(DOM.pageAlert, 'Memuat data petugas...', 'info');

    try {
      const data = await AdminAPI.request(`/api/admin/officers?${params.toString()}`);

      state.page = data?.page ?? state.page;
      state.limit = data?.limit ?? state.limit;
      state.totalData = typeof data?.total_data === 'number' ? data.total_data : (Array.isArray(data?.records) ? data.records.length : 0);
      const totalPage = typeof data?.total_page === 'number' ? data.total_page : 1;
      state.totalPage = totalPage > 0 ? totalPage : 1;
      state.records = Array.isArray(data?.records) ? data.records : [];

      renderStats();
      renderTable();
      renderPagination();
      DOM.emptyState.classList.toggle('hidden', state.records.length > 0);
      AdminAPI.showInlineAlert(DOM.pageAlert);
      console.info('[AdminManageOfficers] Loaded', {
        page: state.page,
        totalPage: state.totalPage,
        totalData: state.totalData,
        search: state.search,
        status: state.status,
      });
    } catch (error) {
      console.error('[AdminManageOfficers] fetch error', error);
      state.records = [];
      state.totalData = 0;
      state.totalPage = 1;
      renderStats();
      renderTable();
      renderPagination();
      DOM.emptyState.classList.remove('hidden');
      AdminAPI.showInlineAlert(DOM.pageAlert, error?.message || 'Gagal memuat data petugas.', 'error');
    } finally {
      toggleLoading(false);
    }
  };

  // Menampilkan kartu ringkasan berdasarkan data yang diterima.
  const renderStats = () => {
    if (!DOM.statsContainer) return;
    const availableCount = state.records.filter((record) => record.status === 'tersedia').length;
    const busyCount = state.records.filter((record) => record.status === 'sibuk').length;

    DOM.statsContainer.innerHTML = `
      <div class="bg-white rounded-xl p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-2xl">
            <i class="material-icons">group</i>
          </div>
          <span class="text-gray-500 text-sm">Total</span>
        </div>
        <div class="text-gray-900 text-2xl font-bold mb-1">${state.totalData}</div>
        <p class="text-gray-600">Petugas terdaftar</p>
      </div>
      <div class="bg-white rounded-xl p-6 border border-green-200">
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 text-2xl">
            <i class="material-icons">check_circle</i>
          </div>
          <span class="text-green-700 text-sm">Tersedia</span>
        </div>
        <div class="text-green-700 text-2xl font-bold mb-1">${availableCount}</div>
        <p class="text-gray-600">Siap menerima tugas</p>
      </div>
      <div class="bg-white rounded-xl p-6 border border-amber-200">
        <div class="flex items-center justify-between mb-4">
          <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center text-amber-600 text-2xl">
            <i class="material-icons">schedule</i>
          </div>
          <span class="text-amber-600 text-sm">Sedang Bertugas</span>
        </div>
        <div class="text-amber-600 text-2xl font-bold mb-1">${busyCount}</div>
        <p class="text-gray-600">Aktif di lapangan</p>
      </div>
    `;
  };

  const renderTable = () => {
    if (!DOM.tableBody) return;
    if (!state.records.length) {
      DOM.tableBody.innerHTML = `
        <tr>
          <td colspan="6" class="px-6 py-8 text-center text-gray-500 text-sm">
            Tidak ada petugas yang sesuai dengan filter atau pencarian.
          </td>
        </tr>
      `;
      if (DOM.showingInfo) {
        DOM.showingInfo.textContent = state.totalData
          ? `Tidak ada data petugas di halaman ${state.page}.`
          : 'Tidak ada data petugas untuk ditampilkan.';
      }
      return;
    }

    DOM.tableBody.innerHTML = state.records
      .map((officer) => {
        const specialization = formatSpecialization(officer.specialization);
        const badge = getStatusBadgeHtml(officer.status);
        return `
          <tr class="border-b border-gray-100 hover:bg-gray-50">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xl">
                  <i class="material-icons">person</i>
                </div>
                <div>
                  <p class="text-gray-900 font-semibold">${officer.name || '-'}</p>
                  <p class="text-gray-500 text-xs">${officer.email || '-'}</p>
                  <p class="text-gray-400 text-xs">ID: ${officer.employee_id || '-'}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-gray-700">${officer.department || '-'}</td>
            <td class="px-6 py-4 text-gray-700">${specialization || '-'}</td>
            <td class="px-6 py-4">
              <div class="space-y-1 text-sm">
                <p class="font-semibold text-gray-900">${officer.active_tasks ?? 0} aktif</p>
                <p class="text-gray-500">${officer.completed_tasks ?? 0} selesai</p>
              </div>
            </td>
            <td class="px-6 py-4">${badge}</td>
            <td class="px-6 py-4 text-center">
              <button
                type="button"
                data-action="open-officer-detail"
                data-officer-id="${officer.id}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors"
              >
                <i class="material-icons text-base">visibility</i>
                Detail
              </button>
            </td>
          </tr>
        `;
      })
      .join('');

    if (DOM.showingInfo) {
      const total = state.totalData;
      const shown = state.records.length;
      DOM.showingInfo.textContent = `Menampilkan ${shown} dari ${total} petugas (Halaman ${state.page}).`;
    }
  };

  const renderPagination = () => {
    if (!DOM.paginationInfo) return;
    DOM.paginationInfo.textContent = `Halaman ${state.page} dari ${state.totalPage}`;

    if (DOM.prevBtn) {
      const disabled = state.page <= 1;
      DOM.prevBtn.disabled = disabled;
      DOM.prevBtn.classList.toggle('opacity-50', disabled);
    }
    if (DOM.nextBtn) {
      const disabled = state.page >= state.totalPage;
      DOM.nextBtn.disabled = disabled;
      DOM.nextBtn.classList.toggle('opacity-50', disabled);
    }
  };

  const showDetailModal = (officerId) => {
    if (!officerId || !DOM.detailModalContainer) return;
    ensureDetailModal();
    detailState.officerId = officerId;
    detailState.detail = null;
    detailState.tasks = [];
    detailState.tasksPage = 1;
    detailState.tasksTotalPage = 1;
    detailState.tasksTotalData = 0;

    if (detailRefs.error) {
      detailRefs.error.classList.add('hidden');
      detailRefs.error.textContent = '';
    }
    detailRefs.skeleton?.classList.remove('hidden');
    detailRefs.content?.classList.add('hidden');
    detailRefs.tasksContainer?.classList.add('hidden');
    detailRefs.tasksLoader?.classList.remove('hidden');

    detailRefs.wrapper?.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    loadOfficerDetail(officerId);
  };

  const closeDetailModal = () => {
    if (!detailRefs.wrapper) return;
    detailRefs.wrapper.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  };

  const handleDocumentKeydown = (event) => {
    if (event.key !== 'Escape') return;
    if (detailRefs.wrapper && !detailRefs.wrapper.classList.contains('hidden')) {
      event.preventDefault();
      closeDetailModal();
    }
  };

  // Menyusun modal detail hanya sekali saat dibutuhkan.
  const ensureDetailModal = () => {
    if (detailRefs.wrapper || !DOM.detailModalContainer) return;
    DOM.detailModalContainer.innerHTML = detailModalTemplate();
    detailRefs.wrapper = DOM.detailModalContainer.querySelector('#detailModalWrapper');
    detailRefs.skeleton = DOM.detailModalContainer.querySelector('#detailModalSkeleton');
    detailRefs.content = DOM.detailModalContainer.querySelector('#detailModalContent');
    detailRefs.error = DOM.detailModalContainer.querySelector('#detailModalError');
    detailRefs.name = DOM.detailModalContainer.querySelector('#detailOfficerName');
    detailRefs.department = DOM.detailModalContainer.querySelector('#detailOfficerDepartment');
    detailRefs.employeeId = DOM.detailModalContainer.querySelector('#detailOfficerEmployeeId');
    detailRefs.statusBadge = DOM.detailModalContainer.querySelector('#detailOfficerStatus');
    detailRefs.email = DOM.detailModalContainer.querySelector('#detailOfficerEmail');
    detailRefs.phone = DOM.detailModalContainer.querySelector('#detailOfficerPhone');
    detailRefs.address = DOM.detailModalContainer.querySelector('#detailOfficerAddress');
    detailRefs.specialization = DOM.detailModalContainer.querySelector('#detailOfficerSpecialization');
    detailRefs.createdAt = DOM.detailModalContainer.querySelector('#detailOfficerCreatedAt');
    detailRefs.updatedAt = DOM.detailModalContainer.querySelector('#detailOfficerUpdatedAt');
    detailRefs.statActive = DOM.detailModalContainer.querySelector('#detailStatActive');
    detailRefs.statFinished = DOM.detailModalContainer.querySelector('#detailStatFinished');
    detailRefs.statTotal = DOM.detailModalContainer.querySelector('#detailStatTotal');
    detailRefs.tasksContainer = DOM.detailModalContainer.querySelector('#detailTasksContainer');
    detailRefs.tasksLoader = DOM.detailModalContainer.querySelector('#detailTasksLoader');
    detailRefs.tasksPageInfo = DOM.detailModalContainer.querySelector('#detailTasksPageInfo');
    detailRefs.taskPrevBtn = DOM.detailModalContainer.querySelector('[data-task-page="prev"]');
    detailRefs.taskNextBtn = DOM.detailModalContainer.querySelector('[data-task-page="next"]');

    const closeTriggers = DOM.detailModalContainer.querySelectorAll('[data-detail-modal-close]');
    closeTriggers.forEach((trigger) => {
      trigger.addEventListener('click', closeDetailModal);
    });

    detailRefs.wrapper?.addEventListener('click', (event) => {
      if (event.target.dataset.detailModalClose !== undefined) {
        closeDetailModal();
      }
    });

    detailRefs.taskPrevBtn?.addEventListener('click', () => handleTaskPagination('prev'));
    detailRefs.taskNextBtn?.addEventListener('click', () => handleTaskPagination('next'));
  };

  const detailModalTemplate = () => `
    <div id="detailModalWrapper" class="fixed inset-0 z-50 hidden flex items-center justify-center px-4 py-6">
      <div class="absolute inset-0 bg-black/40" data-detail-modal-close></div>
      <div class="relative w-full max-w-5xl rounded-3xl bg-white border border-gray-200 shadow-2xl overflow-hidden">
        <div class="p-6 space-y-6">
          <div class="flex items-start justify-between gap-4">
            <div>
              <p class="text-lg font-semibold text-gray-900">Detail Petugas</p>
              <p class="text-sm text-gray-500">Profil lengkap dan tugas aktif</p>
            </div>
            <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold text-gray-600 border border-gray-200 hover:bg-gray-100" data-detail-modal-close>
              <i class="material-icons text-base">close</i>
              Tutup
            </button>
          </div>
          <div id="detailModalError" class="hidden px-4 py-3 rounded-xl border border-red-200 bg-red-50 text-sm text-red-700"></div>

          <div id="detailModalSkeleton" class="space-y-4 animate-pulse">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-full bg-gray-200"></div>
              <div class="space-y-2 flex-1">
                <div class="h-4 bg-gray-200 rounded w-40"></div>
                <div class="h-3 bg-gray-200 rounded w-32"></div>
              </div>
            </div>
            <div class="grid grid-cols-3 gap-3">
              <div class="h-20 bg-gray-100 rounded-xl"></div>
              <div class="h-20 bg-gray-100 rounded-xl"></div>
              <div class="h-20 bg-gray-100 rounded-xl"></div>
            </div>
            <div class="h-48 bg-gray-100 rounded-2xl"></div>
            <div class="h-32 bg-gray-100 rounded-2xl"></div>
          </div>

          <div id="detailModalContent" class="space-y-5 hidden">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
              <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-3xl">
                  <i class="material-icons">badge</i>
                </div>
                <div>
                  <p id="detailOfficerName" class="text-xl font-semibold text-gray-900">-</p>
                  <p id="detailOfficerDepartment" class="text-sm text-gray-500">-</p>
                  <p id="detailOfficerEmployeeId" class="text-xs text-gray-400">ID Pegawai -</p>
                </div>
              </div>
              <div class="flex flex-wrap items-center gap-2">
                <span id="detailOfficerStatus" class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Status</span>
                <a id="detailOfficerEmail" href="#" class="text-sm text-blue-600 flex items-center gap-2"><i class="material-icons text-base">mail</i><span>-</span></a>
                <a id="detailOfficerPhone" href="#" class="text-sm text-blue-600 flex items-center gap-2"><i class="material-icons text-base">phone</i><span>-</span></a>
              </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
              <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs text-gray-500">Tugas Aktif</p>
                <p id="detailStatActive" class="text-2xl font-semibold text-gray-900">0</p>
              </div>
              <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs text-gray-500">Tugas Selesai</p>
                <p id="detailStatFinished" class="text-2xl font-semibold text-gray-900">0</p>
              </div>
              <div class="bg-white rounded-xl border border-gray-100 p-4">
                <p class="text-xs text-gray-500">Total Penugasan</p>
                <p id="detailStatTotal" class="text-2xl font-semibold text-gray-900">0</p>
              </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-2">
                <p class="text-sm font-semibold text-gray-900">Informasi Kontak</p>
                <p id="detailOfficerAddress" class="text-sm text-gray-600">-</p>
                <p class="text-xs text-gray-500">Spesialisasi: <span id="detailOfficerSpecialization">-</span></p>
              </div>
              <div class="bg-white rounded-2xl border border-gray-200 p-5 space-y-1 text-sm text-gray-600">
                <p>Dibuat: <span id="detailOfficerCreatedAt">-</span></p>
                <p>Diperbarui: <span id="detailOfficerUpdatedAt">-</span></p>
              </div>
            </div>

            <div class="space-y-3">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-lg font-semibold text-gray-900">Tugas Aktif</p>
                  <p class="text-sm text-gray-500">Menampilkan penugasan aktif terbaru</p>
                </div>
              </div>
              <div id="detailTasksLoader" class="text-sm text-gray-500">Memuat tugas...</div>
              <div id="detailTasksContainer" class="space-y-3"></div>
              <div class="flex items-center justify-between gap-3">
                <button type="button" data-task-page="prev" class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-100">Sebelumnya</button>
                <span id="detailTasksPageInfo" class="text-xs text-gray-500">Halaman 1 dari 1</span>
                <button type="button" data-task-page="next" class="px-4 py-2 rounded-lg border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-100">Berikutnya</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  `;

  const loadOfficerDetail = async (officerId) => {
    if (!officerId) return;
    try {
      const data = await AdminAPI.request(`/api/admin/officers/${officerId}`);
      detailState.detail = data;
      renderDetailContent();
      await loadOfficerTasks(officerId);
      console.info('[AdminManageOfficers] detail loaded', { officerId });
    } catch (error) {
      console.error('[AdminManageOfficers] detail error', error);
      detailState.detail = null;
      detailState.tasks = [];
      if (detailRefs.skeleton) detailRefs.skeleton.classList.remove('hidden');
      if (detailRefs.content) detailRefs.content.classList.add('hidden');
      if (detailRefs.error) {
        detailRefs.error.textContent = error?.message || 'Gagal memuat detail petugas.';
        detailRefs.error.classList.remove('hidden');
      }
      if (detailRefs.tasksLoader) detailRefs.tasksLoader.classList.add('hidden');
      if (detailRefs.tasksContainer) {
        detailRefs.tasksContainer.innerHTML = '';
        detailRefs.tasksContainer.classList.remove('hidden');
      }
    }
  };

  const loadOfficerTasks = async (officerId) => {
    if (!officerId || !detailRefs.tasksContainer) return;
    detailRefs.tasksLoader?.classList.remove('hidden');
    detailRefs.tasksContainer?.classList.add('hidden');

    try {
      const params = new URLSearchParams({
        page: detailState.tasksPage,
        limit: detailState.tasksLimit,
      });
      const data = await AdminAPI.request(`/api/admin/officers/${officerId}/tasks?${params.toString()}`);
      detailState.tasks = Array.isArray(data?.records) ? data.records : [];
      detailState.tasksTotalPage = typeof data?.total_page === 'number' ? (data.total_page > 0 ? data.total_page : 1) : 1;
      detailState.tasksTotalData = typeof data?.total_data === 'number' ? data.total_data : detailState.tasks.length;
      renderTasksList();
    } catch (error) {
      console.error('[AdminManageOfficers] task error', error);
      detailState.tasks = [];
      detailRefs.tasksContainer.innerHTML = `
        <p class="text-sm text-red-600">Tidak dapat memuat tugas aktif. ${error?.message || ''}</p>
      `;
      detailRefs.tasksContainer.classList.remove('hidden');
    } finally {
      detailRefs.tasksLoader?.classList.add('hidden');
      updateTaskPaginationControls();
    }
  };

  const renderDetailContent = () => {
    if (!detailState.detail || !detailRefs.content) return;
    const officer = detailState.detail.officer || {};
    const stats = detailState.detail.statistics || {};

    detailRefs.name.textContent = officer.name || '-';
    detailRefs.department.textContent = officer.department || '-';
    detailRefs.employeeId.textContent = officer.employee_id ? `ID Pegawai: ${officer.employee_id}` : 'ID Pegawai: -';
    detailRefs.address.textContent = officer.address || '-';
    detailRefs.specialization.textContent = formatSpecialization(officer.specialization);
    detailRefs.createdAt.textContent = AdminAPI.formatDateTime(officer.created_at);
    detailRefs.updatedAt.textContent = officer.updated_at ? AdminAPI.formatDateTime(officer.updated_at) : 'Belum ada perubahan';

    if (detailRefs.email) {
      detailRefs.email.href = officer.email ? `mailto:${officer.email}` : '#';
      detailRefs.email.querySelector('span').textContent = officer.email || '-';
    }
    if (detailRefs.phone) {
      detailRefs.phone.href = officer.phone ? `tel:${officer.phone}` : '#';
      detailRefs.phone.querySelector('span').textContent = officer.phone || '-';
    }

    const statusMeta = getStatusMeta(officer.status);
    if (detailRefs.statusBadge) {
      detailRefs.statusBadge.textContent = statusMeta.text;
      detailRefs.statusBadge.className = `px-3 py-1 rounded-full text-xs font-semibold ${statusMeta.classes}`;
    }

    detailRefs.statActive.textContent = stats?.active_tasks ?? 0;
    detailRefs.statFinished.textContent = stats?.finished_tasks ?? 0;
    detailRefs.statTotal.textContent = stats?.total_tasks ?? 0;

    detailRefs.skeleton?.classList.add('hidden');
    detailRefs.content.classList.remove('hidden');
    detailRefs.error?.classList.add('hidden');
  };

  const renderTasksList = () => {
    if (!detailRefs.tasksContainer) return;
    if (!detailState.tasks.length) {
      detailRefs.tasksContainer.innerHTML = '<p class="text-sm text-gray-500">Petugas tidak memiliki tugas aktif saat ini.</p>';
      detailRefs.tasksContainer.classList.remove('hidden');
      return;
    }

    detailRefs.tasksContainer.innerHTML = detailState.tasks
      .map((task) => {
        const statusLabel = task.status ? task.status.replace(/_/g, ' ') : '-';
        return `
          <div class="border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row md:items-center gap-4">
            <div class="flex-1">
              <p class="font-semibold text-gray-900">${task.title}</p>
              <p class="text-sm text-gray-500">Pengaduan #${task.complaint_id} • ${task.category || '-'}</p>
              <p class="text-xs text-gray-400">Mulai ${AdminAPI.formatDateTime(task.started_at)}</p>
            </div>
            <div class="flex flex-col items-start gap-2">
              <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 capitalize">${statusLabel}</span>
              <a href="admin-detail-tiket.php?id=${task.complaint_id}" class="text-sm text-blue-600 hover:underline">Lihat Tiket</a>
            </div>
          </div>
        `;
      })
      .join('');
    detailRefs.tasksContainer.classList.remove('hidden');
  };

  const updateTaskPaginationControls = () => {
    if (!detailRefs.tasksPageInfo) return;
    detailRefs.tasksPageInfo.textContent = `Halaman ${detailState.tasksPage} dari ${detailState.tasksTotalPage}`;

    if (detailRefs.taskPrevBtn) {
      const disabled = detailState.tasksPage <= 1;
      detailRefs.taskPrevBtn.disabled = disabled;
      detailRefs.taskPrevBtn.classList.toggle('opacity-50', disabled);
    }

    if (detailRefs.taskNextBtn) {
      const disabled = detailState.tasksPage >= detailState.tasksTotalPage;
      detailRefs.taskNextBtn.disabled = disabled;
      detailRefs.taskNextBtn.classList.toggle('opacity-50', disabled);
    }
  };

  const handleTaskPagination = (direction) => {
    if (!detailState.officerId) return;
    if (direction === 'prev' && detailState.tasksPage > 1) {
      detailState.tasksPage -= 1;
      loadOfficerTasks(detailState.officerId);
    }
    if (direction === 'next' && detailState.tasksPage < detailState.tasksTotalPage) {
      detailState.tasksPage += 1;
      loadOfficerTasks(detailState.officerId);
    }
  };

  const getStatusMeta = (status) => {
    if (status === 'tersedia') {
      return { text: 'Tersedia', classes: 'bg-green-100 text-green-700', icon: 'check_circle' };
    }
    if (status === 'sibuk') {
      return { text: 'Sedang Bertugas', classes: 'bg-amber-100 text-amber-700', icon: 'schedule' };
    }
    return { text: 'Status tidak diketahui', classes: 'bg-gray-100 text-gray-600', icon: 'help_outline' };
  };

  const getStatusBadgeHtml = (status) => {
    const meta = getStatusMeta(status);
    return `<span class="px-3 py-1 rounded-full flex items-center gap-1 text-xs font-semibold ${meta.classes}"><i class="material-icons text-sm">${meta.icon}</i>${meta.text}</span>`;
  };

  const formatSpecialization = (value) => {
    if (!value) return '-';
    return value
      .split('_')
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
      .join(' ');
  };
})();
