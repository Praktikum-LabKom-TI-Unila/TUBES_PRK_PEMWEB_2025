// --- 1. DUMMY DATA ---
const mockComplaintsData = {
    total: 6,
    baru: 1,
    proses: 4,
    selesai: 1,
    menu: [
        { title: "Kelola Tiket", subtitle: "Manajemen semua pengaduan", icon: "📑", link: "manajemen-pengaduan.php" },
        { title: "Buat Akun Petugas", subtitle: "Tambahkan petugas baru", icon: "🧑‍💻", link: "tambah-petugas.php" },
        { title: "Kelola Petugas", subtitle: "Lihat daftar petugas", icon: "👥", link: "daftar-petugas.php" }
    ],
    // Data untuk Aktivitas Terbaru
    aktivitasTerbaru: [
        { id: 'TKT-001', title: 'Jalan Berlubang di Jl. Sudirman', reporter: 'Ahmad Wijaya', image: 'img/dummy-road.jpg', status: '' },
        { id: 'TKT-002', title: 'Lampu Jalan Mati di Perumahan Griya Asri', reporter: 'Siti Nurhaliza', image: 'img/dummy-lamp.jpg', status: 'selesai' },
        { id: 'TKT-003', title: 'Saluran Air Tersumbat', reporter: 'Ahmad Wijaya', image: 'img/dummy-drain.jpg', status: '' },
        { id: 'TKT-004', title: 'Trotoar Rusak', reporter: 'Rina Kusuma', image: 'img/dummy-walkway.jpg', status: '' },
        { id: 'TKT-005', title: 'Rambu Lalu Lintas Rusak', reporter: 'Siti Nurhaliza', image: 'img/dummy-sign.jpg', status: '' }
    ]
};

// --- 2. FUNGSI RENDERING ---

function renderStatsCards() {
    const container = document.getElementById('stats-cards-container');
    const stats = [
        { label: 'Total', count: mockComplaintsData.total, subtitle: 'Pengaduan', className: 'status-total', icon: '📄', secondaryLabel: '' },
        { label: 'Baru', count: mockComplaintsData.baru, subtitle: 'Menunggu', className: 'status-baru', icon: '🕒', secondaryLabel: 'Baru' },
        { label: 'Proses', count: mockComplaintsData.proses, subtitle: 'Sedang Ditangani', className: 'status-proses', icon: '🟡', secondaryLabel: 'Proses' },
        { label: 'Selesai', count: mockComplaintsData.selesai, subtitle: 'Terselesaikan', className: 'status-selesai', icon: '✅', secondaryLabel: 'Selesai' }
    ];

    let htmlContent = '';
    stats.forEach(item => {
        htmlContent += `
            <div class="status-card ${item.className}">
                <div class="top-row">
                    <div class="icon-wrap">${item.icon}</div>
                    <span class="status-label">${item.secondaryLabel || item.label}</span>
                </div>
                <div class="bottom-row">
                    <span class="count">${item.count}</span>
                    <p class="keterangan">${item.subtitle}</p>
                </div>
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
            <a href="${item.link}" class="menu-card">
                <div class="icon-wrap">${item.icon}</div>
                <div class="title">${item.title}</div>
                <p class="subtitle">${item.subtitle}</p>
            </a>
        `;
    });
    container.innerHTML = htmlContent;
}

function renderAktivitasTerbaru() {
    const container = document.getElementById('aktivitas-terbaru-container');
    let htmlContent = '';

    mockComplaintsData.aktivitasTerbaru.forEach(item => {
        const badge = item.status === 'selesai' ? '<span class="status-badge selesai">Selesai</span>' : '';
        
        htmlContent += `
            <a href="detail-pengaduan.php?id=${item.id}" class="aktivitas-item">
                <img src="${item.image}" alt="Foto" class="img-thumb">
                <div class="flex-grow">
                    <p class="title">${item.title}</p>
                    <p class="meta">#${item.id} - ${item.reporter}</p>
                </div>
                ${badge}
            </a>
        `;
    });
    container.innerHTML = htmlContent;
}

document.addEventListener('DOMContentLoaded', () => {
    renderStatsCards();
    renderMenuUtama();
    renderAktivitasTerbaru();
});