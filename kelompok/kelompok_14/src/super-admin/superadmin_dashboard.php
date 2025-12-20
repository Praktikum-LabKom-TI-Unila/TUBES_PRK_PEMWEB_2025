<?php
/**
 * Dashboard Superadmin
 * Pusat kontrol tertinggi: Manajemen User, Log Aktivitas, Backup, dan Pengaturan Aplikasi.
 */
session_start();
require_once "../config.php";

// Cek Login Superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

$admin_nama = $_SESSION['nama'] ?? 'Superadmin';

// --- STATISTIK ---
// 1. Total Pendapatan
$pendapatan = $conn->query("SELECT SUM(biaya) AS total FROM servis")->fetch_assoc()['total'] ?? 0;

// 2. Rata-rata Service / Bulan (Quantity)
$q_rata = $conn->query("
    SELECT AVG(jumlah) AS rata FROM (
        SELECT COUNT(*) AS jumlah FROM servis 
        GROUP BY YEAR(tgl_masuk), MONTH(tgl_masuk)
    ) AS t
");
$rata = $q_rata->fetch_assoc()['rata'] ?? 0;

// 2b. Rata-rata Biaya Servis
$rata_rata_biaya = $conn->query("SELECT AVG(biaya) AS avg_cost FROM servis WHERE biaya > 0")->fetch_assoc()['avg_cost'] ?? 0;

// 3. Total User
$total_user = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];

// 4. Total Servis
$total_servis = $conn->query("SELECT COUNT(*) AS total FROM servis")->fetch_assoc()['total'];

