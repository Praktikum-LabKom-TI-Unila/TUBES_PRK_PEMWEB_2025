<?php 
// Panggil konfigurasi & tampilan header
include '../config/koneksi.php'; 
include '../layouts/header.php'; 

// Kalau user sudah login, tendang ke dashboard
if (isset($_SESSION['status']) && $_SESSION['status'] == 'login') {
    echo "<script>location.href='../produk/index.php';</script>";
    exit;
}
?>

<div class="row justify-content-center align-items-center mb-5" style="min-height: 80vh;">
    <div class="col-md-5">
        <div class="card shadow-lg border-0" style="border-radius: 20px;">
            <div class="card-body p-5">
                
                <div class="text-center mb-4">
                    <!-- LOGO DIHAPUS SESUAI REQUEST, TINGGAL TEKS SAJA -->
                    <h2 class="fw-bold" style="color: #4F4A45; letter-spacing: -1px;">Masuk</h2>
                    <p class="text-muted">Kelola kolaborasi bisnis Anda sekarang.</p>
                </div>
                
                <!-- Notifikasi Error -->
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger border-0 shadow-sm" style="background-color: #f8d7da; color: #842029; border-radius: 10px;">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- Notifikasi Sukses -->
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm" style="background-color: #d1e7dd; color: #0f5132; border-radius: 10px;">
                        <i class="fa-solid fa-check-circle me-2"></i>
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <!-- FORM LOGIN -->
                <form action="proses_auth.php?aksi=login" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #6C5F5B;">Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control border-start-0 bg-light" required placeholder="toko@email.com" style="border-radius: 0 10px 10px 0;">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold" style="color: #6C5F5B;">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="password" class="form-control border-start-0 bg-light" required placeholder="******" style="border-radius: 0 10px 10px 0;">
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <!-- TOMBOL WARNA TERRACOTTA (BUKAN BIRU LAGI) -->
                        <button type="submit" class="btn text-white py-2 fw-bold shadow-sm" style="background-color: #ED7D31; border-radius: 50px; transition: 0.3s;">
                            Masuk Sekarang
                        </button>
                    </div>
                </form>
                
                <hr class="my-4" style="opacity: 0.1;">
                
                <div class="text-center">
                    <span class="text-muted">Belum punya akun?</span>
                    <!-- LINK WARNA COKLAT -->
                    <a href="<?php echo $base_url; ?>/auth/register.php" class="text-decoration-none fw-bold" style="color: #ED7D31;">
                        Daftar UMKM Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Efek Hover Tombol Manual -->
<style>
    .btn:hover {
        background-color: #d66a20 !important; /* Warna oranye lebih gelap pas hover */
        transform: translateY(-2px);
    }
    .form-control:focus {
        box-shadow: none;
        border-color: #ED7D31; /* Border jadi oranye pas diklik */
    }
</style>

<?php include '../layouts/footer.php'; ?>