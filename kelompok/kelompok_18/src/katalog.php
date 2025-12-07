<?php
// src/katalog.php
session_start();

// --- 1. DETEKSI KONEKSI OTOMATIS ---
// Mencari file koneksi agar tidak error path
if (file_exists('../config/koneksi.php')) {
    include '../config/koneksi.php';
} elseif (file_exists('config/koneksi.php')) {
    include 'config/koneksi.php';
} else {
    die("Error: File koneksi.php tidak ditemukan.");
}

// --- 2. DETEKSI HEADER OTOMATIS ---
// Ini solusi agar Navbar "Akun" muncul. Kita cari filenya ada di mana.
$path_header = '';
if (file_exists('../layouts/header.php')) {
    $path_header = '../layouts/header.php';
} elseif (file_exists('layouts/header.php')) {
    $path_header = 'layouts/header.php';
}

// Include header jika ketemu
if ($path_header) {
    include $path_header;
} else {
    // Pesan error jika header masih tidak ketemu (untuk debugging)
    echo '<div class="alert alert-danger text-center m-0">Gagal memuat Navbar. File header.php tidak ditemukan di folder layouts.</div>';
}

// --- 3. LOGIKA ROLE USER ---
// Untuk menentukan siapa yang boleh klaim voucher
$user_logged_in = isset($_SESSION['user_id']);
$user_role = 'guest'; // Default Tamu

if ($user_logged_in) {
    $id_saya = $_SESSION['user_id'];
    // Cek database untuk memastikan role terbaru
    $q_role = mysqli_query($koneksi, "SELECT role FROM users WHERE id='$id_saya'");
    if ($r = mysqli_fetch_assoc($q_role)) {
        $user_role = $r['role']; // 'admin' atau 'umkm'
    }
}

// --- 4. FILTER PENCARIAN ---
$where_prod = "";
$where_bundle = "WHERE b.status = 'active'";

if (isset($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['cari']);
    $where_prod = "AND (nama_produk LIKE '%$keyword%' OR deskripsi LIKE '%$keyword%')";
    $where_bundle .= " AND (b.nama_bundle LIKE '%$keyword%')";
}

// --- 5. QUERY DATA ---
$q_bundles = "SELECT b.*, 
              u1.nama_toko as toko1, u2.nama_toko as toko2,
              p1.nama_produk as prod1, p1.gambar as img1,
              p2.nama_produk as prod2, p2.gambar as img2,
              v.id as voucher_id, v.kode_voucher, v.kuota_maksimal, v.expired_at
              FROM bundles b
              JOIN users u1 ON b.pembuat_id = u1.id
              JOIN users u2 ON b.mitra_id = u2.id
              LEFT JOIN products p1 ON b.produk_pembuat_id = p1.id
              LEFT JOIN products p2 ON b.produk_mitra_id = p2.id
              LEFT JOIN vouchers v ON v.bundle_id = b.id AND v.status='available'
              $where_bundle
              ORDER BY b.created_at DESC";
$res_bundles = mysqli_query($koneksi, $q_bundles);

$res_products = mysqli_query($koneksi, "SELECT p.*, u.nama_toko FROM products p JOIN users u ON p.user_id = u.id WHERE 1=1 $where_prod ORDER BY p.created_at DESC");
?>

