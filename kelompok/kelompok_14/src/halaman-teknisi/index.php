<?php
/**
 * Dashboard Teknisi
 * Halaman kerja teknisi: melihat antrian, update status, dan input sparepart.
 */
session_start();
require_once '../config.php';

// Cek login & role teknisi
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teknisi') {
    header("Location: ../login.php");
    exit();
}

$teknisi_id = $_SESSION['user_id'];
$teknisi_id = $_SESSION['user_id'];

// Ambil nama terbaru dari database
$stmt_nama = $conn->prepare("SELECT nama FROM users WHERE id = ?");
$stmt_nama->bind_param("i", $teknisi_id);
$stmt_nama->execute();
$res_nama = $stmt_nama->get_result();
if ($row_nama = $res_nama->fetch_assoc()) {
    $teknisi_nama = $row_nama['nama'];
    $_SESSION['nama'] = $teknisi_nama; // Sync session
} else {
    $teknisi_nama = $_SESSION['nama'] ?? 'Teknisi';
}
$stmt_nama->close();

// Proses update status via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $servis_id = intval($_POST['servis_id']);
    $new_status = $_POST['new_status'];
    
    // Ambil data servis untuk cek tanggal
    $check = $conn->query("SELECT tgl_mulai, tgl_selesai FROM servis WHERE id = $servis_id AND id_teknisi = $teknisi_id")->fetch_assoc();
    
    $tgl_mulai = $check['tgl_mulai'];
    $tgl_selesai = $check['tgl_selesai'];
    
    // Auto set tanggal mulai jika status = Pengerjaan
    if ($new_status == 'Pengerjaan' && !$tgl_mulai) {
        $tgl_mulai = date('Y-m-d H:i:s');
    }
    
    // Auto set tanggal selesai jika status = Selesai
    if ($new_status == 'Selesai' && !$tgl_selesai) {
        $tgl_selesai = date('Y-m-d H:i:s');
    }
    
    // Insert sparepart jika ada
    if (isset($_POST['nama_sparepart']) && isset($_POST['harga_sparepart'])) {
        $nama_input = $_POST['nama_sparepart'];
        $harga_input = $_POST['harga_sparepart'];
        
        // Ensure inputs are arrays (handle single case automatically if setup correctly, but here we expect arrays)
        if (is_array($nama_input) && is_array($harga_input)) {
            $has_inserted = false;
            for ($i = 0; $i < count($nama_input); $i++) {
                $nama = $conn->real_escape_string($nama_input[$i]);
                // Hapus titik dari format ribuan sebelum simpan ke DB (misal: "25.000" -> "25000")
                $harga_clean = str_replace('.', '', $harga_input[$i]);
                $harga = intval($harga_clean);
                
                if (!empty($nama) && $harga > 0) {
                    $conn->query("INSERT INTO biaya_item (id_servis, nama_item, harga) VALUES ($servis_id, '$nama', $harga)");
                    $has_inserted = true;
                }
            }

            if ($has_inserted) {
                // Recalculate Total Biaya (Jasa + Parts)
                $res_biaya = $conn->query("SELECT SUM(harga) as total FROM biaya_item WHERE id_servis = $servis_id")->fetch_assoc();
                $total_biaya = $res_biaya['total'] ?? 0;
                
                // Update servis biaya
                $conn->query("UPDATE servis SET biaya = $total_biaya WHERE id = $servis_id");
            }
        }
    }

    $stmt = $conn->prepare("UPDATE servis SET status = ?, tgl_mulai = ?, tgl_selesai = ? WHERE id = ? AND id_teknisi = ?");
    $stmt->bind_param("sssii", $new_status, $tgl_mulai, $tgl_selesai, $servis_id, $teknisi_id);
    $stmt->execute();
    
    // Jika status berubah menjadi Selesai, redirect ke halaman riwayat
    if ($new_status == 'Selesai') {
        header("Location: riwayat.php?updated=1");
    } else {
        header("Location: index.php?updated=1");
    }
    exit();
}

