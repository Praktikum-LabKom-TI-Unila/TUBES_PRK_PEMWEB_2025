'use strict';

(function () {
  const STATUS_FLOW = [
    { key: 'diajukan', label: 'Diajukan', color: 'blue' },
    { key: 'diverifikasi_admin', label: 'Diverifikasi Admin', color: 'indigo' },
    { key: 'ditugaskan_ke_petugas', label: 'Ditugaskan ke Petugas', color: 'orange' },
    { key: 'dalam_proses', label: 'Dalam Proses', color: 'amber' },
    { key: 'menunggu_validasi_admin', label: 'Menunggu Validasi Admin', color: 'rose' },
    { key: 'selesai', label: 'Selesai', color: 'green' },
  ];

  const STATUS_META = {
    diajukan: { badge: 'bg-blue-100 text-blue-700', info: 'Pengaduan baru diajukan.' },
    diverifikasi_admin: { badge: 'bg-indigo-100 text-indigo-700', info: 'Pengaduan sudah diverifikasi admin.' },
    ditugaskan_ke_petugas: { badge: 'bg-orange-100 text-orange-700', info: 'Menunggu petugas memulai.' },
    dalam_proses: { badge: 'bg-amber-100 text-amber-700', info: 'Petugas sedang mengerjakan tugas.' },
    menunggu_validasi_admin: { badge: 'bg-rose-100 text-rose-700', info: 'Menunggu validasi admin atas bukti penyelesaian.' },
    selesai: { badge: 'bg-green-100 text-green-700', info: 'Pengaduan telah tuntas.' },
    ditolak_admin: { badge: 'bg-red-100 text-red-700', info: 'Pengaduan ditolak admin.' },
  };

  const DOM = {
    root: document.getElementById('detailShell'),
    skeleton: document.getElementById('detailSkeleton'),
    alert: document.getElementById('pageAlert'),
    headerTitle: document.getElementById('ticketHeaderTitle'),
    headerReporter: document.getElementById('ticketHeaderReporter'),
    statusBadge: document.getElementById('statusBadgeContainer'),
    ticketImage: document.getElementById('ticketImage'),
    ticketTitle: document.getElementById('ticketTitle'),
    ticketCreatedAt: document.getElementById('ticketCreatedAt'),
    ticketCategory: document.getElementById('ticketCategory'),
    ticketDescription: document.getElementById('ticketDescription'),
    ticketLocation: document.getElementById('ticketLocation'),
    ticketMap: document.getElementById('ticketMap'),
    reporterName: document.getElementById('reporterName'),
    reporterEmail: document.getElementById('reporterEmail'),
    reporterPhone: document.getElementById('reporterPhone'),
    officerCard: document.getElementById('assignedOfficerCard'),
    officerName: document.getElementById('officerName'),
    officerMeta: document.getElementById('officerMeta'),
    officerStatus: document.getElementById('officerStatus'),
    actionContainer: document.getElementById('actionButtonsContainer'),
    timelineContainer: document.getElementById('timelineContainer'),
    proofSection: document.getElementById('proofSection'),
    proofList: document.getElementById('proofList'),
    proofMeta: document.getElementById('proofMeta'),
    modalRoot: document.getElementById('modalRoot'),
  };

  const state = {
    ticketId: '',
    token: '',
    detail: null,
    proofs: [],
  };

  const mapDefaults = { lat: -6.2, lng: 106.81 };
  let mapInstance = null;
  let mapMarker = null;

  document.addEventListener('DOMContentLoaded', init);

  function init() {
    state.ticketId = (window.__ADMIN_TICKET_ID__ || '').trim() || getTicketIdFromUrl();
    if (!state.ticketId) {
      setPageAlert('error', 'ID tiket tidak ditemukan pada URL.');
      toggleShell(false);
      return;
    }

    state.token = getAdminToken();
    if (!state.token) {
      redirectToLogin();
      return;
    }

    initMap();
    fetchTicketDetail();
  }

  async function fetchTicketDetail() {
    toggleSkeleton(true);
    setPageAlert('info', 'Memuat detail tiket...');
    try {
      const data = await requestWithAuth(`/api/admin/tickets/${state.ticketId}`, state.token);
      state.detail = data || {};
      renderTicketDetail();
      setPageAlert();

      const status = state.detail?.ticket?.status;
      if (status === 'menunggu_validasi_admin' || status === 'selesai') {
        await fetchProofs();
      } else {
        renderProofs(state.detail?.completion_proofs || []);
      }
    } catch (error) {
      console.error('[AdminDetailTicket] fetch detail error', error);
      setPageAlert('error', error?.message || 'Gagal memuat detail tiket.');
      toggleShell(false);
    } finally {
      toggleSkeleton(false);
    }
  }

  async function fetchProofs() {
    try {
      const data = await requestWithAuth(`/api/admin/tickets/${state.ticketId}/proof`, state.token);
      const records = data?.records || [];
      renderProofs(records);
    } catch (error) {
      console.warn('[AdminDetailTicket] proof fetch error', error);
      setPageAlert('error', 'Bukti penyelesaian gagal dimuat.');
    }
  }

  function renderTicketDetail() {
    const ticket = state.detail?.ticket || {};
    const reporter = state.detail?.reporter || {};
    const officer = state.detail?.officer || null;
    const timeline = state.detail?.timeline || [];

    document.title = `Detail Tiket #${ticket?.id || '-'} | SIPINDA`;
    DOM.headerTitle.textContent = `Detail Tiket #${ticket?.id || '-'}`;
    DOM.headerReporter.textContent = reporter?.name || 'Pelapor tidak diketahui';

    renderStatusBadge(ticket?.status);

    DOM.ticketImage.src = normalizeImageUrl(ticket?.photo_before) || 'https://placehold.co/1200x600?text=Tidak+ada+foto';
    DOM.ticketImage.alt = ticket?.title || 'Foto Pengaduan';
    DOM.ticketTitle.textContent = ticket?.title || 'Tanpa judul';
    DOM.ticketCreatedAt.textContent = formatDate(ticket?.created_at);
    DOM.ticketCategory.textContent = ticket?.category || '-';
    DOM.ticketDescription.textContent = ticket?.description || 'Deskripsi tidak tersedia.';
    DOM.ticketLocation.textContent = ticket?.address || '-';
    handleLocationUpdate(ticket);

    DOM.reporterName.textContent = reporter?.name || '-';
    DOM.reporterEmail.textContent = reporter?.email || '-';
    DOM.reporterPhone.textContent = reporter?.phone || '-';

    renderOfficerCard(officer);
    renderTimeline(timeline, ticket?.status);
    renderActions(ticket);
  }

  function renderStatusBadge(status) {
    if (!status) {
      DOM.statusBadge.textContent = 'Status tidak diketahui';
      return;
    }

    const meta = STATUS_META[status] || { badge: 'bg-gray-100 text-gray-700', info: '' };
    DOM.statusBadge.innerHTML = `<span class="px-3 py-1 rounded-full text-sm font-semibold ${meta.badge}">${formatStatusText(status)}</span>`;
  }

  function renderOfficerCard(officer) {
    if (!officer) {
      DOM.officerCard.classList.add('hidden');
      return;
    }

    DOM.officerName.textContent = officer?.name || '-';
    const department = officer?.department ? officer.department : 'Departemen tidak diketahui';
    const specialization = officer?.specialization ? ` • ${officer.specialization}` : '';
    DOM.officerMeta.textContent = `${department}${specialization}`;
    DOM.officerStatus.textContent = officer?.email ? `Email: ${officer.email}` : 'Kontak petugas tidak tersedia';
    DOM.officerCard.classList.remove('hidden');
  }

  function renderTimeline(timeline = [], currentStatus = '') {
    const timelineMap = {};
    (timeline || []).forEach((item) => {
      if (item?.status) timelineMap[item.status] = item;
    });

    const currentIndex = STATUS_FLOW.findIndex((step) => step.key === currentStatus);
    const rows = STATUS_FLOW.map((step, index) => {
      const isActive = index <= currentIndex;
      const entry = timelineMap[step.key];
      const color = getColorClasses(step.color, isActive);
      const dateLabel = entry?.created_at ? formatDateTime(entry.created_at) : 'Belum dilakukan';
      const actor = entry?.created_by?.name || entry?.created_by?.id || '-';
      const note = entry?.note || STATUS_META[step.key]?.info || '';

      return `
                <div class="flex items-start gap-3">
                    <div class="flex flex-col items-center">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-semibold ${color.dot}">${index + 1}</span>
                        ${
                          index < STATUS_FLOW.length - 1
                            ? `<span class="w-px flex-1 ${isActive ? 'bg-' + step.color + '-300' : 'bg-gray-200'}"></span>`
                            : ''
                        }
                    </div>
                    <div class="pb-4">
                        <p class="text-sm font-semibold ${color.text}">${step.label}</p>
                        <p class="text-xs text-gray-500">${dateLabel}${actor ? ` • ${actor}` : ''}</p>
                        ${note ? `<p class="text-gray-600 text-sm mt-1">${note}</p>` : ''}
                    </div>
                </div>
            `;
    }).join('');

    DOM.timelineContainer.innerHTML = rows;
  }

  function renderActions(ticket) {
    const status = ticket?.status;
    DOM.actionContainer.innerHTML = '';

    if (!status) {
      DOM.actionContainer.innerHTML = '<p class="text-sm text-gray-500">Status tiket tidak diketahui.</p>';
      return;
    }

    switch (status) {
      case 'diajukan':
        DOM.actionContainer.appendChild(createPrimaryButton('Verifikasi & Terima', 'shield', () => handleVerify()));
        DOM.actionContainer.appendChild(createDangerButton('Tolak Pengaduan', 'cancel', () => openRejectModal()));
        break;
      case 'diverifikasi_admin':
        DOM.actionContainer.appendChild(createAccentButton('Tugaskan Petugas', 'person_add', () => openAssignOfficerModal()));
        break;
      case 'ditugaskan_ke_petugas':
        DOM.actionContainer.appendChild(
          createInfoBox(
            '⏳ Menunggu petugas memulai',
            'bg-yellow-50 border-yellow-200 text-yellow-800',
            'Petugas telah menerima tugas dan akan segera memulai.'
          )
        );
        break;
      case 'dalam_proses':
        DOM.actionContainer.appendChild(
          createInfoBox(
            '🛠️ Petugas sedang bekerja',
            'bg-amber-50 border-amber-200 text-amber-800',
            'Pantau bukti penyelesaian yang akan dikirimkan petugas.'
          )
        );
        break;
      case 'menunggu_validasi_admin':
        DOM.actionContainer.appendChild(createDangerButton('Validasi Penyelesaian', 'check_circle', () => handleValidate()));
        DOM.actionContainer.appendChild(
          createInfoBox('📌 Periksa bukti sebelum validasi', 'bg-rose-50 border-rose-200 text-rose-700', 'Pastikan bukti penyelesaian sudah lengkap.')
        );
        break;
      case 'selesai':
        DOM.actionContainer.appendChild(
          createInfoBox('✅ Pengaduan telah selesai', 'bg-green-50 border-green-200 text-green-700', 'Tidak ada aksi lanjutan.')
        );
        break;
      case 'ditolak_admin':
        DOM.actionContainer.appendChild(
          createInfoBox('❌ Pengaduan ditolak', 'bg-red-50 border-red-200 text-red-700', 'Status final, tidak ada aksi lanjutan.')
        );
        break;
      default:
        DOM.actionContainer.appendChild(
          createInfoBox('ℹ️ Aksi belum tersedia', 'bg-gray-50 border-gray-200 text-gray-600', 'Hubungi administrator untuk status ini.')
        );
    }
  }

  function renderProofs(records = []) {
    if (!records.length) {
      DOM.proofSection.classList.add('hidden');
      DOM.proofList.innerHTML = '';
      return;
    }

    DOM.proofSection.classList.remove('hidden');
    DOM.proofMeta.textContent = `${records.length} bukti penyelesaian`;
    const cards = records
      .map((proof) => {
        const img = normalizeImageUrl(proof?.photo) || 'https://placehold.co/600x400?text=Tidak+ada+foto';
        const note = proof?.notes || 'Tidak ada catatan.';
        const officerName = proof?.officer?.name || '-';
        const submittedAt = formatDateTime(proof?.submitted_at);

        return `
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <img src="${img}" alt="Bukti Penyelesaian" class="w-full h-56 object-cover">
                    <div class="p-4 space-y-1 text-sm text-gray-600">
                        <p class="font-semibold text-gray-900">${officerName}</p>
                        <p>${note}</p>
                        <p class="text-xs text-gray-400">Dikirim pada ${submittedAt}</p>
                    </div>
                </div>
            `;
      })
      .join('');

    DOM.proofList.innerHTML = cards;
  }

  async function handleVerify() {
    await performAction({
      buttonSelector: '#actionButtonsContainer button:first-child',
      endpoint: `/api/admin/tickets/${state.ticketId}/verify`,
      successMessage: 'Pengaduan berhasil diverifikasi.',
    });
  }

  function openRejectModal() {
    const modal = createModal({
      title: 'Tolak Pengaduan',
      body: `
                <p class="text-sm text-gray-600 mb-3">Opsional: jelaskan alasan penolakan agar pelapor mendapat informasi yang jelas.</p>
                <textarea id="rejectReasonInput" rows="4" class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-red-500" placeholder="Tambahkan catatan penolakan"></textarea>
            `,
      primaryText: 'Kirim Penolakan',
      primaryClass: 'bg-red-600 hover:bg-red-700 text-white',
      onPrimary: async (modalEl) => {
        const reason = modalEl.querySelector('#rejectReasonInput').value.trim();
        await performAction({
          buttonSelector: '#modalRoot button[data-modal-primary] ',
          endpoint: `/api/admin/tickets/${state.ticketId}/reject`,
          method: 'POST',
          body: { reason },
          successMessage: 'Pengaduan berhasil ditolak.',
        });
        closeModal();
      },
    });
    DOM.modalRoot.innerHTML = '';
    DOM.modalRoot.appendChild(modal);
  }

  function openAssignOfficerModal() {
    const modal = createModal({
      title: 'Tugaskan Petugas',
      body: '<div id="officerList" class="space-y-3 text-sm text-gray-600">Memuat daftar petugas...</div>',
      showFooter: false,
    });
    DOM.modalRoot.innerHTML = '';
    DOM.modalRoot.appendChild(modal);
    loadAvailableOfficers(modal.querySelector('#officerList'));
  }

  async function loadAvailableOfficers(container) {
    try {
      const data = await requestWithAuth('/api/admin/officers/available', state.token);
      const records = data?.records || [];
      if (!records.length) {
        container.innerHTML = '<p class="text-sm text-gray-500">Tidak ada petugas yang tersedia.</p>';
        return;
      }

      container.innerHTML = records
        .map(
          (officer) => `
                <button data-officer-id="${
                  officer.id
                }" class="w-full text-left border border-gray-200 rounded-xl p-4 hover:border-blue-500 hover:bg-blue-50 transition flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-gray-900">${officer.name}</p>
                        <p class="text-xs text-gray-500">${officer.department || '-'} | ${officer.specialization || '-'}</p>
                        <p class="text-xs text-gray-400">Tugas aktif: ${officer.active_tasks ?? 0}</p>
                    </div>
                    <span class="material-icons text-blue-500">chevron_right</span>
                </button>
            `
        )
        .join('');

      container.querySelectorAll('button[data-officer-id]').forEach((button) => {
        button.addEventListener('click', async () => {
          const officerId = button.getAttribute('data-officer-id');
          await performAction({
            endpoint: `/api/admin/tickets/${state.ticketId}/assign-officer`,
            method: 'POST',
            body: { officer_id: officerId },
            successMessage: 'Petugas berhasil ditugaskan.',
          });
          closeModal();
        });
      });
    } catch (error) {
      container.innerHTML = `<p class="text-sm text-red-600">Gagal memuat petugas: ${error?.message || 'unknown error'}</p>`;
    }
  }

  async function handleValidate() {
    await performAction({
      buttonSelector: '#actionButtonsContainer button:first-child',
      endpoint: `/api/admin/tickets/${state.ticketId}/validate`,
      successMessage: 'Penyelesaian berhasil divalidasi.',
    });
  }

  async function performAction({ buttonSelector, endpoint, method = 'POST', body = {}, successMessage = 'Berhasil diperbarui.' }) {
    const button = buttonSelector ? document.querySelector(buttonSelector) : null;
    setButtonLoading(button, true);
    try {
      await requestWithAuth(endpoint, state.token, {
        method,
        body: Object.keys(body || {}).length ? JSON.stringify(body) : undefined,
        headers: { 'Content-Type': 'application/json' },
      });
      setPageAlert('success', successMessage);
      await fetchTicketDetail();
    } catch (error) {
      console.error('[AdminDetailTicket] action error', error);
      setPageAlert('error', error?.message || 'Aksi gagal diproses.');
    } finally {
      setButtonLoading(button, false);
    }
  }

  function initMap() {
    if (!DOM.ticketMap || typeof window.L === 'undefined') return;
    if (mapInstance) return;

    mapInstance = L.map('ticketMap', {
      attributionControl: false,
      zoomControl: true,
    }).setView([mapDefaults.lat, mapDefaults.lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
    }).addTo(mapInstance);

  }

  function handleLocationUpdate(ticket) {
    const lat = ticket?.location?.latitude;
    const lng = ticket?.location?.longitude;
    const address = ticket?.address;
    if (!mapInstance) {
      initMap();
    }
    if (Number.isFinite(lat) && Number.isFinite(lng)) {
      updateMapAndFields(lat, lng, address);
      return;
    }
    if (address) {
      geocodeAddress(address).then((result) => {
        if (result) {
          const geolat = Number(result.lat);
          const geolng = Number(result.lon);
          if (Number.isFinite(geolat) && Number.isFinite(geolng)) {
            updateMapAndFields(geolat, geolng, result.display_name || address);
            return;
          }
        }
        updateGeoLabels(null, null, address);
      });
      return;
    }
    updateGeoLabels(null, null, 'Alamat tidak tersedia.');
  }

  function updateMapAndFields(lat, lng, displayName) {
    if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
    if (!mapInstance) {
      initMap();
    }
    updateGeoLabels(lat, lng, displayName);
    placeMapMarker(lat, lng);
  }

  function placeMapMarker(lat, lng) {
    if (!mapInstance || !Number.isFinite(lat) || !Number.isFinite(lng)) return;
    const coords = [lat, lng];
    if (mapMarker) {
      mapMarker.setLatLng(coords);
    } else {
      mapMarker = L.marker(coords).addTo(mapInstance);
    }
    mapInstance.setView(coords, 15);
  }

  async function geocodeAddress(query) {
    if (!query) return null;
    try {
      const response = await fetch(
        `https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(query)}`,
        { headers: { Accept: 'application/json' } }
      );
      if (!response.ok) return null;
      const data = await response.json();
      return Array.isArray(data) && data.length ? data[0] : null;
    } catch (error) {
      console.warn('[AdminDetailTicket] geocode error', error);
      return null;
    }
  }

  function updateGeoLabels(lat, lng, description) {
    if (description && DOM.ticketLocation) {
      DOM.ticketLocation.textContent = description;
    } else if (!description && DOM.ticketLocation && !DOM.ticketLocation.textContent) {
      DOM.ticketLocation.textContent = '-';
    }
  }

  function createPrimaryButton(text, icon, handler) {
    return createButton(text, icon, 'bg-indigo-600 hover:bg-indigo-700 text-white', handler);
  }

  function createDangerButton(text, icon, handler) {
    return createButton(text, icon, 'bg-red-600 hover:bg-red-700 text-white', handler);
  }

  function createAccentButton(text, icon, handler) {
    return createButton(text, icon, 'bg-amber-600 hover:bg-amber-700 text-white', handler);
  }

  function createButton(text, icon, classes, handler) {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = `w-full py-3 rounded-lg transition-colors flex items-center justify-center gap-2 font-semibold ${classes}`;
    button.innerHTML = `<i class="material-icons">${icon}</i> ${text}`;
    button.addEventListener('click', handler);
    return button;
  }

  function createInfoBox(title, classes, subtitle) {
    const div = document.createElement('div');
    div.className = `p-4 border rounded-lg text-sm ${classes}`;
    div.innerHTML = `<p class="font-semibold mb-1">${title}</p>${subtitle ? `<p>${subtitle}</p>` : ''}`;
    return div;
  }

  function createModal({
    title,
    body,
    primaryText = 'Simpan',
    primaryClass = 'bg-blue-600 hover:bg-blue-700 text-white',
    onPrimary,
    showFooter = true,
  }) {
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 bg-black/50 z-50 flex items-center justify-center px-4';
    overlay.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">${title}</h3>
                    <button class="text-gray-400 hover:text-gray-600" data-modal-close>&times;</button>
                </div>
                <div class="p-6 space-y-4 text-sm text-gray-600">${body}</div>
                ${
                  showFooter
                    ? `
                <div class="p-6 border-t border-gray-100 flex justify-end gap-3">
                    <button class="px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50" data-modal-close>Batalkan</button>
                    <button class="px-4 py-2 rounded-lg font-semibold ${primaryClass}" data-modal-primary>${primaryText}</button>
                </div>`
                    : ''
                }
            </div>
        `;

    overlay.addEventListener('click', (event) => {
      if (event.target === overlay || event.target.hasAttribute('data-modal-close')) {
        closeModal();
      }
    });

    if (showFooter && typeof onPrimary === 'function') {
      overlay.querySelector('[data-modal-primary]').addEventListener('click', async () => {
        await onPrimary(overlay);
      });
    }

    return overlay;
  }

  function closeModal() {
    DOM.modalRoot.innerHTML = '';
  }

  function setButtonLoading(button, isLoading) {
    if (!button) return;
    button.disabled = isLoading;
    button.classList.toggle('opacity-70', isLoading);
  }

  function setPageAlert(type, message) {
    if (!message) {
      DOM.alert.classList.add('hidden');
      DOM.alert.innerHTML = '';
      return;
    }

    const palette =
      type === 'success'
        ? 'bg-green-50 border-green-200 text-green-700'
        : type === 'error'
        ? 'bg-red-50 border-red-200 text-red-700'
        : 'bg-blue-50 border-blue-200 text-blue-700';

    DOM.alert.className = `max-w-5xl mx-auto mt-4 px-4 ${palette} border rounded-xl py-3`;
    DOM.alert.textContent = message;
    DOM.alert.classList.remove('hidden');
  }

  function toggleShell(show) {
    if (show) {
      DOM.root.classList.remove('hidden');
    } else {
      DOM.root.classList.add('hidden');
    }
  }

  function toggleSkeleton(show) {
    if (show) {
      DOM.skeleton.classList.remove('hidden');
    } else {
      DOM.skeleton.classList.add('hidden');
    }
  }

  async function requestWithAuth(endpoint, token, options = {}) {
    const response = await fetch(`${resolveBackendBaseUrl()}${endpoint}`, {
      method: options.method || 'GET',
      headers: {
        Authorization: `Bearer ${token}`,
        ...(options.headers || {}),
      },
      mode: 'cors',
      body: options.body,
    });

    const payload = await safeParseJson(response);

    if (response.status === 401) {
      redirectToLogin();
      throw new Error('Sesi berakhir, silakan login ulang.');
    }

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
      console.warn('[AdminDetailTicket] gagal parse JSON', error);
      return {};
    }
  }

  function buildApiErrorMessage(payload, response) {
    const code = payload?.code || response?.status;
    const baseMessage = payload?.message || 'Permintaan gagal diproses.';
    const details = Array.isArray(payload?.errors) ? payload.errors.map((err) => err?.message).filter(Boolean) : [];
    const suffix = details.length ? ` Detail: ${details.join(', ')}` : '';
    return `${baseMessage} (kode ${code}).${suffix}`;
  }

  function getAdminToken() {
    return localStorage.getItem('sipinda_admin_token') || sessionStorage.getItem('sipinda_admin_token');
  }

  function redirectToLogin() {
    window.location.replace('admin-login.php');
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
    const backendPort = hostname === 'localhost' ? 9090 : window.location.port || 80;
    return `${protocol}//${hostname}:${backendPort}`;
  }

  function formatDate(value) {
    if (!value) return '-';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '-';
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'long' }).format(date);
  }

  function formatDateTime(value) {
    if (!value) return '-';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '-';
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(date);
  }

  function formatStatusText(status) {
    return status ? status.replace(/_/g, ' ').replace(/\b\w/g, (char) => char.toUpperCase()) : '-';
  }

  function normalizeImageUrl(rawPath) {
    if (!rawPath) return '';
    const cleanedPath = rawPath.replace(/\/admin(?=\/uploads)/, '');
    const backendUrl = resolveBackendBaseUrl();
    const backendParsed = new URL(backendUrl);
    try {
      const parsed = new URL(cleanedPath, backendUrl);
      parsed.protocol = backendParsed.protocol;
      parsed.hostname = backendParsed.hostname;
      const fallbackPort = backendParsed.hostname === 'localhost' ? '9090' : backendParsed.port;
      parsed.port = fallbackPort || (backendParsed.protocol === 'https:' ? '443' : '80');
      return parsed.toString();
    } catch (error) {
      if (cleanedPath.startsWith('/')) {
        return `${backendUrl}${cleanedPath}`;
      }
      return `${backendUrl}/${cleanedPath}`;
    }
  }

  function getColorClasses(color, isActive) {
    const map = {
      blue: { dot: isActive ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500', text: isActive ? 'text-blue-700' : 'text-gray-500' },
      indigo: { dot: isActive ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500', text: isActive ? 'text-indigo-700' : 'text-gray-500' },
      orange: { dot: isActive ? 'bg-orange-500 text-white' : 'bg-gray-200 text-gray-500', text: isActive ? 'text-orange-700' : 'text-gray-500' },
      amber: { dot: isActive ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-500', text: isActive ? 'text-amber-700' : 'text-gray-500' },
      rose: { dot: isActive ? 'bg-rose-500 text-white' : 'bg-gray-200 text-gray-500', text: isActive ? 'text-rose-700' : 'text-gray-500' },
      green: { dot: isActive ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-500', text: isActive ? 'text-green-700' : 'text-gray-500' },
    };
    return map[color] || { dot: 'bg-gray-200 text-gray-500', text: 'text-gray-500' };
  }

  function getTicketIdFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get('id') || '';
  }
})();
