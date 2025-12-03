<?php
// Perhatikan path include-nya
include 'config/koneksi.php';
include 'layouts/header.php';

// --- LOGIC PENCARIAN ---
$where = "";
if (isset($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['cari']);
    $where = "AND (products.nama_produk LIKE '%$keyword%' OR products.deskripsi LIKE '%$keyword%')";
}

// --- QUERY UTAMA ---
$query = "SELECT products.*, users.nama_toko, users.nama_lengkap 
          FROM products 
          JOIN users ON products.user_id = users.id 
          WHERE 1=1 $where 
          ORDER BY products.id DESC";

$result = mysqli_query($koneksi, $query);
?>

<!-- === HEADER HERO SECTION === -->
<div class="py-5 mb-4 border-bottom" style="background-color: #F6F1EE;">
    <div class="container text-center">
        <h1 class="fw-bold display-5" style="color: #4F4A45;">Katalog Produk UMKM</h1>
        <p class="lead mb-4" style="color: #6C5F5B;">Jelajahi ratusan produk lokal dan temukan peluang kolaborasi.</p>
        
        <!-- Form Pencarian -->
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="" method="GET">
                    <div class="input-group input-group-lg shadow-sm">
                        <input type="text" name="cari" class="form-control border-0" placeholder="Cari kopi, keripik, jasa..." value="<?php echo isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>">
                        <!-- TOMBOL CARI TERRACOTTA -->
                        <button class="btn px-4 text-white" type="submit" style="background-color: #ED7D31;">
                            <i class="fa-solid fa-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- === GRID PRODUK === -->
