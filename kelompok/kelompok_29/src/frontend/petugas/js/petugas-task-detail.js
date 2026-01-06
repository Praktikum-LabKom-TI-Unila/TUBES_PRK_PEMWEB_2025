const params = new URLSearchParams(window.location.search);
const taskId = params.get('id');
const placeholderImage = 'https://placehold.co/600x300?text=Foto';

const DOM = {
    titleHeading: document.getElementById('taskTitleHeading'),
    statusBadge: document.getElementById('statusBadgeContainer'),
    actionButtons: document.getElementById('actionButtonsContainer'),
    heroImage: document.getElementById('taskHeroImage'),
    title: document.getElementById('taskTitle'),
    date: document.getElementById('taskDate'),
    category: document.getElementById('taskCategory'),
    description: document.getElementById('taskDescription'),
    address: document.getElementById('taskAddress'),
    map: document.getElementById('taskMap'),
    mapAddressText: document.getElementById('mapAddressText'),
    reporterName: document.getElementById('reporterName'),
    reporterEmail: document.getElementById('reporterEmail'),
    timeline: document.getElementById('timelineContainer'),
    alert: document.getElementById('taskAlert'),
    completionWrapper: document.getElementById('completionProofWrapper'),
    completionImage: document.getElementById('completionImage'),
    completionNotes: document.getElementById('completionNotes'),
    completionStatusLabel: document.getElementById('completionStatusLabel'),
    editProofButton: document.getElementById('editProofButton')
};

let currentTask = null;
let mapInstance = null;
let mapMarker = null;

function showTaskAlert(message, type = 'error') {
    if (!DOM.alert) return;
    DOM.alert.textContent = message;
    DOM.alert.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'border-red-200', 'bg-green-50', 'text-green-700', 'border-green-200');
    if (type === 'success') {
        DOM.alert.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
    } else {
        DOM.alert.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
    }
}

function clearTaskAlert() {
    if (!DOM.alert) return;
    DOM.alert.classList.add('hidden');
    DOM.alert.textContent = '';
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
}

function formatDateTime(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
}

function getStatusBadgeHtml(status = '') {
    const normalized = status.replace(/_/g, '-');
    if (!normalized) return '';
    let classes = 'inline-block px-3 py-1 rounded-lg font-medium text-sm ';
    if (normalized.includes('proses') || normalized.includes('ditugaskan')) classes += 'bg-yellow-100 text-yellow-700';
    else if (normalized.includes('selesai')) classes += 'bg-green-100 text-green-700';
    else if (normalized.includes('validasi')) classes += 'bg-orange-100 text-orange-700';
    else classes += 'bg-blue-100 text-blue-700';
    return `<span class="${classes}">${normalized.replace(/-/g, ' ')}</span>`;
}

function renderTaskDetail(detail) {
    currentTask = detail;
    DOM.titleHeading.textContent = `#${detail.task_id || detail.complaint_id || '-'}`;
    DOM.statusBadge.innerHTML = getStatusBadgeHtml(detail.status);
    DOM.title.textContent = detail.title || '-';
    DOM.date.textContent = formatDate(detail.timestamps?.complaint_created_at || detail.created_at);
    DOM.category.textContent = detail.category || '-';
    DOM.description.textContent = detail.description || '-';
    DOM.address.innerHTML = `<i class="material-icons text-base text-gray-500">place</i> ${detail.address || 'Lokasi belum tersedia'}`;
    const cover = PetugasAuth.resolveFileUrl(detail.photos?.before || detail.cover_photo || '') || placeholderImage;
    DOM.heroImage.src = cover;
    DOM.heroImage.onerror = () => (DOM.heroImage.src = placeholderImage);

    DOM.reporterName.textContent = detail.reporter?.name || '-';
    DOM.reporterEmail.textContent = detail.reporter?.email || '-';

    if (detail.status === 'menunggu_validasi_admin' || detail.status === 'selesai') {
        loadCompletionProof();
    } else {
        DOM.completionWrapper.classList.add('hidden');
    }

    if (detail.location?.latitude && detail.location?.longitude) {
        initMap(detail.location.latitude, detail.location.longitude, detail.address);
    } else {
        DOM.mapAddressText.textContent = 'Koordinat belum tersedia.';
        DOM.map.textContent = 'Lokasi belum tersedia';
    }
}

