// File: js/admin-manajemen-tiket.js

// --- 1. DUMMY DATA LENGKAP ---
const mockTicketsData = [
    { id: "TKT-001", pelapor: "Ahmad Wijaya", email: "ahmad.wijaya@email.com", judul: "Jalan Berlubang di Jl. Sudirman", kategori: "Jalan Raya", status: "Dalam Proses", lokasi: "Jl. Sudirman No. 45, Jakarta Pusat", petugas: "Budi Santoso" },
    { id: "TKT-002", pelapor: "Siti Nurhaliza", email: "siti.nur@email.com", judul: "Lampu Jalan Mati di Perumahan Griya Asri", kategori: "Penerangan Jalan", status: "Selesai", lokasi: "Perumahan Griya Asri Blok C, Tangerang", petugas: "Andi Pratama" },
    { id: "TKT-003", pelapor: "Ahmad Wijaya", email: "ahmad.wijaya@email.com", judul: "Saluran Air Tersumbat", kategori: "Drainase", status: "Diajukan", lokasi: "Jl. Melati No. 12, Bekasi", petugas: null },
    { id: "TKT-004", pelapor: "Rina Kusuma", email: "rina.k@email.com", judul: "Trotoar Rusak", kategori: "Trotoar", status: "Diverifikasi Admin", lokasi: "Jl. Gatot Subroto, Jakarta Selatan", petugas: null },
    { id: "TKT-005", pelapor: "Siti Nurhaliza", email: "siti.nur@email.com", judul: "Rambu Lalu Lintas Rusak", kategori: "Rambu Lalu Lintas", status: "Ditugaskan ke Petugas", lokasi: "Jl. Ahmad Yani No. 88, Bandung", petugas: "Ahmad Santoso" },
    { id: "TKT-006", pelapor: "Rina Kusuma", email: "rina.k@email.com", judul: "Taman Kota Tidak Terawat", kategori: "Taman", status: "Menunggu Validasi Admin", lokasi: "Taman Kota Mawar, Surabaya", petugas: "Budi Prakoso" },
];

const statusSlugs = [
    { label: "Semua", slug: "all", count: 6 },
    { label: "Diajukan", slug: "diajukan", count: 1 },
    { label: "Diverifikasi", slug: "diverifikasi admin", count: 1 },
    { label: "Ditugaskan", slug: "ditugaskan ke petugas", count: 1 },
    { label: "Dalam Proses", slug: "dalam proses", count: 1 },
    { label: "Menunggu Validasi", slug: "menunggu validasi admin", count: 1 },
    { label: "Selesai", slug: "selesai", count: 1 }
];

let currentFilterStatus = 'all'; 
let currentSearchQuery = '';

// --- 2. FUNGSI UTILITAS ---
function getStatusBadgeHtml(status) {
    let classes = 'inline-block px-3 py-1 text-xs font-semibold rounded-full ';
    const cleanStatus = status.toLowerCase().replace(/ /g, '_');
    
    if (cleanStatus.includes('proses')) classes += 'bg-yellow-100 text-yellow-700';
    else if (cleanStatus.includes('selesai')) classes += 'bg-green-100 text-green-700';
    else if (cleanStatus.includes('diverifikasi')) classes += 'bg-indigo-100 text-indigo-700';
    else if (cleanStatus.includes('ditugaskan')) classes += 'bg-orange-100 text-orange-700';
    else if (cleanStatus.includes('diajukan')) classes += 'bg-blue-100 text-blue-700';
    else if (cleanStatus.includes('validasi')) classes += 'bg-red-100 text-red-700';
    
    return `<span class="${classes}">${status}</span>`;
}

// --- 3. FUNGSI RENDERING ---

