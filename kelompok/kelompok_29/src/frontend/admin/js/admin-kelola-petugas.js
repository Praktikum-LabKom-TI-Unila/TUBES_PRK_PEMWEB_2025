// --- 1. DUMMY DATA ---
const mockOfficers = [
    { id: "EMP-2023-001", name: "Budi Santoso", dept: "Dinas Pekerjaan Umum", specialization: "Jalan & Trotoar", activeTasksCount: 2, status: "busy" },
    { id: "EMP-2023-002", name: "Andi Pratama", dept: "Dinas Energi & Listrik", specialization: "Penerangan & Listrik", activeTasksCount: 1, status: "available" },
    { id: "EMP-2023-003", name: "Sari Wulandari", dept: "Dinas Pengairan", specialization: "Drainase & Air", activeTasksCount: 0, status: "available" },
    { id: "EMP-2023-004", name: "Rahmat Hidayat", dept: "Dinas Pekerjaan Umum", specialization: "Jalan & Trotoar", activeTasksCount: 3, status: "busy" },
    { id: "EMP-2023-005", name: "Dewi Lestari", dept: "Dinas Energi & Listrik", specialization: "Penerangan & Listrik", activeTasksCount: 0, status: "available" }
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

// --- 3. FUNGSI RENDERING ---

function renderStatsCards(officers) {
    const total = officers.length;
    const available = officers.filter(o => o.status === 'available').length;
    const busy = officers.filter(o => o.status === 'busy').length;
    
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
    // Logika filter (Search & Dropdown)
    let filteredOfficers = mockOfficers.filter(o => {
        const matchesSearch = o.name.toLowerCase().includes(currentSearch.toLowerCase()) || 
                              o.dept.toLowerCase().includes(currentSearch.toLowerCase()) ||
                              o.id.toLowerCase().includes(currentSearch.toLowerCase()) ||
                              o.specialization.toLowerCase().includes(currentSearch.toLowerCase());
        const matchesStatus = currentFilter === 'all' || o.status === currentFilter;
        return matchesSearch && matchesStatus;
    });

    renderPetugasTable(filteredOfficers);
    renderStatsCards(mockOfficers); // Stats card tetap menampilkan total data, bukan data filter.
    
    // Update info bar
    document.getElementById('showing-info').textContent = `Menampilkan ${filteredOfficers.length} dari ${mockOfficers.length} petugas`;
    
    // Tampilkan/sembunyikan empty state
    document.getElementById('empty-state').classList.toggle('hidden', filteredOfficers.length > 0);
}


function renderPetugasTable(petugas) {
    const tbody = document.getElementById('tabel-body-petugas');
    tbody.innerHTML = '';
    
    petugas.forEach(p => {
        const row = tbody.insertRow();
        row.className = 'border-b border-gray-100 hover:bg-gray-50';
        const statusHtml = getStatusBadgeHtml(p.status);

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
                <button onclick="showOfficerDetail('${p.id}')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">Detail</button>
            </td>
        `;
    });
}

// --- 4. HANDLER INTERAKSI ---
document.getElementById('searchPetugas').addEventListener('input', (e) => {
    currentSearch = e.target.value;
    filterAndRenderTable();
});

document.getElementById('statusFilter').addEventListener('change', (e) => {
    currentFilter = e.target.value;
    filterAndRenderTable();
});

function showOfficerDetail(id) {
    const officer = mockOfficers.find(o => o.id === id);
    alert(`Simulasi Detail Petugas:\nNama: ${officer.name}\nDepartemen: ${officer.dept}\nTugas Aktif: ${officer.activeTasksCount}\nStatus: ${officer.status.toUpperCase()}`);
}

document.addEventListener('DOMContentLoaded', () => {
    filterAndRenderTable();
});