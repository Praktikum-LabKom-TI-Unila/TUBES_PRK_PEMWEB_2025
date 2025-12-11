// File: js/warga-detail-pengaduan.js

let complaintData = window.complaintData || {};

// --- DUMMY DATA Timeline (Sesuai image_6e65aa.png) ---
const mockTimeline = [
    // Ikon di sini adalah ikon dummy/titik karena tidak ada ikon Material Icons yang sama persis
    // Kita gunakan warna background untuk menunjukkan status
    { status: 'diajukan', notes: 'Diajukan', actor: complaintData.reporterName, color: 'blue', isDone: true, date: '2025-12-01' },
    { status: 'diverifikasi-admin', notes: 'Diverifikasi Admin', actor: 'Admin System', color: 'purple', isDone: true, date: '2025-12-02' },
    { status: 'ditugaskan-ke-petugas', notes: 'Ditugaskan ke Petugas', actor: 'Admin System', color: 'orange', isDone: true, date: '2025-12-03' },
    { status: 'dalam-proses', notes: 'Dalam Proses', actor: complaintData.assignedOfficer, color: 'yellow', isDone: true, date: '2025-12-05' },
    { status: 'menunggu-validasi-admin', notes: 'Menunggu Validasi Admin', actor: 'Belum dilakukan', color: 'gray', isDone: false, date: null },
    { status: 'selesai', notes: 'Selesai', actor: 'Belum dilakukan', color: 'gray', isDone: false, date: null },
];

// --- FUNGSI UTILITAS & RENDERING ---

function getStatusBadgeHtml(status) {
    let text;
    let classes = 'inline-block px-3 py-1 rounded-lg font-medium text-sm '; 
    
    if (status === 'selesai') {
        text = 'Selesai';
        classes += 'bg-green-100 text-green-700';
    } else if (status === 'dalam-proses') {
        text = 'Dalam Proses';
        classes += 'bg-yellow-100 text-yellow-700';
    } else if (status === 'diverifikasi-admin' || status === 'ditugaskan-ke-petugas') {
        text = 'Diverifikasi';
        classes += 'bg-blue-100 text-blue-700';
    } else {
        text = status.toUpperCase().replace(/-/g, ' ');
        classes += 'bg-gray-100 text-gray-700';
    }
    
    return `<span class="${classes}">${text}</span>`;
}

function renderTimeline(timeline, currentStatus) {
    const container = document.getElementById('timelineContainer');
    // Tambahkan garis vertikal di awal container
    container.innerHTML = '<div class="timeline-line"></div>'; 

    let foundCurrent = false;

    timeline.forEach(item => {
        const isCurrent = item.status === currentStatus;
        const isDone = item.isDone; 
        
        if (isCurrent) foundCurrent = true;

        // Tentukan warna dot
        let dotColor = 'bg-gray-400';
        if (isCurrent) {
            dotColor = `bg-${item.color}-600`;
        } else if (isDone) {
             // Untuk status yang sudah lewat tapi tidak sedang aktif, kita gunakan warna sesuai data, tapi tidak semua
             // Sesuai gambar, yang aktif/done: biru, ungu, oranye, kuning. Yang belum: abu-abu.
             if (item.status === 'diajukan') dotColor = 'bg-blue-600';
             else if (item.status === 'diverifikasi-admin') dotColor = 'bg-purple-600';
             else if (item.status === 'ditugaskan-ke-petugas') dotColor = 'bg-orange-600';
             else if (item.status === 'dalam-proses') dotColor = 'bg-yellow-600';
             else dotColor = 'bg-gray-400'; // Default untuk yang belum dimulai
        }
        
        // Teks dan warna font
        const notesClasses = isDone || isCurrent ? 'text-gray-800' : 'text-gray-500';
        const actorClasses = isDone || isCurrent ? 'text-gray-600' : 'text-gray-400';
        const actorText = item.actor === 'Belum dilakukan' ? 'Belum dilakukan' : `oleh ${item.actor}`;

        // Jika sudah selesai atau sedang berjalan, berikan font medium pada Notes
        const isNoteBold = isDone || isCurrent;


        // HTML untuk setiap item timeline
        const timelineItemHtml = `
            <div class="flex gap-3 relative timeline-item mb-4">
                <div class="flex flex-col items-center">
                    <div class="timeline-dot ${dotColor} mt-2" style="margin-left: 10px;"></div>
                </div>

                <div class="flex-1 -mt-1">
                    <p class="${notesClasses} ${isNoteBold ? 'font-medium' : ''}">${item.notes}</p>
                    <p class="text-xs ${actorClasses}">${actorText}</p>
                </div>
            </div>
        `;
        
        // Cek apakah ini item terakhir
        if (item.status === 'selesai') {
            // Hilangkan garis vertikal jika sudah mencapai item terakhir.
            // Kita bisa menggunakan CSS untuk ini, tetapi karena struktur HTML yang kita buat di sini tidak punya 
            // elemen terpisah untuk garis di setiap item, kita biarkan saja. 
            // Solusi: Ganti `.timeline-container::before` di CSS dengan `timeline-line` dan hapus garis terakhir di JS 
            // (namun ini lebih kompleks. Kita pertahankan garis penuh untuk kesederhanaan).
            container.innerHTML += timelineItemHtml.replace('<div class="timeline-line"></div>', '');
        } else {
            container.innerHTML += timelineItemHtml;
        }
    });
    
    // Setelah semua item di-render, kita hapus garis yang berada di bawah dot terakhir yang done/current
    const lastDoneDot = container.querySelector(`.timeline-dot.${dotColor}:last-of-type`);
    if(lastDoneDot) {
        // Karena kita menggunakan satu div .timeline-line untuk garis penuh, kita tidak bisa memotongnya dengan mudah di JS.
        // Kita biarkan saja untuk sementara, dan berikan styling dot yang lebih besar pada CSS agar tampak menutupi garis.
    }
}

// --- EKSEKUSI ---
document.addEventListener('DOMContentLoaded', () => {
    // Memperbarui mockTimeline dengan data dinamis
    mockTimeline[0].actor = complaintData.reporterName || 'Ahmad Wijaya';
    mockTimeline[3].actor = complaintData.assignedOfficer || 'Budi Santoso';

    if (complaintData && complaintData.status) {
        // Tentukan isDone pada mockTimeline berdasarkan currentStatus
        let found = false;
        mockTimeline.forEach(item => {
            if (item.status === complaintData.status) {
                found = true;
                item.isDone = true;
            } else if (!found) {
                item.isDone = true;
            } else {
                item.isDone = false;
            }
        });

        document.getElementById('statusBadgeContainer').innerHTML = getStatusBadgeHtml(complaintData.status);
        renderTimeline(mockTimeline, complaintData.status); 
    } else {
        console.error("Complaint data is missing or incomplete.");
    }
});