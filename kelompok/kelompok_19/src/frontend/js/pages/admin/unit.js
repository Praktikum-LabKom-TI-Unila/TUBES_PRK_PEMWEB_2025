// Admin Kelola Unit Logic
let allUnits = [];
let editingUnitId = null;

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

// Load units
function loadUnits() {
  apiGet("/admin/units")
    .then((data) => {
      if (data.success) {
        allUnits = data.data;
        renderUnitsTable();
      } else {
        showError("Gagal memuat data unit");
      }
    })
    .catch((error) => {
      console.error("Error loading units:", error);
      showError("Terjadi kesalahan saat memuat data");
    });
}

// Render units table
function renderUnitsTable() {
  const tbody = document.getElementById("unitsTable");
  if (!tbody) return;

  if (allUnits.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada unit</td></tr>';
    return;
  }

  tbody.innerHTML = allUnits
    .map(
      (unit) => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${
              unit.name
            }</td>
            <td class="px-6 py-4 text-sm text-gray-600">${
              unit.description || "-"
            }</td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                <span class="px-2 py-1 text-xs rounded ${
                  unit.is_active == 1
                    ? "bg-green-100 text-green-800"
                    : "bg-gray-100 text-gray-800"
                }">
                    ${unit.is_active == 1 ? "Aktif" : "Nonaktif"}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">${
              unit.total_categories || 0
            }</td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                <button onclick="editUnit(${
                  unit.id
                })" class="text-brand-blue hover:underline mr-3">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button onclick="deleteUnit(${unit.id}, '${
        unit.name
      }')" class="text-red-600 hover:underline">
                    <i class="fas fa-trash-alt"></i> Hapus
                </button>
            </td>
        </tr>
    `
    )
    .join("");
}

// Show modal
function showAddUnitModal() {
  editingUnitId = null;
  document.getElementById("modalTitle").textContent = "Tambah Unit Baru";
  document.getElementById("unitForm").reset();
  document.getElementById("isActive").checked = true;
  document.getElementById("modalTambahUnit").classList.remove("hidden");
}

function hideModal() {
  document.getElementById("modalTambahUnit").classList.add("hidden");
}

// Edit unit
function editUnit(id) {
  const unit = allUnits.find((u) => u.id == id);
  if (!unit) return;

  editingUnitId = id;
  document.getElementById("modalTitle").textContent = "Edit Unit";
  document.getElementById("unitName").value = unit.name;
  document.getElementById("unitDescription").value = unit.description || "";
  document.getElementById("isActive").checked = unit.is_active == 1;
  document.getElementById("modalTambahUnit").classList.remove("hidden");
}

// Delete unit
function deleteUnit(id, name) {
  if (!confirm(`Yakin ingin menghapus unit "${name}"?`)) return;

  apiDelete(`/admin/units/${id}`)
    .then((data) => {
      if (data.success) {
        alert("Unit berhasil dihapus");
        loadUnits();
      } else {
        alert(data.message || "Gagal menghapus unit");
      }
    })
    .catch((error) => {
      console.error("Error deleting unit:", error);
      alert("Terjadi kesalahan saat menghapus unit");
    });
}

// Handle form submit
function handleSubmitUnit(event) {
  event.preventDefault();

  const formData = {
    name: document.getElementById("unitName").value,
    description: document.getElementById("unitDescription").value,
    is_active: document.getElementById("isActive").checked ? 1 : 0,
  };

  const apiCall = editingUnitId
    ? apiPut(`/admin/units/${editingUnitId}`, formData)
    : apiPost("/admin/units", formData);

  apiCall
    .then((data) => {
      if (data.success) {
        alert(
          data.message ||
            `Unit berhasil ${editingUnitId ? "diperbarui" : "ditambahkan"}`
        );
        hideModal();
        loadUnits();
      } else {
        alert(data.message || "Terjadi kesalahan");
      }
    })
    .catch((error) => {
      console.error("Error saving unit:", error);
      alert("Terjadi kesalahan saat menyimpan unit");
    });
}

function showError(message) {
  alert(message);
}

// Initialize
document.addEventListener("DOMContentLoaded", function () {
  loadUnits();

  const btnTambah = document.getElementById("btnTambahUnit");
  if (btnTambah) btnTambah.addEventListener("click", showAddUnitModal);

  const btnClose = document.getElementById("btnCloseModal");
  if (btnClose) btnClose.addEventListener("click", hideModal);

  const btnBatal = document.getElementById("btnBatalModal");
  if (btnBatal) btnBatal.addEventListener("click", hideModal);

  const form = document.getElementById("unitForm");
  if (form) form.addEventListener("submit", handleSubmitUnit);
});
