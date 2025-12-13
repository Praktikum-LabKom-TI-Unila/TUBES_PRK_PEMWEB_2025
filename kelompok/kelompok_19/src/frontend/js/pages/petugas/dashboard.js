// Dashboard Petugas JavaScript
// File: js/pages/dashboardPetugas.js

// Initialize dashboard on page load
document.addEventListener("DOMContentLoaded", function () {
  initDashboard();
});

// Initialize dashboard
async function initDashboard() {
  try {
    // Check authentication
    const isAuthenticated = await checkPetugasAuth();

    if (!isAuthenticated) {
      window.location.href = "../login.html";
      return;
    }

    // Load user info and dashboard data
    await Promise.all([loadUserInfo(), loadDashboard()]);
  } catch (error) {
    console.error("Dashboard initialization error:", error);
    showError("Gagal memuat dashboard");
  }
}

// Check if user is authenticated as petugas
async function checkPetugasAuth() {
  try {
    const response = await apiGet("/petugas/dashboard");
    return response && response.success;
  } catch (error) {
    return false;
  }
}

// Load user information
async function loadUserInfo() {
  try {
    // Get user from auth utility
    const user = getCurrentUser();
    console.log("User data from getCurrentUser():", user);

    if (user && user.name) {
      const userName = user.name;
      const unitName = user.unit_name || "Petugas";

      document.getElementById("userName").textContent = userName;
      document.getElementById(
        "welcomeName"
      ).textContent = `${userName} - ${unitName}`;
    } else {
      console.warn("User data not found or missing name field");
      document.getElementById("userName").textContent = "Petugas";
      document.getElementById("welcomeName").textContent = "Petugas";
    }
  } catch (error) {
    console.error("Error loading user info:", error);
    document.getElementById("userName").textContent = "Petugas";
    document.getElementById("welcomeName").textContent = "Petugas";
  }
}

// Load dashboard data
async function loadDashboard() {
  try {
    console.log("Loading dashboard data...");
    showLoading(true);

    const response = await apiGet("/petugas/dashboard");
    console.log("Dashboard API response:", response);
    console.log("Response success:", response?.success);
    console.log("Response data:", response?.data);

    if (response && response.success && response.data) {
      displayDashboard(response.data);
    } else {
      console.error("Invalid response:", response);
      showError(response?.message || "Gagal memuat data dashboard");
    }
  } catch (error) {
    console.error("Error loading dashboard:", error);
    console.error("Error details:", error.message, error.stack);
    showError("Terjadi kesalahan saat memuat dashboard: " + error.message);
  } finally {
    showLoading(false);
  }
}

// Display dashboard data
function displayDashboard(data) {
  console.log("Displaying dashboard data:", data);

  if (!data) {
    console.error("No data received");
    showError("Data tidak tersedia");
    return;
  }

  const { stats, recent_complaints } = data;

  // Update statistics cards
  if (stats) {
    updateStats(stats);
  } else {
    console.warn("No stats data");
  }

  // Update recent activity list
  if (recent_complaints) {
    displayRecentActivity(recent_complaints);
  } else {
    console.warn("No recent complaints");
    displayRecentActivity([]);
  }

  // Show dashboard content
  const dashboardContent = document.getElementById("dashboardContent");
  if (dashboardContent) {
    dashboardContent.style.display = "block";
  }
}

// Update statistics cards
function updateStats(stats) {
  console.log("Updating stats:", stats);
  const statTotal = document.getElementById("statTotal");
  const statPending = document.getElementById("statPending");
  const statProcess = document.getElementById("statProcess");
  const statResolved = document.getElementById("statResolved");

  if (statTotal) statTotal.textContent = stats?.total || 0;
  if (statPending) statPending.textContent = stats?.menunggu || 0;
  if (statProcess) statProcess.textContent = stats?.diproses || 0;
  if (statResolved) statResolved.textContent = stats?.selesai || 0;
}

// Display recent activity
function displayRecentActivity(complaints) {
  const activityList = document.getElementById("activityList");

  if (!complaints || complaints.length === 0) {
    activityList.innerHTML = `
            <p style="text-align:center;color:var(--gray-600);padding:2rem;">
                Tidak ada aktivitas terbaru
            </p>
        `;
    return;
  }

  const html = complaints
    .slice(0, 5)
    .map((complaint) => {
      const statusClass = complaint.status.toLowerCase();
      const statusLabel = getStatusLabel(complaint.status);
      const timeAgo = formatTimeAgo(complaint.created_at);

      return `
            <div class="activity-item blue">
                <div class="activity-title">${escapeHtml(complaint.title)}</div>
                <div class="activity-meta">
                    <span>${escapeHtml(complaint.mahasiswa_name)} - ${
        complaint.nim
      }</span>
                    <span>•</span>
                    <span>${timeAgo}</span>
                </div>
                <span class="activity-status status-${statusClass}">${statusLabel}</span>
            </div>
        `;
    })
    .join("");

  activityList.innerHTML = html;
}

// Chart disabled - not needed

// Handle logout
async function handleLogout() {
  if (!confirm("Yakin ingin keluar?")) {
    return;
  }

  try {
    await apiGet("/logout");
    window.location.href = "../../login.html";
  } catch (error) {
    console.error("Logout error:", error);
    // Redirect anyway
    window.location.href = "../../login.html";
  }
}

// Show/hide loading indicator
function showLoading(show) {
  const loader = document.getElementById("loadingIndicator");
  const content = document.getElementById("dashboardContent");

  if (show) {
    loader.style.display = "block";
    content.style.display = "none";
  } else {
    loader.style.display = "none";
  }
}

// Show error message
function showError(message) {
  alert(message); // You can replace this with a better notification system
}

// Utility: Get status label
function getStatusLabel(status) {
  const statusMap = {
    MENUNGGU: "Menunggu",
    DIPROSES: "Diproses",
    SELESAI: "Selesai",
  };
  return statusMap[status] || status;
}

// Utility: Format time ago
function formatTimeAgo(dateString) {
  const date = new Date(dateString);
  const now = new Date();
  const diffInSeconds = Math.floor((now - date) / 1000);

  if (diffInSeconds < 60) {
    return "Baru saja";
  }

  const diffInMinutes = Math.floor(diffInSeconds / 60);
  if (diffInMinutes < 60) {
    return `${diffInMinutes} menit lalu`;
  }

  const diffInHours = Math.floor(diffInMinutes / 60);
  if (diffInHours < 24) {
    return `${diffInHours} jam lalu`;
  }

  const diffInDays = Math.floor(diffInHours / 24);
  if (diffInDays < 7) {
    return `${diffInDays} hari lalu`;
  }

  // Format as date
  return date.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
}

// Utility: Escape HTML to prevent XSS
function escapeHtml(text) {
  const div = document.createElement("div");
  div.textContent = text;
  return div.innerHTML;
}
