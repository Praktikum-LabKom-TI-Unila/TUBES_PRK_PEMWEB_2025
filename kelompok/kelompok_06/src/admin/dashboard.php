<?php
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

// Get counts
$stmt = $pdo->query("SELECT COUNT(*) FROM transportasi");
$total_transport = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM laporan");
$total_laporan = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM laporan WHERE status='received'");
$pending_laporan = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SILATIUM</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/icons/dashboard.svg" alt="Logo" class="icon" style="color: var(--primary-color);">
                SILATIUM Admin
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-link active">
                    <img src="../assets/icons/dashboard.svg" class="icon" alt=""> Dashboard
                </a>
                <a href="transportasi.php" class="nav-link">
                    <img src="../assets/icons/bus.svg" class="icon" alt=""> Manajemen Transportasi
                </a>
                <a href="laporan.php" class="nav-link">
                    <img src="../assets/icons/file-text.svg" class="icon" alt=""> Daftar Laporan
                </a>
                <a href="../auth/logout.php" class="nav-link logout">
                    <img src="../assets/icons/log-out.svg" class="icon" alt=""> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <!-- Topbar -->
            <header class="topbar">
                <button id="sidebarToggle">
                    <img src="../assets/icons/menu.svg" class="icon" alt="Menu">
                </button>
                <div class="user-profile">
                    <div class="user-avatar">
                        <img src="../assets/icons/user.svg" class="icon" alt="User">
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?= htmlspecialchars($_SESSION['name']) ?></span>
                        <span class="user-role">Administrator</span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="main-content">
                <h2 class="mb-4">Dashboard Overview</h2>

                <div class="stats-grid">
                    <!-- Card 1 -->
                    <div class="stat-card">
                        <div class="stat-icon"
                            style="background-color: var(--primary-light); color: var(--primary-color);">
                            <img src="../assets/icons/bus.svg" class="icon" alt="">
                        </div>
                        <div class="stat-info">
                            <h3>Total Rute</h3>
                            <p><?= $total_transport ?></p>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="stat-card">
                        <div class="stat-icon"
                            style="background-color: var(--primary-light); color: var(--primary-color);">
                            <img src="../assets/icons/file-text.svg" class="icon" alt="">
                        </div>
                        <div class="stat-info">
                            <h3>Total Laporan</h3>
                            <p><?= $total_laporan ?></p>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: var(--warning-bg); color: var(--warning-text);">
                            <img src="../assets/icons/clock.svg" class="icon" alt="">
                        </div>
                        <div class="stat-info">
                            <h3>Laporan Pending</h3>
                            <p><?= $pending_laporan ?></p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h3 class="mb-4">Selamat Datang, Administrator!</h3>
                    <p style="color: var(--text-muted);">
                        Anda sedang login sebagai Administrator. Gunakan menu di sebelah kiri untuk mengelola data
                        transportasi umum
                        dan memantau laporan serta pengaduan dari masyarakat. Pastikan untuk selalu memeriksa laporan
                        yang masuk secara berkala.
                    </p>
                </div>
            </main>
        </div>
    </div>
    <script src="../assets/js/script.js"></script>
</body>

</html>