<?php
require_once '../../koneksi/database.php'; 
require_once '../../backend/manajemenUser/auth_middleware.php'; 

checkAuthorization(['STAFF', 'ADMIN', 'OWNER']);

$product_categories = ambil_banyak_data("SELECT DISTINCT category FROM products ORDER BY category ASC");
$default_units = ['pcs', 'rim', 'meter', 'box', 'roll']; 

$error_msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$current_page = 'products.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk Baru - Fotocopy Nagoya</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-slate-50 font-[inter] text-slate-800 flex">
    
    <?php include '../../sidebar/sidebar.php'; ?> 

    <div class="flex-1 flex flex-col">
        <?php include '../../header/header.php'; ?> 

        <main class="flex-1 p-6 flex items-center justify-center">
            
            <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100 w-full max-w-2xl">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <a href="products.php" class="text-slate-400 hover:text-slate-600"><i data-lucide="arrow-left"></i></a>
                    <h2 class="text-xl font-bold text-slate-800">Tambah Produk / Bahan Baru</h2>
                </div>
                
                <?php if ($error_msg): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?= $error_msg ?></span>
                    </div>
                <?php endif; ?>

                <form action="../../backend/products/process.php" method="POST" class="space-y-5" enctype="multipart/form-data">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Produk</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Produk (Unik)</label>
                            <input type="text" name="code" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div id="category-container">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Kategori</label>
                            <select name="category" id="category-select" onchange="checkCategoryInput(this.value)" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                <?php foreach ($product_categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['category']) ?>">
                                        <?= htmlspecialchars(ucfirst($cat['category'])) ?>
                                    </option>
                                <?php endforeach; ?>
                                <option value="Lainnya">Lainnya (Input baru)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Unit Satuan</label>
                            <select name="unit" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                                <option value="" disabled selected>-- Pilih Satuan --</option>
                                <?php foreach ($default_units as $unit): ?>
                                    <option value="<?= $unit ?>">
                                        <?= htmlspecialchars(ucfirst($unit)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Gambar Produk (Max 2MB, JPG/PNG/GIF)</label>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/gif" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ada gambar. File akan disimpan di folder assets/uploads/products.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Harga Jual Satuan (Rp)</label>
                            <input type="number" name="selling_price" required min="0" value="0" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Stok Awal</label>
                            <input type="number" name="current_stock" required min="0" value="0" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Minimal Stok (Alert)</label>
                            <input type="number" name="min_stock" required min="0" value="5" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex items-center gap-3">
                            <input id="is_active" type="checkbox" name="is_active" checked class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_active" class="text-sm font-semibold text-slate-700">Aktifkan Produk (Dapat dijual)</label>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Non-aktifkan jika produk tidak lagi dijual/digunakan.</p>
                    </div>

                    <button type="submit" name="add_product" class="w-full bg-slate-900 text-white font-bold py-2.5 rounded-lg hover:bg-slate-800 transition-colors mt-4">
                        <i data-lucide="save" class="w-5 h-5 mr-2 inline-block"></i> Simpan Produk
                    </button>
                </form>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();

        function checkCategoryInput(selectedValue) {
            const container = document.getElementById('category-container');
            const currentSelect = document.getElementById('category-select');
            
            if (selectedValue === 'Lainnya') {
                currentSelect.remove();
                
                const newInput = document.createElement('input');
                newInput.type = 'text';
                newInput.name = 'category'; 
                newInput.required = true;
                newInput.placeholder = 'Masukkan Kategori Baru';
                newInput.className = 'w-full px-4 py-2 border border-blue-500 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white mt-1';
                
                const newLabel = document.querySelector('#category-container label');
                newLabel.textContent = 'Kategori Baru';

                container.appendChild(newInput);
                
                const backButton = document.createElement('button');
                backButton.type = 'button';
                backButton.textContent = 'Batalkan (Gunakan Daftar)';
                backButton.className = 'mt-2 text-xs text-red-500 hover:text-red-700';
                backButton.onclick = function() {
                    window.location.reload(); 
                };
                container.appendChild(backButton);

            }
        }
    </script>
</body>
</html>