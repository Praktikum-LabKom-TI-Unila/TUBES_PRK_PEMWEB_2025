<?php
include '../config/koneksi.php';
include '../layouts/header.php';

$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) echo "<script>window.location='../auth/login.php';</script>";

$bundle_id = $_GET['bundle_id'] ?? null;

// 1. Validasi Bundle
$q = mysqli_query($koneksi, "SELECT * FROM bundles WHERE id='$bundle_id' AND (pembuat_id='$my_id' OR mitra_id='$my_id')");
if (mysqli_num_rows($q) == 0) {
    echo "<script>alert('Akses ditolak'); window.location='my_bundles.php';</script>";
    exit;
}
$bundle = mysqli_fetch_assoc($q);

// 2. Tentukan ID Saya & ID Partner
if ($bundle['pembuat_id'] == $my_id) {
    $my_role = 'pembuat';
    $partner_id = $bundle['mitra_id'];
} else {
    $my_role = 'mitra';
    $partner_id = $bundle['pembuat_id'];
}

// 3. Ambil Data Partner & Produk Masing-masing
$partner = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_toko FROM users WHERE id='$partner_id'"));

// Produk Saya
$my_products = mysqli_query($koneksi, "SELECT * FROM products WHERE user_id='$my_id'");
// Produk Partner
$partner_products = mysqli_query($koneksi, "SELECT * FROM products WHERE user_id='$partner_id'");
?>

<link rel="stylesheet" href="../assets/css/style_partner.css">

<div class="container py-5" style="max-width: 800px;">
    <div class="d-flex align-items-center mb-4">
        <a href="my_bundles.php" class="btn btn-light rounded-circle me-3 border"><i class="fa fa-arrow-left"></i></a>
        <h3 class="fw-bold mb-0">Atur Kolaborasi</h3>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="alert alert-info border-0 d-flex align-items-center gap-3">
                <i class="fa fa-info-circle fa-lg"></i>
                <div class="small">
                    Anda sedang mengatur kesepakatan dengan <strong><?= htmlspecialchars($partner['nama_toko']) ?></strong>. 
                    <br>Penawaran ini akan dikirim ke chat untuk disetujui oleh mitra.
                </div>
            </div>

            <form action="proses_partner.php" method="POST">
                <input type="hidden" name="action" value="propose_deal"> <input type="hidden" name="bundle_id" value="<?= $bundle_id ?>">

                <div class="mb-4">
                    <label class="fw-bold mb-2">Nama Paket Bundling</label>
                    <input type="text" name="nama_bundle" class="form-control rounded-pill px-3 py-2" 
                           value="<?= htmlspecialchars($bundle['nama_bundle']) ?>" placeholder="Contoh: Paket Sarapan Hemat" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="fw-bold mb-2 text-primary">Produk Anda</label>
                        <select name="produk_saya" class="form-select rounded-pill" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php while($p = mysqli_fetch_assoc($my_products)): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['nama_produk']) ?> (Rp <?= number_format($p['harga']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold mb-2 text-secondary">Produk <?= htmlspecialchars($partner['nama_toko']) ?></label>
                        <select name="produk_partner" class="form-select rounded-pill" required>
                            <option value="">-- Pilih Produk --</option>
                            <?php while($p = mysqli_fetch_assoc($partner_products)): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['nama_produk']) ?> (Rp <?= number_format($p['harga']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-4 mt-2">
                    <label class="fw-bold mb-2">Harga Jual Bundle (Final)</label>
                    <div class="input-group">
                        <span class="input-group-text rounded-start-pill bg-white border-end-0">Rp</span>
                        <input type="number" name="harga_bundle" class="form-control rounded-end-pill border-start-0" 
                               value="<?= $bundle['harga_bundle'] ?>" placeholder="0" required>
                    </div>
                    <div class="form-text ms-2">Harga ini yang akan tampil di katalog pembeli.</div>
                </div>

                <div class="mb-4">
                    <label class="fw-bold mb-2">Pesan untuk Mitra (Opsional)</label>
                    <textarea name="pesan_proposal" class="form-control" rows="2" placeholder="Contoh: Gimana kalau harganya segini?"></textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold">
                    <i class="fa fa-paper-plane me-2"></i> Kirim Penawaran ke Chat
                </button>
            </form>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>