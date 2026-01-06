// File: js/admin-select-officer.js

// --- 1. DUMMY DATA Petugas ---
const mockOfficers = [
    { id: "EMP-2023-001", name: "Budi Santoso", department: "Dinas Pekerjaan Umum", specialization: "Jalan & Trotoar", status: "available", activeTasksCount: 2, email: "budi.s@infra.go.id", phone: "081234567890" },
    { id: "EMP-2023-002", name: "Andi Pratama", department: "Dinas Energi & Listrik", specialization: "Penerangan & Listrik", status: "available", activeTasksCount: 1, email: "andi.p@infra.go.id", phone: "081234567891" },
    { id: "EMP-2023-003", name: "Sari Wulandari", department: "Dinas Pengairan", specialization: "Drainase & Air", status: "busy", activeTasksCount: 3, email: "sari.w@infra.go.id", phone: "081234567892" },
];

let currentTicketId = null;

// --- 2. HANDLER LOGIKA ---

function handleSelectOfficer(officer) {
    if (confirm(`Tugaskan tiket ${currentTicketId} kepada ${officer.name}? Status tiket akan diperbarui.`)) {
        // Simulasi POST AJAX (Nanti Ganti dengan fetch() ke PHP)
        alert(`Petugas ${officer.name} berhasil ditugaskan ke tiket ${currentTicketId}!`);
        closeOfficerModal();
        
        window.location.href = `admin-detail-tiket.php?id=${currentTicketId}`;
    }
}

function closeOfficerModal() {
    const modalContainer = document.getElementById('modal-container');
    if (modalContainer) modalContainer.innerHTML = '';
    document.body.style.overflow = ''; 
}

// --- 3. RENDERING MODAL ---

function renderOfficerSelectionModal(ticketId, currentOfficerName) {
    currentTicketId = ticketId;
    const modalContainer = document.getElementById('modal-container');
    if (!modalContainer) return;

    let officerListHtml = '';

    mockOfficers.forEach((officer) => {
        const isSelected = currentOfficerName === officer.name;
        const isAvailable = officer.status === 'available';
        const officerJson = JSON.stringify(officer).replace(/"/g, '&quot;'); 

        const cardClasses = `w-full p-4 border-2 rounded-xl transition-all text-left block ${
            isSelected 
                ? 'border-blue-600 bg-blue-50'
                : isAvailable
                ? 'border-gray-200 hover:border-blue-600 hover:bg-blue-50 cursor-pointer'
                : 'border-gray-200 bg-gray-50 opacity-70 cursor-not-allowed'
        }`;
        
        const avatarClasses = isAvailable ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600';
        const statusBadgeClasses = isAvailable ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
        const statusText = isAvailable ? 'Tersedia' : `${officer.activeTasksCount} Tugas`;

        const actionAttribute = isAvailable || isSelected ? `onclick='handleSelectOfficer(${officerJson})'` : `disabled`;

        officerListHtml += `
            <button ${actionAttribute} class="${cardClasses}">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl ${avatarClasses}">
                        <i class="material-icons">person</i>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-gray-900 font-semibold">${officer.name}</p>
                                    ${isSelected ? '<span class="px-2 py-0.5 bg-blue-600 text-white rounded-full text-xs">Saat Ini</span>' : ''}
                                </div>
                                <p class="text-gray-600 text-sm">${officer.specialization}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusBadgeClasses}">${statusText}</span>
                            </div>
                        </div>

                        <div class="space-y-1 text-gray-600 text-sm">
                            <div class="flex items-center gap-2"><i class="material-icons text-base">work</i><span>${officer.department}</span></div>
                            <div class="flex items-center gap-2"><i class="material-icons text-base">email</i><span>${officer.email}</span></div>
                        </div>
                    </div>
                    ${isSelected ? '<i class="material-icons text-green-600 text-3xl flex-shrink-0">check_circle</i>' : ''}
                </div>
            </button>
        `;
    });

    const modalHtml = `
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center p-4 z-50" onclick="closeOfficerModal()">
            <div class="bg-white rounded-xl max-w-2xl w-full max-h-[80vh] overflow-hidden" onclick="event.stopPropagation()">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Pilih Petugas Lapangan</h2>
                    <button onclick="closeOfficerModal()" class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="material-icons text-xl">close</i>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto h-[50vh]">
                    <div class="space-y-3">
                        ${officerListHtml}
                    </div>
                </div>

                <div class="p-6 border-t border-gray-200 bg-gray-50">
                    <p class="text-gray-600 text-center text-sm">Klik pada petugas untuk menugaskan</p>
                </div>
            </div>
        </div>
    `;

    modalContainer.innerHTML = modalHtml;
    document.body.style.overflow = 'hidden'; 
}