// admin_dashboard.js - JavaScript untuk dashboard admin
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
    loadCharts();
    loadMap();
});

// Load statistics
async function loadStatistics() {
    try {
        const response = await fetch('../api/statistik_data.php');
        const result = await response.json();
        
        if (result.success) {
            const data = result.data;
            document.getElementById('total-laporan').textContent = data.total.total_laporan || 0;
            document.getElementById('total-petugas').textContent = data.total.total_petugas || 0;
            document.getElementById('total-pelapor').textContent = data.total.total_pelapor || 0;
            document.getElementById('total-penugasan').textContent = data.total.total_penugasan || 0;
        }
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

// Load charts
async function loadCharts() {
    try {
        const response = await fetch('../api/statistik_data.php');
        const result = await response.json();
        
        if (result.success) {
            const data = result.data;
            
            // Chart Status
            new Chart(document.getElementById('chart-status'), {
                type: 'doughnut',
                data: {
                    labels: ['Baru', 'Diproses', 'Selesai'],
                    datasets: [{
                        data: [
                            data.status.baru || 0,
                            data.status.diproses || 0,
                            data.status.selesai || 0
                        ],
                        backgroundColor: ['#f59e0b', '#3b82f6', '#10b981']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Chart Kategori
            new Chart(document.getElementById('chart-kategori'), {
                type: 'pie',
                data: {
                    labels: ['Organik', 'Non-Organik', 'Lainnya'],
                    datasets: [{
                        data: [
                            data.kategori.organik || 0,
                            data.kategori['non-organik'] || 0,
                            data.kategori.lainnya || 0
                        ],
                        backgroundColor: ['#22c55e', '#ef4444', '#8b5cf6']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Chart Trend
            const trendLabels = data.trend_bulan.map(item => item.bulan);
            const trendData = data.trend_bulan.map(item => parseInt(item.jumlah));
            
            new Chart(document.getElementById('chart-trend'), {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Jumlah Laporan',
                        data: trendData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    } catch (error) {
        console.error('Error loading charts:', error);
    }
}

// Load map
async function loadMap() {
    // Initialize map centered on Indonesia
    const map = L.map('map').setView([-2.5, 118], 5);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    try {
        const response = await fetch('../api/map_data.php');
        const result = await response.json();
        
        if (result.success && result.data.length > 0) {
            const markers = [];
            
            result.data.forEach(laporan => {
                if (laporan.lat && laporan.lng) {
                    // Status color
                    let color = 'orange';
                    if (laporan.status === 'selesai') color = 'green';
                    else if (laporan.status === 'diproses') color = 'blue';
                    
                    const marker = L.circleMarker([laporan.lat, laporan.lng], {
                        radius: 8,
                        fillColor: color,
                        color: '#fff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(map);
                    
                    marker.bindPopup(`
                        <div class="p-2">
                            <h4 class="font-bold text-sm">${laporan.judul}</h4>
                            <p class="text-xs text-gray-600">${laporan.kategori}</p>
                            <p class="text-xs"><span class="font-semibold">Status:</span> ${laporan.status}</p>
                            <p class="text-xs"><span class="font-semibold">Pelapor:</span> ${laporan.nama_pelapor}</p>
                            <p class="text-xs text-gray-500">${new Date(laporan.created_at).toLocaleDateString('id-ID')}</p>
                        </div>
                    `);
                    
                    markers.push(marker);
                }
            });
            
            // Fit bounds to show all markers
            if (markers.length > 0) {
                const group = L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }
    } catch (error) {
        console.error('Error loading map data:', error);
    }
}