<!-- HERO SECTION (Desain Lama) -->
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

    <!-- BAGIAN 1: PAKET BUNDLE -->
    <div class="d-flex align-items-center mb-3">
        <h3 class="fw-bold me-3" style="color: #ED7D31;"><i class="fa-solid fa-box-open me-2"></i>Paket Bundle Spesial</h3>
        <hr class="flex-grow-1" style="color: #ED7D31;">
    </div>

    <div class="row g-4 mb-5">
        <?php if(mysqli_num_rows($res_bundles) > 0): ?>
            <?php while($b = mysqli_fetch_assoc($res_bundles)): 
                
                // LOGIKA STATUS TOMBOL VOUCHER
                $is_claimed = false;
                // Cek Session Browser (Tamu)
                if (isset($_SESSION['claimed_vouchers']) && in_array($b['voucher_id'], $_SESSION['claimed_vouchers'])) {
                    $is_claimed = true;
                }
                $is_habis = ($b['kuota_maksimal'] <= 0);
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 overflow-hidden bundle-card-public">
                    
                    <!-- Gambar Bundle -->
                    <div class="d-flex" style="height: 180px;">
                        <div class="w-50 bg-light" style="background: url('../assets/img/<?= $b['img1'] ?? 'no-image.jpg' ?>') center/cover;"></div>
                        <div class="w-50 bg-light" style="background: url('../assets/img/<?= $b['img2'] ?? 'no-image.jpg' ?>') center/cover; border-left: 2px solid white;"></div>
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
                        
                        <?php if($b['kode_voucher']): ?>
                            <small class="text-muted d-block mt-2 mb-1">
                                Sisa Kuota: <b><?= $b['kuota_maksimal'] ?></b>
                            </small>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer bg-white border-0 pb-3">
                        <?php if($b['kode_voucher']): ?>
                            
                            <!-- LOGIKA TAMPILAN TOMBOL -->
                            <?php if ($user_role == 'admin' || $user_role == 'umkm'): ?>
                                <!-- KASUS 1: ADMIN/UMKM (Tidak Bisa Klaim) -->
                                <button class="btn btn-light w-100 text-muted" disabled style="font-size:13px;">
                                    Mode Mitra (Tidak Perlu Klaim)
                                </button>

                            <?php elseif ($is_claimed): ?>
                                <!-- KASUS 2: SUDAH KLAIM -->
                                <button class="btn btn-secondary w-100 fw-bold" disabled>
                                    <i class="fa-solid fa-check-circle me-2"></i> SUDAH DIKLAIM
                                </button>
                                <div class="text-center mt-1">
                                    <small class="text-success fw-bold" style="cursor:pointer;" onclick="showKode('<?= $b['kode_voucher'] ?>')">
                                        Lihat Kode: <?= $b['kode_voucher'] ?>
                                    </small>
                                </div>

                            <?php elseif ($is_habis): ?>
                                <!-- KASUS 3: HABIS -->
                                <button class="btn btn-secondary w-100 fw-bold" disabled>
                                    <i class="fa-solid fa-ban me-2"></i> VOUCHER HABIS
                                </button>

                            <?php else: ?>
                                <!-- KASUS 4: MASYARAKAT UMUM (BISA KLAIM) -->
                                <button id="btn-klaim-<?= $b['voucher_id'] ?>" 
                                        class="btn btn-outline-danger w-100 fw-bold border-2" 
                                        onclick="klaimVoucher(<?= $b['voucher_id'] ?>, this)">
                                    <i class="fa-solid fa-ticket me-2"></i> KLAIM VOUCHER
                                </button>
                            <?php endif; ?>

                        <?php else: ?>
                            <button class="btn btn-light w-100 text-muted" disabled>Promo Tidak Tersedia</button>
                        <?php endif; ?>
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
                <div style="height: 160px; background: url('../assets/img/<?= $p['gambar'] ?>') center/cover; border-radius: 10px 10px 0 0;"></div>
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

<!-- MODAL POPUP KODE -->
<div class="modal fade" id="modalSuksesKlaim" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4 border-0 shadow">
            <h4 class="fw-bold text-dark">Selamat! Voucher Didapat</h4>
            <p class="text-muted">Gunakan kode ini saat checkout di kedua toko!</p>
            <div class="p-3 bg-light border border-danger border-dashed rounded mb-3">
                <h2 class="mb-0 fw-bold text-danger ls-2" id="textKodeVoucher">...</h2>
            </div>
            <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Tutup</button>
        </div>
    </div>
</div>

<style>
    .bundle-card-public:hover { transform: translateY(-5px); transition: 0.3s; box-shadow: 0 10px 20px rgba(237, 125, 49, 0.2)!important; }
    .ls-2 { letter-spacing: 2px; }
</style>

<!-- SCRIPT JS -->
<!-- Note: Jika header.php sudah memuat bootstrap JS, baris ini opsional. Tapi aman dipasang untuk jaga-jaga -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function klaimVoucher(idVoucher, btnElement) {
        let originalText = btnElement.innerHTML;
        btnElement.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Memproses...';
        btnElement.disabled = true;

        $.ajax({
            url: 'proses_klaim.php',
            type: 'POST',
            data: { voucher_id: idVoucher },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#textKodeVoucher').text(response.voucher_code);
                    var myModal = new bootstrap.Modal(document.getElementById('modalSuksesKlaim'));
                    myModal.show();

                    $(btnElement).removeClass('btn-outline-danger').addClass('btn-secondary');
                    $(btnElement).html('<i class="fa-solid fa-check-circle me-2"></i> SUDAH DIKLAIM');
                } else {
                    alert(response.message);
                    btnElement.innerHTML = originalText;
                    btnElement.disabled = false;
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                alert('Gagal menghubungi server.');
                btnElement.innerHTML = originalText;
                btnElement.disabled = false;
            }
        });
    }

    function showKode(kode) {
        $('#textKodeVoucher').text(kode);
        var myModal = new bootstrap.Modal(document.getElementById('modalSuksesKlaim'));
        myModal.show();
    }
</script>

<?php 
// Tambahan penutup body/html jika header tidak termuat dengan benar
// Tapi karena kita sudah mendeteksi path header, ini seharusnya aman.
?>
</body>
</html>