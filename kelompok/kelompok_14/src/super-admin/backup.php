<?php
session_start();
require_once "../config.php";
require_once "../log_helper.php";

// Cek Login Superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

// Handle Backup logic
if (isset($_POST['backup'])) {
    
    // Log first
    logActivity($conn, $_SESSION['user_id'], $_SESSION['nama'] ?? 'Superadmin', 'Backup Database', 'Mendownload backup .sql');

    // Get all tables
    $tables = [];
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }

    $sqlScript = "-- RepairinBro Database Backup\n";
    $sqlScript .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
    $sqlScript .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    foreach ($tables as $table) {
        // Structure
        $row2 = $conn->query("SHOW CREATE TABLE $table")->fetch_row();
        $sqlScript .= "\n\n" . $row2[1] . ";\n\n";

        // Data
        $result3 = $conn->query("SELECT * FROM $table");
        $num_fields = $result3->field_count;

        for ($i = 0; $i < $num_fields; $i++) {
            while ($row = $result3->fetch_row()) {
                $sqlScript .= "INSERT INTO $table VALUES(";
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = preg_replace("/\n/", "\\n", $row[$j]);
                    if (isset($row[$j])) {
                        $sqlScript .= '"' . $row[$j] . '"';
                    } else {
                        $sqlScript .= '""';
                    }
                    if ($j < ($num_fields - 1)) {
                        $sqlScript .= ',';
                    }
                }
                $sqlScript .= ");\n";
            }
        }
    }
    
    $sqlScript .= "\n\nSET FOREIGN_KEY_CHECKS=1;";

    // Download
    $filename = 'backup_repairinbro_' . date('Y-m-d_H-i-s') . '.sql';
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $filename);
    echo $sqlScript;
    exit;
}

$admin_nama = $_SESSION['nama'] ?? 'Superadmin';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Database - RepairinBro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center shadow-sm sticky top-0 z-30">
        <div class="flex items-center gap-3">
            <a href="superadmin_dashboard.php" class="bg-white p-3 rounded-xl shadow-sm border border-slate-200 text-slate-500 hover:text-purple-600 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-xl font-bold text-slate-800">
                RepairinBro <span class="text-purple-600">Superadmin</span>
            </h1>
        </div>

        <div class="flex items-center gap-6">
            <a href="superadmin_dashboard.php" class="text-slate-600 hover:text-purple-600 font-medium transition text-sm">Dashboard</a>
            <a href="superadmin.php" class="text-slate-600 hover:text-purple-600 font-medium transition text-sm">Kelola User</a>
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
        <div class="flex items-center gap-3 mb-6">
            <div class="p-3 bg-purple-100 text-purple-600 rounded-xl">
                <i class="fas fa-database text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Backup Database</h1>
                <p class="text-slate-500">Amankan data sistem dengan mengunduh backup secara berkala.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center">
            
            <img src="https://illustrations.popsy.co/amber/server.svg" alt="Backup" class="h-40 mx-auto mb-6 opacity-80">
            
            <h3 class="text-lg font-bold text-slate-800 mb-2">Siap untuk Backup?</h3>
            <p class="text-slate-500 mb-8 max-w-md mx-auto">Sistem akan men-generate file SQL yang berisi seluruh struktur dan data aplikasi RepairinBro. Simpan file ini di tempat aman.</p>

            <form method="post">
                <button name="backup" class="bg-purple-600 hover:bg-purple-700 text-white px-8 py-4 rounded-xl font-bold shadow-lg shadow-purple-200 transition-all flex items-center gap-3 mx-auto">
                    <i class="fas fa-download"></i> Download Backup Database
                </button>
            </form>

        </div>
    </div>
</body>
</html>
