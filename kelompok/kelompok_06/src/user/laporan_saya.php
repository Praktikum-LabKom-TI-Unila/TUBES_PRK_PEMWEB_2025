<?php
require_once '../config/database.php';
require_once '../config/auth.php';

checkLogin();

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM laporan WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$laporan = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Saya - SILATIUM</title>
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
                <a href="tambah_laporan.php" class="nav-link">
                    <img src="../assets/icons/plus.svg" class="icon" alt=""> Buat Laporan
                </a>
                <a href="laporan_saya.php" class="nav-link active">
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
                <div class="flex justify-between items-center mb-4">
                    <h2>Riwayat Laporan Saya</h2>
                    <a href="tambah_laporan.php" class="btn btn-primary"
                        style="font-size: 0.85rem; padding: 0.5rem 1rem;">
                        <img src="../assets/icons/plus.svg" class="icon" alt=""> Buat Baru
                    </a>
                </div>

                <div class="card" style="padding: 0; overflow: hidden;">
                    <div class="table-responsive" style="border: none;">
                        <table style="width: 100%;">
                            <thead>
                                <tr>
                                    <th width="20%">Tanggal</th>
                                    <th width="25%">Judul</th>
                                    <th width="40%">Isi Laporan</th>
                                    <th width="15%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($laporan as $l): ?>
                                    <tr>
                                        <td style="color: var(--text-muted); font-size: 0.85rem;">
                                            <?= date('d M Y H:i', strtotime($l['created_at'])) ?>
                                        </td>
                                        <td style="font-weight: 600; color: var(--primary-color);">
                                            <?= htmlspecialchars($l['judul_laporan']) ?></td>
                                        <td style="color: var(--text-muted);">
                                            <?= nl2br(htmlspecialchars($l['isi_laporan'])) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $l['status'] ?>">
                                                <?= ucfirst($l['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (count($laporan) == 0): ?>
                            <div class="text-center" style="padding: 4rem; color: var(--text-muted);">
                                <img src="../assets/icons/file-text.svg"
                                    style="width: 64px; opacity: 0.2; margin-bottom: 1rem;">
                                <p>Anda belum mengirimkan laporan apapun.</p>
                                <a href="tambah_laporan.php" class="btn btn-primary mt-4">Buat Laporan Pertama</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="../assets/js/script.js"></script>
</body>

</html>