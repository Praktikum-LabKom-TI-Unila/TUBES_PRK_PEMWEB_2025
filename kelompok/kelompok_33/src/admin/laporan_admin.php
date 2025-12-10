<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Laporan - CleanSpot Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                <span class="text-green-200">Kelola Laporan</span>
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
                <a href="beranda_admin.php" class="py-3 hover:text-green-600">Dashboard</a>
                <a href="laporan_admin.php" class="py-3 border-b-2 border-green-600 text-green-600 font-semibold">Laporan</a>
                <a href="kelola_pengguna.php" class="py-3 hover:text-green-600">Pengguna</a>
                <a href="log_aktivitas.php" class="py-3 hover:text-green-600">Log Aktivitas</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-6">
        <!-- Filter -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="filter-status" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">Semua Status</option>
                        <option value="baru">Baru</option>
                        <option value="diproses">Diproses</option>
                        <option value="selesai">Selesai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select id="filter-kategori" class="w-full border border-gray-300 rounded px-3 py-2">
                        <option value="">Semua Kategori</option>
                        <option value="organik">Organik</option>
                        <option value="non-organik">Non-Organik</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <input type="text" id="filter-search" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Judul atau pelapor...">
                </div>
                <div class="flex items-end">
                    <button onclick="loadLaporan(1)" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Filter</button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pelapor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="table-body">
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex justify-between items-center" id="pagination">
            <div class="text-sm text-gray-600" id="pagination-info"></div>
            <div class="flex space-x-2" id="pagination-buttons"></div>
        </div>
    </div>

    <!-- Modal Assign Petugas -->
    <div id="modal-assign" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold mb-4">Tugaskan ke Petugas</h3>
            <form id="form-assign" onsubmit="submitAssign(event)">
                <input type="hidden" id="assign-laporan-id">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Petugas</label>
                    <select id="assign-petugas" class="w-full border border-gray-300 rounded px-3 py-2" required>
                        <option value="">-- Pilih Petugas --</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prioritas</label>
                    <select id="assign-prioritas" class="w-full border border-gray-300 rounded px-3 py-2" required>
                        <option value="sedang">Sedang</option>
                        <option value="tinggi">Tinggi</option>
                        <option value="rendah">Rendah</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea id="assign-catatan" class="w-full border border-gray-300 rounded px-3 py-2" rows="3"></textarea>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Tugaskan</button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../aset/js/admin_laporan.js"></script>
</body>
</html>
