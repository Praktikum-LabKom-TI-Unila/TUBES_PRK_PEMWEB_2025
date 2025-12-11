// File: js/admin-kelola_petugas.js
// Mengintegrasikan logika tabel, filter, dan Modal Detail Petugas (sesuai image_720917.png)

// --- 1. DUMMY DATA ---
const mockOfficers = [
    // Data Petugas Tambahan untuk simulasi Kontak/Spesialisasi di Modal
    { id: "EMP-2023-001", name: "Budi Santoso", dept: "Dinas Pekerjaan Umum", specialization: "Jalan & Trotoar", activeTasksCount: 2, status: "busy", email: "budi.santoso@system.go.id", phone: "+62 812-3456-7890" },
    { id: "EMP-2023-002", name: "Andi Pratama", dept: "Dinas Energi & Listrik", specialization: "Penerangan & Listrik", activeTasksCount: 1, status: "available", email: "andi.pratama@system.go.id", phone: "+62 812-3456-7891" },
    { id: "EMP-2023-003", name: "Sari Wulandari", dept: "Dinas Pengairan", specialization: "Drainase & Air", activeTasksCount: 0, status: "available", email: "sari.w@system.go.id", phone: "+62 812-3456-7892" },
    { id: "EMP-2023-004", name: "Rahmat Hidayat", dept: "Dinas Pekerjaan Umum", specialization: "Jalan & Trotoar", activeTasksCount: 3, status: "busy", email: "rahmat.h@system.go.id", phone: "+62 812-3456-7893" },
    { id: "EMP-2023-005", name: "Dewi Lestari", dept: "Dinas Energi & Listrik", specialization: "Penerangan & Listrik", activeTasksCount: 0, status: "available", email: "dewi.l@system.go.id", phone: "+62 812-3456-7894" }
];

let currentSearch = '';
let currentFilter = 'all';

// --- 2. FUNGSI UTILITAS ---
function getStatusBadgeHtml(status) {
    let text = '';
    let classes = 'px-3 py-1 rounded-full flex items-center gap-1 text-xs font-semibold ';
    let icon = ''; 

    switch (status) {
        case 'available':
            text = 'Tersedia'; classes += 'bg-green-100 text-green-700'; icon = 'check_circle'; break;
        case 'busy':
            text = 'Sibuk'; classes += 'bg-yellow-100 text-yellow-700'; icon = 'schedule'; break;
        case 'offline':
            text = 'Offline'; classes += 'bg-gray-100 text-gray-700'; icon = 'cancel'; break;
        default:
            return '';
    }
    return `<span class="${classes}"><i class="material-icons text-sm">${icon}</i>${text}</span>`;
}

// --- 3. FUNGSI RENDERING TABEL & KARTU STATS ---

function renderStatsCards(officers) {
    const total = mockOfficers.length;
    const available = mockOfficers.filter(o => o.status === 'available').length;
    const busy = mockOfficers.filter(o => o.status === 'busy').length;
    
    const container = document.getElementById('ringkasan-petugas-container');
    container.innerHTML = `
        <div class="bg-white rounded-xl p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-2xl">
                <i class="material-icons">group</i>
              </div>
              <span class="text-gray-500 text-sm">Total</span>
            </div>
            <div class="text-gray-900 text-2xl font-bold mb-1">${total}</div>
            <p class="text-gray-600">Petugas</p>
        </div>
        
        <div class="bg-white rounded-xl p-6 border border-green-200">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-green-600 text-2xl">
                <i class="material-icons">check_circle</i>
              </div>
              <span class="text-green-600 text-sm">Tersedia</span>
            </div>
            <div class="text-green-600 text-2xl font-bold mb-1">${available}</div>
            <p class="text-gray-600">Siap Tugas</p>
        </div>
        
        <div class="bg-white rounded-xl p-6 border border-yellow-200">
            <div class="flex items-center justify-between mb-4">
              <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600 text-2xl">
                <i class="material-icons">schedule</i>
              </div>
              <span class="text-yellow-600 text-sm">Sibuk</span>
            </div>
            <div class="text-yellow-600 text-2xl font-bold mb-1">${busy}</div>
            <p class="text-gray-600">Sedang Bertugas</p>
        </div>
    `;
}

function filterAndRenderTable() {
    let filteredOfficers = mockOfficers.filter(o => {
        const matchesSearch = o.name.toLowerCase().includes(currentSearch.toLowerCase()) || 
                              o.dept.toLowerCase().includes(currentSearch.toLowerCase()) ||
                              o.id.toLowerCase().includes(currentSearch.toLowerCase()) ||
                              o.specialization.toLowerCase().includes(currentSearch.toLowerCase());
        const matchesStatus = currentFilter === 'all' || o.status === currentFilter;
        return matchesSearch && matchesStatus;
    });

    renderPetugasTable(filteredOfficers);
    renderStatsCards(filteredOfficers); 
    
    document.getElementById('showing-info').textContent = `Menampilkan ${filteredOfficers.length} dari ${mockOfficers.length} petugas`;
    document.getElementById('empty-state').classList.toggle('hidden', filteredOfficers.length > 0);
}


