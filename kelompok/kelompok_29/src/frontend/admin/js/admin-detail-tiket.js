// Data Tiket (Diambil dari PHP, asumsikan sudah ada di 'ticketData')

// --- 1. DUMMY DATA Timeline ---
const mockTimeline = [
    { status: 'selesai', date: '2025-10-18', notes: 'Penyelesaian disetujui.', actor: 'Admin System' },
    { status: 'menunggu-validasi-admin', date: '2025-10-17', notes: 'Petugas upload bukti penyelesaian.', actor: 'Budi Santoso' },
    { status: 'dalam-proses', date: '2025-10-15', notes: 'Petugas mulai mengerjakan.', actor: 'Budi Santoso' },
    { status: 'ditugaskan-ke-petugas', date: '2025-10-12', notes: 'Ditugaskan ke Budi Santoso.', actor: 'Admin System' },
    { status: 'diverifikasi-admin', date: '2025-10-11', notes: 'Pengaduan diverifikasi dan diterima.', actor: 'Admin System' },
    { status: 'diajukan', date: '2025-10-10', notes: 'Pengaduan baru diajukan.', actor: 'Ahmad Wijaya' },
];

// --- 2. FUNGSI UTILITAS ---

function getStatusBadge(status) {
    let text = status.toUpperCase().replace(/-/g, ' ');
    let classes = 'badge-status-detail px-3 py-1 rounded-full font-semibold text-xs ';
    
    // Mapping Status ke Tailwind Classes
    if (status.includes('proses')) classes += 'bg-yellow-100 text-yellow-700';
    else if (status.includes('selesai')) classes += 'bg-green-100 text-green-700';
    else if (status.includes('diverifikasi')) classes += 'bg-indigo-100 text-indigo-700';
    else if (status.includes('ditugaskan')) classes += 'bg-orange-100 text-orange-700';
    else if (status.includes('diajukan')) classes += 'bg-blue-100 text-blue-700';
    else if (status.includes('validasi')) classes += 'bg-red-100 text-red-700';

    return `<span class="${classes}">${text}</span>`;
}

// --- 3. FUNGSI RENDERING ---

function renderTimeline(timeline) {
    const container = document.getElementById('timelineContainer');
    let html = '<div class="timeline-wrapper relative pt-2">';
    
    timeline.forEach(item => {
        const colorClass = item.status.includes('selesai') ? 'bg-green-600' : (item.status.includes('validasi') ? 'bg-red-600' : 'bg-blue-600');
        
        html += `
            <div class="timeline-item flex mb-4 relative">
                <div class="timeline-point w-3 h-3 rounded-full ${colorClass} border-2 border-white absolute left-0 transform -translate-x-1/2 z-10"></div>
                <div class="timeline-content ml-4 pb-2 border-l border-gray-300 pl-4 w-full">
                    <p class="timeline-status font-medium text-gray-800">${item.notes}</p>
                    <p class="timeline-meta text-xs text-gray-500 mt-1">${new Date(item.date).toLocaleDateString('id-ID')} - ${item.actor}</p>
                </div>
            </div>
        `;
    });
    html = `<div class="absolute top-0 bottom-0 left-0 w-0.5 bg-gray-300 ml-1"></div>` + html;
    container.innerHTML = html;
}


function renderActionButtons(status) {
    const container = document.getElementById('actionButtonsContainer');
    container.innerHTML = '';
    let html = '';

    switch (status) {
        case 'diajukan':
            html = `
                <button onclick="handleVerify('${ticketData.id}')" class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2 font-semibold">
                    <i class="material-icons">shield</i> Verifikasi & Terima
                </button>
                <button onclick="handleReject('${ticketData.id}')" class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition-colors font-semibold">
                    <i class="material-icons">cancel</i> Tolak Pengaduan
                </button>
            `;
            break;
        case 'diverifikasi-admin':
            html = `
                <button onclick="showOfficerModal()" class="w-full bg-amber-600 text-white py-3 rounded-lg hover:bg-amber-700 transition-colors flex items-center justify-center gap-2 font-semibold">
                    <i class="material-icons">person_add</i> Tugaskan Petugas
                </button>
            `;
            break;
        case 'ditugaskan-ke-petugas':
        case 'dalam-proses':
            const msgClass = status === 'ditugaskan-ke-petugas' ? 'bg-yellow-50 border-yellow-200 text-yellow-800' : 'bg-orange-50 border-orange-200 text-orange-800';
            html = `
                <div class="p-4 border rounded-lg ${msgClass}">
                    <p class="font-medium mb-1">⏳ ${status === 'ditugaskan-ke-petugas' ? 'Menunggu petugas memulai' : 'Petugas sedang bekerja'}</p>
                    <p class="text-sm">Petugas: ${ticketData.assignedOfficer}</p>
                </div>
            `;
            break;
        case 'menunggu-validasi-admin':
            html = `
                <button onclick="window.location.href='admin-validasi-selesai.php?id=${ticketData.id}'" class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition-colors flex items-center justify-center gap-2 font-semibold">
                    <i class="material-icons">check_circle</i> Validasi Penyelesaian
                </button>
            `;
            break;
        case 'selesai':
            html = `
                <div class="p-4 border rounded-lg bg-green-50 border-green-200 text-green-800">
                    <p class="font-medium">✅ Pengaduan telah selesai</p>
                </div>
            `;
            break;
    }
    container.innerHTML = html;
}

// --- 4. HANDLER INTERAKSI (JS NATIVE) ---

function handleVerify(id) {
    if (confirm('Yakin ingin memverifikasi pengaduan ini?')) {
        alert('Pengaduan berhasil diverifikasi!');
        window.location.reload(); 
    }
}

function handleReject(id) {
    if (confirm('Yakin ingin menolak pengaduan ini?')) {
        alert('Pengaduan ditolak.');
        window.location.href = 'admin-manajemen-tiket.php'; 
    }
}

function showOfficerModal() {
    if (typeof renderOfficerSelectionModal !== 'undefined') {
        renderOfficerSelectionModal(ticketData.id, ticketData.assignedOfficer);
    } else {
        alert('Error: admin-select-officer.js tidak dimuat.');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('statusBadgeContainer').innerHTML = getStatusBadge(ticketData.status);
    renderActionButtons(ticketData.status);
    renderTimeline(mockTimeline); 
});