// Data Servis Terbaru (Limit 5)
$q_servis = $conn->query("SELECT * FROM servis ORDER BY tgl_masuk DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Superadmin Dashboard - RepairinBro</title>
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
        <div class="flex items-center gap-6">
            <a href="superadmin_dashboard.php" class="text-purple-600 font-bold text-sm">Dashboard</a>
            <a href="superadmin.php" class="text-slate-600 hover:text-purple-600 font-medium transition text-sm">Kelola User</a>
            <a href="activity_logs.php" class="text-slate-600 hover:text-purple-600 font-medium transition text-sm">Log Aktivitas</a>
            <a href="settings.php" class="text-slate-600 hover:text-purple-600 font-medium transition text-sm">Pengaturan</a>
            
            <div class="h-4 w-px bg-slate-200"></div>

            <span class="text-slate-600 text-sm font-medium">Halo, <?php echo htmlspecialchars($admin_nama); ?></span>

            <a href="../profile/profile.php" class="text-blue-600 hover:text-blue-700 flex items-center gap-1 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </a>

            <a href="logout.php" class="text-red-600 hover:text-red-700 flex items-center gap-1 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </a>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8 max-w-7xl">
        
        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-[#001F3F] to-[#003366] rounded-2xl p-8 mb-8 text-white shadow-xl flex justify-between items-center relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-3xl font-bold mb-2">Dashboard Superadmin</h2>
                <p class="text-blue-100">Pantau performa teknisi, kelola user, dan amankan sistem.</p>
            </div>
            <!-- Decorative circle -->
            <div class="absolute right-0 top-0 h-64 w-64 bg-white opacity-5 rounded-full transform translate-x-1/2 -translate-y-1/2"></div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Card 1: Total User -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg transition">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-purple-100 text-purple-600 rounded-xl">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Total User</p>
                        <h3 class="text-2xl font-bold text-slate-800"><?= $total_user ?></h3>
                    </div>
                </div>
            </div>

            <!-- Card 2: Total Servis -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg transition">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                        <i class="fas fa-tools text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Total Servis</p>
                        <h3 class="text-2xl font-bold text-slate-800"><?= $total_servis ?></h3>
                    </div>
                </div>
            </div>

            <!-- Card 3: Pendapatan -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg transition">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-100 text-green-600 rounded-xl">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Pendapatan Total</p>
                        <h3 class="text-2xl font-bold text-slate-800">Rp <?= number_format($pendapatan, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>

            <!-- Card 4: Rata2 Service -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 hover:shadow-lg transition">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-orange-100 text-orange-600 rounded-xl">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500 font-medium">Avg. Biaya</p>
                        <h3 class="text-2xl font-bold text-slate-800">Rp <?= number_format($rata_rata_biaya, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Grid -->
        <h3 class="text-lg font-bold text-slate-800 mb-4">Fitur Utama</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="superadmin.php" class="bg-white p-6 rounded-2xl border border-slate-100 hover:border-purple-500 hover:shadow-md transition group">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-xl group-hover:bg-purple-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <i class="fas fa-arrow-right text-slate-300 group-hover:text-purple-600 transition"></i>
                </div>
                <h4 class="font-bold text-slate-800 text-lg">Kelola User</h4>
                <p class="text-sm text-slate-500 mt-1">Tambah, edit, atau hapus user Admin & Teknisi.</p>
            </a>

            <a href="activity_logs.php" class="bg-white p-6 rounded-2xl border border-slate-100 hover:border-blue-500 hover:shadow-md transition group">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <i class="fas fa-arrow-right text-slate-300 group-hover:text-blue-600 transition"></i>
                </div>
                <h4 class="font-bold text-slate-800 text-lg">Audit Logs</h4>
                <p class="text-sm text-slate-500 mt-1">Pantau aktivitas login dan perubahan data sistem.</p>
            </a>

            <div class="grid grid-cols-2 gap-4">
                <a href="settings.php" class="bg-white p-6 rounded-2xl border border-slate-100 hover:border-orange-500 hover:shadow-md transition group flex flex-col justify-center items-center text-center">
                    <div class="p-3 bg-orange-50 text-orange-600 rounded-xl group-hover:bg-orange-600 group-hover:text-white transition mb-3">
                        <i class="fas fa-cog text-xl"></i>
                    </div>
                    <span class="font-bold text-slate-800">Settings</span>
                </a>
                <a href="backup.php" class="bg-white p-6 rounded-2xl border border-slate-100 hover:border-green-500 hover:shadow-md transition group flex flex-col justify-center items-center text-center">
                    <div class="p-3 bg-green-50 text-green-600 rounded-xl group-hover:bg-green-600 group-hover:text-white transition mb-3">
                        <i class="fas fa-database text-xl"></i>
                    </div>
                    <span class="font-bold text-slate-800">Backup</span>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Laporan Servis Terbaru</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold tracking-wider">
                            <th class="px-6 py-4">No Resi</th>
                            <th class="px-6 py-4">Tanggal Masuk</th>
                            <th class="px-6 py-4">Pelanggan</th>
                            <th class="px-6 py-4">Barang</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Biaya</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php while($r = $q_servis->fetch_assoc()) { 
                            // Badge Status
                            $status_class = 'bg-slate-100 text-slate-700';
                            if ($r['status'] == 'Barang Masuk') $status_class = 'bg-blue-100 text-blue-700';
                            elseif ($r['status'] == 'Pengerjaan') $status_class = 'bg-yellow-100 text-yellow-700';
                            elseif ($r['status'] == 'Selesai') $status_class = 'bg-green-100 text-green-700';
                            elseif ($r['status'] == 'Diambil') $status_class = 'bg-gray-100 text-gray-700 line-through';
                            elseif ($r['status'] == 'Batal') $status_class = 'bg-red-100 text-red-700';
                        ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-800"><?= $r['no_resi'] ?></td>
                            <td class="px-6 py-4 text-slate-500 text-sm"><?= date('d M Y', strtotime($r['tgl_masuk'])) ?></td>
                            <td class="px-6 py-4 text-slate-700 font-medium"><?= htmlspecialchars($r['nama_pelanggan']) ?></td>
                            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($r['nama_barang']) ?></td>
                            <td class="px-6 py-4">
                                <span class="<?= $status_class ?> px-3 py-1 rounded-full text-xs font-semibold">
                                    <?= $r['status'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-700">
                                <?= $r['biaya'] ? "Rp ".number_format($r['biaya'],0,',','.') : "-" ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100 flex justify-center">
                <a href="laporan_print.php" class="text-blue-600 font-medium hover:underline text-sm">Lihat Semua Data (Cetak PDF)</a>
            </div>
        </div>

    </div>

</body>
</html>
