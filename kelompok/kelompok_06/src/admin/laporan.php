<?php
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

// Get Reports with User Names
$sql = "SELECT laporan.*, users.name as pelapor 
        FROM laporan 
        JOIN users ON laporan.user_id = users.id 
        ORDER BY laporan.created_at DESC";
$stmt = $pdo->query($sql);
$laporan = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Laporan - SILATIUM</title>
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
                <a href="dashboard.php" class="nav-link">
                    <img src="../assets/icons/dashboard.svg" class="icon" alt=""> Dashboard
                </a>
                <a href="transportasi.php" class="nav-link">
                    <img src="../assets/icons/bus.svg" class="icon" alt=""> Manajemen Transportasi
                </a>
                <a href="laporan.php" class="nav-link active">
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

            <!-- Main Content -->
            <main class="main-content">
                <div class="flex justify-between items-center mb-4">
                    <h2>Daftar Laporan Masuk</h2>
                    <!-- Optional Filter UI could go here -->
                </div>

                <div class="card" style="padding: 0; overflow: hidden;">
                    <div class="table-responsive" style="border: none;">
                        <table style="width: 100%;">
                            <thead>
                                <tr>
                                    <th width="15%">Tanggal</th>
                                    <th width="15%">Pelapor</th>
                                    <th width="20%">Judul</th>
                                    <th width="30%">Isi Laporan</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($laporan as $l): ?>
                                    <tr>
                                        <td style="color: var(--text-muted); font-size: 0.85rem;">
                                            <?= date('d M Y H:i', strtotime($l['created_at'])) ?>
                                        </td>
                                        <td style="font-weight: 500;"><?= htmlspecialchars($l['pelapor']) ?></td>
                                        <td style="font-weight: 600; color: var(--primary-color);">
                                            <?= htmlspecialchars($l['judul_laporan']) ?></td>
                                        <td style="color: var(--text-muted);">
                                            <?= nl2br(htmlspecialchars($l['isi_laporan'])) ?></td>
                                        <td>
                                            <span class="badge badge-<?= $l['status'] ?>">
                                                <?= ucfirst($l['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <form action="update_status.php" method="POST" class="flex gap-2 items-center">
                                                <input type="hidden" name="laporan_id" value="<?= $l['id'] ?>">
                                                <select name="status"
                                                    style="width: auto; padding: 0.25rem 0.5rem; font-size: 0.8rem; border-color: var(--border-color);">
                                                    <option value="received" <?= $l['status'] == 'received' ? 'selected' : '' ?>>Received</option>
                                                    <option value="processing" <?= $l['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                                    <option value="completed" <?= $l['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                                                </select>
                                                <button type="submit" class="btn btn-primary"
                                                    style="padding: 0.25rem 0.5rem; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;"
                                                    title="Update Status">
                                                    <img src="../assets/icons/check-circle.svg" class="icon"
                                                        style="width: 16px; height: 16px;">
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php if (count($laporan) == 0): ?>
                            <div class="text-center" style="padding: 3rem; color: var(--text-muted);">
                                <img src="../assets/icons/file-text.svg"
                                    style="width: 48px; opacity: 0.2; margin-bottom: 0.5rem;">
                                <p>Belum ada laporan masuk.</p>
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