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
    <title>Buat Laporan - SILATIUM</title>
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
                <a href="transportasi.php" class="nav-link">
                    <img src="../assets/icons/bus.svg" class="icon" alt=""> Info Transportasi
                </a>
                <a href="tambah_laporan.php" class="nav-link active">
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
                <h2 class="mb-4">Buat Laporan / Pengaduan</h2>

                <div class="card" style="max-width: 800px;">
                    <div class="flex items-center gap-2 mb-4">
                        <img src="../assets/icons/edit.svg" class="icon" style="color: var(--primary-color);">
                        <h3>Formulir Pengaduan</h3>
                    </div>

                    <p class="mb-4" style="color: var(--text-muted);">Silakan isi formulir di bawah ini untuk
                        menyampaikan kritik, saran, atau laporan terkait layanan transportasi.</p>

                    <form action="../laporan/proses_laporan.php" method="POST">
                        <div class="form-group">
                            <label for="judul">Judul Laporan</label>
                            <input type="text" id="judul" name="judul_laporan" required
                                placeholder="Contoh: Keterlambatan Bus Rute A">
                        </div>

                        <div class="form-group">
                            <label for="isi">Isi Laporan</label>
                            <textarea id="isi" name="isi_laporan" rows="6" required
                                placeholder="Jelaskan detail laporan Anda..."></textarea>
                        </div>

                        <div class="flex gap-4">
                            <button type="submit" class="btn btn-primary" style="flex: 1;">
                                <img src="../assets/icons/check-circle.svg" class="icon" alt=""> Kirim Laporan
                            </button>
                            <a href="dashboard.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <script src="../assets/js/script.js"></script>
</body>

</html>