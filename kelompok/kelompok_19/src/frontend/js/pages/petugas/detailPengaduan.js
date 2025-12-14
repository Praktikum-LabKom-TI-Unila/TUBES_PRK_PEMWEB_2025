// Petugas Detail Pengaduan Logic

let complaintId = null;
let complaintData = null;

// Get complaint ID from URL
function getComplaintId() {
  const params = new URLSearchParams(window.location.search);
  return params.get("id");
}

// Logout function
function logout() {
  if (confirm("Yakin ingin keluar?")) {
    apiGet("/logout")
      .then(() => {
        window.location.href = "../../login.html";
      })
      .catch(() => {
        window.location.href = "../../login.html";
      });
  }
}

// Load complaint detail
function loadComplaintDetail() {
  complaintId = getComplaintId();

  if (!complaintId) {
    alert("ID pengaduan tidak ditemukan");
    window.location.href = "listPengaduan.html";
    return;
  }

  apiGet(`/petugas/complaints/${complaintId}`)
    .then((response) => {
      if (response.success && response.data) {
        complaintData = response.data.complaint;
        const notes = response.data.notes || [];

        displayComplaintDetail(complaintData);
        displayNotes(notes);
      } else {
        alert("Pengaduan tidak ditemukan");
        window.location.href = "listPengaduan.html";
      }
    })
    .catch((error) => {
      console.error("Error loading complaint:", error);
      alert("Terjadi kesalahan saat memuat data");
    });
}

// Display complaint detail
function displayComplaintDetail(complaint) {
  console.log("Displaying complaint:", complaint);

  // Helper function to safely set text content
  const setText = (id, value) => {
    const el = document.getElementById(id);
    if (el) el.textContent = value || "-";
  };

  // Set title and ID - match HTML IDs
  setText("judul", complaint.title);
  setText("subtitleId", `ID Pengaduan: ${complaint.id || "-"}`);

  // Set mahasiswa info - match HTML IDs
  setText("pelaporNama", complaint.mahasiswa_name);
  setText("pelaporNpm", `NIM: ${complaint.nim || "-"}`);
  setText("pelaporEmail", complaint.mahasiswa_email);

  // Set complaint info - match HTML IDs
  setText("kategori", complaint.category_name);
  setText("deskripsi", complaint.description);
  setText("tanggal", formatDate(complaint.created_at));

  // Set status badge in topStatusContainer
  const topStatusContainer = document.getElementById("topStatusContainer");
  if (topStatusContainer) {
    topStatusContainer.innerHTML = `
      <span class="px-3 py-1 rounded-full text-xs font-semibold ${getStatusClass(
        complaint.status
      )}">
        ${complaint.status}
      </span>
    `;
  }

  // Set status select
  const statusSelect = document.getElementById("statusSelect");
  if (statusSelect) {
    statusSelect.value = complaint.status;
  }

  // Display evidence
  if (complaint.evidence_path) {
    const evidenceContainer = document.getElementById("evidenceContainer");
    if (evidenceContainer) {
      evidenceContainer.innerHTML = `
        <a href="${API_URL}${complaint.evidence_path}" target="_blank" class="text-blue-600 hover:underline text-sm">
          📎 Lihat Bukti Pendukung
        </a>
      `;
    }
  }
}

// Display notes/history
function displayNotes(notes) {
  // Try multiple possible container IDs
  let container = document.getElementById("notesContainer");
  if (!container) {
    container = document.getElementById("historyTimeline");
  }

  if (!container) {
    console.warn("Notes container not found");
    return;
  }

  if (!notes || notes.length === 0) {
    container.innerHTML =
      '<p class="text-gray-500 text-center py-4">Belum ada catatan</p>';
    return;
  }

  container.innerHTML = notes
    .map(
      (note) => `
    <div class="border-l-4 border-blue-600 bg-gray-50 p-4 rounded mb-3">
      <div class="flex justify-between items-start mb-2">
        <div>
          <p class="font-semibold text-gray-900">${escapeHtml(
            note.petugas_name || "Petugas"
          )}</p>
          <p class="text-sm text-gray-600">${escapeHtml(
            note.jabatan || "Petugas"
          )}</p>
        </div>
        <p class="text-sm text-gray-500">${formatDateTime(note.created_at)}</p>
      </div>
      <p class="text-gray-700">${escapeHtml(note.note || "")}</p>
    </div>
  `
    )
    .join("");
}

// Escape HTML helper
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}

