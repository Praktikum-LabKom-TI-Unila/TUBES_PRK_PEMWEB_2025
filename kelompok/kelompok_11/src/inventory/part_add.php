<?php
$pageTitle = 'Tambah Sparepart';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../auth/cek_login.php';
require_role(['Admin']);

$error = '';
$success = '';

$suppliers = fetchAll("SELECT * FROM suppliers ORDER BY nama");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama = $_POST['nama'] ?? '';
    $sku = $_POST['sku'] ?? '';
    $deskripsi = $_POST['deskripsi'] ?? '';
    $harga_beli = $_POST['harga_beli'] ?? 0;
    $harga_jual = $_POST['harga_jual'] ?? 0;
    $stok = $_POST['stok'] ?? 0;
    $supplier_id = $_POST['supplier_id'] ?? null;
    $min_stok = $_POST['min_stok'] ?? 0;

    if (!empty($nama) && !empty($sku)) {

        $conn = getConnection();
        $nama = mysqli_real_escape_string($conn, $nama);
        $sku = mysqli_real_escape_string($conn, $sku);
        $deskripsi = mysqli_real_escape_string($conn, $deskripsi);
        $supplier_id = $supplier_id ? intval($supplier_id) : 'NULL';

        $sql = "INSERT INTO parts 
                (nama, sku, deskripsi, harga_beli, harga_jual, stok, supplier_id, min_stok, created_at)
                VALUES 
                ('$nama', '$sku', '$deskripsi', $harga_beli, $harga_jual, $stok, $supplier_id, $min_stok, NOW())";

        if (mysqli_query($conn, $sql)) {
            mysqli_close($conn);

            header('Location: part_list.php');
            exit;
        } else {
            $error = 'Gagal menambah sparepart: ' . mysqli_error($conn);
        }

        mysqli_close($conn);

    } else {
        $error = 'Nama dan SKU harus diisi.';
    }
}

require_once __DIR__ . '/../layout/header.php';
?>

<div class="flex justify-end items-center mb-6">
    <a href="part_list.php" class="glass-panel px-4 py-2 rounded-xl hover:shadow-glass transition flex items-center gap-2">
        <i class="fas fa-arrow-left text-brand-blue"></i>
        <span class="font-medium text-brand-dark">Kembali</span>
    </a>
</div>

<?php if (!empty($error)): ?>
<div class="glass-panel p-4 rounded-2xl border-l-4 border-red-500 mb-6">
    <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
        <p class="text-red-700 font-medium"><?= htmlspecialchars($error) ?></p>
    </div>
</div>
<?php endif; ?>

<div class="glass-panel rounded-3xl shadow-glass overflow-hidden">
    <div class="px-6 py-4 bg-white/40 border-b border-white/50">
        <h3 class="text-lg font-bold text-brand-dark">Form Data Sparepart</h3>
        <p class="text-sm text-brand-gray">Isi semua field yang wajib diisi (*)</p>
    </div>
    
    <div class="p-6">
        <form method="POST" class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Nama Sparepart *</label>
                    <input type="text" name="nama" required
                        value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                        class="w-full px-4 py-3 rounded-xl border">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">SKU *</label>
                    <input type="text" name="sku" required
                        value="<?= htmlspecialchars($_POST['sku'] ?? '') ?>"
                        class="w-full px-4 py-3 rounded-xl border">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                    class="w-full px-4 py-3 rounded-xl border"><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Harga Beli (Rp)</label>
                    <input type="number" name="harga_beli"
                        value="<?= htmlspecialchars($_POST['harga_beli'] ?? '0') ?>"
                        class="w-full px-4 py-3 rounded-xl border">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Harga Jual (Rp)</label>
                    <input type="number" name="harga_jual"
                        value="<?= htmlspecialchars($_POST['harga_jual'] ?? '0') ?>"
                        class="w-full px-4 py-3 rounded-xl border">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium mb-2">Stok Awal</label>
                    <input type="number" name="stok"
                        value="<?= htmlspecialchars($_POST['stok'] ?? '0') ?>"
                        class="w-full px-4 py-3 rounded-xl border">
                </div>
                
                <div>
                    <label class="block text-sm font-medium mb-2">Stok Minimum</label>
                    <input type="number" name="min_stok"
                        value="<?= htmlspecialchars($_POST['min_stok'] ?? '0') ?>"
                        class="w-full px-4 py-3 rounded-xl border">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Supplier</label>
                    <select name="supplier_id" class="w-full px-4 py-3 rounded-xl border bg-white">
                        <option value="">— Pilih Supplier —</option>
                        <?php foreach ($suppliers as $supplier): ?>
                            <option value="<?= $supplier['id'] ?>"
                                <?= (($_POST['supplier_id'] ?? '') == $supplier['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($supplier['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-3 bg-brand-blue text-white rounded-xl">
                    Simpan Sparepart
                </button>

                <a href="part_list.php" class="px-6 py-3 bg-gray-200 rounded-xl">
                    Batal
                </a>
            </div>

        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
