<?php
require_once '../../koneksi/database.php'; 
require_once '../../backend/manajemenUser/auth_middleware.php'; 

checkAuthorization(['STAFF', 'ADMIN', 'OWNER']);

if (!isset($_GET['id'])) header("Location: products.php");
$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) die("Produk tidak ditemukan.");

$product_categories = ambil_banyak_data("SELECT DISTINCT category FROM products ORDER BY category ASC");
$default_units = ['pcs', 'rim', 'meter', 'box', 'roll']; 

$error_msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$current_page = 'products.php'; 

$image_url = '../../../assets/uploads/products/' . htmlspecialchars($product['image'] ?? 'default.png');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Fotocopy Nagoya</title>
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
                    <h2 class="text-xl font-bold text-slate-800">Edit Produk: <?= htmlspecialchars($product['name']) ?></h2>
                </div>

                <?php if ($error_msg): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?= $error_msg ?></span>
                    </div>
                <?php endif; ?>

                <form action="../../backend/products/process.php" method="POST" class="space-y-5" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
                    <input type="hidden" name="old_image" value="<?= htmlspecialchars($product['image'] ?? 'default.png') ?>">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Produk</label>
                            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Kode Produk (Unik)</label>
                            <input type="text" name="code" value="<?= htmlspecialchars($product['code']) ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <p class="text-[10px] text-slate-400 mt-1">Hati-hati mengubah kode.</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Kategori</label>
                            <select name="category" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                                <?php 
                                    $all_categories = array_unique(array_merge([$product['category']], array_column($product_categories, 'category')));
                                ?>
                                <?php foreach ($all_categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat) ?>" <?= $product['category'] == $cat ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(ucfirst($cat)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Unit Satuan</label>
                            <select name="unit" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none bg-white">
                                <?php 
                                    $all_units = array_unique(array_merge([$product['unit']], $default_units));
                                ?>
                                <?php foreach ($all_units as $unit): ?>
                                    <option value="<?= $unit ?>" <?= $product['unit'] == $unit ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(ucfirst($unit)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-end">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Upload Gambar Baru (Max 2MB, JPG/PNG/GIF)</label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/gif" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm bg-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-slate-500 mt-1">Abaikan jika tidak ingin mengubah gambar.</p>
                        </div>
                        <div class="text-center">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Gambar Saat Ini</label>
                            <img src="<?php echo $image_url; ?>" alt="Gambar Produk Lama" class="w-20 h-20 object-cover rounded-md border border-slate-300 mx-auto">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Harga Jual Satuan (Rp)</label>
                            <input type="number" name="selling_price" required min="0" value="<?= htmlspecialchars($product['selling_price']) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Stok Saat Ini</label>
                            <input type="number" name="current_stock" required min="0" value="<?= htmlspecialchars($product['current_stock']) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                            <p class="text-[10px] text-red-500 mt-1">Perubahan stok harusnya melalui Batch!</p>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Minimal Stok (Alert)</label>
                            <input type="number" name="min_stock" required min="0" value="<?= htmlspecialchars($product['min_stock']) ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex items-center gap-3">
                            <input id="is_active" type="checkbox" name="is_active" <?= $product['is_active'] ? 'checked' : '' ?> class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="is_active" class="text-sm font-semibold text-slate-700">Aktifkan Produk (Dapat dijual)</label>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Non-aktifkan jika produk tidak lagi dijual/digunakan.</p>
                    </div>

                    <button type="submit" name="edit_product" class="w-full bg-blue-600 text-white font-bold py-2.5 rounded-lg hover:bg-blue-700 transition-colors mt-4">
                        <i data-lucide="refresh-ccw" class="w-5 h-5 mr-2 inline-block"></i> Update Data
                    </button>
                </form>
            </div>
        </main>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>