// Statistik
$antrian = $conn->query("SELECT COUNT(*) as total FROM servis WHERE id_teknisi = $teknisi_id AND status IN ('Barang Masuk', 'Pengecekan')")->fetch_assoc()['total'];
$proses = $conn->query("SELECT COUNT(*) as total FROM servis WHERE id_teknisi = $teknisi_id AND status IN ('Pengerjaan', 'Menunggu Sparepart')")->fetch_assoc()['total'];
$selesai = $conn->query("SELECT COUNT(*) as total FROM servis WHERE id_teknisi = $teknisi_id AND status = 'Selesai'")->fetch_assoc()['total'];
// Omset hanya dari biaya jasa yang sudah selesai
$omset = $conn->query("SELECT COALESCE(SUM(bi.harga), 0) as total FROM biaya_item bi 
    JOIN servis s ON bi.id_servis = s.id 
    WHERE s.id_teknisi = $teknisi_id AND s.status IN ('Selesai', 'Diambil') AND bi.nama_item = 'Biaya Jasa' AND MONTH(s.tgl_selesai) = MONTH(CURRENT_DATE())")->fetch_assoc()['total'];

// Daftar servis untuk teknisi ini (exclude status Selesai dan Diambil)
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$query = "SELECT * FROM servis WHERE id_teknisi = $teknisi_id AND status NOT IN ('Selesai', 'Diambil')";
if ($search) {
    $query .= " AND (nama_pelanggan LIKE '%$search%' OR no_resi LIKE '%$search%' OR nama_barang LIKE '%$search%')";
}
if ($status_filter) {
    $query .= " AND status = '$status_filter'";
}
$query .= " ORDER BY tgl_masuk DESC";
$servis_list = $conn->query($query);

// Daftar riwayat servis
$search_riwayat = $_GET['search_riwayat'] ?? '';
$query_riwayat = "SELECT * FROM servis WHERE id_teknisi = $teknisi_id AND status IN ('Selesai', 'Diambil')";
if ($search_riwayat) {
    $query_riwayat .= " AND (nama_pelanggan LIKE '%$search_riwayat%' OR no_resi LIKE '%$search_riwayat%' OR nama_barang LIKE '%$search_riwayat%')";
}
$query_riwayat .= " ORDER BY tgl_selesai DESC";
$riwayat_list = $conn->query($query_riwayat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Teknisi - RepairinBro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen">

    <!-- Header -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <header class="sticky top-0 z-40 bg-white shadow-sm border-b border-gray-200 px-6 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="../assets/photos/logo.png" alt="RepairinBro" class="h-12 w-12 object-contain">
                <h1 class="text-xl font-bold text-gray-800">RepairinBro <span class="text-green-600 font-normal">Teknisi</span></h1>
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
        
        <?php if (isset($_GET['updated'])): ?>
            <div id="notification" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-r transition-opacity duration-500">
                <p class="font-medium">Status berhasil diupdate!</p>
            </div>
            <script>
                setTimeout(function() {
                    const notification = document.getElementById('notification');
                    notification.style.opacity = '0';
                    setTimeout(function() {
                        notification.style.display = 'none';
                    }, 500);
                }, 3000);
            </script>
        <?php endif; ?>
        
        <!-- Page Title -->
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
            <p class="text-gray-500 text-sm mt-1">Daftar servis yang ditugaskan kepada Anda</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Antrian</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $antrian; ?></p>
                        <p class="text-xs text-gray-400 mt-1">Perlu Diproses</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Proses</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $proses; ?></p>
                        <p class="text-xs text-gray-400 mt-1">Sedang Dikerjakan</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-tools text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Selesai</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $selesai; ?></p>
                        <p class="text-xs text-gray-400 mt-1">Siap Diambil</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-gray-500 text-sm mb-1">Pendapatan Teknisi</p>
                        <p class="text-2xl font-bold text-gray-800">Rp <?php echo number_format($omset, 0, ',', '.'); ?></p>
                        <p class="text-xs text-gray-400 mt-1">Bulan Ini</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Servis Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <!-- Tab Navigation -->
            <div class="px-6 py-4 border-b border-gray-200 flex gap-4">
                <button onclick="showTab('daftar')" id="btn-daftar" class="px-4 py-2 font-semibold text-yellow-600 border-b-2 border-yellow-600 transition">
                    <i class="fas fa-tasks mr-2"></i> Daftar Servis
                </button>
                <button onclick="showTab('riwayat')" id="btn-riwayat" class="px-4 py-2 font-semibold text-gray-500 border-b-2 border-transparent transition hover:text-gray-700">
                    <i class="fas fa-history mr-2"></i> Riwayat Servis
                </button>
            </div>

            <!-- Tab 1: Daftar Servis -->
            <div id="tab-daftar" class="block">
                <!-- Search & Filter -->
                <div class="px-6 py-4 border-b border-gray-100">
                    <form action="" method="GET" class="flex flex-wrap gap-3">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                            placeholder="Cari pelanggan / resi / barang..." 
                            class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 outline-none text-sm flex-1"
                            onchange="this.form.submit()">
                        <select name="status" class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-yellow-500 outline-none text-sm" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Barang Masuk" <?php echo $status_filter == 'Barang Masuk' ? 'selected' : ''; ?>>Barang Masuk</option>
                            <option value="Pengecekan" <?php echo $status_filter == 'Pengecekan' ? 'selected' : ''; ?>>Pengecekan</option>
                            <option value="Menunggu Sparepart" <?php echo $status_filter == 'Menunggu Sparepart' ? 'selected' : ''; ?>>Menunggu Sparepart</option>
                            <option value="Pengerjaan" <?php echo $status_filter == 'Pengerjaan' ? 'selected' : ''; ?>>Pengerjaan</option>
                            <option value="Batal" <?php echo $status_filter == 'Batal' ? 'selected' : ''; ?>>Batal</option>
                        </select>
                    </form>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-yellow-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">No. Resi</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Pelanggan</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Biaya</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if ($servis_list && $servis_list->num_rows > 0): ?>
                                <?php while($row = $servis_list->fetch_assoc()): ?>
                                    <tr class="bg-white border-b border-gray-100">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-800"><?php echo $row['no_resi']; ?></div>
                                            <div class="text-xs text-gray-400"><?php echo date('d M Y', strtotime($row['tgl_masuk'])); ?></div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                                        <td class="px-6 py-4">
                                            <form method="POST" class="inline status-form">
                                                <input type="hidden" name="update_status" value="1">
                                                <input type="hidden" name="servis_id" value="<?php echo $row['id']; ?>">
                                                <select name="new_status" onchange="handleStatusChange(this, '<?php echo $row['status']; ?>')" 
                                                    class="px-3 py-2 text-sm border-2 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white cursor-pointer
                                                    <?php 
                                                        if ($row['status'] == 'Pengerjaan') echo 'border-yellow-500 text-yellow-700';
                                                        elseif ($row['status'] == 'Pengecekan') echo 'border-indigo-500 text-indigo-700';
                                                        elseif ($row['status'] == 'Menunggu Sparepart') echo 'border-orange-500 text-orange-700';
                                                        elseif ($row['status'] == 'Batal') echo 'border-red-500 text-red-700';
                                                        else echo 'border-gray-300 text-gray-700';
                                                    ?>">
                                                    <option value="Barang Masuk" <?php echo $row['status'] == 'Barang Masuk' ? 'selected' : ''; ?>>Barang Masuk</option>
                                                    <option value="Pengecekan" <?php echo $row['status'] == 'Pengecekan' ? 'selected' : ''; ?>>Pengecekan</option>
                                                    <option value="Menunggu Sparepart" <?php echo $row['status'] == 'Menunggu Sparepart' ? 'selected' : ''; ?>>Menunggu Sparepart</option>
                                                    <option value="Pengerjaan" <?php echo $row['status'] == 'Pengerjaan' ? 'selected' : ''; ?>>Pengerjaan</option>
                                                    <option value="Selesai" <?php echo $row['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                                                    <option value="Batal" <?php echo $row['status'] == 'Batal' ? 'selected' : ''; ?>>Batal</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                            <?php echo $row['biaya'] ? 'Rp ' . number_format($row['biaya'], 0, ',', '.') : '-'; ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="update_servis.php?id=<?php echo $row['id']; ?>" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                                <i class="fas fa-edit mr-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center text-gray-400">
                                            <i class="fas fa-check-circle text-4xl mb-3"></i>
                                            <p>Semua servis sudah selesai!</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Riwayat Servis -->
            <div id="tab-riwayat" class="hidden">
                <!-- Search -->
                <div class="px-6 py-4 border-b border-gray-100">
                    <form action="" method="GET" class="flex gap-3">
                        <input type="text" name="search_riwayat" value="<?php echo htmlspecialchars($search_riwayat); ?>" 
                            placeholder="Cari riwayat..." 
                            class="px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm flex-1"
                            onchange="this.form.submit()">
                    </form>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-green-50 border-b border-gray-200">
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
                            <?php if ($riwayat_list && $riwayat_list->num_rows > 0): ?>
                                <?php while($row = $riwayat_list->fetch_assoc()): ?>
                                    <tr class="hover:bg-green-50">
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
                                            <a href="update_servis.php?id=<?php echo $row['id']; ?>&from=riwayat" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                                <i class="fas fa-eye mr-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center text-gray-400">
                                            <i class="fas fa-inbox text-4xl mb-3"></i>
                                            <p>Belum ada riwayat servis.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            function showTab(tab) {
                // Hide all tabs
                document.getElementById('tab-daftar').classList.add('hidden');
                document.getElementById('tab-riwayat').classList.add('hidden');
                
                // Remove active style from all buttons
                document.getElementById('btn-daftar').classList.remove('text-yellow-600', 'border-yellow-600');
                document.getElementById('btn-riwayat').classList.remove('text-green-600', 'border-green-600');
                
                document.getElementById('btn-daftar').classList.add('text-gray-500', 'border-transparent');
                document.getElementById('btn-riwayat').classList.add('text-gray-500', 'border-transparent');
                
                // Show selected tab and style button
                if (tab === 'daftar') {
                    document.getElementById('tab-daftar').classList.remove('hidden');
                    document.getElementById('btn-daftar').classList.remove('text-gray-500', 'border-transparent');
                    document.getElementById('btn-daftar').classList.add('text-yellow-600', 'border-yellow-600');
                } else {
                    document.getElementById('tab-riwayat').classList.remove('hidden');
                    document.getElementById('btn-riwayat').classList.remove('text-gray-500', 'border-transparent');
                    document.getElementById('btn-riwayat').classList.add('text-green-600', 'border-green-600');
                }
            }
            
            // Check if need to show riwayat tab on load
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') === 'riwayat') {
                showTab('riwayat');
            }

            // Fungsi Format Rupiah (Ribuan dengan titik)
            function formatRupiah(input) {
                // Hapus karakter selain angka
                let value = input.value.replace(/[^0-9]/g, '');
                
                // Format dengan titik
                if (value) {
                    value = parseInt(value, 10).toLocaleString('id-ID');
                }
                
                input.value = value;
            }

            // Fungsi Handle Status Change dengan SweetAlert
            function handleStatusChange(select, currentStatus) {
                const newStatus = select.value;
                const form = select.form;

                // Logika: 
                // 1. Ke 'Menunggu Sparepart' -> Wajib Input
                // 2. Ke 'Pengerjaan' DARI selain 'Menunggu Sparepart' -> Wajib Input (Bisa jadi ada sparepart tambahan/langsung pasang)
                if (newStatus === 'Menunggu Sparepart' || (newStatus === 'Pengerjaan' && currentStatus !== 'Menunggu Sparepart' && currentStatus !== 'Pengerjaan')) {
                    Swal.fire({
                        title: 'Input Data Sparepart',
                        width: '600px',
                        html: `
                            <div class="text-left">
                                <p class="text-sm text-slate-500 mb-4">Masukkan detail sparepart yang diperlukan. Klik <b>(+) Tambah</b> untuk lebih dari satu item.</p>
                                
                                <div id="sparepart-container" class="space-y-3 max-h-60 overflow-y-auto p-1">
                                    <div class="sparepart-row flex gap-2">
                                        <input type="text" class="swal-nama w-2/3 px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Nama Sparepart (cth: LCD)">
                                        <input type="text" class="swal-harga w-1/3 px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Harga (Rp)" oninput="formatRupiah(this)">
                                        <button type="button" class="text-red-500 hover:text-red-700 font-bold px-2 invisible"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>

                                <button type="button" onclick="addSparepartRow()" class="mt-3 text-sm text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1">
                                    <i class="fas fa-plus-circle"></i> Tambah Item Lain
                                </button>

                                <div class="mt-4 pt-4 border-t border-slate-100">
                                    <label class="flex items-center gap-2 text-slate-600 text-sm cursor-pointer select-none">
                                        <input type="checkbox" id="swal-skip" class="rounded text-green-600 w-4 h-4 focus:ring-0">
                                        <span>Tidak ada sparepart tambahan (Hanya Jasa)</span>
                                    </label>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Simpan & Update Status',
                        confirmButtonColor: '#16a34a', // green-600
                        cancelButtonText: 'Batal',
                        cancelButtonColor: '#64748b', // slate-500
                        didOpen: () => {
                            // Focus ke input pertama
                            const modal = Swal.getPopup();
                            modal.querySelector('.swal-nama').focus();
                        },
                        preConfirm: () => {
                            const skip = document.getElementById('swal-skip').checked;
                            if (skip) return { skip: true };

                            const rows = document.querySelectorAll('.sparepart-row');
                            let data = [];
                            let isValid = false;

                            rows.forEach(row => {
                                const nama = row.querySelector('.swal-nama').value.trim();
                                const harga = row.querySelector('.swal-harga').value.replace(/\./g, '').trim(); // Hapus titik untuk validasi
                                if (nama && harga && parseInt(harga) > 0) { // Check if harga is a valid number > 0
                                    isValid = true;
                                }
                            });

                            if (!isValid) {
                                Swal.showValidationMessage('Harap isi minimal satu sparepart dengan harga valid atau centang "Tidak ada sparepart"');
                                return false;
                            }
                            
                            // Ambil data RAW dari input (dengan titik) biar user enak liat, tapi sebenernya backend yg harus clean.
                            // Mari kita kirim nilai ASLI input ke backend (yg ada titiknya).
                            // Tapi ambil data row lagi untuk return values
                            data = [];
                            rows.forEach(row => {
                                const nama = row.querySelector('.swal-nama').value.trim();
                                const harga = row.querySelector('.swal-harga').value.trim(); // Kirim dengan titik
                                if(nama) data.push({nama, harga});
                            });
                            
                            return { skip: false, items: data };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (!result.value.skip) {
                                result.value.items.forEach(item => {
                                    const inputNama = document.createElement('input');
                                    inputNama.type = 'hidden';
                                    inputNama.name = 'nama_sparepart[]';
                                    inputNama.value = item.nama;
                                    form.appendChild(inputNama);

                                    const inputHarga = document.createElement('input');
                                    inputHarga.type = 'hidden';
                                    inputHarga.name = 'harga_sparepart[]';
                                    inputHarga.value = item.harga; // Dikirim string "25.000"
                                    form.appendChild(inputHarga);
                                });
                            }
                            form.submit();
                        } else {
                            select.value = currentStatus;
                        }
                    });
                } else {
                    form.submit();
                }
            }

            // Fungsi Helper untuk menambah baris
            window.addSparepartRow = function() {
                const container = document.getElementById('sparepart-container');
                const div = document.createElement('div');
                div.className = 'sparepart-row flex gap-2 animate-fade-in-down'; // animate class if avail or just css
                div.innerHTML = `
                    <input type="text" class="swal-nama w-2/3 px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Nama Sparepart">
                    <input type="text" class="swal-harga w-1/3 px-3 py-2 border border-slate-300 rounded focus:ring-2 focus:ring-blue-500 outline-none text-sm" placeholder="Harga" oninput="formatRupiah(this)">
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 font-bold px-2"><i class="fas fa-trash"></i></button>
                `;
                container.appendChild(div);
                // Auto focus ke nama baru
                div.querySelector('.swal-nama').focus();
            }
        </script>

    </main>

</body>
</html>