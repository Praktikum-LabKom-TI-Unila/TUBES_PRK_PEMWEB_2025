<?php
/**
 * Pengaturan Aplikasi
 * Mengubah Metadata toko (Nama Toko, Alamat, Logo) secara dinamis.
 */
session_start();
require_once "../config.php";
require_once "../log_helper.php";

// Cek Login Superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

$admin_nama = $_SESSION['nama'] ?? 'Superadmin';
$msg = "";

// Handle Update
if (isset($_POST['submit'])) {
    $company = $_POST['company_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $map = $_POST['location_map'];
    
    // Simple update
    $stmt = $conn->prepare("UPDATE app_settings SET company_name=?, address=?, phone=?, email=?, location_map=? WHERE id=1");
    $stmt->bind_param("sssss", $company, $address, $phone, $email, $map);
    
    if($stmt->execute()) {
        $msg = "<div class='bg-green-100 text-green-700 p-4 rounded-lg mb-4'>Pengaturan berhasil disimpan!</div>";
        logActivity($conn, $_SESSION['user_id'], $admin_nama, 'Update Settings', 'Mengupdate profil aplikasi');
    } else {
        $msg = "<div class='bg-red-100 text-red-700 p-4 rounded-lg mb-4'>Gagal menyimpan: ".$conn->error."</div>";
    }
}

// Fetch current
$settings = $conn->query("SELECT * FROM app_settings WHERE id=1")->fetch_assoc();
if(!$settings) {
    // Should depend on setup_features.php having run. If not, insert defaults.
    $conn->query("INSERT INTO app_settings (id, app_name) VALUES (1, 'RepairinBro')");
    $settings = $conn->query("SELECT * FROM app_settings WHERE id=1")->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Aplikasi - RepairinBro</title>
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
            <a href="activity_logs.php" class="text-slate-600 hover:text-purple-600 font-medium transition text-sm">Log Aktivitas</a>
            <a href="settings.php" class="text-purple-600 font-bold text-sm">Pengaturan</a>
            
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
        <div class="flex items-center gap-3 mb-6">
            <div class="p-3 bg-purple-100 text-purple-600 rounded-xl">
                <i class="fas fa-cogs text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Pengaturan Aplikasi</h1>
                <p class="text-slate-500">Konfigurasi informasi toko yang tampil di laporan.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <?= $msg ?>

            <form method="post">
                <div class="mb-5">
                    <label class="block text-slate-700 font-medium mb-2">Nama Toko / Perusahaan</label>
                    <input type="text" name="company_name" value="<?= htmlspecialchars($settings['company_name'] ?? '') ?>" required
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                </div>

                <div class="mb-5">
                    <label class="block text-slate-700 font-medium mb-2">Alamat Lengkap</label>
                    <textarea name="address" rows="3" required
                              class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all"><?= htmlspecialchars($settings['address'] ?? '') ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                    <div>
                        <label class="block text-slate-700 font-medium mb-2">No. Telepon</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($settings['phone'] ?? '') ?>" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-slate-700 font-medium mb-2">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($settings['email'] ?? '') ?>" required
                               class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all">
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-slate-700 font-medium mb-2">Embed Google Maps (HTML Iframe)</label>
                    <textarea name="location_map" rows="4" placeholder='<iframe src="https://www.google.com/maps/embed?..."></iframe>'
                              class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all text-sm font-mono text-slate-600"><?= htmlspecialchars($settings['location_map'] ?? '') ?></textarea>
                    <p class="text-xs text-slate-400 mt-2">Copy code HTML Embed dari Google Maps lalu tempel di sini.</p>
                </div>

                <?php if(!empty($settings['location_map'])): ?>
                    <div class="mb-8">
                        <label class="block text-slate-700 font-medium mb-2">Preview Peta</label>
                        <div class="rounded-xl overflow-hidden shadow-sm border border-slate-200 h-64">
                            <?= $settings['location_map'] ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="flex justify-end">
                    <button name="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-3 rounded-xl font-semibold shadow-lg shadow-purple-200 transition-all flex items-center gap-2">
                        <i class="fas fa-save"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
