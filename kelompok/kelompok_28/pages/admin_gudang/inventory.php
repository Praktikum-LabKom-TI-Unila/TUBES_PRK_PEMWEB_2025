<?php
session_start();
require_once '../../config/database.php';

// --- 1. LOGIKA CEK LOGIN & SESSION ---
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin_gudang') {

    header("Location: ../../auth/login.php"); 
    exit;
} else {
    $fullname = $_SESSION['fullname'];
    $user_id = $_SESSION['user_id'];
    $sql_emp = "SELECT store_id FROM employees WHERE id = '$user_id'";
    $res_emp = mysqli_query($conn, $sql_emp);
    
    if($res_emp && mysqli_num_rows($res_emp) > 0){
        $store_id = mysqli_fetch_assoc($res_emp)['store_id'];
        $_SESSION['store_id'] = $store_id;
    } else {

        $store_id = 0; 
    }
}

// --- 2. LOGIKA STATISTIK (DASHBOARD MINI) ---
$stats_query = "SELECT 
                    COUNT(*) as total_items,
                    SUM(stock * price) as total_asset,
                    SUM(CASE WHEN stock < 5 THEN 1 ELSE 0 END) as low_stock_count
                FROM products 
                WHERE store_id = '$store_id' AND is_active = 1";
$stats_res = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_res);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gudang - Inventory Manager</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; }
        .glass-card { background: white; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .animate-enter { animation: enter 0.5s ease-out; }
        @keyframes enter { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Custom Scrollbar for Table */
        .custom-scroll::-webkit-scrollbar { height: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen pb-20">

    <nav class="bg-white border-b border-gray-200 px-6 py-4 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200">
                    <i class="bi bi-box-seam text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="font-bold text-gray-800 text-lg leading-tight">Gudang Panel</h1>
                    <p class="text-xs text-gray-500 font-medium">Inventory System v1.0</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-gray-800"><?= htmlspecialchars($fullname) ?></p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        Admin Gudang
                    </span>
                </div>
                <a href="../../auth/logout.php" onclick="return confirmLogout(event)" class="w-10 h-10 rounded-full bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all duration-300">
                    <i class="bi bi-box-arrow-right text-lg"></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto mt-8 px-6">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-enter">
            <div class="glass-card p-6 rounded-2xl flex items-center gap-4 relative overflow-hidden group">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-boxes"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Produk</p>
                    <h3 class="text-2xl font-bold text-gray-800"><?= $stats['total_items'] ?? 0 ?> <span class="text-sm font-normal text-gray-400">Unit</span></h3>
                </div>
            </div>

            <div class="glass-card p-6 rounded-2xl flex items-center gap-4 relative overflow-hidden group">
                <div class="w-14 h-14 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium">Estimasi Aset</p>
                    <h3 class="text-2xl font-bold text-gray-800">Rp <?= number_format($stats['total_asset'] ?? 0, 0, ',', '.') ?></h3>
                </div>
            </div>

            <div class="glass-card p-6 rounded-2xl flex items-center gap-4 relative overflow-hidden group border-l-4 <?= ($stats['low_stock_count'] > 0) ? 'border-red-500' : 'border-gray-200' ?>">
                <div class="w-14 h-14 <?= ($stats['low_stock_count'] > 0) ? 'bg-red-50 text-red-500' : 'bg-gray-50 text-gray-400' ?> rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm font-medium">Stok Menipis</p>
                    <h3 class="text-2xl font-bold <?= ($stats['low_stock_count'] > 0) ? 'text-red-600' : 'text-gray-800' ?>">
                        <?= $stats['low_stock_count'] ?? 0 ?> <span class="text-sm font-normal text-gray-400">Item</span>
                    </h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-1 animate-enter" style="animation-delay: 0.1s;">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-28">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-gray-800 text-lg">Input Barang</h3>
                        <span class="text-xs bg-indigo-50 text-indigo-600 px-2 py-1 rounded font-bold">New</span>
                    </div>

                    <form action="../../process/barang_handler.php?act=add" method="POST" class="space-y-5">
                        
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Nama Produk</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="bi bi-tag text-gray-400"></i>
                                </div>
                                <input type="text" name="name" class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all" placeholder="Contoh: MacBook Air M1" required>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Kategori</label>
                            <div class="flex gap-2">
                                <div class="relative w-full">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-grid text-gray-400"></i>
                                    </div>
                                    <select name="category_id" class="w-full pl-10 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all appearance-none" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <?php
                                        $cat_query = mysqli_query($conn, "SELECT * FROM categories WHERE store_id = '$store_id'");
                                        while($cat = mysqli_fetch_assoc($cat_query)) {
                                            echo "<option value='".$cat['id']."'>".$cat['name']."</option>";
                                        }
                                        ?>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                        <i class="bi bi-chevron-down text-xs"></i>
                                    </div>
                                </div>
                                
                                <button type="button" onclick="openModalCategory()" class="bg-indigo-50 text-indigo-600 border border-indigo-100 w-12 rounded-xl flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Tambah Kategori Baru">
                                    <i class="bi bi-plus-lg text-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Harga Jual</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-gray-400 text-xs font-bold">Rp</span>
                                    <input type="number" name="price" class="w-full pl-9 pr-3 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="0" required>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wide">Stok</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="bi bi-box text-gray-400"></i>
                                    </div>
                                    <input type="number" name="stock" class="w-full pl-9 pr-3 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="0" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transform hover:-translate-y-1 transition-all duration-200 flex items-center justify-center gap-2">
                            <i class="bi bi-plus-circle"></i> Simpan Data
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2 animate-enter" style="animation-delay: 0.2s;">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
                    
                    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <h3 class="font-bold text-gray-800 text-lg">Daftar Inventory</h3>
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="bi bi-search text-gray-400"></i>
                            </div>
                            <input type="text" id="searchInput" onkeyup="searchTable()" class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" placeholder="Cari nama barang...">
                        </div>
                    </div>

                    <div class="overflow-x-auto custom-scroll flex-grow">
                        <table class="w-full" id="inventoryTable">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Produk</th>
                                    <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Harga</th>
                                    <th class="text-center py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th class="text-right py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
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
                                        $isLow = $row['stock'] < 5;
                                ?>
                                <tr class="hover:bg-indigo-50/30 transition-colors group">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-indigo-500 font-bold text-lg">
                                                <?= strtoupper(substr($row['name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-800 text-sm productName"><?= htmlspecialchars($row['name']) ?></div>
                                                <div class="text-[10px] text-gray-400 font-mono">CODE: <?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                            <?= htmlspecialchars($row['category_name'] ?? 'General') ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-bold text-gray-700 text-sm">
                                        Rp <?= number_format($row['price'], 0, ',', '.') ?>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-sm font-bold <?= $isLow ? 'text-red-600' : 'text-gray-800' ?>"><?= $row['stock'] ?></span>
                                            <?php if($isLow): ?>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-600 animate-pulse mt-1">Stok Tipis!</span>
                                            <?php else: ?>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-600 mt-1">Aman</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <button onclick="confirmDelete(<?= $row['id'] ?>)" 
                                           class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all" title="Hapus Barang">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="py-12 text-center text-gray-400">
                                        <i class="bi bi-inbox text-4xl mb-2 block text-gray-300"></i>
                                        Belum ada data barang di gudang.
                                    </td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div id="categoryModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity backdrop-blur-sm"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <form action="../../process/barang_handler.php?act=add_category" method="POST">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="bi bi-tags text-indigo-600 text-lg"></i>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Tambah Kategori Baru</h3>
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                                        <input type="text" name="category_name" class="w-full rounded-lg border-gray-300 border px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Misal: Snack, Minuman, Alat Tulis" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">Simpan</button>
                            <button type="button" onclick="closeModalCategory()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 1. Fitur Search Realtime
        function searchTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toUpperCase();
            let table = document.getElementById("inventoryTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let td = tr[i].getElementsByClassName("productName")[0];
                if (td) {
                    let txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }       
            }
        }

        // 2. SweetAlert untuk Konfirmasi Delete
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus barang ini?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `../../process/barang_handler.php?act=delete&id=${id}`;
                }
            })
        }

        // 3. SweetAlert untuk Logout
        function confirmLogout(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Keluar sesi?',
                text: "Anda harus login ulang nanti.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = e.target.closest('a').href;
                }
            })
        }

        // 4. Modal Logic
        const modal = document.getElementById('categoryModal');

        function openModalCategory() {
            modal.classList.remove('hidden');
        }

        function closeModalCategory() {
            modal.classList.add('hidden');
        }

        // Tutup modal jika klik area gelap
        window.onclick = function(event) {
            if (event.target == modal.querySelector('.bg-opacity-75')) {
                closeModalCategory();
            }
        }

        // 5. Cek Parameter URL untuk Notifikasi
        const urlParams = new URLSearchParams(window.location.search);
        
        // Notifikasi Tambah/Hapus Barang
        if (urlParams.has('status')) {
            if (urlParams.get('status') === 'success') {
                Swal.fire('Berhasil!', 'Data barang berhasil ditambahkan.', 'success');
            } else if (urlParams.get('status') === 'success_cat') {
                Swal.fire('Berhasil!', 'Kategori baru berhasil ditambahkan.', 'success')
                .then(() => {
                    // Hapus parameter URL biar bersih
                    window.history.replaceState(null, null, window.location.pathname);
                });
            } else if (urlParams.get('status') === 'error') {
                Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
            }
        }
        if (urlParams.has('msg') && urlParams.get('msg') === 'deleted') {
            Swal.fire('Terhapus!', 'Data barang telah dihapus.', 'success');
        }
    </script>

</body>
</html>