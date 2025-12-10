// --- 1. DUMMY DATA ---
const mockComplaintsData = {
    total: 6, baru: 1, proses: 4, selesai: 1,
    stats: [
        { label: 'Total', count: 6, subtitle: 'Pengaduan', icon: 'description', color: 'gray', borderColor: 'gray-200', bgColor: 'gray-100' },
        { label: 'Baru', count: 1, subtitle: 'Menunggu', icon: 'schedule', color: 'blue', borderColor: 'blue-200', bgColor: 'blue-100' },
        { label: 'Proses', count: 4, subtitle: 'Sedang Ditangani', icon: 'watch_later', color: 'yellow', borderColor: 'yellow-200', bgColor: 'yellow-100' },
        { label: 'Selesai', count: 1, subtitle: 'Terselesaikan', icon: 'check_circle', color: 'green', borderColor: 'green-200', bgColor: 'green-100' }
    ],
    menu: [
        { title: "Kelola Tiket", subtitle: "Manajemen semua pengaduan", icon: 'receipt_long', link: "admin-manajemen-tiket.php" },
        { title: "Buat Akun Petugas", subtitle: "Tambahkan petugas baru", icon: 'person_add', link: "admin-buat-petugas.php" },
        { title: "Kelola Petugas", subtitle: "Lihat daftar petugas", icon: 'group', link: "admin-kelola-petugas.php" }
    ],
    aktivitasTerbaru: [
        { id: 'TKT-001', title: 'Jalan Berlubang di Jl. Sudirman', reporter: 'Ahmad Wijaya', image: 'img/dummy-road.jpg', status: 'diproses' },
        { id: 'TKT-002', title: 'Lampu Jalan Mati di Perumahan Griya Asri', reporter: 'Siti Nurhaliza', image: 'img/dummy-lamp.jpg', status: 'selesai' },
    ]
};

// --- 2. FUNGING RENDERING ---

function renderStatsCards() {
    const container = document.getElementById('stats-cards-container');
    let htmlContent = '';

    mockComplaintsData.stats.forEach(item => {
        htmlContent += `
            <div class="bg-white rounded-xl p-6 border border-${item.borderColor}">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-${item.bgColor} rounded-lg flex items-center justify-center text-${item.color}-600 text-2xl">
                        <i class="material-icons">${item.icon}</i>
                    </div>
                    <span class="text-${item.color}-600 text-sm">${item.label}</span>
                </div>
                <div class="text-${item.color}-600 text-2xl font-bold mb-1">${item.count}</div>
                <p class="text-gray-600">${item.subtitle}</p>
            </div>
        `;
    });
    container.innerHTML = htmlContent;
}

function renderMenuUtama() {
    const container = document.getElementById('menu-utama-container');
    let htmlContent = '';

    mockComplaintsData.menu.forEach(item => {
        htmlContent += `
            <a href="${item.link}" class="p-6 border-2 border-gray-200 rounded-xl hover:border-blue-600 hover:bg-blue-50 transition-all text-left block">
                <i class="material-icons text-blue-600 text-3xl mb-3">${item.icon}</i>
                <div class="text-gray-900 font-semibold mb-1">${item.title}</div>
                <p class="text-gray-600 text-sm">${item.subtitle}</p>
            </a>
        `;
    });
    container.innerHTML = htmlContent;
}

function renderAktivitasTerbaru() {
    const container = document.getElementById('aktivitas-terbaru-container');
    let htmlContent = '';

    mockComplaintsData.aktivitasTerbaru.forEach(item => {
        const statusClass = item.status === 'selesai' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
        const statusText = item.status === 'selesai' ? 'Selesai' : 'Proses';
        
        htmlContent += `
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-200 flex-shrink-0">
                    <img src="${item.image}" alt="" class="w-full h-full object-cover" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-gray-900 font-medium truncate">${item.title}</p>
                    <p class="text-gray-500 text-sm">#${item.id} - ${item.reporter}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium ${statusClass}">${statusText}</span>
            </div>
        `;
    });
    container.innerHTML = htmlContent;
}

document.addEventListener('DOMContentLoaded', () => {
    renderStatsCards();
    renderMenuUtama();
    renderAktivitasTerbaru();
});