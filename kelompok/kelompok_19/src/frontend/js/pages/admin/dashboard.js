// Admin Dashboard Logic

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

// Load dashboard data
function loadDashboardData() {
  apiGet("/admin/dashboard")
    .then((data) => {
      if (data.success) {
        const stats = data.data.stats;
        const complaintsByUnit = data.data.complaintsByUnit;

        // Update statistics cards
        document.getElementById("totalComplaints").textContent =
          stats.total_complaints || "0";
        document.getElementById("totalMahasiswa").textContent =
          stats.total_mahasiswa || "0";
        document.getElementById("totalPetugas").textContent =
          stats.total_petugas || "0";
        document.getElementById("totalUnits").textContent =
          stats.total_units || "0";
        document.getElementById("totalCategories").textContent =
          stats.total_categories || "0";

        // Render complaints by unit table
        renderUnitsTable(complaintsByUnit);
      } else {
        showError("Gagal memuat data dashboard");
      }
    })
    .catch((error) => {
      console.error("Error loading dashboard:", error);
      showError("Terjadi kesalahan saat memuat data");
    });
}

// Render units table
function renderUnitsTable(units) {
  const tbody = document.querySelector("#unitsTable tbody");

  if (!units || units.length === 0) {
    tbody.innerHTML =
      '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data pengaduan</td></tr>';
    return;
  }

  tbody.innerHTML = units
    .map(
      (unit) => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${
              unit.unit_name
            }</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-brand-blue">${
              unit.total || 0
            }</td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-yellow-600">${
              unit.menunggu || 0
            }</td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-blue-600">${
              unit.diproses || 0
            }</td>
            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-green-600">${
              unit.selesai || 0
            }</td>
        </tr>
    `
    )
    .join("");
}

function showError(message) {
  alert(message);
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", function () {
  loadDashboardData();
});