function initMap(lat, lng, fallbackAddress = '') {
    if (!DOM.map) return;
    if (!mapInstance) {
        DOM.map.innerHTML = '';
        mapInstance = L.map('taskMap').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(mapInstance);
    } else {
        mapInstance.setView([lat, lng], 15);
    }
    if (mapMarker) {
        mapMarker.setLatLng([lat, lng]);
    } else {
        mapMarker = L.marker([lat, lng]).addTo(mapInstance);
    }
    DOM.mapAddressText.textContent = fallbackAddress || 'Memuat alamat terdekat...';
    updateMapAddressFromGeocode(lat, lng);
}

async function updateMapAddressFromGeocode(lat, lng) {
    try {
        const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
        const data = await res.json();
        if (data?.display_name) {
            DOM.mapAddressText.textContent = data.display_name;
        }
    } catch (error) {
        console.warn('[Map] Reverse geocode gagal', error);
    }
}

function renderActionButtons(status) {
    if (!DOM.actionButtons) return;
    const buttons = [];
    const navButton = `<button data-action=\"navigate\" class=\"bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 font-semibold\"><i class=\"material-icons\">navigation</i> Arahkan ke Lokasi</button>`;

    switch (status) {
        case 'ditugaskan_ke_petugas':
            buttons.push(`<button data-action=\"start\" class=\"flex-1 bg-yellow-600 text-white py-3 rounded-lg hover:bg-yellow-700 transition-colors flex items-center justify-center gap-2 font-semibold\"><i class=\"material-icons\">play_arrow</i> Mulai Proses</button>`);
            buttons.push(navButton);
            break;
        case 'dalam_proses':
            buttons.push(`<button data-action=\"cancel\" class=\"flex-1 bg-white border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2 font-semibold\"><i class=\"material-icons\">close</i> Batal Proses</button>`);
            buttons.push(`<button data-action=\"upload\" class=\"flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2 font-semibold\"><i class=\"material-icons\">cloud_upload</i> Upload Bukti</button>`);
            buttons.push(navButton);
            break;
        case 'menunggu_validasi_admin':
            buttons.push(`<div class=\"flex-1 bg-orange-50 border border-orange-200 rounded-lg p-3 text-orange-800 font-medium text-sm text-center\">⏳ Menunggu validasi admin</div>`);
            buttons.push(`<button data-action=\"upload\" class=\"bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2 font-semibold\"><i class=\"material-icons\">edit</i> Perbarui Bukti</button>`);
            break;
        case 'selesai':
            buttons.push(`<div class=\"w-full bg-green-50 border border-green-200 rounded-lg p-3 text-green-800 font-medium text-center\">✅ Tugas telah divalidasi admin</div>`);
            break;
        default:
            buttons.push(navButton);
    }

    DOM.actionButtons.innerHTML = buttons.join('');
}

function renderTimeline(records = []) {
    if (!DOM.timeline) return;
    if (!records.length) {
        DOM.timeline.innerHTML = '<p class=\"text-sm text-gray-500\">Timeline belum tersedia.</p>';
        return;
    }
    const rows = records.map(item => {
        const isCompleted = ['selesai', 'menunggu_validasi_admin', 'dalam_proses'].includes(item.status);
        return `
            <div class=\"flex gap-4 relative\">
                <div class=\"flex flex-col items-center\">
                    <div class=\"w-8 h-8 rounded-full flex items-center justify-center text-xl border-2 border-white ${isCompleted ? 'text-blue-600 bg-blue-50' : 'text-gray-400 bg-gray-100'}\">
                        <i class=\"material-icons text-lg\">event</i>
                    </div>
                    <div class=\"flex-grow w-0.5 bg-gray-200\"></div>
                </div>
                <div class=\"flex-1 pb-6 -mt-1\">
                    <p class=\"font-medium text-gray-800\">${item.note || '-'}</p>
                    <p class=\"text-xs text-gray-500\">oleh ${item.created_by?.name || 'Sistem'} · ${formatDateTime(item.created_at)}</p>
                </div>
            </div>
        `;
    }).join('');
    DOM.timeline.innerHTML = rows;
}

