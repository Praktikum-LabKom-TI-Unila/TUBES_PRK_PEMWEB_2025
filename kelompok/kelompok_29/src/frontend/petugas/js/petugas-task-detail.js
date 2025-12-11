// File: js/petugas-task-detail.js

let taskData = window.taskData || {};

// --- 1. DUMMY DATA Timeline ---
const mockTimeline = [
    { status: 'selesai', date: '2025-10-18', notes: 'Penyelesaian disetujui (Admin).', actor: 'Admin System' },
    { status: 'menunggu-validasi-admin', date: '2025-10-17', notes: 'Petugas mengirim bukti penyelesaian.', actor: 'Budi Santoso' },
    { status: 'dalam-proses', date: '2025-10-15', notes: 'Petugas mulai menangani masalah di lapangan.', actor: 'Budi Santoso' },
    { status: 'ditugaskan-ke-petugas', date: '2025-10-12', notes: 'Tugas diterima dan ditugaskan.', actor: 'Admin System' },
];

// --- 2. FUNGSI UTILITAS & RENDERING ---

function getStatusBadgeHtml(status) {
    let text = status.toUpperCase().replace(/-/g, ' ');
    let classes = 'inline-block px-3 py-1 rounded-full font-semibold text-xs ';
    
    // Mapping Status ke Tailwind Classes
    if (status.includes('proses') || status.includes('ditugaskan')) classes += 'bg-yellow-100 text-yellow-700';
    else if (status.includes('selesai')) classes += 'bg-green-100 text-green-700';
    else if (status.includes('validasi')) classes += 'bg-red-100 text-red-700';
    
    return `<span class="${classes}">${text}</span>`;
}

function renderTimeline(timeline) {
    const container = document.getElementById('timelineContainer');
    let html = '<div class="timeline-wrapper relative pt-2">';
    
    timeline.forEach(item => {
        const colorClass = item.status.includes('selesai') ? 'bg-green-600' : (item.status.includes('proses') || item.status.includes('ditugaskan') ? 'bg-yellow-600' : 'bg-blue-600');
        
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
    // Garis vertikal
    html = `<div class="absolute top-0 bottom-0 left-0 w-0.5 bg-gray-300 ml-1"></div>` + html;
    container.innerHTML = html;
}

function renderActionButtons(status) {
    const container = document.getElementById('actionButtonsContainer');
    const taskId = taskData.id;
    let html = '';

    const navigationButton = `
        <button onclick="window.open('https://maps.google.com/?q=${taskData.location}', '_blank')"
            class="bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2 font-semibold">
            <i class="material-icons">navigation</i> Arahkan ke Lokasi
        </button>
    `;

    switch (status) {
        case 'ditugaskan-ke-petugas':
            html = `
                <button onclick="handleStartProcess('${taskId}')" class="flex-1 bg-yellow-600 text-white py-3 rounded-lg hover:bg-yellow-700 transition-colors flex items-center justify-center gap-2 font-semibold">
                    <i class="material-icons">play_arrow</i> Mulai Proses
                </button>
                ${navigationButton}
            `;
            break;
        case 'dalam-proses':
            html = `
                <button onclick="handleCancelProcess('${taskId}')" class="flex-1 bg-white border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2 font-semibold">
                    <i class="material-icons">close</i> Batal Proses
                </button>
                <a href="petugas-upload-proof.php?id=${taskId}" class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2 font-semibold">
                    <i class="material-icons">check_circle</i> Upload Bukti Selesai
                </a>
                ${navigationButton}
            `;
            break;
        case 'menunggu-validasi-admin':
            html = `
                <div class="flex-1 bg-orange-50 border border-orange-200 rounded-lg p-4 text-orange-800 font-medium">
                    ⏳ Menunggu validasi dari admin
                </div>
                <a href="petugas-upload-proof.php?id=${taskId}" class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2 font-semibold">
                    <i class="material-icons">edit</i> Edit Bukti
                </a>
            `;
            break;
        case 'selesai':
            html = `
                <div class="flex-1 bg-green-50 border border-green-200 rounded-lg p-4 text-green-800 font-medium">
                    ✅ Tugas telah selesai dan divalidasi
                </div>
            `;
            break;
    }
    container.innerHTML = html;
}


// --- 4. HANDLER INTERAKSI ---

function handleStartProcess(id) {
    // Simulasi AJAX call untuk update status
    if (confirm('Yakin ingin memulai proses pekerjaan?')) {
        alert('Proses pekerjaan dimulai!');
        // Dalam implementasi nyata: fetch('api/start-task.php', { method: 'POST', body: { id } })
        window.location.reload(); 
    }
}

function handleCancelProcess(id) {
    if (confirm('Yakin ingin membatalkan proses? Status akan kembali menjadi Ditugaskan.')) {
        alert('Proses pekerjaan dibatalkan.');
        // Dalam implementasi nyata: fetch('api/cancel-task.php', { method: 'POST', body: { id } })
        window.location.reload();
    }
}


// --- EKSEKUSI ---
document.addEventListener('DOMContentLoaded', () => {
    // Ambil data tiket global saat DOM siap
    taskData = window.taskData || {};
    
    if (taskData && taskData.status) {
        document.getElementById('statusBadgeContainer').innerHTML = getStatusBadgeHtml(taskData.status);
        renderActionButtons(taskData.status);
        renderTimeline(mockTimeline); 
    } else {
        console.error("Task data is missing or incomplete.");
    }
});