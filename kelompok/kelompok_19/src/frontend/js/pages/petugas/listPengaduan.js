// frontend/js/pages/listPengaduan.js
// Meng-handle tampilan daftar pengaduan (menggantikan skrip inline pada HTML)

(() => {
  // State
  let allPengaduan = [];
  let filteredPengaduan = [];

  // Init
  document.addEventListener("DOMContentLoaded", () => {
    loadUserInfo();
    loadPengaduan();
    setupSearchListener();
    setupModalCloseHandlers();
  });

  // Ambil & tampilkan info user
  function loadUserInfo() {
    try {
      const user = getCurrentUser && getCurrentUser();
      console.log("User in listPengaduan:", user);
      if (user) {
        const el = document.getElementById("userName");
        if (el) el.textContent = user.name || "Petugas";
      }
    } catch (err) {
      console.warn("loadUserInfo error", err);
    }
  }

  // Fetch pengaduan dari API
  async function loadPengaduan() {
    try {
      // Gunakan apiGet jika tersedia, fallback ke apiRequest
      const fetcher =
        typeof apiGet === "function"
          ? apiGet
          : (endpoint) => apiRequest(endpoint, { method: "GET" });
      const response = await fetcher("/petugas/complaints");

      if (!response) {
        throw new Error("No response from API");
      }

      if (response.success) {
        allPengaduan = response.data.complaints || [];
        filteredPengaduan = [...allPengaduan];
        renderPengaduan();
        updateTotalBadge();
      } else {
        // jika response.success=false, tampilkan empty atau pesan
        allPengaduan = [];
        filteredPengaduan = [];
        renderPengaduan();
        updateTotalBadge();
        console.warn("API responded with success=false", response);
      }
    } catch (error) {
      console.error("Error loading pengaduan:", error);
      showEmptyState();
    } finally {
      hideLoading();
    }
  }

  // Render list pengaduan (membangun DOM secara aman)
  function renderPengaduan() {
    const listContainer = document.getElementById("pengaduanList");
    const emptyState = document.getElementById("emptyState");

    if (!listContainer || !emptyState) return;

    // Kosongkan container
    listContainer.innerHTML = "";

    if (!filteredPengaduan || filteredPengaduan.length === 0) {
      listContainer.style.display = "none";
      emptyState.style.display = "block";
      return;
    }

    listContainer.style.display = "flex";
    emptyState.style.display = "none";

    filteredPengaduan.forEach((pengaduan) => {
      const card = document.createElement("div");
      card.className = "pengaduan-card";

      // Header
      const header = document.createElement("div");
      header.className = "pengaduan-header";

      const left = document.createElement("div");
      const title = document.createElement("h3");
      title.className = "pengaduan-title";
      title.textContent = pengaduan.title || "Tanpa Judul";
      left.appendChild(title);

      const status = document.createElement("span");
      status.className = `status-badge status-${getStatusClass(
        pengaduan.status
      )}`;
      status.textContent = pengaduan.status || "MENUNGGU";

      header.appendChild(left);
      header.appendChild(status);

      // Info grid
      const info = document.createElement("div");
      info.className = "pengaduan-info";

      info.appendChild(
        createInfoItem("Pelapor", pengaduan.mahasiswa_name || "-")
      );
      info.appendChild(createInfoItem("NIM", pengaduan.nim || "-"));
      info.appendChild(
        createInfoItem("Kategori", pengaduan.category_name || "-")
      );
      info.appendChild(
        createInfoItem("Tanggal", formatDate(pengaduan.created_at))
      );

      // Footer with button
      const footer = document.createElement("div");
      footer.className = "pengaduan-footer";

      const btn = document.createElement("button");
      btn.className = "btn btn-primary";
      btn.type = "button";
      btn.title = "Lihat Detail";
      btn.innerHTML = `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                       </svg> Lihat Detail`;
      // bind click -> gunakan id; pastikan fungsi global tersedia
      btn.addEventListener("click", () => {
        // Redirect to detail page instead of modal
        window.location.href = `detailPengaduan.html?id=${pengaduan.id}`;
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
    const wrapper = document.createElement("div");
    wrapper.className = "info-item";

    const label = document.createElement("span");
    label.className = "info-label";
    label.textContent = labelText;

    const value = document.createElement("span");
    value.className = "info-value";
    value.textContent = valueText;

    wrapper.appendChild(label);
    wrapper.appendChild(value);
    return wrapper;
  }

  // Tampilkan detail ke modal
  function showDetail(id) {
    const pengaduan = allPengaduan.find((p) => p.id === id);
    if (!pengaduan) return;

    setText("detailNomor", `#${pengaduan.id}` || "-");
    setText("detailKategori", pengaduan.category_name || "-");
    setText("detailTanggal", formatDate(pengaduan.created_at));
    setText("detailNama", pengaduan.mahasiswa_name || "-");
    setText("detailNpm", pengaduan.nim || "-");
    setText("detailJudul", pengaduan.title || "Tanpa Judul");
    setText("detailDeskripsi", pengaduan.description || "Tidak ada deskripsi");
    setText("detailLokasi", pengaduan.location || "-");

    const statusBadgeHtml = `<span class="status-badge status-${getStatusClass(
      pengaduan.status
    )}">${pengaduan.status || "MENUNGGU"}</span>`;
    const statusContainer = document.getElementById("detailStatus");
    if (statusContainer) statusContainer.innerHTML = statusBadgeHtml;

    const modal = document.getElementById("detailModal");
    if (modal) modal.classList.add("active");
  }

  // Utility untuk set textContent jika elemen ada
  function setText(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
  }

  // Tutup modal
  function closeDetailModal() {
    const modal = document.getElementById("detailModal");
    if (modal) modal.classList.remove("active");
  }

  // Setup pencarian
  function setupSearchListener() {
    const searchInput = document.getElementById("searchInput");
    if (!searchInput) return;

    searchInput.addEventListener("input", function (e) {
      const query = (e.target.value || "").toLowerCase().trim();

      if (query === "") {
        filteredPengaduan = [...allPengaduan];
      } else {
        filteredPengaduan = allPengaduan.filter((p) => {
          return (
            (p.title || "").toLowerCase().includes(query) ||
            (p.mahasiswa_name || "").toLowerCase().includes(query) ||
            (p.nim || "").toLowerCase().includes(query) ||
            (p.category_name || "").toLowerCase().includes(query) ||
            (p.status || "").toLowerCase().includes(query)
          );
        });
      }

      renderPengaduan();
      updateTotalBadge();
    });
  }

  // Update jumlah pengaduan di badge
  function updateTotalBadge() {
    const el = document.getElementById("totalPengaduan");
    if (el) el.textContent = filteredPengaduan.length;
  }

  // Map status ke kelas
  function getStatusClass(status) {
    const statusMap = {
      menunggu: "menunggu",
      diproses: "diproses",
      selesai: "selesai",
    };
    return statusMap[(status || "").toLowerCase()] || "menunggu";
  }

  // Format tanggal
  function formatDate(dateString) {
    if (!dateString) return "-";
    const date = new Date(dateString);
    if (isNaN(date)) return dateString;
    return date.toLocaleDateString("id-ID", {
      year: "numeric",
      month: "long",
      day: "numeric",
    });
  }

  // Hide loading indicator
  function hideLoading() {
    const el = document.getElementById("loadingIndicator");
    if (el) el.style.display = "none";
  }

  // Tampilkan empty state
  function showEmptyState() {
    const loading = document.getElementById("loadingIndicator");
    const list = document.getElementById("pengaduanList");
    const empty = document.getElementById("emptyState");

    if (loading) loading.style.display = "none";
    if (list) list.style.display = "none";
    if (empty) empty.style.display = "block";
  }

  // Logout handler dipanggil dari tombol header
  window.handleLogout = function handleLogout() {
    if (confirm("Apakah Anda yakin ingin keluar?")) {
      if (typeof logout === "function") {
        logout();
      }
      window.location.href = "../login.html";
    }
  };

  // Setup agar klik di luar modal menutupnya dan ESC juga
  function setupModalCloseHandlers() {
    const modal = document.getElementById("detailModal");
    if (modal) {
      modal.addEventListener("click", function (e) {
        if (e.target === modal) {
          closeDetailModal();
        }
      });
    }

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        closeDetailModal();
      }
    });

    // expose closeDetailModal dan showDetail ke global agar kompatibel dengan HTML lama
    window.closeDetailModal = closeDetailModal;
    window.showDetail = function (id) {
      showDetail(id);
    };
  }
})();
