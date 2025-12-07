<?php
session_start();
// PERBAIKAN 2: Gunakan require_once ke database.php dengan path yang benar
require_once '../../config/database.php';

// Cek Login & Role (Sesuaikan jika role di db adalah 'admin_gudang')
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin_gudang') {
    // Redirect ke login jika bukan admin gudang
   // header("Location: ../../auth/login.php"); 
   // exit;
   // Note: Saya komen dulu redirectnya biar kamu bisa cek tampilan meski belum setup login session
   $store_id = 1; // Default dummy buat testing tampilan
   $fullname = "Admin Gudang";
} else {
   $fullname = $_SESSION['fullname'];
   $user_id = $_SESSION['user_id'];
   
   // Ambil store_id karyawan
   $sql_emp = "SELECT store_id FROM employees WHERE id = '$user_id'";
   $res_emp = mysqli_query($conn, $sql_emp);
   $store_id = mysqli_fetch_assoc($res_emp)['store_id'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Gudang - DigiNiaga</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .animate-fadeIn { animation: fadeIn 0.6s ease-out; }
        .animate-slideUp { animation: slideUp 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .hover-lift { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); }
        .card-gradient { background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%); }
    </style>
</head>
<body class="min-h-screen pb-16">

    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-50 shadow-sm">
        <div class="flex items-center gap-3 animate-fadeIn">
            <div class="h-10 w-10 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg flex items-center justify-center border border-purple-50">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div>
                <span class="block font-bold text-gray-800 text-lg">Gudang Panel</span>
                <span class="block text-xs text-gray-500 font-medium">Kelola Stok & Barang</span>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-bold text-gray-700"><?= htmlspecialchars($fullname) ?></p>
                <p class="text-xs font-semibold text-purple-600">Admin Gudang</p>
            </div>
            <a href="../../auth/logout.php" class="text-red-500 bg-red-50 p-3 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto mt-8 px-6">
        
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 animate-slideUp">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800">Manajemen Stok</h1>
                <p class="text-gray-500 mt-1">Pastikan data barang selalu update</p>
            </div>
            <div class="mt-4 md:mt-0">
                <?php 
                // Hitung total barang aktif
                $count_sql = "SELECT COUNT(*) as total FROM products WHERE store_id='$store_id' AND is_active=1";
                $count_res = mysqli_query($conn, $count_sql);
                $total_items = mysqli_fetch_assoc($count_res)['total'];
                ?>
                <span class="bg-purple-100 text-purple-700 px-4 py-2 rounded-full text-sm font-bold border border-purple-200">
                    Total: <?= $total_items ?> Item
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 animate-slideUp">
                <div class="bg-white p-6 rounded-3xl shadow-xl border border-gray-100 sticky top-24">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg">Input Barang Baru</h3>
                    </div>

                    <form action="../../process/barang_handler.php?act=add" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Produk</label>
                            <input type="text" name="name" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition-all" placeholder="Cth: Kopi Susu Gula Aren" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kategori</label>
                            <div class="relative">
                                <select name="category_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition-all appearance-none" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php
                                    $cat_query = mysqli_query($conn, "SELECT * FROM categories WHERE store_id = '$store_id'");
                                    while($cat = mysqli_fetch_assoc($cat_query)) {
                                        echo "<option value='".$cat['id']."'>".$cat['name']."</option>";
                                    }
                                    ?>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Harga Jual</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-gray-400 text-xs font-bold">Rp</span>
                                    <input type="number" name="price" class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-8 pr-4 py-3 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition-all" placeholder="0" required>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Stok Awal</label>
                                <input type="number" name="stock" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition-all" placeholder="0" required>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold py-3 rounded-xl hover:shadow-lg hover:scale-[1.02] transition-all duration-300 mt-2">
                            + Simpan ke Database
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 animate-slideUp" style="animation-delay: 0.2s;">
                <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="text-left py-4 px-6 text-xs font-extrabold text-gray-500 uppercase tracking-wider">Info Produk</th>
                                    <th class="text-left py-4 px-6 text-xs font-extrabold text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="text-left py-4 px-6 text-xs font-extrabold text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th class="text-center py-4 px-6 text-xs font-extrabold text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th class="text-right py-4 px-6 text-xs font-extrabold text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php
                                $query = "SELECT p.*, c.name as category_name 
                                          FROM products p 
                                          LEFT JOIN categories c ON p.category_id = c.id 
                                          WHERE p.store_id = '$store_id' AND p.is_active = 1 
                                          ORDER BY p.id DESC";
                                $result = mysqli_query($conn, $query);

                                if(mysqli_num_rows($result) > 0){
                                    while($row = mysqli_fetch_assoc($result)) {
                                        // Logic Badge Stok
                                        $isLow = $row['stock'] < 5;
                                        $stockClass = $isLow ? 'bg-red-50 text-red-600 border-red-100' : 'bg-green-50 text-green-600 border-green-100';
                                ?>
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($row['name']) ?></div>
                                                <div class="text-xs text-gray-400">ID: #<?= $row['id'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-600 border border-blue-100">
                                            <?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-bold text-gray-700 text-sm">
                                        Rp <?= number_format($row['price'], 0, ',', '.') ?>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-sm font-bold text-gray-800"><?= $row['stock'] ?></span>
                                            <?php if($isLow): ?>
                                                <span class="text-[10px] font-bold text-red-500 animate-pulse">Menipis!</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <a href="../../process/barang_handler.php?act=delete&id=<?= $row['id'] ?>" 
                                           onclick="return confirm('Hapus barang ini?');"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="py-8 text-center text-gray-400 text-sm">Belum ada data barang di gudang.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>