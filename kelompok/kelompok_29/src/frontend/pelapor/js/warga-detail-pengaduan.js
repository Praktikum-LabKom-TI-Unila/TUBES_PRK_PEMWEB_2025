const DETAIL_DOM = {
    alert: document.getElementById('detailAlert'),
    statusBadge: document.getElementById('statusBadgeContainer'),
    title: document.getElementById('detailTitle'),
    category: document.getElementById('detailCategory'),
    createdAt: document.getElementById('detailCreatedAt'),
    description: document.getElementById('detailDescription'),
    address: document.getElementById('detailAddress'),
    photoBefore: document.getElementById('detailPhotoBefore'),
    timeline: document.getElementById('timelineContainer'),
    officerSection: document.getElementById('assignedOfficerSection'),
    officerName: document.getElementById('officerName'),
    officerMeta: document.getElementById('officerMeta'),
    completionSection: document.getElementById('completionSection'),
    completionProofs: document.getElementById('completionProofs')
};

const complaintId = window.COMPLAINT_ID || new URLSearchParams(window.location.search).get('id');
let detailMap;
let detailMarker;

function showDetailAlert(message = '', type = 'error') {
    if (!DETAIL_DOM.alert) return;
    if (!message) {
        DETAIL_DOM.alert.classList.add('hidden');
        DETAIL_DOM.alert.textContent = '';
        return;
    }
    const palette =
        type === 'success'
            ? 'bg-green-50 border border-green-200 text-green-700'
            : 'bg-red-50 border border-red-200 text-red-700';
    DETAIL_DOM.alert.className = `p-3 rounded-lg text-sm ${palette}`;
    DETAIL_DOM.alert.textContent = message;
    DETAIL_DOM.alert.classList.remove('hidden');
}

function initDetailMap() {
    const container = document.getElementById('detailMap');
    if (!container) return;
    detailMap = L.map('detailMap').setView([-6.2, 106.816], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(detailMap);
}

function updateMap(lat, lng) {
    if (!detailMap || !lat || !lng) return;
    if (!detailMarker) {
        detailMarker = L.marker([lat, lng]).addTo(detailMap);
    } else {
        detailMarker.setLatLng([lat, lng]);
    }
    detailMap.setView([lat, lng], 15);
}

function formatDate(value) {
    if (!value) return '-';
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? '-' : date.toLocaleString('id-ID');
}

function statusBadge(status = '') {
    const normalized = status.replace(/_/g, '-');
    let classes = 'inline-block px-3 py-1 rounded-lg font-medium text-sm ';
    if (normalized.includes('selesai')) classes += 'bg-green-100 text-green-700';
    else if (normalized.includes('proses') || normalized.includes('tugaskan') || normalized.includes('validasi'))
        classes += 'bg-yellow-100 text-yellow-700';
    else classes += 'bg-gray-100 text-gray-700';
    return `<span class="${classes}">${normalized.replace(/-/g, ' ')}</span>`;
}

function renderDetail(data) {
    DETAIL_DOM.title.textContent = data.title || 'Pengaduan Infrastruktur';
    DETAIL_DOM.category.textContent = data.category || '-';
    DETAIL_DOM.createdAt.textContent = formatDate(data.created_at);
    DETAIL_DOM.description.textContent = data.description || '-';
    DETAIL_DOM.address.textContent = data.address || '-';
    if (DETAIL_DOM.photoBefore) {
        const photoUrl = PelaporAuth.resolveFileUrl(data.photo_before);
        DETAIL_DOM.photoBefore.src = photoUrl || 'https://placehold.co/600x300?text=Tidak+ada+foto';
    }
    DETAIL_DOM.statusBadge.innerHTML = statusBadge(data.status || '');
    updateMap(data.location?.latitude, data.location?.longitude);

    if (data.assigned_officer) {
        DETAIL_DOM.officerSection.classList.remove('hidden');
        DETAIL_DOM.officerName.textContent = data.assigned_officer.name || '-';
        const metaParts = [];
        if (data.assigned_officer.department) metaParts.push(data.assigned_officer.department);
        if (data.assigned_officer.specialization) metaParts.push(data.assigned_officer.specialization);
        DETAIL_DOM.officerMeta.textContent = metaParts.join(' • ') || 'Petugas Lapangan';
    } else {
        DETAIL_DOM.officerSection.classList.add('hidden');
    }

    if (Array.isArray(data.completion_proofs) && data.completion_proofs.length > 0) {
        DETAIL_DOM.completionSection.classList.remove('hidden');
        DETAIL_DOM.completionProofs.innerHTML = data.completion_proofs
            .map((proof) => {
                const img = PelaporAuth.resolveFileUrl(proof.photo_after);
                return `
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <img src="${img}" alt="Bukti Penyelesaian" class="w-full h-48 object-cover bg-gray-100">
                        <div class="p-3 text-sm text-gray-600">
                            ${proof.notes || 'Tidak ada catatan.'}
                            <div class="text-xs text-gray-400 mt-1">${formatDate(proof.created_at)}</div>
                        </div>
                    </div>
                `;
            })
            .join('');
    } else {
        DETAIL_DOM.completionSection.classList.add('hidden');
    }
}

function renderTimeline(records = []) {
    if (!DETAIL_DOM.timeline) return;
    if (!records.length) {
        DETAIL_DOM.timeline.innerHTML = '<p class="text-sm text-gray-500">Timeline belum tersedia.</p>';
        return;
    }
    DETAIL_DOM.timeline.innerHTML = '<div class="timeline-line"></div>';
    records.forEach((item) => {
        DETAIL_DOM.timeline.innerHTML += `
            <div class="flex gap-3 relative timeline-item mb-4">
                <div class="flex flex-col items-center">
                    <div class="timeline-dot bg-blue-500 mt-2" style="margin-left: 10px;"></div>
                </div>
                <div class="flex-1 -mt-1">
                    <p class="text-gray-800 font-medium">${item.note || item.status}</p>
                    <p class="text-xs text-gray-500">${item.created_by?.name || '-'} • ${formatDate(item.created_at)}</p>
                </div>
            </div>
        `;
    });
}

async function loadComplaintDetail() {
    if (!complaintId) {
        showDetailAlert('ID pengaduan tidak ditemukan pada URL.');
        return;
    }
    try {
        const payload = await PelaporAPI.request(`/pelapor/complaints/${complaintId}`);
        renderDetail(payload?.data || {});
        await loadComplaintTimeline();
    } catch (error) {
        console.error('[DetailPengaduan] error', error);
        showDetailAlert(error.message || 'Gagal memuat detail pengaduan.');
    }
}

async function loadComplaintTimeline() {
    try {
        const payload = await PelaporAPI.request(`/pelapor/complaints/${complaintId}/timeline`);
        renderTimeline(payload?.data?.records || []);
    } catch (error) {
        console.error('[DetailPengaduan] timeline error', error);
        renderTimeline([]);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    PelaporAuth.requirePelapor();
    initDetailMap();
    loadComplaintDetail();
});
