<?php
include 'config/koneksi.php';
include 'layouts/header.php';

// Filter Pencarian
$where_prod = "";
$where_bundle = "WHERE b.status = 'active'";

if (isset($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['cari']);
    $where_prod = "AND (nama_produk LIKE '%$keyword%' OR deskripsi LIKE '%$keyword%')";
    $where_bundle .= " AND (b.nama_bundle LIKE '%$keyword%')";
}

// 1. QUERY BUNDLE (GABUNGAN)
$q_bundles = "SELECT b.*, 
              u1.nama_toko as toko1, u2.nama_toko as toko2,
              p1.nama_produk as prod1, p1.gambar as img1,
              p2.nama_produk as prod2, p2.gambar as img2,
              v.kode_voucher
              FROM bundles b
              JOIN users u1 ON b.pembuat_id = u1.id
              JOIN users u2 ON b.mitra_id = u2.id
              LEFT JOIN products p1 ON b.produk_pembuat_id = p1.id
              LEFT JOIN products p2 ON b.produk_mitra_id = p2.id
              LEFT JOIN vouchers v ON v.bundle_id = b.id AND v.status='available'
              $where_bundle
              ORDER BY b.created_at DESC";
$res_bundles = mysqli_query($koneksi, $q_bundles);

// 2. QUERY PRODUK BIASA (SATUAN)
$q_products = "SELECT p.*, u.nama_toko 
               FROM products p 
               JOIN users u ON p.user_id = u.id 
               WHERE 1=1 $where_prod 
               ORDER BY p.created_at DESC";
$res_products = mysqli_query($koneksi, $q_products);
?>

<!-- HERO SECTION -->
<div class="py-5 mb-4 border-bottom" style="background-color: #F6F1EE;">
    <div class="container text-center">
        <h1 class="fw-bold display-5" style="color: #4F4A45;">Katalog X-Bundle</h1>
        <p class="lead mb-4" style="color: #6C5F5B;">Temukan produk satuan atau hemat lebih banyak dengan Paket Bundle!</p>
        <form action="" method="GET" class="row justify-content-center">
            <div class="col-md-6">
                <div class="input-group input-group-lg shadow-sm">
                    <input type="text" name="cari" class="form-control border-0" placeholder="Cari bundle atau produk..." value="<?= $_GET['cari'] ?? '' ?>">
                    <button class="btn text-white" type="submit" style="background-color: #ED7D31;"><i class="fa-solid fa-search"></i> Cari</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="container mb-5">

    <!-- BAGIAN 1: PAKET BUNDLE (GABUNGAN) -->
    <div class="d-flex align-items-center mb-3">
        <h3 class="fw-bold me-3" style="color: #ED7D31;"><i class="fa-solid fa-box-open me-2"></i>Paket Bundle Spesial</h3>
        <hr class="flex-grow-1" style="color: #ED7D31;">
    </div>

    <div class="row g-4 mb-5">
        <?php if(mysqli_num_rows($res_bundles) > 0): ?>
            <?php while($b = mysqli_fetch_assoc($res_bundles)): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 overflow-hidden bundle-card-public">
                    <!-- Tampilan Gabungan 2 Gambar -->
                    <div class="d-flex" style="height: 180px;">
                        <div class="w-50 bg-light" style="background: url('assets/img/<?= $b['img1'] ?? 'no-image.jpg' ?>') center/cover;"></div>
                        <div class="w-50 bg-light" style="background: url('assets/img/<?= $b['img2'] ?? 'no-image.jpg' ?>') center/cover; border-left: 2px solid white;"></div>
                        <!-- Badge Bundle -->
                        <div class="position-absolute top-0 start-50 translate-middle-x mt-2">
                            <span class="badge bg-danger shadow"><i class="fa-solid fa-fire me-1"></i> HEMAT</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">
                            <?= $b['toko1'] ?> <i class="fa-solid fa-xmark mx-1 text-danger"></i> <?= $b['toko2'] ?>
                        </small>
                        <h5 class="fw-bold mt-1 text-dark"><?= htmlspecialchars($b['nama_bundle']) ?></h5>
                        <div class="small text-secondary mb-2">
                            <i class="fa-solid fa-check text-success"></i> <?= $b['prod1'] ?? 'Produk A' ?><br>
                            <i class="fa-solid fa-check text-success"></i> <?= $b['prod2'] ?? 'Produk B' ?>
                        </div>
                        <h4 class="fw-bold" style="color: #ED7D31;">Rp <?= number_format($b['harga_bundle'] ?? 0) ?></h4>
                    </div>
                    <div class="card-footer bg-white border-0 pb-3">
                        <?php if($b['kode_voucher']): ?>
                            <button class="btn btn-outline-danger w-100 fw-bold border-2" data-bs-toggle="modal" data-bs-target="#modalVoc<?= $b['id'] ?>">
                                <i class="fa-solid fa-ticket me-2"></i> KLAIM VOUCHER
                            </button>
                        <?php else: ?>
                            <button class="btn btn-light w-100 text-muted" disabled>Promo Habis</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Modal Voucher Dinamis -->
            <div class="modal fade" id="modalVoc<?= $b['id'] ?>">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-4 border-0 shadow">
                        <h4 class="fw-bold text-dark">Kode Voucher Bundle</h4>
                        <p class="text-muted">Gunakan kode ini saat checkout di kedua toko!</p>
                        <div class="p-3 bg-light border border-danger border-dashed rounded mb-3">
                            <h2 class="mb-0 fw-bold text-danger ls-2"><?= $b['kode_voucher'] ?></h2>
                        </div>
                        <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted py-4">Belum ada paket bundle aktif.</div>
        <?php endif; ?>
    </div>

    <!-- BAGIAN 2: PRODUK SATUAN -->
    <div class="d-flex align-items-center mb-3">
        <h3 class="fw-bold me-3 text-secondary"><i class="fa-solid fa-store me-2"></i>Produk Satuan</h3>
        <hr class="flex-grow-1">
    </div>

    <div class="row g-4">
        <?php while($p = mysqli_fetch_assoc($res_products)): ?>
        <div class="col-6 col-md-3">
            <div class="card h-100 border-0 shadow-sm product-card-public">
                <div style="height: 160px; background: url('assets/img/<?= $p['gambar'] ?>') center/cover; border-radius: 10px 10px 0 0;"></div>
                <div class="card-body p-3">
                    <small class="text-muted"><?= $p['nama_toko'] ?></small>
                    <h6 class="fw-bold text-truncate mb-1"><?= htmlspecialchars($p['nama_produk']) ?></h6>
                    <span class="text-dark fw-bold">Rp <?= number_format($p['harga']) ?></span>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<style>
    .bundle-card-public:hover { transform: translateY(-5px); transition: 0.3s; box-shadow: 0 10px 20px rgba(237, 125, 49, 0.2)!important; }
    .ls-2 { letter-spacing: 2px; }
</style>

<?php include 'layouts/footer.php'; ?>