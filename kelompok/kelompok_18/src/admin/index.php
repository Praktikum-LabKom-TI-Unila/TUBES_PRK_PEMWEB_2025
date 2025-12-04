<?php
// Gunakan __DIR__ agar path include selalu benar dan aman
include __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../layouts/navbar_admin.php';

// --- LOGIC STATISTIK ---
// 1. Hitung Total Mitra UMKM
$query_user = mysqli_query($koneksi, "SELECT * FROM users WHERE role='umkm'");
$total_user = mysqli_num_rows($query_user);

// 2. Hitung Total Produk
$query_produk = mysqli_query($koneksi, "SELECT * FROM products");
$total_produk = mysqli_num_rows($query_produk);

// 3. Hitung Kolaborasi Aktif
$query_bundle = mysqli_query($koneksi, "SELECT * FROM bundles WHERE status='active'");
$total_bundle = mysqli_num_rows($query_bundle);
?>

<div class="container mt-4 mb-5">
    
    <!-- Header Dashboard -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold text-brown">Dashboard Admin</h2>
            <p class="text-muted">Selamat datang, Administrator! Berikut ringkasan performa X-Bundle.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <span class="badge bg-secondary p-2 shadow-sm">
                <i class="fa-regular fa-calendar me-1"></i> <?php echo date('l, d F Y'); ?>
            </span>
        </div>
    </div>

    <!-- KARTU STATISTIK -->
    <div class="row g-4">
        
        <!-- Card 1: Total Mitra (Warna Terracotta) -->
        <div class="col-md-4">
            <div class="card card-stat bg-terracotta p-4 h-100 text-white">
                <div class="position-relative z-1">
                    <h5 class="text-white-50 text-uppercase fw-bold" style="font-size: 0.9rem;">Total Mitra UMKM</h5>
                    <h1 class="fw-bold display-4 mb-0"><?php echo $total_user; ?></h1>
                    <small>Toko terdaftar</small>
                </div>
                <i class="fa-solid fa-store icon-bg"></i>
            </div>
        </div>

        <!-- Card 2: Total Produk (Warna Coklat Tua) -->
        <div class="col-md-4">
            <div class="card card-stat bg-brown p-4 h-100 text-white">
                <div class="position-relative z-1">
                    <h5 class="text-white-50 text-uppercase fw-bold" style="font-size: 0.9rem;">Produk Terdaftar</h5>
                    <h1 class="fw-bold display-4 mb-0"><?php echo $total_produk; ?></h1>
                    <small>Item siap kolaborasi</small>
                </div>
                <i class="fa-solid fa-box-open icon-bg"></i>
            </div>
        </div>

        <!-- Card 3: Kolaborasi Aktif (Warna Sage/Hijau Abu) -->
        <div class="col-md-4">
            <div class="card card-stat bg-sage p-4 h-100 text-white">
                <div class="position-relative z-1">
                    <h5 class="text-white-50 text-uppercase fw-bold" style="font-size: 0.9rem;">Kolaborasi Aktif</h5>
                    <h1 class="fw-bold display-4 mb-0"><?php echo $total_bundle; ?></h1>
                    <small>Bundle sedang berjalan</small>
                </div>
                <i class="fa-solid fa-handshake icon-bg"></i>
            </div>
        </div>

    </div>

    <!-- Panel Pintasan (Quick Actions) -->
    <div class="row mt-5">
        <div class="col-12">
            <h5 class="fw-bold text-brown mb-3"><i class="fa-solid fa-bolt me-2"></i> Aksi Cepat</h5>
        </div>
        
        <div class="col-md-6 mb-3">
            <a href="users.php" class="card text-decoration-none h-100 border-0 shadow-sm hover-up">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="bg-light rounded-circle p-3 me-3 text-terracotta">
                        <i class="fa-solid fa-users-gear fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Kelola User</h5>
                        <p class="text-muted mb-0 small">Lihat daftar mitra atau hapus akun bermasalah.</p>
                    </div>
                    <i class="fa-solid fa-chevron-right ms-auto text-muted"></i>
                </div>
            </a>
        </div>

        <div class="col-md-6 mb-3">
            <a href="laporan.php" class="card text-decoration-none h-100 border-0 shadow-sm hover-up">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="bg-light rounded-circle p-3 me-3 text-brown">
                        <i class="fa-solid fa-file-pdf fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Cetak Laporan</h5>
                        <p class="text-muted mb-0 small">Rekapitulasi penggunaan voucher sistem global.</p>
                    </div>
                    <i class="fa-solid fa-chevron-right ms-auto text-muted"></i>
                </div>
            </a>
        </div>
    </div>

    <!-- Info Sistem -->
    <div class="alert alert-light border mt-4 shadow-sm">
        <div class="d-flex">
            <div class="me-3 text-info">
                <i class="fa-solid fa-circle-info fa-2x"></i>
            </div>
            <div>
                <h6 class="fw-bold text-dark">Status Sistem</h6>
                <p class="mb-0 text-muted small">
                    Database terhubung. Semua fitur berjalan normal. <br>
                    <strong>Versi Aplikasi:</strong> v4.2 (Earth Tone Edition)
                </p>
            </div>
        </div>
    </div>

</div>

<!-- CSS Tambahan Khusus Halaman Ini (Efek Hover) -->
<style>
    .hover-up { transition: transform 0.2s; }
    .hover-up:hover { transform: translateY(-5px); border-left: 5px solid #ED7D31 !important; }
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>