<div class="container mb-5">
    
    <?php if(isset($_GET['cari'])): ?>
        <div class="alert mb-4" style="background-color: #F6F1EE; color: #4F4A45; border-left: 4px solid #ED7D31;">
            Menampilkan hasil pencarian untuk: <strong>"<?php echo htmlspecialchars($_GET['cari']); ?>"</strong>
            <a href="katalog.php" class="float-end text-decoration-none fw-bold" style="color: #ED7D31;">Reset</a>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 shadow-sm border-0 product-card hover-effect" style="border-radius: 12px;">
                        
                        <!-- Gambar Produk -->
                        <div style="height: 200px; overflow: hidden; background: #f8f9fa; border-top-left-radius: 12px; border-top-right-radius: 12px;" class="d-flex align-items-center justify-content-center position-relative">
                            <?php if($row['gambar'] != 'no-image.jpg' && file_exists('assets/img/'.$row['gambar'])): ?>
                                <img src="assets/img/<?php echo $row['gambar']; ?>" class="w-100 h-100" style="object-fit: cover;" alt="<?php echo $row['nama_produk']; ?>">
                            <?php else: ?>
                                <div class="text-center text-muted">
                                    <i class="fa-solid fa-image fa-3x mb-2" style="color: #ccc;"></i><br>
                                    <small>No Image</small>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Badge Stok -->
                            <span class="position-absolute top-0 end-0 badge m-2" style="background-color: rgba(79, 74, 69, 0.8);">
                                Stok: <?php echo $row['stok']; ?>
                            </span>
                        </div>

                        <div class="card-body">
                            <!-- Badge Kategori (Kuning/Emas soft) -->
                            <span class="badge text-dark mb-2" style="background-color: #FAE3C6; color: #4F4A45 !important; font-weight: normal;">
                                <?php echo isset($row['kategori']) ? strtoupper($row['kategori']) : 'UMUM'; ?>
                            </span>

                            <!-- Nama Toko -->
                            <small class="text-uppercase fw-bold d-block mb-1" style="font-size: 0.75rem; color: #6C5F5B;">
                                <i class="fa-solid fa-store" style="color: #ED7D31;"></i> <?php echo htmlspecialchars($row['nama_toko']); ?>
                            </small>
                            
                            <!-- Nama Produk -->
                            <h5 class="card-title fw-bold text-dark text-truncate" title="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                                <?php echo htmlspecialchars($row['nama_produk']); ?>
                            </h5>
                            
                            <!-- Harga (WARNA TERRACOTTA) -->
                            <p class="card-text fw-bold fs-5 mb-2" style="color: #ED7D31;">
                                Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?>
                                <span class="text-muted small fw-normal" style="font-size: 0.8rem;">/ <?php echo isset($row['satuan']) ? $row['satuan'] : 'pcs'; ?></span>
                            </p>
                            
                            <p class="card-text text-muted small text-truncate">
                                <?php echo htmlspecialchars($row['deskripsi']); ?>
                            </p>
                        </div>

                        <!-- Footer Card: Tombol Aksi -->
                        <div class="card-footer bg-white border-0 pb-3 pt-0">
                            <div class="d-grid gap-2">
                                
                                <?php if(!isset($_SESSION['status'])): ?>
                                    <!-- LOGIKA 1: TAMU (Tombol Kuning/Emas) -->
                                    <button type="button" class="btn text-dark fw-bold btn-sm" style="background-color: #FFD580; border: none;" data-bs-toggle="modal" data-bs-target="#modalVoucher<?php echo $row['id']; ?>">
                                        <i class="fa-solid fa-ticket"></i> Cek Promo
                                    </button>
                                    
                                <?php elseif($_SESSION['user_id'] == $row['user_id']): ?>
                                    <!-- LOGIKA 2: PEMILIK (Outline Coklat) -->
                                    <a href="produk/index.php" class="btn btn-sm" style="border: 1px solid #6C5F5B; color: #6C5F5B;">
                                        <i class="fa-solid fa-gear"></i> Kelola Produk
                                    </a>

                                <?php else: ?>
                                    <!-- LOGIKA 3: UMKM LAIN (Tombol Terracotta) -->
                                    <a href="partner/detail.php?id=<?php echo $row['id']; ?>" class="btn btn-sm text-white" style="background-color: #ED7D31;">
                                        <i class="fa-solid fa-handshake"></i> Ajak Kolaborasi
                                    </a>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- MODAL VOUCHER (Desain Earth Tone) -->
                <div class="modal fade" id="modalVoucher<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-4 border-0 shadow" style="border-radius: 15px;">
                        <div class="mb-3">
                            <i class="fa-solid fa-gift fa-3x" style="color: #ED7D31;"></i>
                        </div>
                        <h4 class="fw-bold" style="color: #4F4A45;">Promo Spesial!</h4>
                        <p class="text-muted">Gunakan kode voucher di bawah ini saat bertransaksi di toko <strong><?php echo $row['nama_toko']; ?></strong>.</p>
                        
                        <div class="p-3 rounded-3 border border-dashed mb-3 position-relative" style="background-color: #F6F1EE; border-color: #ED7D31 !important;">
                            <h2 class="mb-0 fw-bold ls-2" style="color: #ED7D31;">HEMAT50</h2>
                            <small class="text-muted fst-italic mt-2 d-block">*Contoh Kode Promo</small>
                        </div>
                        
                        <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal" style="background-color: #6C5F5B; border: none;">Tutup</button>
                    </div>
                  </div>
                </div>

            <?php endwhile; ?>
        <?php else: ?>
            
            <div class="col-12 text-center py-5">
                <div class="opacity-50 mb-3" style="color: #6C5F5B;">
                    <i class="fa-regular fa-folder-open fa-5x"></i>
                </div>
                <h3 class="fw-bold" style="color: #4F4A45;">Belum ada produk.</h3>
                <?php if(isset($_SESSION['status'])): ?>
                    <a href="produk/tambah.php" class="btn text-white mt-2" style="background-color: #ED7D31;">Upload Produk Sekarang</a>
                <?php else: ?>
                    <a href="auth/login.php" class="btn text-white mt-2" style="background-color: #ED7D31;">Login untuk Upload</a>
                <?php endif; ?>
            </div>

        <?php endif; ?>

    </div>
</div>

<style>
    .product-card { transition: transform 0.2s, box-shadow 0.2s; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(79, 74, 69, 0.15) !important; }
    .ls-2 { letter-spacing: 2px; }
</style>

<?php include 'layouts/footer.php'; ?>