function renderStatusTabs() {
    const tabsContainer = document.getElementById('status-tabs');
    tabsContainer.innerHTML = '';

    statusSlugs.forEach(tab => {
        const isActive = tab.slug === currentFilterStatus;
        const classes = isActive 
            ? 'bg-blue-600 text-white border-blue-600' 
            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50';
            
        const button = document.createElement('button');
        button.className = `tab-button px-4 py-2 rounded-lg border transition-colors whitespace-nowrap text-sm ${classes}`;
        button.textContent = `${tab.label} (${tab.count})`;
        
        button.addEventListener('click', () => {
             currentFilterStatus = tab.slug;
             filterAndRenderTable();
             renderStatusTabs(); 
        });
        tabsContainer.appendChild(button);
    });
}

function filterAndRenderTable() {
    let filteredTickets = mockTicketsData.filter(ticket => {
        const matchesStatus = currentFilterStatus === 'all' || 
                              ticket.status.toLowerCase().replace(/ /g, '_') === currentFilterStatus.toLowerCase().replace(/ /g, '_');
        
        const matchesSearch = ticket.judul.toLowerCase().includes(currentSearchQuery) ||
                              ticket.id.toLowerCase().includes(currentSearchQuery) ||
                              ticket.pelapor.toLowerCase().includes(currentSearchQuery);

        return matchesStatus && matchesSearch;
    });
    
    renderTicketTable(filteredTickets);
    document.getElementById('empty-state').classList.toggle('hidden', filteredTickets.length > 0);
}


function renderTicketTable(tickets) {
    const tbody = document.getElementById('tabel-body-pengaduan');
    tbody.innerHTML = '';
    
    tickets.forEach(ticket => {
        const row = tbody.insertRow();
        row.className = 'hover:bg-gray-50 transition-colors';
        const statusHtml = getStatusBadgeHtml(ticket.status);
        
        const isAssignable = ticket.status === 'Diajukan' || ticket.status === 'Diverifikasi Admin';
        const ticketJson = JSON.stringify(ticket).replace(/"/g, '&quot;'); 

        const petugasDisplay = ticket.petugas ? 
            `<p class="text-gray-900">${ticket.petugas}</p>` : 
            `<a href="#" class="text-blue-600 hover:underline flex items-center gap-1 text-sm" onclick='showOfficerModalFromTable(${ticketJson}); return false;'><i class="material-icons text-base">person_add</i> Assign</a>`;

        const actionButton = `<a href="admin-detail-tiket.php?id=${ticket.id}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Lihat Detail"><i class="material-icons text-xl">visibility</i></a>`;


        row.innerHTML = `
            <td class="px-4 py-3"><span class="text-blue-600 font-medium">#${ticket.id}</span></td>
            <td class="px-4 py-3">
                <p class="text-gray-900">${ticket.pelapor}</p>
                <p class="text-gray-500 text-sm">${ticket.email}</p>
            </td>
            <td class="px-4 py-3">
                <p class="text-gray-900 mb-1">${ticket.judul}</p>
                <span class="badge-kategori inline-block px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">${ticket.kategori}</span>
            </td>
            <td class="px-4 py-3">${statusHtml}</td>
            <td class="px-4 py-3"><p class="text-gray-600 max-w-xs truncate">${ticket.lokasi}</p></td>
            <td class="px-4 py-3">${petugasDisplay}</td>
            <td class="px-4 py-3 text-center">${actionButton}</td>
        `;
        tbody.appendChild(row);
    });
}

// Handler baru yang dipanggil dari onclick tabel Manajemen Tiket
function showOfficerModalFromTable(ticketObj) {
    if (typeof renderOfficerSelectionModal !== 'undefined') {
        renderOfficerSelectionModal(ticketObj.id, ticketObj.petugas); 
    } else {
        alert('Error: admin-select-officer.js tidak dimuat.');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    renderStatusTabs();
    filterAndRenderTable(); 

    document.getElementById('searchInput').addEventListener('input', (e) => {
        currentSearchQuery = e.target.value.toLowerCase();
        filterAndRenderTable();
        renderStatusTabs(); 
    });
});