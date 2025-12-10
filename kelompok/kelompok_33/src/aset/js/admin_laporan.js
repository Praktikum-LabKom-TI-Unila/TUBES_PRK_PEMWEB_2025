// admin_laporan.js - JavaScript untuk halaman laporan admin
let currentPage = 1;
let petugasList = [];

document.addEventListener('DOMContentLoaded', function() {
    loadPetugas();
    loadLaporan(1);
});

// Load daftar petugas untuk dropdown
async function loadPetugas() {
    try {
        const response = await fetch('../api/admin/ambil_pengguna.php?role=petugas&per_page=100');
        const result = await response.json();
        
        if (result.success) {
            petugasList = result.data;
            const select = document.getElementById('assign-petugas');
            select.innerHTML = '<option value="">-- Pilih Petugas --</option>';
            
            result.data.forEach(petugas => {
                select.innerHTML += `<option value="${petugas.id}">${petugas.nama} (${petugas.email})</option>`;
            });
        }
    } catch (error) {
        console.error('Error loading petugas:', error);
    }
}

// Load laporan dengan filter
async function loadLaporan(page) {
    currentPage = page;
    
    const status = document.getElementById('filter-status').value;
    const kategori = document.getElementById('filter-kategori').value;
    const search = document.getElementById('filter-search').value;
    
    let url = `../api/admin/ambil_laporan.php?page=${page}`;
    if (status) url += `&status=${status}`;
    if (kategori) url += `&kategori=${kategori}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    
    try {
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            renderTable(result.data);
            renderPagination(result.pagination);
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error loading laporan:', error);
        alert('Terjadi kesalahan saat memuat data');
    }
}

// Render tabel
function renderTable(data) {
    const tbody = document.getElementById('table-body');
    
    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">Tidak ada data</td></tr>';
        return;
    }
    
    tbody.innerHTML = data.map(row => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm">#${row.id}</td>
            <td class="px-6 py-4 text-sm font-medium">${row.judul}</td>
            <td class="px-6 py-4 text-sm">${row.nama_pelapor}</td>
            <td class="px-6 py-4 text-sm">
                <span class="px-2 py-1 text-xs rounded ${getKategoriColor(row.kategori)}">
                    ${row.kategori}
                </span>
            </td>
            <td class="px-6 py-4 text-sm">
                <span class="px-2 py-1 text-xs rounded ${getStatusColor(row.status)}">
                    ${row.status}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">${formatDate(row.created_at)}</td>
            <td class="px-6 py-4 text-sm">
                <div class="flex space-x-2">
                    <a href="detail_laporan_admin.php?id=${row.id}" class="text-blue-600 hover:text-blue-800">Detail</a>
                    ${row.status === 'baru' ? `<button onclick="openAssignModal(${row.id})" class="text-green-600 hover:text-green-800">Tugaskan</button>` : ''}
                </div>
            </td>
        </tr>
    `).join('');
}

// Render pagination
function renderPagination(pagination) {
    const info = document.getElementById('pagination-info');
    const buttons = document.getElementById('pagination-buttons');
    
    info.textContent = `Menampilkan ${pagination.total} data`;
    
    let buttonsHTML = '';
    
    if (pagination.current_page > 1) {
        buttonsHTML += `<button onclick="loadLaporan(${pagination.current_page - 1})" class="px-3 py-1 bg-white border rounded hover:bg-gray-50">Prev</button>`;
    }
    
    for (let i = 1; i <= pagination.total_pages; i++) {
        if (i === pagination.current_page) {
            buttonsHTML += `<button class="px-3 py-1 bg-green-600 text-white rounded">${i}</button>`;
        } else if (i === 1 || i === pagination.total_pages || Math.abs(i - pagination.current_page) <= 2) {
            buttonsHTML += `<button onclick="loadLaporan(${i})" class="px-3 py-1 bg-white border rounded hover:bg-gray-50">${i}</button>`;
        } else if (Math.abs(i - pagination.current_page) === 3) {
            buttonsHTML += `<span class="px-2">...</span>`;
        }
    }
    
    if (pagination.current_page < pagination.total_pages) {
        buttonsHTML += `<button onclick="loadLaporan(${pagination.current_page + 1})" class="px-3 py-1 bg-white border rounded hover:bg-gray-50">Next</button>`;
    }
    
    buttons.innerHTML = buttonsHTML;
}

// Helper functions
function getStatusColor(status) {
    const colors = {
        'baru': 'bg-yellow-100 text-yellow-800',
        'diproses': 'bg-blue-100 text-blue-800',
        'selesai': 'bg-green-100 text-green-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
}

function getKategoriColor(kategori) {
    const colors = {
        'organik': 'bg-green-100 text-green-800',
        'non-organik': 'bg-red-100 text-red-800',
        'lainnya': 'bg-purple-100 text-purple-800'
    };
    return colors[kategori] || 'bg-gray-100 text-gray-800';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Modal functions
function openAssignModal(laporanId) {
    document.getElementById('assign-laporan-id').value = laporanId;
    document.getElementById('modal-assign').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modal-assign').classList.add('hidden');
    document.getElementById('form-assign').reset();
}

// Submit assign
async function submitAssign(event) {
    event.preventDefault();
    
    const laporan_id = document.getElementById('assign-laporan-id').value;
    const petugas_id = document.getElementById('assign-petugas').value;
    const prioritas = document.getElementById('assign-prioritas').value;
    const catatan = document.getElementById('assign-catatan').value;
    
    try {
        const response = await fetch('../api/admin/tugaskan_petugas.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                laporan_id: parseInt(laporan_id),
                petugas_id: parseInt(petugas_id),
                prioritas: prioritas,
                catatan: catatan
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Berhasil menugaskan petugas!');
            closeModal();
            loadLaporan(currentPage);
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error assigning petugas:', error);
        alert('Terjadi kesalahan saat menugaskan petugas');
    }
}
