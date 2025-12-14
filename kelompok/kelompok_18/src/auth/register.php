<?php 
include '../config/koneksi.php'; 
include '../layouts/header.php'; 
?>

<div class="row justify-content-center mt-5 mb-5">
    <div class="col-md-6">
        <div class="card shadow-lg border-0" style="border-radius: 15px; overflow: hidden;">
            
            <!-- Header Card dengan Warna Earth Tone -->
            <div class="card-header text-white text-center py-4" style="background: linear-gradient(135deg, #6C5F5B, #4F4A45);">
                <h3 class="fw-bold mb-0"><i class="fa-solid fa-store me-2"></i> Daftar UMKM</h3>
                <small style="color: #F6F1EE;">Bergabunglah dengan komunitas bisnis lokal</small>
            </div>

            <div class="card-body p-5" style="background-color: #fff;">
                
                <!-- Notifikasi Error -->
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger border-0 shadow-sm" style="border-left: 5px solid #dc3545;">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form action="proses_auth.php?aksi=register" method="POST">
                    
                    <h5 class="mb-3 pb-2 border-bottom" style="color: #ED7D31; font-weight: bold;">1. Data Pemilik</h5>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #4F4A45;">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" required placeholder="Contoh: Budi Santoso">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold" style="color: #4F4A45;">Email</label>
                            <input type="email" name="email" class="form-control" required placeholder="email@contoh.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold" style="color: #4F4A45;">Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="******">
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4 pb-2 border-bottom" style="color: #ED7D31; font-weight: bold;">2. Identitas Toko</h5>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #4F4A45;">Nama Toko / Usaha</label>
                        <input type="text" name="nama_toko" class="form-control" required placeholder="Contoh: Kopi Senja">
                    </div>

                    <!-- INPUT KATEGORI BISNIS (BARU) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #4F4A45;">Kategori Bisnis</label>
                        <select name="kategori_bisnis" class="form-select" required>
                            <option value="" selected disabled>-- Pilih Jenis Usaha --</option>
                            <option value="Kuliner (FnB)">Kuliner (FnB)</option>
                            <option value="Fashion">Fashion & Busana</option>
                            <option value="Agribisnis">Agribisnis / Pertanian</option>
                            <option value="Manufaktur/Kerajinan">Manufaktur / Kerajinan</option>
                            <option value="Jasa">Jasa / Layanan</option>
                            <option value="Retail/Grosir">Retail / Grosir</option>
                            <option value="Teknologi">Teknologi / Digital</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold" style="color: #4F4A45;">Alamat Toko</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Jl. Mawar No. 12..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <!-- Tombol Submit Custom -->
                        <button type="submit" class="btn btn-lg text-white shadow" style="background-color: #ED7D31; border: none;">
                            <i class="fa-solid fa-paper-plane me-2"></i> Daftar Sekarang
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <small class="text-muted">Sudah punya akun?</small> 
                    <a href="<?php echo $base_url; ?>/auth/login.php" class="fw-bold text-decoration-none" style="color: #6C5F5B;">Login di sini</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>