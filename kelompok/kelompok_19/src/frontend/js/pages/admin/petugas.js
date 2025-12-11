// Admin Kelola Petugas Logic
let allPetugas = [];
let allUnits = [];
let editingPetugasId = null;

function logout() {
  if (confirm("Yakin ingin keluar?")) {
    fetch(API_URL + "/logout", {
      method: "GET",
      credentials: "include",
    })
      .then(() => {
        window.location.href = "../../login.html";
      })
      .catch(() => {
        window.location.href = "../../login.html";
      });
  }
}

// Load petugas and units
function loadPetugas() {
  apiGet("/admin/petugas")
    .then((data) => {
      if (data.success) {
        allPetugas = data.data.petugas;
        allUnits = data.data.units;
        renderPetugasTable();
        populateUnitSelect();
      } else {
        showError("Gagal memuat data petugas");
      }
    })
    .catch((error) => {
      console.error("Error loading petugas:", error);
      showError("Terjadi kesalahan saat memuat data");
    });
}

// Render petugas table
function renderPetugasTable() {
  const tbody = document.getElementById("petugasTable");
  if (!tbody) return;

  if (allPetugas.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada petugas</td></tr>';
    return;
  }

  tbody.innerHTML = allPetugas
    .map(
      (petugas) => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${
              petugas.name
            }</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${
              petugas.email
            }</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${
              petugas.unit_name
            }</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${
              petugas.jabatan || "-"
            }</td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                <button onclick="editPetugas(${
                  petugas.id
                })" class="text-brand-blue hover:underline mr-3">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button onclick="deletePetugas(${petugas.id}, '${
        petugas.name
      }')" class="text-red-600 hover:underline">
                    <i class="fas fa-trash-alt"></i> Hapus
                </button>
            </td>
        </tr>
    `
    )
    .join("");
}

// Populate unit select
function populateUnitSelect() {
  const select = document.getElementById("petugasUnit");
  if (!select) return;

  select.innerHTML =
    '<option value="">Pilih Unit</option>' +
    allUnits
      .map((unit) => `<option value="${unit.id}">${unit.name}</option>`)
      .join("");
}

// Show modal
function showAddPetugasModal() {
  editingPetugasId = null;

  // Populate unit select
  populateUnitSelect();

  document.getElementById("modalTitle").textContent = "Tambah Petugas Baru";
  document.getElementById("petugasForm").reset();
  document.getElementById("passwordField").classList.remove("hidden");
  document.getElementById("modalTambahPetugas").classList.remove("hidden");
}

function hideModal() {
  document.getElementById("modalTambahPetugas").classList.add("hidden");
}

// Edit petugas
function editPetugas(id) {
  const petugas = allPetugas.find((p) => p.id == id);
  if (!petugas) return;

  editingPetugasId = id;

  // Populate unit select first
  populateUnitSelect();

  document.getElementById("modalTitle").textContent = "Edit Petugas";
  document.getElementById("petugasName").value = petugas.name;
  document.getElementById("petugasEmail").value = petugas.email;
  document.getElementById("petugasUnit").value = petugas.unit_id;
  document.getElementById("petugasJabatan").value = petugas.jabatan || "";
  document.getElementById("passwordField").classList.add("hidden");
  document.getElementById("modalTambahPetugas").classList.remove("hidden");
}

// Delete petugas
function deletePetugas(id, name) {
  if (!confirm(`Yakin ingin menghapus petugas "${name}"?`)) return;

  apiDelete(`/admin/petugas/${id}`)
    .then((data) => {
      if (data.success) {
        alert("Petugas berhasil dihapus");
        loadPetugas();
      } else {
        alert(data.message || "Gagal menghapus petugas");
      }
    })
    .catch((error) => {
      console.error("Error deleting petugas:", error);
      alert("Terjadi kesalahan saat menghapus petugas");
    });
}

// Handle form submit
function handleSubmitPetugas(event) {
  event.preventDefault();

  const formData = {
    name: document.getElementById("petugasName").value,
    email: document.getElementById("petugasEmail").value,
    unit_id: document.getElementById("petugasUnit").value,
    jabatan: document.getElementById("petugasJabatan").value,
  };

  if (!editingPetugasId) {
    formData.password = document.getElementById("petugasPassword").value;
  } else {
    const password = document.getElementById("petugasPassword")?.value;
    if (password) {
      formData.password = password;
    }
  }

  const apiCall = editingPetugasId
    ? apiPut(`/admin/petugas/${editingPetugasId}`, formData)
    : apiPost("/admin/petugas", formData);

  apiCall
    .then((data) => {
      if (data.success) {
        alert(
          data.message ||
            `Petugas berhasil ${
              editingPetugasId ? "diperbarui" : "ditambahkan"
            }`
        );
        hideModal();
        loadPetugas();
      } else {
        alert(data.message || "Terjadi kesalahan");
      }
    })
    .catch((error) => {
      console.error("Error saving petugas:", error);
      alert("Terjadi kesalahan saat menyimpan petugas");
    });
}

function showError(message) {
  alert(message);
}

// Initialize
document.addEventListener("DOMContentLoaded", function () {
  loadPetugas();

  const btnTambah = document.getElementById("btnTambahPetugas");
  if (btnTambah) btnTambah.addEventListener("click", showAddPetugasModal);

  const btnClose = document.getElementById("btnCloseModal");
  if (btnClose) btnClose.addEventListener("click", hideModal);

  const btnBatal = document.getElementById("btnBatalModal");
  if (btnBatal) btnBatal.addEventListener("click", hideModal);

  const form = document.getElementById("petugasForm");
  if (form) form.addEventListener("submit", handleSubmitPetugas);
});
