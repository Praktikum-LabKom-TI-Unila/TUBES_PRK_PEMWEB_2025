// File: js/warga-dashboard.js

// --- DUMMY DATA ---
const userComplaints = [
    { id: 'TKT-001', reporterId: 'user1', status: 'dalam-proses', title: 'Jalan Berlubang di Jl. Sudirman', location: 'Jl. Sudirman No. 45', createdAt: '2025-12-05', imageUrl: 'img/dummy-road.jpg', category: 'Jalan Raya' },
    { id: 'TKT-002', reporterId: 'user1', status: 'ditugaskan-ke-petugas', title: 'Lampu Jalan Mati', location: 'Perumahan Griya Asri', createdAt: '2025-12-01', imageUrl: 'img/dummy-lamp.jpg', category: 'Penerangan' },
    { id: 'TKT-003', reporterId: 'user1', status: 'selesai', title: 'Trotoar Rusak', location: 'Jl. Gatot Subroto', createdAt: '2025-11-20', imageUrl: 'img/dummy-walkway.jpg', category: 'Trotoar' },
    { id: 'TKT-004', reporterId: 'user1', status: 'diajukan', title: 'Saluran Tersumbat', location: 'Jl. Melati', createdAt: '2025-12-10', imageUrl: 'img/dummy-drain.jpg', category: 'Drainase' },
];

// --- FUNGSI UTILITAS ---
function getStatusBadgeHtml(status) {
    let text = status.toUpperCase().replace(/-/g, ' ');
    let classes = 'inline-block px-3 py-1 text-xs font-medium rounded-lg ';
    
    if (status.includes('selesai')) classes += 'bg-green-100 text-green-700';
    else if (status.includes('proses') || status.includes('ditugaskan') || status.includes('diajukan') || status.includes('diverifikasi') || status.includes('validasi')) classes += 'bg-yellow-100 text-yellow-700';
    else classes += 'bg-gray-100 text-gray-700';

    return `<span class="${classes}">${text}</span>`;
}

// --- FUNGSI RENDERING ---

function renderDashboard() {
    const totalComplaints = userComplaints.length;
    const inProgress = userComplaints.filter(c => c.status !== 'selesai').length;
    const completed = userComplaints.filter(c => c.status === 'selesai').length;
    const recentComplaints = userComplaints.slice(0, 3);
    
    renderStats(totalComplaints, inProgress, completed);
    renderRecentComplaints(recentComplaints);
}

function renderStats(total, inProgress, completed) {
    const container = document.getElementById('warga-stats-container');
    container.innerHTML = `
        <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
            <div class="text-gray-900 font-bold mb-1 text-xl">${total}</div>
            <p class="text-gray-600 text-sm">Total</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-yellow-200 text-center">
            <div class="text-yellow-600 font-bold mb-1 text-xl">${inProgress}</div>
            <p class="text-gray-600 text-sm">Diproses</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-green-200 text-center">
            <div class="text-green-600 font-bold mb-1 text-xl">${completed}</div>
            <p class="text-gray-600 text-sm">Selesai</p>
        </div>
    `;
}

function renderRecentComplaints(complaints) {
    const container = document.getElementById('recent-complaints-list');
    container.innerHTML = '';

    if (complaints.length === 0) {
        document.getElementById('no-complaints-state').classList.remove('hidden');
        return;
    }
    document.getElementById('no-complaints-state').classList.add('hidden');

    complaints.forEach(c => {
        const badge = getStatusBadgeHtml(c.status);
        container.innerHTML += `
            <a href="warga-detail-pengaduan.php?id=${c.id}" class="bg-white rounded-xl p-4 border border-gray-200 cursor-pointer hover:shadow-md transition-shadow flex gap-4 items-start">
                <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                    <img src="${c.imageUrl}" alt="${c.title}" class="w-full h-full object-cover" />
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start mb-1">
                        <p class="text-gray-900 font-medium truncate">${c.title}</p>
                        ${badge}
                    </div>
                    <div class="space-y-1 text-gray-600 text-sm mt-1">
                        <p class="flex items-center gap-1">#${c.id} - ${c.category}</p>
                        <p class="flex items-center gap-1 text-gray-500"><i class="material-icons text-base">place</i> ${c.location}</p>
                    </div>
                </div>
            </a>
        `;
    });
}

// --- EKSEKUSI ---
document.addEventListener('DOMContentLoaded', renderDashboard);