<?php
session_start();
require_once "../config.php";

// Cek Login Superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

$admin_nama = $_SESSION['nama'] ?? 'Superadmin';

if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash Password!
    $role = $_POST['role'];

    // Gunakan Prepared Statement untuk keamanan
    $stmt = $conn->prepare("INSERT INTO users(username, password, nama, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $nama, $role);
    
    if ($stmt->execute()) {
        header("Location: superadmin.php");
        exit;
    } else {
        $error = "Gagal menambah user: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User - RepairinBro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shadow-sm sticky top-0 z-30">
        <div class="flex items-center gap-3">
            <a href="superadmin.php" class="bg-white p-3 rounded-xl shadow-sm border border-slate-200 text-slate-500 hover:text-purple-600 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
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

            <span class="text-slate-600 text-sm font-medium">Halo, <?= htmlspecialchars($admin_nama); ?></span>
            <a href="../profile/profile.php" class="text-blue-600 hover:text-blue-700 flex items-center gap-1 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </a>
            <a href="logout.php" class="text-red-600 hover:text-red-700 flex items-center gap-1 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </a>
        </div>
    </nav>

    <div class="container mx-auto px-6 py-8 max-w-2xl">
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h2 class="text-xl font-bold text-slate-800">Tambah User Baru</h2>
                <a href="superadmin.php" class="text-slate-500 hover:text-slate-700 text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="p-8">
                <?php if(isset($error)): ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-5">
                        <label class="block text-slate-700 font-medium mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-3.5 text-slate-400"></i>
                            <input type="text" name="nama" required placeholder="Contoh: Budi Santoso"
                                   class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-slate-700 font-medium mb-2">Username</label>
                        <div class="relative">
                            <i class="fas fa-id-badge absolute left-3 top-3.5 text-slate-400"></i>
                            <input type="text" name="username" required placeholder="Username untuk login"
                                   class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-slate-700 font-medium mb-2">Password</label>
                        <div class="relative">
                            <i class="fas fa-lock absolute left-3 top-3.5 text-slate-400"></i>
                            <input type="password" name="password" required placeholder="Password default pengguna"
                                   class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                        </div>
                    </div>

                    <div class="mb-8">
                        <label class="block text-slate-700 font-medium mb-2">Role Access</label>
                        <div class="relative">
                            <i class="fas fa-user-shield absolute left-3 top-3.5 text-slate-400"></i>
                            <select name="role" class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all bg-white">
                                <option value="teknisi">Teknisi</option>
                                <option value="admin">Admin</option>
                                <option value="superadmin">Superadmin</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button name="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg shadow-purple-200 transition-all flex items-center gap-2">
                            <i class="fas fa-save"></i> Simpan User
                        </button>
                        <a href="superadmin.php" class="bg-slate-100 text-slate-600 px-6 py-3 rounded-xl font-semibold hover:bg-slate-200 transition-all">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
