<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../transportasi/data_transportasi.php';

checkLogin();

// Get Transportation Data
$transportasi = getTransportasi();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Transportasi - SILATIUM</title>
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
                <a href="dashboard.php" class="nav-link">
                    <img src="../assets/icons/dashboard.svg" class="icon" alt=""> Dashboard
                </a>
                <a href="transportasi.php" class="nav-link active">
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
                <h2 class="mb-4">Informasi Transportasi Publik</h2>

                <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                    <?php foreach ($transportasi as $t): ?>
                        <div class="card" style="display: flex; flex-direction: column; height: 100%;">
                            <div class="flex items-center gap-2 mb-2">
                                <div style="background-color: var(--primary-light); padding: 0.5rem; border-radius: 8px;">
                                    <img src="../assets/icons/bus.svg" class="icon" style="color: var(--primary-color);">
                                </div>
                                <h3 style="margin: 0; font-size: 1.1rem;"><?= htmlspecialchars($t['nama_transportasi']) ?>
                                </h3>
                            </div>

                            <div style="margin-top: 1rem; flex-grow: 1;">
                                <p
                                    style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; font-weight: 600; margin-bottom: 0.25rem;">
                                    Rute</p>
                                <p style="margin-bottom: 1rem;"><?= htmlspecialchars($t['rute']) ?></p>

                                <p
                                    style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; font-weight: 600; margin-bottom: 0.25rem;">
                                    Jadwal Operasional</p>
                                <div class="flex items-center gap-2 mb-2">
                                    <img src="../assets/icons/clock.svg" class="icon"
                                        style="width: 16px; height: 16px; color: var(--secondary-color);">
                                    <span><?= htmlspecialchars($t['jadwal']) ?></span>
                                </div>

                                <?php if (!empty($t['keterangan'])): ?>
                                    <div
                                        style="background-color: #f8fafc; padding: 0.75rem; border-radius: var(--radius-md); font-size: 0.9rem; margin-top: 1rem; border: 1px solid var(--border-color);">
                                        <?= htmlspecialchars($t['keterangan']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($transportasi) == 0): ?>
                    <div class="text-center" style="padding: 4rem; color: var(--text-muted);">
                        <img src="../assets/icons/bus.svg" style="width: 64px; opacity: 0.2; margin-bottom: 1rem;">
                        <p>Belum ada data transportasi tersedia.</p>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>
    <script src="../assets/js/script.js"></script>
</body>

</html>