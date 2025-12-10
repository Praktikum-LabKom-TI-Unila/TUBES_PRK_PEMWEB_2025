<?php
// Dummy data PHP
$totalComplaints = 6;
$newSubmissions = 1;
$inProgress = 4;
$completed = 1;

// Data untuk Aktivitas Terbaru 
$recentActivities = [
    ['id' => 'TKT-001', 'title' => 'Jalan Berlubang di Jl. Sudirman', 'reporter' => 'Ahmad Wijaya', 'image' => 'img/dummy-road.jpg', 'status' => ''],
    ['id' => 'TKT-002', 'title' => 'Lampu Jalan Mati di Perumahan Griya Asri', 'reporter' => 'Siti Nurhaliza', 'image' => 'img/dummy-lamp.jpg', 'status' => 'selesai'],
    ['id' => 'TKT-003', 'title' => 'Saluran Air Tersumbat', 'reporter' => 'Ahmad Wijaya', 'image' => 'img/dummy-drain.jpg', 'status' => ''],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="css/style.css"> 
    <link rel="stylesheet" href="css/admin-dashboard.css"> 
</head>
<body>
    
    <header class="admin-header">
        <div class="max-w-container mx-auto header-content">
            <div>
                <p class="text-subtitle">Dashboard Admin</p>
                <h1>Manajemen Pengaduan</h1>
            </div>
            <div class="flex gap-2">
                <a href="edit-profile.php" class="btn-icon header-btn">👤</a>
                <a href="logout.php" class="btn-icon header-btn">🚪</a>
            </div>
        </div>
    </header>

    <main class="dashboard-main">
        <div class="max-w-container mx-auto p-4">
            
            <div class="grid-4-cols gap-4 mb-6" id="stats-cards-container">
                </div>

            <div class="card p-6 border mb-6">
                <h2>Menu Utama</h2>
                <div class="grid-3-cols gap-4" id="menu-utama-container">
                    </div>
            </div>

            <div class="card p-6 border">
                <h2>Aktivitas Terbaru</h2>
                <div class="space-y-4" id="aktivitas-terbaru-container">
                    </div>
            </div>
        </div>
    </main>
    
    <script src="js/admin-dashboard.js"></script>
</body>
</html>