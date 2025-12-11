// File: js/petugas-task-list.js
// --- DUMMY DATA ---
// Catatan: Di implementasi PHP nyata, ini akan diambil via AJAX (fetch) dari API
const mockComplaints = [
    { id: 'TKT-001', assignedOfficer: 'Budi Santoso', status: 'dalam-proses', title: 'Jalan Berlubang di Jl. Sudirman', location: 'Jl. Sudirman No. 45, Jakarta Pusat', createdAt: '10/10/2025', imageUrl: 'img/dummy-road.jpg', category: 'Jalan Raya' },
    { id: 'TKT-005', assignedOfficer: 'Budi Santoso', status: 'ditugaskan-ke-petugas', title: 'Rambu Lalu Lintas Rusak', location: 'Jl. Ahmad Yani No. 88, Bandung', createdAt: '10/11/2025', imageUrl: 'img/dummy-sign.jpg', category: 'Rambu Lalu Lintas' },
    { id: 'TKT-002', assignedOfficer: 'Budi Santoso', status: 'selesai', title: 'Lampu Jalan Mati', location: 'Perumahan Griya Asri', createdAt: '10/05/2025', imageUrl: 'img/dummy-lamp.jpg', category: 'Penerangan Jalan' },
    { id: 'TKT-006', assignedOfficer: 'Andi Pratama', status: 'ditugaskan-ke-petugas', title: 'Taman Kota Tidak Terawat', location: 'Taman Kota Mawar', createdAt: '10/12/2025', imageUrl: 'img/dummy-park.jpg', category: 'Taman' },
];
const currentOfficerName = "Budi Santoso";

// --- FUNGSI UTILITAS ---
function getStatusBadgeHtml(status) {
    let text = status.toUpperCase().replace(/-/g, ' ');
    let classes = 'inline-block px-3 py-1 text-xs font-semibold rounded-full ';
    
    if (status.includes('proses') || status.includes('ditugaskan')) classes += 'bg-yellow-100 text-yellow-700';
    else if (status.includes('selesai')) classes += 'bg-green-100 text-green-700';
    
    return `<span class="${classes}">${text}</span>`;
}


// --- FUNGSI RENDERING ---

function renderTaskList() {
    const assignedTasks = mockComplaints.filter(c => c.assignedOfficer === currentOfficerName);
    const activeTasks = assignedTasks.filter(c => c.status !== 'selesai');
    const completedTasks = assignedTasks.filter(c => c.status === 'selesai');

    renderStats(assignedTasks.length, activeTasks.length, completedTasks.length);
    
    renderActiveTasks(activeTasks);
    renderCompletedTasks(completedTasks);
}

function renderStats(total, active, completed) {
    const container = document.getElementById('stats-cards-container');
    container.innerHTML = `
        <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
            <div class="text-gray-900 font-bold mb-1">${total}</div>
            <p class="text-gray-600 text-sm">Total Tugas</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-yellow-200 text-center">
            <div class="text-yellow-600 font-bold mb-1">${active}</div>
            <p class="text-gray-600 text-sm">Aktif</p>
        </div>
        <div class="bg-white rounded-xl p-4 border border-green-200 text-center">
            <div class="text-green-600 font-bold mb-1">${completed}</div>
            <p class="text-gray-600 text-sm">Selesai</p>
        </div>
    `;
}

function renderActiveTasks(tasks) {
    const container = document.getElementById('active-tasks-list');
    container.innerHTML = '';
    
    if (tasks.length === 0) {
        document.getElementById('no-active-tasks').classList.remove('hidden');
        return;
    }
    document.getElementById('no-active-tasks').classList.add('hidden');

    tasks.forEach(task => {
        const badge = getStatusBadgeHtml(task.status);
        container.innerHTML += `
            <a href="petugas-task-detail.php?id=${task.id}" class="bg-white rounded-xl p-4 border border-gray-200 cursor-pointer hover:shadow-md transition-shadow flex gap-4">
                <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                    <img src="${task.imageUrl}" alt="${task.title}" class="w-full h-full object-cover" />
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-2">
                      <div>
                        <div class="text-gray-500 text-sm mb-1">#${task.id}</div>
                        <p class="text-gray-900 font-medium">${task.title}</p>
                      </div>
                      ${badge}
                    </div>
                    
                    <div class="space-y-1 text-gray-600 text-sm">
                      <div class="flex items-center gap-2 truncate">
                        <i class="material-icons text-base">place</i>
                        <span class="truncate">${task.location}</span>
                      </div>
                      <div class="flex items-center gap-2">
                        <i class="material-icons text-base">near_me</i>
                        <span>±2.3 km dari lokasi Anda</span>
                      </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-gray-200">
                      <a href="petugas-task-detail.php?id=${task.id}"
                        class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center font-semibold">
                        <i class="material-icons text-xl mr-2">visibility</i> Lihat Detail & Mulai
                      </a>
                    </div>
                </div>
            </a>
        `;
    });
}

function renderCompletedTasks(tasks) {
    const container = document.getElementById('completed-tasks-list');
    container.innerHTML = '';
    
    if (tasks.length === 0) {
        document.getElementById('no-completed-tasks').classList.remove('hidden');
        return;
    }
    document.getElementById('no-completed-tasks').classList.add('hidden');

    tasks.forEach(task => {
        const badge = getStatusBadgeHtml(task.status);
        container.innerHTML += `
            <a href="petugas-task-detail.php?id=${task.id}" class="bg-white rounded-xl p-4 border border-gray-200 cursor-pointer hover:shadow-md transition-shadow opacity-75 flex gap-4">
                <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                    <img src="${task.imageUrl}" alt="${task.title}" class="w-full h-full object-cover" />
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-2">
                      <div>
                        <div class="text-gray-500 text-sm mb-1">#${task.id}</div>
                        <p class="text-gray-900 font-medium">${task.title}</p>
                      </div>
                      ${badge}
                    </div>
                    <div class="flex items-center gap-4 text-gray-500 text-sm">
                      <div class="flex items-center gap-1">
                        <i class="material-icons text-base">place</i>
                        <span class="truncate">${task.location}</span>
                      </div>
                    </div>
                </div>
            </a>
        `;
    });
}

// --- EKSEKUSI ---
document.addEventListener('DOMContentLoaded', () => {
    renderTaskList();
});