// Update status
function updateStatus() {
  const newStatus = document.getElementById("statusSelect").value;

  if (!newStatus) {
    alert("Pilih status terlebih dahulu");
    return;
  }

  if (!confirm(`Ubah status menjadi ${newStatus}?`)) {
    return;
  }

  const submitBtn = document.getElementById("btnUpdateStatus");
  submitBtn.disabled = true;
  submitBtn.textContent = "Menyimpan...";

  apiPatch(`/petugas/complaints/${complaintId}/status`, { status: newStatus })
    .then((response) => {
      if (response.success) {
        alert("Status berhasil diperbarui");
        loadComplaintDetail(); // Reload
      } else {
        alert(response.message || "Gagal memperbarui status");
      }
    })
    .catch((error) => {
      console.error("Error updating status:", error);
      alert("Terjadi kesalahan saat memperbarui status");
    })
    .finally(() => {
      submitBtn.disabled = false;
      submitBtn.textContent = "Update Status";
    });
}

// Add note
function addNote() {
  const noteInput = document.getElementById("noteInput");
  const note = noteInput.value.trim();

  if (!note) {
    alert("Masukkan catatan terlebih dahulu");
    return;
  }

  const submitBtn = document.getElementById("btnAddNote");
  submitBtn.disabled = true;
  submitBtn.textContent = "Menyimpan...";

  apiPost(`/petugas/complaints/${complaintId}/notes`, { note: note })
    .then((response) => {
      if (response.success) {
        alert("Catatan berhasil ditambahkan");
        noteInput.value = "";
        loadComplaintDetail(); // Reload
      } else {
        alert(response.message || "Gagal menambahkan catatan");
      }
    })
    .catch((error) => {
      console.error("Error adding note:", error);
      alert("Terjadi kesalahan saat menambahkan catatan");
    })
    .finally(() => {
      submitBtn.disabled = false;
      submitBtn.textContent = "Tambah Catatan";
    });
}

// Save changes (update status and add note combined)
async function saveChanges() {
  const newStatus = document.getElementById("statusSelect").value;
  const catatan = document.getElementById("catatanInput").value.trim();
  const submitBtn = document.getElementById("saveStatusBtn");

  // Validation
  if (!newStatus) {
    alert("Pilih status terlebih dahulu");
    return;
  }

  if (!catatan) {
    alert("Catatan wajib diisi");
    return;
  }

  if (!confirm(`Ubah status menjadi ${newStatus} dan simpan catatan?`)) {
    return;
  }

  submitBtn.disabled = true;
  submitBtn.textContent = "Menyimpan...";

  try {
    // 1. Update status
    console.log("Updating status to:", newStatus);
    const statusResponse = await apiPatch(
      `/petugas/complaints/${complaintId}/status`,
      { status: newStatus }
    );

    if (!statusResponse.success) {
      throw new Error(statusResponse.message || "Gagal update status");
    }

    // 2. Add note
    console.log("Adding note:", catatan);
    const noteResponse = await apiPost(
      `/petugas/complaints/${complaintId}/notes`,
      { note: catatan }
    );

    if (!noteResponse.success) {
      throw new Error(noteResponse.message || "Gagal menambahkan catatan");
    }

    // Success
    alert("Perubahan berhasil disimpan!");
    document.getElementById("catatanInput").value = "";
    loadComplaintDetail(); // Reload data
  } catch (error) {
    console.error("Error saving changes:", error);
    alert(error.message || "Terjadi kesalahan saat menyimpan perubahan");
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = "Simpan Perubahan";
  }
}

// Helper functions
function getStatusClass(status) {
  switch (status) {
    case "MENUNGGU":
      return "bg-yellow-100 text-yellow-800";
    case "DIPROSES":
      return "bg-blue-100 text-blue-800";
    case "SELESAI":
      return "bg-green-100 text-green-800";
    default:
      return "bg-gray-100 text-gray-800";
  }
}

function formatDate(dateString) {
  if (!dateString) return "-";
  const date = new Date(dateString);
  return date.toLocaleDateString("id-ID", {
    day: "2-digit",
    month: "long",
    year: "numeric",
  });
}

function formatDateTime(dateString) {
  if (!dateString) return "-";
  const date = new Date(dateString);
  return date.toLocaleString("id-ID", {
    day: "2-digit",
    month: "long",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

// Load user info
function loadUserInfo() {
  try {
    const user = getCurrentUser();
    console.log("User in detailPengaduan:", user);
    if (user && user.name) {
      const headerUserName = document.getElementById("headerUserName");
      if (headerUserName) {
        headerUserName.textContent = user.name;
      }
    }
  } catch (error) {
    console.error("Error loading user info:", error);
  }
}

// Initialize
document.addEventListener("DOMContentLoaded", function () {
  loadUserInfo();
  loadComplaintDetail();

  // Save changes button (status + notes)
  const btnSaveStatus = document.getElementById("saveStatusBtn");
  if (btnSaveStatus) {
    btnSaveStatus.addEventListener("click", saveChanges);
  }

  // Logout button
  const btnLogout = document.getElementById("btnLogout");
  if (btnLogout) {
    btnLogout.addEventListener("click", logout);
  }
});
