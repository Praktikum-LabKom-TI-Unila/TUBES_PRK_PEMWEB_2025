<?php
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

// Get statistics
$stats_sql = "SELECT 
    COUNT(CASE WHEN status = 'received' THEN 1 END) as received_count,
    COUNT(CASE WHEN status = 'processing' THEN 1 END) as processing_count,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_count,
    COUNT(*) as total_count
FROM laporan";
$stats = $pdo->query($stats_sql)->fetch();

// Get Reports with User Names
$sql = "SELECT laporan.*, users.name as pelapor, users.email as email_pelapor
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
                </div>

                <!-- Statistics Cards -->
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
                    <!-- Total Laporan -->
                    <div class="card" style="background: #6366f1; color: white; border: none; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <p style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem; font-weight: 500;">Total Laporan</p>
                                <h3 style="font-size: 2.5rem; margin: 0; color: white; font-weight: 700;"><?= $stats['total_count'] ?></h3>
                            </div>
                            <div style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <img src="../assets/icons/file-text.svg" class="icon" style="width: 28px; height: 28px; filter: brightness(0) invert(1);">
                            </div>
                        </div>
                    </div>

                    <!-- Received -->
                    <div class="card" style="background: #ef4444; color: white; border: none; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <p style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem; font-weight: 500;">Pending</p>
                                <h3 style="font-size: 2.5rem; margin: 0; color: white; font-weight: 700;"><?= $stats['received_count'] ?></h3>
                            </div>
                            <div style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <img src="../assets/icons/inbox.svg" class="icon" style="width: 28px; height: 28px; filter: brightness(0) invert(1);">
                            </div>
                        </div>
                    </div>

                    <!-- Processing -->
                    <div class="card" style="background: #f59e0b; color: white; border: none; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <p style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem; font-weight: 500;">Diproses</p>
                                <h3 style="font-size: 2.5rem; margin: 0; color: white; font-weight: 700;"><?= $stats['processing_count'] ?></h3>
                            </div>
                            <div style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <img src="../assets/icons/refresh-cw.svg" class="icon" style="width: 28px; height: 28px; filter: brightness(0) invert(1);">
                            </div>
                        </div>
                    </div>

                    <!-- Completed -->
                    <div class="card" style="background: #10b981; color: white; border: none; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <p style="font-size: 0.875rem; opacity: 0.9; margin-bottom: 0.5rem; font-weight: 500;">Selesai</p>
                                <h3 style="font-size: 2.5rem; margin: 0; color: white; font-weight: 700;"><?= $stats['completed_count'] ?></h3>
                            </div>
                            <div style="width: 56px; height: 56px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                                <img src="../assets/icons/check-circle.svg" class="icon" style="width: 28px; height: 28px; filter: brightness(0) invert(1);">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card" style="padding: 0; overflow: hidden;">
                    <div class="table-responsive" style="border: none;">
                        <table style="width: 100%;">
                            <thead>
                                <tr>
                                    <th width="12%">Tanggal</th>
                                    <th width="15%">Pelapor</th>
                                    <th width="23%">Judul</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($laporan as $l): ?>
                                    <tr>
                                        <td style="color: var(--text-muted); font-size: 0.85rem;">
                                            <?= date('d M Y', strtotime($l['created_at'])) ?>
                                        </td>
                                        <td style="font-weight: 500;"><?= htmlspecialchars($l['pelapor']) ?></td>
                                        <td style="font-weight: 600; color: var(--primary-color);">
                                            <?= htmlspecialchars($l['judul_laporan']) ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $l['status'] ?>">
                                                <?= $l['status'] == 'received' ? 'Pending' : ($l['status'] == 'processing' ? 'Diproses' : 'Selesai') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button onclick="openDetailModal(<?= htmlspecialchars(json_encode($l)) ?>)" 
                                                class="btn-detail" 
                                                style="padding: 0.5rem 1rem; background: #6366f1; color: white; border: none; cursor: pointer; border-radius: 6px; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.5rem; font-weight: 500; transition: all 0.2s;">
                                                <img src="../assets/icons/eye.svg" class="icon" style="width: 16px; height: 16px; filter: brightness(0) invert(1);"> 
                                                Detail
                                            </button>
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

    <!-- Modal Detail Laporan -->
    <div id="detailModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
        <div class="card" style="max-width: 600px; width: 90%; margin: 2rem; position: relative; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="margin: 0;">Detail Laporan</h3>
                <button onclick="closeDetailModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--text-muted);">&times;</button>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">User</p>
                <p id="modal-pelapor" style="font-weight: 500; margin: 0;"></p>
                <p id="modal-email" style="font-size: 0.875rem; color: var(--text-muted); margin: 0.25rem 0 0 0;"></p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Judul Laporan</p>
                <p id="modal-judul" style="font-weight: 600; color: var(--primary-color); margin: 0;"></p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Isi Laporan</p>
                <p id="modal-isi" style="margin: 0; line-height: 1.6;"></p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">Tanggal Laporan</p>
                <p id="modal-tanggal" style="margin: 0;"></p>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.75rem;">Update Status</p>
                <form id="statusForm" action="update_status.php" method="POST">
                    <input type="hidden" name="laporan_id" id="modal-laporan-id">
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                        <button type="submit" name="status" value="received" class="status-btn" style="flex: 1; padding: 0.75rem 1rem; border: 2px solid #ef4444; background: white; font-weight: 600; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            Pending
                        </button>
                        <button type="submit" name="status" value="processing" class="status-btn" style="flex: 1; padding: 0.75rem 1rem; border: 2px solid #f59e0b; background: white; font-weight: 600; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            Diproses
                        </button>
                        <button type="submit" name="status" value="completed" class="status-btn" style="flex: 1; padding: 0.75rem 1rem; border: 2px solid #10b981; background: white; font-weight: 600; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                            Selesai
                        </button>
                    </div>
                </form>
            </div>

            <div style="text-align: right;">
                <button onclick="closeDetailModal()" class="btn btn-secondary">Tutup</button>
            </div>
        </div>
    </div>

    <style>
        .btn-detail:hover {
            background: #4f46e5;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }
        .status-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .status-btn[name="status"][value="received"] {
            border-color: #ef4444;
            color: #ef4444;
        }
        .status-btn[name="status"][value="received"]:hover {
            background: #ef4444;
            color: white;
        }
        .status-btn[name="status"][value="processing"] {
            border-color: #f59e0b;
            color: #f59e0b;
        }
        .status-btn[name="status"][value="processing"]:hover {
            background: #f59e0b;
            color: white;
        }
        .status-btn[name="status"][value="completed"] {
            border-color: #10b981;
            color: #10b981;
        }
        .status-btn[name="status"][value="completed"]:hover {
            background: #10b981;
            color: white;
        }
        .badge-received {
            background: #fee2e2;
            color: #dc2626;
            padding: 0.4rem 0.85rem;
            border-radius: 16px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-processing {
            background: #fef3c7;
            color: #d97706;
            padding: 0.4rem 0.85rem;
            border-radius: 16px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-completed {
            background: #d1fae5;
            color: #059669;
            padding: 0.4rem 0.85rem;
            border-radius: 16px;
            font-size: 0.8rem;
            font-weight: 600;
        }
    </style>

    <script>
        function openDetailModal(data) {
            document.getElementById('modal-pelapor').textContent = data.pelapor;
            document.getElementById('modal-email').textContent = data.email_pelapor;
            document.getElementById('modal-judul').textContent = data.judul_laporan;
            document.getElementById('modal-isi').textContent = data.isi_laporan;
            document.getElementById('modal-tanggal').textContent = new Date(data.created_at).toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            document.getElementById('modal-laporan-id').value = data.id;
            
            const modal = document.getElementById('detailModal');
            modal.style.display = 'flex';
        }

        function closeDetailModal() {
            document.getElementById('detailModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('detailModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDetailModal();
            }
        });
    </script>
    <script src="../assets/js/script.js"></script>
</body>

</html>