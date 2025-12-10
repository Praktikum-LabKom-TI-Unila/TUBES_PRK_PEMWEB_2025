<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - CleanSpot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-gray-100">
<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../fungsi_helper.php';

cek_login();
cek_role(['admin']);

$nama_admin = $_SESSION['nama'] ?? 'Admin';
?>

    <!-- Navigation -->
    <nav class="bg-green-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <h1 class="text-2xl font-bold">CleanSpot Admin</h1>
                <span class="text-green-200">Dashboard</span>
            </div>
            <div class="flex items-center space-x-4">
                <span>Halo, <?= htmlspecialchars($nama_admin) ?></span>
                <a href="../auth/logout.php" class="bg-green-700 hover:bg-green-800 px-4 py-2 rounded">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Menu -->
    <div class="bg-white shadow">
        <div class="container mx-auto px-4">
            <div class="flex space-x-6 text-sm">
                <a href="beranda_admin.php" class="py-3 border-b-2 border-green-600 text-green-600 font-semibold">Dashboard</a>
                <a href="laporan_admin.php" class="py-3 hover:text-green-600">Laporan</a>
                <a href="kelola_pengguna.php" class="py-3 hover:text-green-600">Pengguna</a>
                <a href="log_aktivitas.php" class="py-3 hover:text-green-600">Log Aktivitas</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-6">
        <!-- Statistik Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6" id="stats-cards">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-gray-600 text-sm">Total Laporan</div>
                <div class="text-3xl font-bold text-green-600" id="total-laporan">-</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-gray-600 text-sm">Total Petugas</div>
                <div class="text-3xl font-bold text-blue-600" id="total-petugas">-</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-gray-600 text-sm">Total Pelapor</div>
                <div class="text-3xl font-bold text-purple-600" id="total-pelapor">-</div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="text-gray-600 text-sm">Total Penugasan</div>
                <div class="text-3xl font-bold text-orange-600" id="total-penugasan">-</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Laporan per Status</h3>
                <canvas id="chart-status"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Laporan per Kategori</h3>
                <canvas id="chart-kategori"></canvas>
            </div>
        </div>

        <!-- Trend Chart -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h3 class="text-lg font-semibold mb-4">Trend Laporan 12 Bulan Terakhir</h3>
            <canvas id="chart-trend"></canvas>
        </div>

        <!-- Map -->
        <div class="bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Peta Laporan</h3>
            <div id="map" class="h-96 rounded"></div>
        </div>
    </div>

    <script src="../aset/js/admin_dashboard.js"></script>
</body>
</html>
