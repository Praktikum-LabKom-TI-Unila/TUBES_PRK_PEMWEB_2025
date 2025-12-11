// frontend/js/pages/listPengaduan.js
// Meng-handle tampilan daftar pengaduan (menggantikan skrip inline pada HTML)

(() => {
  // State
  let allPengaduan = [];
  let filteredPengaduan = [];

  // Init
  document.addEventListener('DOMContentLoaded', () => {
    loadUserInfo();
    loadPengaduan();
    setupSearchListener();
    setupModalCloseHandlers();
  });

  // Ambil & tampilkan info user
  function loadUserInfo() {
    try {
      const user = getCurrentUser && getCurrentUser();
      if (user) {
        const el = document.getElementById('userName');
        if (el) el.textContent = user.nama || 'Petugas';
      }
    } catch (err) {
      console.warn('loadUserInfo error', err);
    }
  }

  // Fetch pengaduan dari API
  async function loadPengaduan() {
    try {
      // Gunakan apiGet jika tersedia, fallback ke apiRequest
      const fetcher = (typeof apiGet === 'function') ? apiGet : (endpoint => apiRequest(endpoint, { method: 'GET' }));
      const response = await fetcher('/api/pengaduan/unit');

      if (!response) {
        throw new Error('No response from API');
      }

      if (response.success) {
        allPengaduan = response.data || [];
        filteredPengaduan = [...allPengaduan];
        renderPengaduan();
        updateTotalBadge();
      } else {
        // jika response.success=false, tampilkan empty atau pesan
        allPengaduan = [];
        filteredPengaduan = [];
        renderPengaduan();
        updateTotalBadge();
        console.warn('API responded with success=false', response);
      }
    } catch (error) {
      console.error('Error loading pengaduan:', error);
      showEmptyState();
    } finally {
      hideLoading();
    }
  }

  // Render list pengaduan (membangun DOM secara aman)
  function renderPengaduan() {
    const listContainer = document.getElementById('pengaduanList');
    const emptyState = document.getElementById('emptyState');

    if (!listContainer || !emptyState) return;

    // Kosongkan container
    listContainer.innerHTML = '';

    if (!filteredPengaduan || filteredPengaduan.length === 0) {
      listContainer.style.display = 'none';
      emptyState.style.display = 'block';
      return;
    }

    listContainer.style.display = 'flex';
    emptyState.style.display = 'none';

    filteredPengaduan.forEach(pengaduan => {
      const card = document.createElement('div');
      card.className = 'pengaduan-card';

      // Header
      const header = document.createElement('div');
      header.className = 'pengaduan-header';

      const left = document.createElement('div');
      const title = document.createElement('h3');
      title.className = 'pengaduan-title';
      title.textContent = pengaduan.judul_pengaduan || 'Tanpa Judul';
      left.appendChild(title);

      const status = document.createElement('span');
      status.className = `status-badge status-${getStatusClass(pengaduan.status_pengaduan)}`;
      status.textContent = (pengaduan.status_pengaduan || 'Menunggu');

      header.appendChild(left);
      header.appendChild(status);

      // Info grid
      const info = document.createElement('div');
      info.className = 'pengaduan-info';

      info.appendChild(createInfoItem('Pelapor', pengaduan.nama_mahasiswa || '-'));
      info.appendChild(createInfoItem('NPM', pengaduan.npm_pelapor || '-'));
      info.appendChild(createInfoItem('Kategori', pengaduan.kategori_pengaduan || 'Fasilitas'));
      info.appendChild(createInfoItem('Tanggal', formatDate(pengaduan.tanggal_pengaduan)));

      // Footer with button
      const footer = document.createElement('div');
      footer.className = 'pengaduan-footer';

      const btn = document.createElement('button');
      btn.className = 'btn btn-primary';
      btn.type = 'button';
      btn.title = 'Lihat Detail';
      btn.innerHTML = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                       </svg> Lihat Detail`;
      // bind click -> gunakan id_pengaduan; pastikan fungsi global tersedia
      btn.addEventListener('click', () => {
        // Jika showDetail di global, panggil; kalau tidak, gunakan internal
        if (typeof window.showDetail === 'function') {
          window.showDetail(pengaduan.id_pengaduan);
        } else {
          showDetail(pengaduan.id_pengaduan);
        }
      });

      footer.appendChild(btn);

      // Susun card
      card.appendChild(header);
      card.appendChild(info);
      card.appendChild(footer);

      listContainer.appendChild(card);
    });
  }

  // Helper: buat item info
  function createInfoItem(labelText, valueText) {
    const wrapper = document.createElement('div');
    wrapper.className = 'info-item';

    const label = document.createElement('span');
    label.className = 'info-label';
    label.textContent = labelText;

    const value = document.createElement('span');
    value.className = 'info-value';
    value.textContent = valueText;

    wrapper.appendChild(label);
    wrapper.appendChild(value);
    return wrapper;
  }

  // Tampilkan detail ke modal
  function showDetail(id) {
    const pengaduan = allPengaduan.find(p => p.id_pengaduan === id);
    if (!pengaduan) return;

    setText('detailNomor', pengaduan.nomor_pengaduan || '-');
    setText('detailKategori', pengaduan.kategori_pengaduan || 'Fasilitas');
    setText('detailTanggal', formatDate(pengaduan.tanggal_pengaduan));
    setText('detailNama', pengaduan.nama_mahasiswa || '-');
    setText('detailNpm', pengaduan.npm_pelapor || '-');
    setText('detailJudul', pengaduan.judul_pengaduan || 'Tanpa Judul');
    setText('detailDeskripsi', pengaduan.deskripsi_pengaduan || 'Tidak ada deskripsi');
    setText('detailLokasi', pengaduan.lokasi_kejadian || '-');

    const statusBadgeHtml = `<span class="status-badge status-${getStatusClass(pengaduan.status_pengaduan)}">${pengaduan.status_pengaduan || 'Menunggu'}</span>`;
    const statusContainer = document.getElementById('detailStatus');
    if (statusContainer) statusContainer.innerHTML = statusBadgeHtml;

    const modal = document.getElementById('detailModal');
    if (modal) modal.classList.add('active');
  }

  // Utility untuk set textContent jika elemen ada
  function setText(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
  }

  // Tutup modal
  function closeDetailModal() {
    const modal = document.getElementById('detailModal');
    if (modal) modal.classList.remove('active');
  }

  // Setup pencarian
  function setupSearchListener() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    searchInput.addEventListener('input', function (e) {
      const query = (e.target.value || '').toLowerCase().trim();

      if (query === '') {
        filteredPengaduan = [...allPengaduan];
      } else {
        filteredPengaduan = allPengaduan.filter(p => {
          return (
            (p.judul_pengaduan || '').toLowerCase().includes(query) ||
            (p.nama_mahasiswa || '').toLowerCase().includes(query) ||
            (p.npm_pelapor || '').toLowerCase().includes(query) ||
            (p.kategori_pengaduan || '').toLowerCase().includes(query) ||
            (p.status_pengaduan || '').toLowerCase().includes(query)
          );
        });
      }

      renderPengaduan();
      updateTotalBadge();
    });
  }

  // Update jumlah pengaduan di badge
  function updateTotalBadge() {
    const el = document.getElementById('totalPengaduan');
    if (el) el.textContent = filteredPengaduan.length;
  }

  // Map status ke kelas
  function getStatusClass(status) {
    const statusMap = {
      'menunggu': 'menunggu',
      'diproses': 'diproses',
      'selesai': 'selesai'
    };
    return statusMap[(status || '').toLowerCase()] || 'menunggu';
  }

  // Format tanggal
  function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date)) return dateString;
    return date.toLocaleDateString('id-ID', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }

  // Hide loading indicator
  function hideLoading() {
    const el = document.getElementById('loadingIndicator');
    if (el) el.style.display = 'none';
  }

  // Tampilkan empty state
  function showEmptyState() {
    const loading = document.getElementById('loadingIndicator');
    const list = document.getElementById('pengaduanList');
    const empty = document.getElementById('emptyState');

    if (loading) loading.style.display = 'none';
    if (list) list.style.display = 'none';
    if (empty) empty.style.display = 'block';
  }

  // Logout handler dipanggil dari tombol header
  window.handleLogout = function handleLogout() {
    if (confirm('Apakah Anda yakin ingin keluar?')) {
      if (typeof logout === 'function') {
        logout();
      }
      window.location.href = '../login.html';
    }
  };

  // Setup agar klik di luar modal menutupnya dan ESC juga
  function setupModalCloseHandlers() {
    const modal = document.getElementById('detailModal');
    if (modal) {
      modal.addEventListener('click', function (e) {
        if (e.target === modal) {
          closeDetailModal();
        }
      });
    }

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        closeDetailModal();
      }
    });

    // expose closeDetailModal dan showDetail ke global agar kompatibel dengan HTML lama
    window.closeDetailModal = closeDetailModal;
    window.showDetail = function (id) { showDetail(id); };
  }

})();