function renderPetugasTable(petugas) {
    const tbody = document.getElementById('tabel-body-petugas');
    tbody.innerHTML = '';
    
    petugas.forEach(p => {
        const row = tbody.insertRow();
        row.className = 'border-b border-gray-100 hover:bg-gray-50';
        const statusHtml = getStatusBadgeHtml(p.status);

        // PERBAIKAN: Menggunakan JSON.stringify untuk mempassing object Petugas ke handler
        const officerJson = JSON.stringify(p).replace(/"/g, '&quot;');

        row.innerHTML = `
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xl">
                        <i class="material-icons">person</i>
                    </div>
                    <div>
                        <p class="text-gray-900">${p.name}</p>
                        <p class="text-gray-500 text-sm">ID: ${p.id}</p>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4"><p class="text-gray-900">${p.dept}</p></td>
            <td class="px-6 py-4"><p class="text-gray-700">${p.specialization}</p></td>
            <td class="px-6 py-4">
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                    ${p.activeTasksCount} tugas
                </span>
            </td>
            <td class="px-6 py-4">${statusHtml}</td>
            <td class="px-6 py-4 text-center">
                <button onclick='showOfficerDetail(${officerJson})' class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-semibold">Detail</button>
            </td>
        `;
    });
}

// --- 4. FUNGSI MODAL DETAIL PETUGAS (PERBAIKAN SESUAI image_720917.png) ---

function closeDetailModal() {
    document.getElementById('detail-modal-container').innerHTML = '';
    document.body.style.overflow = ''; // Mengembalikan scroll
}

function showOfficerDetail(officer) {
    // Data dummy tugas aktif (sesuai image_720917.png)
    const activeTasks = [
        { id: 'TKT-001', title: 'Jalan Berlubang di Jl. Sudirman', status: 'Proses', badge: 'bg-yellow-100 text-yellow-700' },
    ];
    
    document.body.style.overflow = 'hidden'; // Nonaktifkan scroll
    
    const tasksListHtml = activeTasks.map(task => `
        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
            <div class="w-10 h-10 bg-gray-300 rounded-md flex-shrink-0">
                </div>
            <div class="flex-1">
                <p class="text-gray-900 font-medium">${task.title}</p>
                <p class="text-gray-500 text-xs">#${task.id}</p>
            </div>
            <span class="px-2 py-1 rounded text-xs font-semibold ${task.badge}">${task.status}</span>
        </div>
    `).join('');

    const statusHtml = getStatusBadgeHtml(officer.status);

    const modalHtml = `
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50" onclick="closeDetailModal()">
            <div class="bg-white rounded-xl max-w-lg w-full max-h-[90vh] overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
                
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-800">Detail Petugas</h3>
                    <button onclick="closeDetailModal()" class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg text-gray-500">
                        <i class="material-icons text-xl">close</i>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto space-y-6">
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-blue-600 text-3xl bg-blue-100">
                                <i class="material-icons">person</i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold">${officer.name}</h3>
                                <p class="text-gray-600 text-sm">${officer.dept}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                             ${statusHtml}
                        </div>
                    </div>


                    <div class="space-y-3">
                        <h4 class="text-lg font-semibold text-gray-800">Informasi Kontak</h4>
                        <div class="text-sm space-y-2">
                            <p class="flex items-center gap-3 text-gray-700"><i class="material-icons text-base text-gray-400">mail</i>${officer.email || 'N/A'}</p>
                            <p class="flex items-center gap-3 text-gray-700"><i class="material-icons text-base text-gray-400">phone</i>${officer.phone || 'N/A'}</p>
                            <p class="flex items-center gap-3 text-gray-700"><i class="material-icons text-base text-gray-400">vpn_key</i>ID Pegawai: ${officer.id}</p>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h4 class="font-semibold text-gray-700 mb-1">Spesialisasi</h4>
                        <p class="text-blue-700 text-sm">${officer.specialization}</p>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-semibold text-gray-700">Tugas Aktif</h4>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">${officer.activeTasksCount} tugas</span>
                        </div>
                        <div class="space-y-2">
                            ${officer.activeTasksCount > 0 ? tasksListHtml : `<p class="text-gray-500 text-sm text-center py-2">Tidak ada tugas aktif.</p>`}
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200 bg-gray-50">
                    <button onclick="closeDetailModal()" class="w-full bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 font-semibold">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    `;

    document.getElementById('detail-modal-container').innerHTML = modalHtml;
}


// --- 5. HANDLER INTERAKSI ---
document.getElementById('searchPetugas').addEventListener('input', (e) => {
    currentSearch = e.target.value;
    filterAndRenderTable();
});

document.getElementById('statusFilter').addEventListener('change', (e) => {
    currentFilter = e.target.value;
    filterAndRenderTable();
});

document.addEventListener('DOMContentLoaded', () => {
    filterAndRenderTable();
});