<div class="max-w-7xl mx-auto">
    <div class="glass-effect p-6 rounded-3xl mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold flex items-center gap-3">
                    <i data-lucide="package" class="w-8 h-8"></i>
                    Kelola Produk
                </h1>
                <p class="text-gray-200 mt-2">Manajemen data produk UMKM</p>
            </div>
            <a href="<?= BASE_URL ?>/product/create" 
               class="btn-primary px-6 py-3 rounded-xl font-semibold flex items-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                Tambah Produk
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-500/20 text-green-200 p-4 mb-6 rounded-xl border border-green-400/40 flex items-start gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
            <span><?= $_SESSION['success'] ?></span>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-500/20 text-red-200 p-4 mb-6 rounded-xl border border-red-400/40 flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
            <span><?= $_SESSION['error'] ?></span>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="glass-effect rounded-3xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-white/10">
                    <tr>
                        <th class="px-6 py-4 text-left font-semibold">ID</th>
                        <th class="px-6 py-4 text-left font-semibold">Nama Produk</th>
                        <th class="px-6 py-4 text-left font-semibold">Harga</th>
                        <th class="px-6 py-4 text-left font-semibold">Stok</th>
                        <th class="px-6 py-4 text-left font-semibold">Gambar</th>
                        <th class="px-6 py-4 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-300">
                                <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                                <p>Belum ada produk</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $p): ?>
                            <tr class="border-t border-white/10 hover:bg-white/5 transition">
                                <td class="px-6 py-4"><?= $p['id'] ?></td>
                                <td class="px-6 py-4 font-semibold"><?= htmlspecialchars($p['name']) ?></td>
                                <td class="px-6 py-4 text-blue-300">
                                    Rp <?= number_format($p['price'], 0, ',', '.') ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm <?= $p['stock'] > 10 ? 'bg-green-500/20 text-green-300' : 'bg-red-500/20 text-red-300' ?>">
                                        <?= $p['stock'] ?> unit
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                    $imagePath = BASE_URL . "/public/uploads/products/" . $p['image'];
                                    $imageExists = file_exists(BASE_PATH . "/src/public/uploads/products/" . $p['image']);
                                    ?>
                                    <?php if ($imageExists && $p['image'] !== 'default.jpg'): ?>
                                        <img src="<?= $imagePath ?>" 
                                             alt="<?= htmlspecialchars($p['name']) ?>"
                                             class="w-16 h-16 object-cover rounded-lg border-2 border-white/10">
                                    <?php else: ?>
                                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500/30 to-purple-500/30 rounded-lg flex items-center justify-center">
                                            <i data-lucide="package" class="w-8 h-8 text-blue-200"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center gap-2">
                                        <a href="<?= BASE_URL ?>/product/edit/<?= $p['id'] ?>" 
                                           class="glass-effect px-4 py-2 rounded-lg hover:bg-blue-500/20 transition flex items-center gap-2">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                            Edit
                                        </a>
                                        <button onclick="confirmDelete(<?= $p['id'] ?>, '<?= htmlspecialchars($p['name']) ?>')" 
                                                class="glass-effect px-4 py-2 rounded-lg hover:bg-red-500/20 text-red-300 transition flex items-center gap-2">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    if (confirm(`Yakin ingin menghapus produk "${name}"?`)) {
        window.location.href = '<?= BASE_URL ?>/product/delete/' + id;
    }
}
</script>