<?php
require_once '../config/database.php';
require_once '../config/auth.php';

checkLogin();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - SILATIUM</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/icons/dashboard.svg" alt="Logo" class="icon" style="color: var(--primary-color);">
                SILATIUM User
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-link active">
                    <img src="../assets/icons/dashboard.svg" class="icon" alt=""> Dashboard
                </a>
                <a href="transportasi.php" class="nav-link">
                    <img src="../assets/icons/bus.svg" class="icon" alt=""> Info Transportasi
                </a>
                <a href="tambah_laporan.php" class="nav-link">
                    <img src="../assets/icons/plus.svg" class="icon" alt=""> Buat Laporan
                </a>
                <a href="laporan_saya.php" class="nav-link">
                    <img src="../assets/icons/file-text.svg" class="icon" alt=""> Laporan Saya
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
                    <div class="user-avatar" style="background-color: var(--accent-color); color: white;">
                        <img src="../assets/icons/user.svg" class="icon" alt="User">
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?= htmlspecialchars($_SESSION['name']) ?></span>
                        <span class="user-role">Masyarakat</span>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="main-content">
                <h1 class="mb-4">Halo, <?= htmlspecialchars($_SESSION['name']) ?>!</h1>

                <div class="card"
                    style="background: linear-gradient(135deg, white 0%, #eff6ff 100%); border: 1px solid var(--primary-light);">
                    <div class="flex items-center gap-4"
                        style="flex-direction: column; text-align: center; padding: 2rem;">
                        <img src="../assets/icons/bus.svg"
                            style="width: 64px; height: 64px; color: var(--primary-color);">
                        <div style="max-width: 600px;">
                            <h3 style="color: var(--primary-color); font-size: 1.5rem; margin-bottom: 1rem;">Selamat
                                Datang di SILATIUM</h3>
                            <p style="color: var(--text-muted); margin-bottom: 2rem;">
                                Sistem Informasi Layanan Transportasi Umum. Kami hadir untuk memudahkan mobilitas Anda
                                dan menampung aspirasi demi pelayanan publik yang lebih baik.
                            </p>
                            <div class="flex gap-4 justify-center">
                                <a href="transportasi.php" class="btn btn-primary">
                                    <img src="../assets/icons/bus.svg" class="icon" alt=""> Lihat Jadwal
                                </a>
                                <a href="tambah_laporan.php" class="btn btn-primary"
                                    style="background-color: var(--accent-color); border: none;">
                                    <img src="../assets/icons/plus.svg" class="icon" alt=""> Buat Pengaduan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="../assets/js/script.js"></script>
</body>

</html>