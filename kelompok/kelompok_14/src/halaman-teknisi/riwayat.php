<?php
session_start();
require_once '../config.php';

// Cek login & role teknisi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teknisi') {
    header("Location: ../login.php");
    exit();
}

$teknisi_id = $_SESSION['user_id'];

// Ambil nama terbaru dari database
$stmt_nama = $conn->prepare("SELECT nama FROM users WHERE id = ?");
$stmt_nama->bind_param("i", $teknisi_id);
$stmt_nama->execute();
$res_nama = $stmt_nama->get_result();
if ($row_nama = $res_nama->fetch_assoc()) {
    $teknisi_nama = $row_nama['nama'];
    $_SESSION['nama'] = $teknisi_nama;
} else {
    $teknisi_nama = $_SESSION['nama'] ?? 'Teknisi';
}
$stmt_nama->close();

// Daftar servis yang sudah selesai
$search = $_GET['search'] ?? '';

$query = "SELECT * FROM servis WHERE id_teknisi = $teknisi_id AND status IN ('Selesai', 'Diambil')";
if ($search) {
    $query .= " AND (nama_pelanggan LIKE '%$search%' OR no_resi LIKE '%$search%' OR nama_barang LIKE '%$search%')";
}
$query .= " ORDER BY tgl_selesai DESC";
$servis_list = $conn->query($query);

// Statistik riwayat
$total_selesai = $conn->query("SELECT COUNT(*) as total FROM servis WHERE id_teknisi = $teknisi_id AND status IN ('Selesai', 'Diambil')")->fetch_assoc()['total'];
$total_omset = $conn->query("SELECT COALESCE(SUM(biaya), 0) as total FROM servis WHERE id_teknisi = $teknisi_id AND status IN ('Selesai', 'Diambil')")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Servis - FixTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen">

    <!-- Header -->
    <header class="sticky top-0 z-40 bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="../assets/photos/logo.png" alt="FixTrack" class="h-12 w-12 object-contain">
                <h1 class="text-xl font-bold text-gray-800">FixTrack <span class="text-green-600 font-normal">Teknisi</span></h1>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-gray-600 text-sm">Halo, <?php echo htmlspecialchars($teknisi_nama); ?></span>
                <a href="../../src/profile/profile.php" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    <i class="fas fa-user mr-1"></i> Profil
                </a>
                <a href="logout.php" class="text-red-600 hover:text-red-700 text-sm font-medium">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto p-6 space-y-6">
        
        <!-- Breadcrumb -->
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <a href="index.php" class="text-blue-600 hover:text-blue-700 font-medium">
                <i class="fas fa-home mr-1"></i> Dashboard
            </a>
            <span><i class="fas fa-chevron-right"></i></span>
            <span class="text-gray-800 font-medium">Riwayat Servis</span>
        </div>

        <!-- Page Title -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Riwayat Servis</h2>
            <p class="text-gray-500 text-sm mt-1">Daftar servis yang telah diselesaikan</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Total Selesai</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $total_selesai; ?></p>
                        <p class="text-xs text-gray-400 mt-1">Servis Selesai</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Total Omset</p>
                        <p class="text-2xl font-bold text-gray-800">Rp <?php echo number_format($total_omset, 0, ',', '.'); ?></p>
                        <p class="text-xs text-gray-400 mt-1">Dari Semua Servis</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800">Daftar Riwayat Servis</h3>
            </div>

            <!-- Search -->
            <div class="px-6 py-4 border-b border-gray-100">
                <form action="" method="GET" class="flex gap-3">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                        placeholder="Cari pelanggan / resi / barang..." 
                        class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm flex-1"
                        onchange="this.form.submit()">
                </form>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No. Resi</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pelanggan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Barang</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Tgl Selesai</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Biaya</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if ($servis_list && $servis_list->num_rows > 0): ?>
                            <?php while($row = $servis_list->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-800"><?php echo $row['no_resi']; ?></div>
                                        <div class="text-xs text-gray-400"><?php echo date('d M Y', strtotime($row['tgl_masuk'])); ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700"><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-700"><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="inline-block px-3 py-2 text-sm font-medium rounded-lg 
                                        <?php 
                                            if ($row['status'] == 'Selesai') echo 'bg-green-100 text-green-700';
                                            elseif ($row['status'] == 'Diambil') echo 'bg-blue-100 text-blue-700';
                                            else echo 'bg-gray-100 text-gray-700';
                                        ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <?php echo $row['tgl_selesai'] ? date('d M Y H:i', strtotime($row['tgl_selesai'])) : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                        <?php echo $row['biaya'] ? 'Rp ' . number_format($row['biaya'], 0, ',', '.') : '-'; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="update_servis.php?id=<?php echo $row['id']; ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                            <i class="fas fa-eye mr-1"></i> Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center text-gray-400">
                                        <i class="fas fa-history text-4xl mb-3"></i>
                                        <p>Belum ada riwayat servis yang selesai.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>