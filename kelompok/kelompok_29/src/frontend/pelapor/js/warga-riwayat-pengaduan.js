// File: js/warga-riwayat-pengaduan.js

// --- DUMMY DATA ---
const allUserComplaints = [
    { id: 'TKT-001', reporterId: 'user1', status: 'dalam-proses', title: 'Jalan Berlubang di Jl. Sudirman', location: 'Jl. Sudirman No. 45, Jakarta Pusat', createdAt: '2025-12-05', imageUrl: 'img/dummy-road.jpg', category: 'Jalan Raya' },
    { id: 'TKT-002', reporterId: 'user1', status: 'selesai', title: 'Lampu Jalan Mati', location: 'Perumahan Griya Asri', createdAt: '2025-12-01', imageUrl: 'img/dummy-lamp.jpg', category: 'Penerangan' },
    { id: 'TKT-003', reporterId: 'user1', status: 'ditugaskan-ke-petugas', title: 'Trotoar Rusak', location: 'Jl. Gatot Subroto', createdAt: '2025-11-20', imageUrl: 'img/dummy-walkway.jpg', category: 'Trotoar' },
    { id: 'TKT-004', reporterId: 'user1', status: 'diajukan', title: 'Saluran Tersumbat', location: 'Jl. Melati, Bekasi', createdAt: '2025-12-10', imageUrl: 'img/dummy-drain.jpg', category: 'Drainase' },
];

const statusFilters = [
    { slug: 'all', label: 'Semua' },
    { slug: 'diajukan', label: 'Diajukan' },
    { slug: 'diverifikasi-admin', label: 'Diverifikasi' },
    { slug: 'ditugaskan-ke-petugas', label: 'Ditugaskan' },
    { slug: 'dalam-proses', label: 'Dalam Proses' },
    { slug: 'menunggu-validasi-admin', label: 'Menunggu Validasi' },
    { slug: 'selesai', label: 'Selesai' },
];

let currentFilterStatus = 'all';

// --- FUNGSI UTILITAS ---
function getStatusBadgeHtml(status) {
    let text = status.toUpperCase().replace(/-/g, ' ');
    let classes = 'inline-block px-3 py-1 text-xs font-medium rounded-lg ';
    
    if (status.includes('selesai')) classes += 'bg-green-100 text-green-700';
    else if (status.includes('proses') || status.includes('ditugaskan') || status.includes('validasi')) classes += 'bg-yellow-100 text-yellow-700';
    else if (status.includes('diajukan') || status.includes('diverifikasi')) classes += 'bg-blue-100 text-blue-700';
    
    return `<span class="${classes}">${text}</span>`;
}

// --- FUNGSI RENDERING ---

function renderHistory() {
    renderFilterButtons();
    filterAndRenderComplaints();
}

function calculateCounts(statusList) {
    const counts = {};
    statusList.forEach(s => {
        counts[s.slug] = allUserComplaints.filter(c => c.status === s.slug).length;
    });
    counts.all = allUserComplaints.length;
    return counts;
}

function renderFilterButtons() {
    const container = document.getElementById('filter-buttons-container');
    const counts = calculateCounts(statusFilters);
    container.innerHTML = '';
    
    statusFilters.forEach(s => {
        const isActive = s.slug === currentFilterStatus;
        const classes = isActive 
            ? 'bg-blue-600 text-white border-blue-600' 
            : 'bg-white text-gray-700 border-gray-300 hover:border-blue-600';
            
        const button = document.createElement('button');
        button.className = `px-4 py-2 rounded-lg border transition-colors text-sm whitespace-nowrap ${classes}`;
        button.textContent = `${s.label} (${counts[s.slug] || 0})`;
        
        button.addEventListener('click', () => {
            currentFilterStatus = s.slug;
            renderHistory();
        });
        container.appendChild(button);
    });
}

function filterAndRenderComplaints() {
    const filteredComplaints = currentFilterStatus === 'all'
        ? allUserComplaints
        : allUserComplaints.filter(c => c.status === currentFilterStatus);

    renderHistoryStats(allUserComplaints);
    renderComplaintsList(filteredComplaints);
    
    document.getElementById('no-filtered-complaints').classList.toggle('hidden', filteredComplaints.length > 0);
}

function renderHistoryStats(allComplaints) {
    const container = document.getElementById('history-stats-container');
    const total = allComplaints.length;
    const completed = allComplaints.filter(c => c.status === 'selesai').length;
    const progress = total - completed;
    
    // Stats Diterima/Diproses/Selesai
    container.innerHTML = `
        <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
            <div class="text-gray-900 font-bold mb-1">${total}</div>
            <p class="text-gray-600 text-sm">Total</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-blue-200 text-center">
            <div class="text-blue-600 font-bold mb-1">${allComplaints.filter(c => c.status === 'diajukan' || c.status === 'diverifikasi-admin').length}</div>
            <p class="text-gray-600 text-sm">Diterima</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-yellow-200 text-center">
            <div class="text-yellow-600 font-bold mb-1">${allComplaints.filter(c => c.status === 'dalam-proses' || c.status === 'ditugaskan-ke-petugas' || c.status === 'menunggu-validasi-admin').length}</div>
            <p class="text-gray-600 text-sm">Diproses</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-green-200 text-center">
            <div class="text-green-600 font-bold mb-1">${completed}</div>
            <p class="text-gray-600 text-sm">Selesai</p>
        </div>
    `;
}

function renderComplaintsList(complaints) {
    const container = document.getElementById('complaints-history-list');
    container.innerHTML = '';
    
    complaints.forEach(c => {
        const badge = getStatusBadgeHtml(c.status);
        container.innerHTML += `
            <a href="warga-detail-pengaduan.php?id=${c.id}" class="bg-white rounded-xl p-4 border border-gray-200 cursor-pointer hover:shadow-md transition-shadow flex gap-4 items-start">
                <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                    <img src="${c.imageUrl}" alt="${c.title}" class="w-full h-full object-cover" />
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <div class="text-gray-500 text-sm mb-1">#${c.id}</div>
                            <p class="text-gray-900 font-medium">${c.title}</p>
                        </div>
                        ${badge}
                    </div>
                    
                    <div class="space-y-1 text-gray-600 text-sm">
                        <p class="flex items-center gap-1">
                            <i class="material-icons text-base">place</i>
                            <span class="truncate">${c.location}</span>
                        </p>
                        <p class="flex items-center gap-1 text-gray-500">
                            <i class="material-icons text-base">calendar_today</i>
                            <span>Diajukan: ${c.createdAt}</span>
                        </p>
                    </div>
                </div>
            </a>
        `;
    });
}

// --- EKSEKUSI ---
document.addEventListener('DOMContentLoaded', renderHistory);