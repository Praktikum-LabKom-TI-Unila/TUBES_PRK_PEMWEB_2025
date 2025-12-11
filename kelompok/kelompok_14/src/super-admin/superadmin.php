<?php
/**
 * Manajemen User (Superadmin)
 * Halaman CRUD untuk mengelola akun Admin dan Teknisi.
 */
session_start();
require_once "../config.php";

// Cek Login Superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

// Ambil semua user
$q_user = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User - RepairinBro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

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
            <a href="superadmin.php" class="text-purple-600 font-bold text-sm">Kelola User</a>
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

    <div class="container mx-auto px-6 py-8 max-w-6xl">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-slate-800">Manajemen Pengguna</h2>
                <p class="text-slate-500 mt-1">Kelola akun admin dan teknisi sistem</p>
            </div>
            <a href="tambah_user.php" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-purple-200 transition-all flex items-center gap-2 transform hover:-translate-y-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Tambah User Baru
            </a>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 text-slate-500 text-xs uppercase font-semibold tracking-wider">
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Nama Lengkap</th>
                            <th class="px-6 py-4">Username</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php while($u = $q_user->fetch_assoc()) { ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-500">#<?= $u['id'] ?></td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800"><?= htmlspecialchars($u['nama']) ?></div>
                            </td>
                            <td class="px-6 py-4 text-slate-600"><?= htmlspecialchars($u['username']) ?></td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    <?php 
                                        if($u['role'] == 'superadmin') echo 'bg-purple-100 text-purple-700';
                                        elseif($u['role'] == 'admin') echo 'bg-blue-100 text-blue-700';
                                        else echo 'bg-yellow-100 text-yellow-700';
                                    ?>">
                                    <?= ucfirst($u['role']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="edit_user.php?id=<?= $u['id'] ?>" class="bg-blue-50 text-blue-600 hover:bg-blue-100 p-2 rounded-lg transition-colors flex items-center justify-center w-8 h-8" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <?php if($u['id'] != $_SESSION['user_id']): // Prevent delete self ?>
                                    <a href="hapus_user.php?id=<?= $u['id'] ?>" onclick="return confirm('Yakin ingin menghapus user ini?')" class="bg-red-50 text-red-600 hover:bg-red-100 p-2 rounded-lg transition-colors flex items-center justify-center w-8 h-8" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>
