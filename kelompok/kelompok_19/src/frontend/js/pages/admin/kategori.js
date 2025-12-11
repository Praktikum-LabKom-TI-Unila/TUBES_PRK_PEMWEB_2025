// Admin Kelola Kategori Logic
let allCategories = [];
let allUnits = [];
let editingCategoryId = null;

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

// Load categories and units
function loadCategories() {
  apiGet("/admin/categories")
    .then((data) => {
      if (data.success) {
        allCategories = data.data.categories;
        allUnits = data.data.units;
        renderCategoriesByUnit();
        populateUnitSelect();
      } else {
        showError("Gagal memuat data kategori");
      }
    })
    .catch((error) => {
      console.error("Error loading categories:", error);
      showError("Terjadi kesalahan saat memuat data");
    });
}

// Render categories grouped by unit
function renderCategoriesByUnit() {
  const container = document.getElementById("categoriesContainer");
  if (!container) return;

  if (allCategories.length === 0) {
    container.innerHTML =
      '<div class="bg-white p-8 rounded-lg shadow-sm border text-center text-gray-500"><p>Belum ada kategori</p></div>';
    return;
  }

  // Group categories by unit
  const grouped = {};
  allCategories.forEach((cat) => {
    if (!grouped[cat.unit_name]) {
      grouped[cat.unit_name] = [];
    }
    grouped[cat.unit_name].push(cat);
  });

  container.innerHTML = Object.keys(grouped)
    .map(
      (unitName) => `
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <h2 class="text-xl font-semibold text-brand-blue mb-6">${unitName}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                ${grouped[unitName]
                  .map(
                    (cat) => `
                    <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="font-semibold text-gray-900 text-base">${
                                      cat.name
                                    }</h3>
                                    <span class="${
                                      cat.is_active == 1
                                        ? "text-green-600"
                                        : "text-gray-400"
                                    }">
                                        <i class="fas fa-circle text-xs"></i>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">${
                                  cat.description || "Tidak ada deskripsi"
                                }</p>
                                <div class="flex items-center gap-4">
                                    <button onclick="editCategory(${
                                      cat.id
                                    })" class="text-brand-blue hover:text-blue-700 text-sm font-medium transition-colors">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </button>
                                    <button onclick="deleteCategory(${
                                      cat.id
                                    }, '${cat.name.replace(
                      /'/g,
                      "\\'"
                    )}')" class="text-red-600 hover:text-red-700 text-sm font-medium transition-colors">
                                        <i class="fas fa-trash-alt mr-1"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `
                  )
                  .join("")}
            </div>
        </div>
    `
    )
    .join("");
}

// Populate unit select in modal
function populateUnitSelect() {
  const select = document.getElementById("unitId");
  if (!select) return;

  select.innerHTML =
    '<option value="">Pilih Unit</option>' +
    allUnits
      .map((unit) => `<option value="${unit.id}">${unit.name}</option>`)
      .join("");
}

// Show modal
function showAddCategoryModal() {
  editingCategoryId = null;
  document.getElementById("modalTitle").textContent = "Tambah Kategori Baru";
  document.getElementById("kategoriForm").reset();
  document.getElementById("modalTambahKategori").classList.remove("hidden");
}

function hideModal() {
  document.getElementById("modalTambahKategori").classList.add("hidden");
}

// Edit category
function editCategory(id) {
  const category = allCategories.find((c) => c.id == id);
  if (!category) return;

  editingCategoryId = id;
  document.getElementById("modalTitle").textContent = "Edit Kategori";
  document.getElementById("categoryName").value = category.name;
  document.getElementById("categoryDescription").value =
    category.description || "";
  document.getElementById("unitId").value = category.unit_id;
  document.getElementById("categoryActive").checked = category.is_active == 1;
  document.getElementById("modalTambahKategori").classList.remove("hidden");
}

// Delete category
function deleteCategory(id, name) {
  if (!confirm(`Yakin ingin menghapus kategori "${name}"?`)) return;

  apiDelete(`/admin/categories/${id}`)
    .then((data) => {
      if (data.success) {
        alert("Kategori berhasil dihapus");
        loadCategories();
      } else {
        alert(data.message || "Gagal menghapus kategori");
      }
    })
    .catch((error) => {
      console.error("Error deleting category:", error);
      alert("Terjadi kesalahan saat menghapus kategori");
    });
}

// Handle form submit
function handleSubmitCategory(event) {
  event.preventDefault();

  const formData = {
    name: document.getElementById("categoryName").value,
    description: document.getElementById("categoryDescription").value,
    unit_id: document.getElementById("unitId").value,
    is_active: document.getElementById("categoryActive").checked ? 1 : 0,
  };

  const apiCall = editingCategoryId
    ? apiPut(`/admin/categories/${editingCategoryId}`, formData)
    : apiPost("/admin/categories", formData);

  apiCall
    .then((data) => {
      if (data.success) {
        alert(
          data.message ||
            `Kategori berhasil ${
              editingCategoryId ? "diperbarui" : "ditambahkan"
            }`
        );
        hideModal();
        loadCategories();
      } else {
        alert(data.message || "Terjadi kesalahan");
      }
    })
    .catch((error) => {
      console.error("Error saving category:", error);
      alert("Terjadi kesalahan saat menyimpan kategori");
    });
}

function showError(message) {
  alert(message);
}

// Initialize
document.addEventListener("DOMContentLoaded", function () {
  loadCategories();

  const btnTambah = document.getElementById("btnTambahKategori");
  if (btnTambah) btnTambah.addEventListener("click", showAddCategoryModal);

  const btnClose = document.getElementById("btnCloseModal");
  if (btnClose) btnClose.addEventListener("click", hideModal);

  const btnBatal = document.getElementById("btnBatalModal");
  if (btnBatal) btnBatal.addEventListener("click", hideModal);

  const form = document.getElementById("kategoriForm");
  if (form) form.addEventListener("submit", handleSubmitCategory);
});
