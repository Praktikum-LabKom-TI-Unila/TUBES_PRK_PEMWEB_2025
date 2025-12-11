<?php
/**
 * Log Aktivitas
 * Mencatat dan menampilkan riwayat tindakan penting (Login, Tambah Servis, Edit, dll).
 */
session_start();
require_once "../config.php";

// Cek Login Superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

$admin_nama = $_SESSION['nama'] ?? 'Superadmin';

// Pagination setup
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

$total_res = $conn->query("SELECT COUNT(*) as total FROM activity_logs");
$total_logs = $total_res->fetch_assoc()['total'];
$pages = ceil($total_logs / $limit);

$q_logs = $conn->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT $start, $limit");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - RepairinBro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shadow-sm sticky top-0 z-30">
        <div class="flex items-center gap-3">
            <img src="../assets/photos/logo.png" alt="RepairinBro" class="h-10 w-10 object-contain">
            <h1 class="text-xl font-bold text-slate-800">
                RepairinBro <span class="text-purple-600">Superadmin</span>
            </h1>
        </div>

        <div class="flex items-center gap-6">
            <a href="superadmin_dashboard.php" class="text-slate-600 hover:text-purple-600 font-medium transition text-sm">Dashboard</a>
            <a href="superadmin.php" class="text-slate-600 hover:text-purple-600 font-medium transition text-sm">Kelola User</a>
            <a href="activity_logs.php" class="text-purple-600 font-bold text-sm">Log Aktivitas</a>
            <a href="settings.php" class="text-slate-600 hover:text-purple-600 font-medium transition text-sm">Pengaturan</a>
            
            <div class="h-4 w-px bg-slate-200"></div>

            <span class="text-slate-600 text-sm font-medium">Halo, <?= htmlspecialchars($admin_nama); ?></span>
            <a href="../profile/profile.php" class="text-blue-600 hover:text-blue-700 flex items-center gap-1 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </a>
            <a href="logout.php" class="text-red-600 hover:text-red-700 flex items-center gap-1 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </a>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8 max-w-6xl">
        <div class="flex items-center gap-3 mb-6">
            <div class="p-3 bg-purple-100 text-purple-600 rounded-xl">
                <i class="fas fa-history text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Activity Logs (Audit Trail)</h1>
                <p class="text-slate-500">Rekam jejak aktivitas pengguna dalam sistem.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold tracking-wider">
                            <th class="px-6 py-4">Waktu</th>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Aksi</th>
                            <th class="px-6 py-4">Deskripsi</th>
                            <th class="px-6 py-4">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if($total_logs > 0): ?>
                            <?php while($row = $q_logs->fetch_assoc()): ?>
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-slate-500 text-sm whitespace-nowrap">
                                    <?= date('d M Y H:i:s', strtotime($row['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-800"><?= htmlspecialchars($row['user_name']) ?></div>
                                    <div class="text-xs text-slate-400">ID: <?= $row['user_id'] ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-xs font-bold uppercase
                                        <?php
                                            $a = strtolower($row['action']);
                                            if(strpos($a, 'login')!==false) echo 'bg-green-100 text-green-700';
                                            elseif(strpos($a, 'logout')!==false) echo 'bg-gray-100 text-gray-700';
                                            elseif(strpos($a, 'delete')!==false || strpos($a, 'hapus')!==false) echo 'bg-red-100 text-red-700';
                                            elseif(strpos($a, 'add')!==false || strpos($a, 'tambah')!==false) echo 'bg-blue-100 text-blue-700';
                                            else echo 'bg-purple-100 text-purple-700';
                                        ?>">
                                        <?= htmlspecialchars($row['action']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-slate-600 text-sm">
                                    <?= htmlspecialchars($row['description']) ?>
                                </td>
                                <td class="px-6 py-4 text-slate-400 text-xs font-mono">
                                    <?= htmlspecialchars($row['ip_address']) ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada aktivitas terekam.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if($pages > 1): ?>
            <div class="p-4 border-t border-slate-100 flex justify-center gap-2">
                <?php for($i=1; $i<=$pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="px-3 py-1 rounded-lg border <?= $i==$page ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
