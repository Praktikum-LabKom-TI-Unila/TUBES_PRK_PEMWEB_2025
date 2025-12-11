<?php
require_once '../../koneksi/database.php'; 
require_once '../../backend/manajemenUser/auth_middleware.php'; 

checkAuthorization(['STAFF', 'ADMIN', 'OWNER']);

$stmt = $pdo->query("SELECT id, code, name, category, unit, current_stock, min_stock, selling_price, is_active, created_at, image
                     FROM products 
                     ORDER BY name ASC");
$products = $stmt->fetchAll();

$current_page = 'products.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - Fotocopy Nagoya</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50 font-[inter] text-slate-800 flex">

    <?php include '../../sidebar/sidebar.php'; ?> 

    <div class="flex-1 flex flex-col">
        <?php include '../../header/header.php'; ?> 

        <main class="flex-1 p-6">
            <div class="max-w-full mx-auto md:ml-64">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">Manajemen Stok Bahan (ATK)</h1>
                        <p class="text-slate-500 text-sm mt-1">Kelola daftar produk non-layanan, termasuk bahan baku stok.</p>
                    </div>
                    <a href="create.php" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl font-medium flex items-center shadow-md transition-all">
                        <i data-lucide="plus" class="w-5 h-5 mr-2"></i> Tambah Produk
                    </a>
                </div>

                <?php if (isset($_GET['status'])): ?>
                    <div class="mb-4 p-3 rounded-lg text-white font-medium 
                        <?php 
                            if ($_GET['status'] == 'success_add' || $_GET['status'] == 'success_edit' || $_GET['status'] == 'success_delete') echo 'bg-green-500'; 
                            else if (isset($_GET['msg']) || $_GET['status'] == 'error') echo 'bg-red-500';
                            else echo 'bg-yellow-500';
                        ?>">
                        <?php 
                            if ($_GET['status'] == 'success_add') echo '✅ Produk baru berhasil ditambahkan!';
                            else if ($_GET['status'] == 'success_edit') echo '✅ Data produk berhasil diperbarui!';
                            else if ($_GET['status'] == 'success_delete') echo '✅ Produk berhasil dihapus!';
                            else if (isset($_GET['msg'])) echo '❌ Terjadi kesalahan: ' . htmlspecialchars($_GET['msg']);
                        ?>
                    </div>
                <?php endif; ?>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Gambar</th> <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Nama Produk</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Kode</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Kategori</th> 
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Stok Saat Ini</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Harga Jual</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase">Aktif</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach($products as $p): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <img src="../../../assets/uploads/products/<?php echo htmlspecialchars($p['image'] ?? 'default.png'); ?>" 
                                             alt="<?= htmlspecialchars($p['name']) ?>" 
                                             class="w-12 h-12 object-cover rounded-md border border-slate-200">
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-800">
                                        <?= htmlspecialchars($p['name']) ?>
                                        <div class="text-xs text-slate-400">Unit: <?= htmlspecialchars(ucfirst($p['unit'])) ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-500 font-mono"><?= htmlspecialchars($p['code']) ?></td>
                                    <td class="px-6 py-4 text-sm text-slate-500"><?= htmlspecialchars(ucfirst($p['category'])) ?></td> 
                                    <td class="px-6 py-4 text-center">
                                        <?php 
                                            $stock_class = 'bg-green-100 text-green-800'; 
                                            if ($p['current_stock'] <= $p['min_stock']) {
                                                $stock_class = 'bg-red-100 text-red-800 animate-pulse';
                                            } elseif ($p['current_stock'] <= $p['min_stock'] * 2) {
                                                $stock_class = 'bg-yellow-100 text-yellow-800';
                                            }
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $stock_class ?>">
                                            <?= htmlspecialchars(number_format($p['current_stock'], 0, ',', '.')) ?>
                                        </span>
                                        <?php if ($p['current_stock'] <= $p['min_stock']): ?>
                                            <div class="text-[10px] text-red-500 font-bold mt-1">Stok Min!</div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-slate-800">
                                        <?= format_rupiah($p['selling_price']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if ($p['is_active']): ?>
                                            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mx-auto"></i>
                                        <?php else: ?>
                                            <i data-lucide="x-circle" class="w-5 h-5 text-red-400 mx-auto"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right flex justify-end gap-2">
                                        <a href="edit.php?id=<?= $p['id'] ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                            <i data-lucide="pencil" class="w-4 h-4"></i>
                                        </a>
                                        <a href="../../backend/products/process.php?delete_id=<?= $p['id'] ?>" 
                                           onclick="return confirm('Yakin hapus produk <?= $p['name'] ?>? Semua data batch produk ini juga akan terhapus.')"
                                           class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>