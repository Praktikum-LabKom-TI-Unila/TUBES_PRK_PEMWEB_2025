<?php
session_start();
include '../config.php';

// Pastikan session admin (sesuaikan validasi role jika perlu)
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit();
}

// Logika Data Dashboard Admin
$total_penjualan = $conn->query("SELECT SUM(total) as total FROM transaksi")->fetch_assoc()['total'] ?? 0;
$total_transaksi = $conn->query("SELECT COUNT(*) as total FROM transaksi")->fetch_assoc()['total'] ?? 0;
$menu_terpopuler = $conn->query("
    SELECT m.nama_menu, SUM(d.jumlah) as total_terjual 
    FROM detail_transaksi d 
    JOIN menu m ON d.id_menu = m.id_menu 
    GROUP BY m.id_menu 
    ORDER BY total_terjual DESC 
    LIMIT 1
")->fetch_assoc();

$kategori_penjualan = $conn->query("
    SELECT k.nama_kategori, SUM(d.subtotal) as total
    FROM detail_transaksi d
    JOIN menu m ON d.id_menu = m.id_menu
    JOIN kategori_menu k ON m.id_kategori = k.id_kategori
    GROUP BY k.id_kategori
");

$recent_transactions = $conn->query("SELECT * FROM transaksi ORDER BY tanggal DESC LIMIT 5");

// Data User untuk Sidebar/Header
$admin_id = $_SESSION['id_user'];
$query_user = $conn->query("SELECT profile_picture, nama FROM users WHERE id_user = '$admin_id'");
$data_user = $query_user->fetch_assoc();
$nama_user = $data_user['nama'];
$foto_db = $data_user['profile_picture'];
$foto = !empty($foto_db) && file_exists('../' . $foto_db) ? '../' . $foto_db : 'https://ui-avatars.com/api/?name=' . urlencode($nama_user);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EasyResto Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'antique-white': '#F7EBDF',
                        'pale-taupe': '#B7A087',
                        'primary': '#B7A087',
                        'secondary': '#F7EBDF'
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #F7EBDF; }
        .sidebar { background: linear-gradient(to bottom, #B7A087, #8B7355); }
        .card { background: white; border: 1px solid #E5D9C8; }
        .btn-primary { background-color: #B7A087; color: white; }
        .btn-primary:hover { background-color: #8B7355; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-antique-white flex h-screen overflow-hidden font-sans text-gray-800">

    <div class="w-64 sidebar shadow-xl flex flex-col h-full relative z-20 flex-shrink-0">
        <div>
            <div class="h-16 flex items-center justify-center bg-pale-taupe">
                <div class="text-white text-center">
                    <h1 class="text-xl font-bold">EasyResto</h1>
                    <p class="text-xs text-white opacity-90">Admin Panel</p>
                </div>
            </div>
            
            <nav class="mt-8">
                <a href="dashboard.php" class="flex items-center px-6 py-3 text-white bg-pale-taupe bg-opacity-40 border-l-4 border-white transition-all">
                    <i class="fas fa-chart-line w-6"></i>
                    <span class="mx-3 font-medium">Dashboard</span>
                </a>
                <a href="manajemen_pengguna.php" class="flex items-center px-6 py-3 text-white hover:bg-pale-taupe hover:bg-opacity-30 transition-colors">
                    <i class="fas fa-users w-6"></i>
                    <span class="mx-3 font-medium">Manajemen Pengguna</span>
                </a>
                <a href="manajemen_menu.php" class="flex items-center px-6 py-3 text-white hover:bg-pale-taupe hover:bg-opacity-30 transition-colors">
                    <i class="fas fa-utensils w-6"></i>
                    <span class="mx-3 font-medium">Manajemen Menu</span>
                </a>
                <a href="manajemen_transaksi.php" class="flex items-center px-6 py-3 text-white hover:bg-pale-taupe hover:bg-opacity-30 transition-colors">
                    <i class="fas fa-cash-register w-6"></i>
                    <span class="mx-3 font-medium">Manajemen Transaksi</span>
                </a>
                <a href="laporan_penjualan.php" class="flex items-center px-6 py-3 text-white hover:bg-pale-taupe hover:bg-opacity-30 transition-colors">
                    <i class="fas fa-file-invoice-dollar w-6"></i>
                    <span class="mx-3 font-medium">Laporan Penjualan</span>
                </a>
                 <a href="manajemen_hak_akses.php" class="flex items-center px-6 py-3 text-white hover:bg-pale-taupe hover:bg-opacity-30 transition-colors">
                    <i class="fas fa-user-shield w-6"></i>
                    <span class="mx-3 font-medium">Hak Akses</span>
                </a>
                <a href="profil.php" class="flex items-center px-6 py-3 text-white hover:bg-pale-taupe hover:bg-opacity-30 transition-colors">
                    <i class="fas fa-user-cog w-6"></i>
                    <span class="mx-3 font-medium">Profil</span>
                </a>
            </nav>
        </div>
        
        <div class="absolute bottom-0 w-full p-4 bg-pale-taupe bg-opacity-80">
            <div class="flex items-center gap-3">
                <a href="profil.php" class="shrink-0">
                    <img src="<?= $foto ?>" class="w-10 h-10 rounded-full border-2 border-white object-cover hover:opacity-80 transition-opacity">
                </a>
                <div class="overflow-hidden text-white">
                    <p class="font-bold text-sm truncate leading-tight"><?= htmlspecialchars($nama_user) ?></p>
                    <p class="text-xs opacity-90">Role: Admin</p>
                    <a href="../logout.php" class="text-xs text-red-200 hover:text-white flex items-center gap-1 mt-1 transition-colors w-max">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="flex-1 flex flex-col h-full overflow-hidden relative">
        <header class="bg-white shadow-sm border-b border-[#E5D9C8] flex-shrink-0 px-8 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-gray-500 text-sm mt-1">Ringkasan kinerja restoran</p>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="profil.php" class="flex items-center gap-3 pl-4 border-l border-gray-200 hidden sm:flex hover:bg-gray-50 p-2 rounded-lg transition-colors cursor-pointer" title="Lihat Profil">
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Selamat datang</p>
                        <p class="font-bold text-gray-800 text-sm truncate max-w-[150px]"><?= htmlspecialchars($nama_user) ?></p>
                    </div>
                    <img src="<?= $foto ?>" class="w-10 h-10 rounded-full border border-gray-200 object-cover p-0.5">
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8 scrollbar-hide">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="card rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0"><i class="fas fa-wallet text-2xl text-green-500"></i></div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-600">Total Penjualan</h3>
                                <p class="text-2xl font-bold text-gray-900">Rp <?php echo number_format($total_penjualan, 0, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card rounded-xl shadow-lg p-6 border-l-4 border-blue-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0"><i class="fas fa-shopping-cart text-2xl text-blue-500"></i></div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-600">Total Transaksi</h3>
                                <p class="text-2xl font-bold text-gray-900"><?php echo number_format($total_transaksi, 0, ',', '.'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0"><i class="fas fa-star text-2xl text-purple-500"></i></div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-600">Menu Terpopuler</h3>
                                <p class="text-lg font-bold text-gray-900 truncate max-w-[150px]"><?php echo htmlspecialchars($menu_terpopuler['nama_menu'] ?? 'Belum ada data'); ?></p>
                                <p class="text-sm text-gray-500">Terjual: <?php echo $menu_terpopuler['total_terjual'] ?? '0'; ?> pcs</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800"><i class="fas fa-fire mr-1"></i> Hot</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <div class="lg:col-span-2 card rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-800">Penjualan per Kategori</h3>
                    </div>
                    <div class="h-80 relative">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-gradient-to-r from-pale-taupe to-amber-800 rounded-xl shadow-lg p-6 text-white">
                        <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="laporan_penjualan.php" class="flex items-center p-3 bg-white bg-opacity-10 rounded-lg hover:bg-opacity-20 transition-all group">
                                <i class="fas fa-chart-bar mr-3 group-hover:scale-110 transition-transform"></i>
                                <span>Lihat Laporan</span>
                                <i class="fas fa-chevron-right ml-auto text-sm opacity-70"></i>
                            </a>
                            <a href="manajemen_menu.php" class="flex items-center p-3 bg-white bg-opacity-10 rounded-lg hover:bg-opacity-20 transition-all group">
                                <i class="fas fa-utensils mr-3 group-hover:scale-110 transition-transform"></i>
                                <span>Kelola Menu</span>
                                <i class="fas fa-chevron-right ml-auto text-sm opacity-70"></i>
                            </a>
                            <a href="manajemen_pengguna.php" class="flex items-center p-3 bg-white bg-opacity-10 rounded-lg hover:bg-opacity-20 transition-all group">
                                <i class="fas fa-users mr-3 group-hover:scale-110 transition-transform"></i>
                                <span>Kelola Pengguna</span>
                                <i class="fas fa-chevron-right ml-auto text-sm opacity-70"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">Transaksi Terbaru</h3>
                    <a href="manajemen_transaksi.php" class="text-sm text-pale-taupe hover:text-amber-800 font-medium flex items-center">
                        Lihat Semua <i class="fas fa-chevron-right ml-1 text-xs"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b border-gray-200">
                                <th class="pb-4 font-semibold text-gray-600 text-sm">ID</th>
                                <th class="pb-4 font-semibold text-gray-600 text-sm">Pelanggan</th>
                                <th class="pb-4 font-semibold text-gray-600 text-sm">Tanggal</th>
                                <th class="pb-4 font-semibold text-gray-600 text-sm">Total</th>
                                <th class="pb-4 font-semibold text-gray-600 text-sm">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recent_transactions->num_rows > 0): ?>
                                <?php while($transaction = $recent_transactions->fetch_assoc()): ?>
                                <tr class="border-b border-gray-100 hover:bg-pale-taupe hover:bg-opacity-10 transition-colors">
                                    <td class="py-4 text-sm font-medium text-gray-900">
                                        <span class="bg-pale-taupe bg-opacity-20 px-2 py-1 rounded text-gray-700">#<?php echo $transaction['id_transaksi']; ?></span>
                                    </td>
                                    <td class="py-4 text-sm text-gray-600"><?php echo htmlspecialchars($transaction['nama_pelanggan']); ?></td>
                                    <td class="py-4 text-sm text-gray-600">
                                        <div><?php echo date('d M Y', strtotime($transaction['tanggal'])); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo date('H:i', strtotime($transaction['tanggal'])); ?></div>
                                    </td>
                                    <td class="py-4 text-sm font-semibold text-gray-900">Rp <?php echo number_format($transaction['total'], 0, ',', '.'); ?></td>
                                    <td class="py-4">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 flex items-center w-fit">
                                            <i class="fas fa-check mr-1 text-xs"></i> Selesai
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">Belum ada transaksi</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            const categoryLabels = [
                <?php
                $cat_labels = [];
                $cat_data = [];
                $kategori_penjualan->data_seek(0);
                while ($row = $kategori_penjualan->fetch_assoc()) {
                    $cat_labels[] = "'" . htmlspecialchars($row['nama_kategori']) . "'";
                    $cat_data[] = $row['total'];
                }
                echo empty($cat_labels) ? "'Makanan', 'Minuman', 'Dessert'" : implode(', ', $cat_labels);
                ?>
            ];
            const categoryData = [<?php echo empty($cat_data) ? "100, 50, 30" : implode(', ', $cat_data); ?>];
            
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryData,
                        backgroundColor: ['#B7A087', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444', '#3b82f6'],
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { padding: 20, usePointStyle: true, font: { family: "'Segoe UI', sans-serif" } } }
                    },
                    cutout: '65%'
                }
            });
        });
    </script>
</body>
</html>