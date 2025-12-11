<?php
require_once 'includes/auth.php';
requireLogin();
$userInfo = getAdminInfo();

// Ambil ID dari URL
$reportId = $_GET['id'] ?? null;
if (!$reportId) {
    header('Location: petugas.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tugas - <?php echo htmlspecialchars($reportId); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js untuk Kurva Progress -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = { 
            theme: { 
                extend: { 
                    fontFamily: { sans: ['Inter', 'sans-serif'] }, 
                    colors: { 
                        smart: { DEFAULT: '#0284c7' }, 
                        eco: { DEFAULT: '#059669' } // Warna utama disamakan dengan dashboard
                    } 
                } 
            } 
        }
    </script>
</head>
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen pb-20">

    <!-- Header Navigation (Disesuaikan dengan Dashboard) -->
    <div class="bg-white border-b border-slate-200 sticky top-0 z-40 w-full h-16 shadow-sm flex items-center px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto w-full flex justify-between items-center">
            <div class="flex items-center gap-4">
                <a href="petugas.php" class="w-9 h-9 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 transition text-slate-600 border border-slate-200">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-slate-800 leading-tight">Detail Pengerjaan</h1>
                    <p class="text-[11px] text-slate-500 font-mono tracking-wide">ID: <?php echo htmlspecialchars($reportId); ?></p>
                </div>
            </div>
            <div class="text-right">
                <span id="header-status" class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold uppercase tracking-wider border border-slate-200">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- KOLOM KIRI (Informasi Utama) - Span 8 -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- 1. Card Informasi Pelapor & Lokasi -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-white px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-eco"></i> Informasi Laporan</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pelapor</p>
                        <p class="text-base font-semibold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-user text-slate-300"></i> <span id="info-reporter">...</span>
                        </p>
                        
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1 mt-4">Kategori</p>
                        <span id="info-category" class="px-2.5 py-1 rounded bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">-</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Lokasi Kejadian</p>
                        <p class="text-sm font-medium text-slate-800 mb-3 line-clamp-2" id="info-location">...</p>
                        <!-- Tombol Maps -->
                        <a id="btn-maps" href="#" target="_blank" class="inline-flex items-center gap-2 text-xs font-bold text-smart bg-blue-50 hover:bg-blue-100 border border-blue-100 px-4 py-2 rounded-lg transition">
                            <i class="fa-solid fa-map-location-dot"></i> Buka Google Maps
                        </a>
                    </div>
                    <div class="md:col-span-2 bg-yellow-50/50 p-4 rounded-xl border border-yellow-100">
                        <p class="text-xs font-bold text-yellow-600 uppercase mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Deskripsi Masalah</p>
                        <p class="text-sm text-slate-700" id="info-desc">...</p>
                    </div>
                </div>
            </div>

            <!-- 2. Before / After Comparison -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-white px-6 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2"><i class="fa-solid fa-image text-eco"></i> Bukti Before & After</h3>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Before -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-1 rounded border border-red-100">BEFORE (Awal)</span>
                            <span class="text-[10px] text-slate-400" id="date-before">-</span>
                        </div>
                        <div class="aspect-video bg-slate-50 rounded-lg overflow-hidden relative group border border-slate-200 cursor-pointer" onclick="viewImage('img-before')">
                            <img id="img-before" src="" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-black/10 hidden group-hover:flex items-center justify-center text-white pointer-events-none"><i class="fa-solid fa-expand drop-shadow-md"></i></div>
                        </div>
                    </div>
                    <!-- After -->
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded border border-emerald-100">AFTER (Terkini)</span>
                            <span class="text-[10px] text-slate-400" id="date-after">-</span>
                        </div>
                        <div class="aspect-video bg-slate-50 rounded-lg overflow-hidden relative group border border-slate-200 cursor-pointer" id="container-after">
                            <img id="img-after" src="" class="w-full h-full object-cover hidden hover:scale-105 transition duration-500">
                            <!-- Placeholder -->
                            <div id="no-progress" class="w-full h-full flex flex-col items-center justify-center text-slate-400">
                                <i class="fa-solid fa-camera-retro text-3xl mb-2 opacity-20"></i>
                                <span class="text-xs">Belum ada update</span>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 italic line-clamp-1" id="note-after"></p>
                    </div>
                </div>
            </div>

            <!-- 3. Kurva Progress (Chart) -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-white px-6 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2"><i class="fa-solid fa-chart-line text-eco"></i> Grafik Progress</h3>
                </div>
                <div class="p-6">
                    <div class="h-64 w-full">
                        <canvas id="progressChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- 4. Riwayat Timeline List -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 mb-6 pl-2 border-l-4 border-eco">Riwayat Aktivitas</h3>
                <div class="space-y-6 relative border-l-2 border-slate-100 ml-3 pl-8" id="timeline-container">
                    <div class="text-sm text-slate-400">Memuat riwayat...</div>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN (Form Update) - Span 4 -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm sticky top-24 overflow-hidden">
                <!-- Header Form lebih bersih (Clean Style) -->
                <div class="bg-white p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-eco/10 text-eco flex items-center justify-center"><i class="fa-solid fa-pen-to-square"></i></div>
                        Update Laporan
                    </h3>
                    <p class="text-xs text-slate-500 mt-1 ml-10">Perbarui status dan bukti pengerjaan.</p>
                </div>
                
                <div class="p-5">
                    <form onsubmit="submitProgress(event)" class="space-y-5">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($reportId); ?>">
                        <input type="hidden" name="user_id" value="<?php echo $userInfo['id']; ?>">
                        
                        <!-- Status Dropdown -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Status Pengerjaan</label>
                            <div class="relative">
                                <select name="status" id="input-status" class="w-full p-3 pl-10 bg-slate-50 hover:bg-white rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-eco/20 focus:border-eco outline-none appearance-none font-semibold text-slate-700 transition cursor-pointer">
                                    <option value="Menunggu">Menunggu</option>
                                    <option value="Diproses">Diproses</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                                <i class="fa-solid fa-list-check absolute left-3.5 top-3.5 text-slate-400"></i>
                                <i class="fa-solid fa-chevron-down absolute right-3.5 top-3.5 text-slate-400 text-xs"></i>
                            </div>
                        </div>

                        <!-- Foto Upload -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Foto Bukti (Wajib)</label>
                            <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-slate-300 border-dashed rounded-xl cursor-pointer bg-slate-50 hover:bg-slate-100 hover:border-eco/50 transition relative overflow-hidden group">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-slate-400 group-hover:text-eco transition" id="upload-placeholder">
                                    <i class="fa-solid fa-camera text-3xl mb-2"></i>
                                    <p class="text-xs font-semibold">Ambil / Upload Foto</p>
                                    <p class="text-[10px]">JPG, PNG (Max 2MB)</p>
                                </div>
                                <img id="preview-upload" class="absolute inset-0 w-full h-full object-cover hidden">
                                <input type="file" name="image" class="hidden" accept="image/*" onchange="previewImage(this)" required>
                            </label>
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-2 uppercase tracking-wider">Catatan Progress</label>
                            <textarea name="notes" rows="4" class="w-full p-3 bg-slate-50 hover:bg-white rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-eco/20 focus:border-eco outline-none resize-none transition" placeholder="Deskripsikan apa yang sudah dikerjakan..." required></textarea>
                        </div>

                        <!-- Submit Button (Warna Eco) -->
                        <button type="submit" id="btn-submit" class="w-full py-3.5 bg-eco hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-100 transition flex justify-center items-center gap-2 group">
                            <span>Kirim Update</span> <i class="fa-solid fa-paper-plane group-hover:translate-x-1 transition"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Gambar Fullscreen -->
    <div id="modal-img" class="fixed inset-0 bg-black/95 z-[100] hidden flex items-center justify-center p-4 cursor-zoom-out" onclick="this.classList.add('hidden')">
        <img id="img-fullscreen" class="max-w-full max-h-screen rounded shadow-2xl">
        <button class="absolute top-5 right-5 text-white text-3xl opacity-70 hover:opacity-100 transition"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <script>
        const API_URL = 'api.php';
        const REPORT_ID = '<?php echo $reportId; ?>';
        let myChart = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadReportData();
            loadHistory();
        });

        // 1. Load Data Utama Laporan
        async function loadReportData() {
            try {
                const res = await fetch(`${API_URL}?action=getReport&id=${REPORT_ID}`);
                const json = await res.json();
                if(json.success) {
                    const data = json.data;
                    
                    // Header Status Badge
                    const statusColors = { 
                        'Menunggu':'text-orange-700 bg-orange-100 border-orange-200', 
                        'Diproses':'text-blue-700 bg-blue-100 border-blue-200', 
                        'Selesai':'text-emerald-700 bg-emerald-100 border-emerald-200' 
                    };
                    const badgeClass = statusColors[data.status] || 'text-slate-600 bg-slate-100 border-slate-200';
                    const headerStatus = document.getElementById('header-status');
                    headerStatus.className = `px-3 py-1.5 rounded-lg text-xs font-bold uppercase tracking-wider border ${badgeClass}`;
                    headerStatus.innerText = data.status;

                    // Form Default Value
                    document.getElementById('input-status').value = data.status;

                    // Info Card
                    document.getElementById('info-reporter').innerText = data.reporter_name || 'Anonim';
                    document.getElementById('info-category').innerText = data.category;
                    document.getElementById('info-location').innerText = data.location;
                    document.getElementById('info-desc').innerText = data.description;
                    
                    // Maps Logic
                    const mapQuery = encodeURIComponent(data.location);
                    document.getElementById('btn-maps').href = `https://www.google.com/maps/search/?api=1&query=${mapQuery}`;

                    // Before Image
                    const imgBefore = document.getElementById('img-before');
                    if(data.img) {
                        imgBefore.src = data.img;
                    } else {
                        imgBefore.src = 'https://via.placeholder.com/400x300?text=Tidak+Ada+Foto';
                    }
                    document.getElementById('date-before').innerText = data.date;
                }
            } catch(e) { console.error('Gagal load report:', e); }
        }

        // 2. Load History, After Image & Chart
        async function loadHistory() {
            try {
                const res = await fetch(`${API_URL}?action=getReportHistory&id=${REPORT_ID}`);
                const json = await res.json();
                const container = document.getElementById('timeline-container');
                container.innerHTML = '';

                let chartLabels = [];
                let chartData = [];

                if(json.success && json.data.length > 0) {
                    const history = json.data;
                    
                    // Logic After Image (Terbaru)
                    const latest = history[0];
                    if(latest.image_path) {
                        const imgAfter = document.getElementById('img-after');
                        imgAfter.src = latest.image_path;
                        imgAfter.classList.remove('hidden');
                        document.getElementById('no-progress').classList.add('hidden');
                        document.getElementById('container-after').setAttribute('onclick', "viewImage('img-after')");
                        document.getElementById('date-after').innerText = latest.date_formatted;
                        document.getElementById('note-after').innerText = latest.notes;
                    }

                    // Render Timeline List
                    history.forEach(item => {
                        const isDone = item.status === 'Selesai';
                        const dotColor = isDone ? 'bg-emerald-500 ring-emerald-200' : (item.status === 'Diproses' ? 'bg-blue-500 ring-blue-200' : 'bg-orange-500 ring-orange-200');
                        
                        container.innerHTML += `
                            <div class="relative pb-2">
                                <span class="absolute -left-[41px] top-1.5 h-4 w-4 rounded-full border-2 border-white ring-2 ${dotColor}"></span>
                                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 hover:border-slate-200 transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="text-xs font-bold text-slate-700">${item.date_formatted}</span>
                                        <span class="text-[10px] px-2 py-0.5 rounded font-bold uppercase bg-white border border-slate-200 text-slate-600">${item.status}</span>
                                    </div>
                                    <p class="text-sm text-slate-600 mb-3">${item.notes}</p>
                                    ${item.image_path ? `<img src="${item.image_path}" class="h-20 rounded-lg border border-slate-200 cursor-pointer hover:opacity-90 transition" onclick="viewSrc('${item.image_path}')">` : ''}
                                    <div class="mt-3 pt-3 border-t border-slate-200/50 flex items-center gap-2 text-[10px] text-slate-400">
                                        <i class="fa-solid fa-user-gear"></i> ${item.petugas_name || 'Petugas'}
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    // Prepare Chart Data
                    const chartHistory = [...history].reverse();
                    chartHistory.forEach(item => {
                        const d = new Date(item.created_at);
                        const label = `${d.getDate()}/${d.getMonth()+1} ${d.getHours()}:${d.getMinutes()}`;
                        let val = 0;
                        if(item.status === 'Menunggu') val = 1;
                        if(item.status === 'Diproses') val = 2;
                        if(item.status === 'Selesai') val = 3;
                        chartLabels.push(label);
                        chartData.push(val);
                    });

                } else {
                    container.innerHTML = `<div class="p-4 bg-slate-50 rounded-xl border border-dashed border-slate-300 text-center text-slate-400 text-sm italic">Belum ada riwayat update.</div>`;
                    chartLabels = ['Mulai'];
                    chartData = [0];
                }

                renderChart(chartLabels, chartData);

            } catch(e) { 
                console.error(e);
                document.getElementById('timeline-container').innerHTML = 'Gagal memuat history.';
            }
        }

        // 3. Render Chart.js (Warna Disesuaikan Eco/Green)
        function renderChart(labels, dataPoints) {
            const ctx = document.getElementById('progressChart').getContext('2d');
            if(myChart) myChart.destroy();

            myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Progress',
                        data: dataPoints,
                        borderColor: '#059669', // Warna Eco
                        backgroundColor: 'rgba(5, 150, 105, 0.05)',
                        borderWidth: 2,
                        tension: 0.4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#059669',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 3.5,
                            ticks: {
                                callback: function(value) {
                                    if(value === 1) return 'Menunggu';
                                    if(value === 2) return 'Diproses';
                                    if(value === 3) return 'Selesai';
                                    return '';
                                },
                                font: { size: 10, family: "'Inter', sans-serif" },
                                color: '#64748b'
                            },
                            grid: { color: '#f1f5f9' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { display: false } // Hide label tanggal jika terlalu panjang
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            titleFont: { family: "'Inter', sans-serif" },
                            bodyFont: { family: "'Inter', sans-serif" },
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    }
                }
            });
        }

        // 4. Handle Submit Form
        async function submitProgress(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Mengirim...';
            btn.disabled = true;

            const formData = new FormData(e.target);
            try {
                const res = await fetch(`${API_URL}?action=updateProgress`, { method:'POST', body:formData });
                const json = await res.json();
                if(json.success) {
                    alert('Update berhasil disimpan!');
                    e.target.reset();
                    document.getElementById('preview-upload').classList.add('hidden');
                    document.getElementById('upload-placeholder').classList.remove('hidden');
                    loadReportData();
                    loadHistory();
                } else {
                    alert('Gagal: ' + json.message);
                }
            } catch(err) {
                alert('Terjadi kesalahan koneksi.');
            } finally {
                btn.innerHTML = originalContent;
                btn.disabled = false;
            }
        }

        // Helpers
        function previewImage(input) {
            if(input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.getElementById('preview-upload');
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    document.getElementById('upload-placeholder').classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        function viewImage(id) { viewSrc(document.getElementById(id).src); }
        function viewSrc(src) {
            document.getElementById('img-fullscreen').src = src;
            document.getElementById('modal-img').classList.remove('hidden');
        }
    </script>
</body>
</html>