async function loadTimeline() {
    if (!taskId) return;
    try {
        const payload = await PetugasAPI.request(`/officer/tasks/${taskId}/timeline`);
        renderTimeline(payload.data?.records || []);
    } catch (error) {
        console.error('[Timeline] error', error);
        DOM.timeline.innerHTML = '<p class=\"text-sm text-red-500\">Gagal memuat timeline.</p>';
    }
}

async function loadCompletionProof() {
    if (!taskId) return;
    try {
        const payload = await PetugasAPI.request(`/officer/tasks/${taskId}/completion-proof`);
        const proof = payload.data?.proof;
        if (!proof) {
            DOM.completionWrapper.classList.add('hidden');
            return;
        }
        DOM.completionWrapper.classList.remove('hidden');
        if (proof.photo_after) {
            DOM.completionImage.src = PetugasAuth.resolveFileUrl(proof.photo_after);
            DOM.completionImage.classList.remove('hidden');
        } else {
            DOM.completionImage.classList.add('hidden');
        }
        DOM.completionNotes.textContent = proof.notes || 'Tidak ada catatan.';
        DOM.completionStatusLabel.textContent = currentTask.status === 'selesai' ? 'Sudah divalidasi' : 'Menunggu validasi admin';
        if (currentTask.status === 'menunggu_validasi_admin') {
            DOM.editProofButton.classList.remove('hidden');
        } else {
            DOM.editProofButton.classList.add('hidden');
        }
    } catch (error) {
        DOM.completionWrapper.classList.add('hidden');
    }
}

async function loadTaskDetail() {
    if (!taskId) {
        showTaskAlert('ID tugas tidak ditemukan.');
        return;
    }
    try {
        clearTaskAlert();
        const payload = await PetugasAPI.request(`/officer/tasks/${taskId}`);
        renderTaskDetail(payload.data || {});
        renderActionButtons(payload.data?.status);
        await loadTimeline();
    } catch (error) {
        console.error('[Detail] load error', error);
        showTaskAlert(error.message || 'Gagal memuat detail tugas.');
    }
}

async function handleStartProcess() {
    if (!taskId) return;
    if (!confirm('Mulai proses tugas ini?')) return;
    try {
        await PetugasAPI.request(`/officer/tasks/${taskId}/start`, {
            method: 'POST',
            body: { note: 'Petugas memulai pengerjaan.' }
        });
        showTaskAlert('Tugas dimulai.', 'success');
        await loadTaskDetail();
    } catch (error) {
        showTaskAlert(error.message || 'Gagal memulai tugas.');
    }
}

async function handleCancelProcess() {
    if (!taskId) return;
    const reason = prompt('Alasan membatalkan proses?');
    if (!reason) return;
    try {
        await PetugasAPI.request(`/officer/tasks/${taskId}/cancel`, {
            method: 'POST',
            body: { reason }
        });
        showTaskAlert('Proses dibatalkan dan dikembalikan ke admin.', 'success');
        await loadTaskDetail();
    } catch (error) {
        showTaskAlert(error.message || 'Gagal membatalkan proses.');
    }
}

function handleUploadProof() {
    window.location.href = `petugas-upload-proof.php?id=${taskId}`;
}

function handleNavigate() {
    if (!currentTask?.location) return;
    const { latitude, longitude } = currentTask.location;
    if (latitude && longitude) {
        window.open(`https://maps.google.com/?q=${latitude},${longitude}`, '_blank');
    } else if (currentTask.address) {
        window.open(`https://maps.google.com/?q=${encodeURIComponent(currentTask.address)}`, '_blank');
    }
}

DOM.actionButtons?.addEventListener('click', (event) => {
    const button = event.target.closest('[data-action]');
    if (!button) return;
    const action = button.dataset.action;
    if (action === 'start') handleStartProcess();
    else if (action === 'cancel') handleCancelProcess();
    else if (action === 'upload') handleUploadProof();
    else if (action === 'navigate') handleNavigate();
});

DOM.editProofButton?.addEventListener('click', handleUploadProof);

document.addEventListener('DOMContentLoaded', () => {
    if (!taskId) {
        showTaskAlert('ID tugas tidak ditemukan.');
        return;
    }
    loadTaskDetail();
});
