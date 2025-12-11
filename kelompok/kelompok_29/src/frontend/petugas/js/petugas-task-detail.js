// File: js/petugas-task-detail.js (Perbaikan Final Tampilan & Logika)

let taskData = window.taskData || {};

// --- 1. DUMMY DATA Timeline (Sesuai 25d3505d-adb5-471a-ba67-f726e9e284e2.jpg) ---
const mockTimeline = [
    { status: 'diajukan', date: '2025-12-01', notes: 'Diajukan oleh Ahmad Wijaya', actor: 'Ahmad Wijaya', icon: 'description', color: 'blue' },
    { status: 'diverifikasi-admin', date: '2025-12-02', notes: 'Diverifikasi oleh Admin System', actor: 'Admin System', icon: 'shield', color: 'indigo' },
    { status: 'ditugaskan-ke-petugas', date: '2025-12-03', notes: 'Ditugaskan ke Petugas', actor: 'Admin System', icon: 'person_add', color: 'orange' },
    { status: 'dalam-proses', date: '2025-12-05', notes: 'Dalam Proses', actor: 'Budi Santoso', icon: 'construction', color: 'yellow' },
    { status: 'menunggu-validasi-admin', date: null, notes: 'Menunggu Validasi Admin', actor: 'Belum dilakukan', icon: 'pending', color: 'gray' },
    { status: 'selesai', date: null, notes: 'Selesai', actor: 'Belum dilakukan', icon: 'check_circle', color: 'gray' },
];

// --- 2. FUNGSI UTILITAS & RENDERING ---

function getStatusBadgeHtml(status) {
    let text = status.toUpperCase().replace(/-/g, ' ');
    let classes = 'inline-block px-3 py-1 rounded-lg font-medium text-sm '; // Rounded-lg untuk badge di header
    
    if (status.includes('proses') || status.includes('ditugaskan')) classes += 'bg-yellow-100 text-yellow-700';
    else if (status.includes('selesai')) classes += 'bg-green-100 text-green-700';
    else if (status.includes('diverifikasi') || status.includes('diajukan')) classes += 'bg-blue-100 text-blue-700';
    
    return `<span class="${classes}">${text}</span>`;
}

function renderTimeline(timeline, currentStatus) {
    const container = document.getElementById('timelineContainer');
    container.innerHTML = '';
    let foundCurrent = false;

    timeline.forEach(item => {
        const isCurrent = item.status === currentStatus;
        const isActive = !foundCurrent && item.date !== null; 
        
        if (isCurrent) foundCurrent = true;

        const iconClasses = isCurrent || isActive ? `text-${item.color}-600 bg-white` : 'text-gray-400 bg-gray-100';
        const notesClasses = isCurrent || isActive ? 'font-medium text-gray-800' : 'text-gray-500';
        const actorText = item.actor === 'Belum dilakukan' && !isCurrent ? 'Belum dilakukan' : item.actor;

        // Tampilan Timeline sesuai 25d3505d-adb5-471a-ba67-f726e9e284e2.jpg
        container.innerHTML += `
            <div class="flex gap-4 relative">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xl border-2 border-white ${iconClasses}">
                        <i class="material-icons text-lg">${item.icon}</i>
                    </div>
                    <div class="flex-grow w-0.5 ${isActive ? 'bg-gray-300' : 'bg-gray-200'}"></div>
                </div>

                <div class="flex-1 pb-6 -mt-1">
                    <p class="${notesClasses}">${item.notes}</p>
                    <p class="text-xs ${isCurrent || isActive ? 'text-gray-600' : 'text-gray-400'}">oleh ${actorText}</p>
                </div>
            </div>
        `;
    });
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
            // Sesuai 25d3505d-adb5-471a-ba67-f726e9e284e2.jpg
            html = `
                <button onclick="handleCancelProcess('${taskId}')" class="flex-1 bg-white border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition-colors flex items-center justify-center gap-2 font-semibold">
                    <i class="material-icons">close</i> Batal Proses
                </button>
                <a href="petugas-upload-proof.php?id=${taskId}" class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2 font-semibold">
                    <i class="material-icons">cloud_upload</i> Upload Bukti Selesai
                </a>
                ${navigationButton}
            `;
            break;
        case 'menunggu-validasi-admin':
            html = `
                <div class="flex-1 bg-orange-50 border border-orange-200 rounded-lg p-3 text-orange-800 font-medium text-sm text-center">
                    ⏳ Menunggu validasi dari admin
                </div>
                <a href="petugas-upload-proof.php?id=${taskId}" class="flex-1 bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center gap-2 font-semibold">
                    <i class="material-icons">edit</i> Edit Bukti
                </a>
            `;
            break;
        case 'selesai':
            html = `
                <div class="w-full bg-green-50 border border-green-200 rounded-lg p-3 text-green-800 font-medium text-center">
                    ✅ Tugas telah selesai dan divalidasi
                </div>
            `;
            break;
    }
    container.innerHTML = html;
}

// --- 4. HANDLER INTERAKSI (SIMULASI UPDATE) ---

function handleStartProcess(id) {
    if (confirm('Yakin ingin memulai proses pekerjaan?')) {
        alert('Proses pekerjaan dimulai! Status diubah menjadi Dalam Proses.');
        // Perlu implementasi AJAX call ke backend di sini
        window.location.reload(); 
    }
}

function handleCancelProcess(id) {
    if (confirm('Yakin ingin membatalkan proses? Status akan kembali menjadi Ditugaskan.')) {
        alert('Proses pekerjaan dibatalkan.');
        // Perlu implementasi AJAX call ke backend di sini
        window.location.reload();
    }
}


// --- EKSEKUSI ---
document.addEventListener('DOMContentLoaded', () => {
    taskData = window.taskData || {};
    
    if (taskData && taskData.status) {
        document.getElementById('statusBadgeContainer').innerHTML = getStatusBadgeHtml(taskData.status);
        renderActionButtons(taskData.status);
        renderTimeline(mockTimeline, taskData.status); 
    } else {
        console.error("Task data is missing or incomplete.");
    }
});