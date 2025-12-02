<?php
// Perhatikan path include-nya (tidak pakai ../ lagi)
include 'config/koneksi.php';
include 'layouts/header.php';

// Logic Pencarian
$where = "";
if (isset($_GET['cari'])) {
    $keyword = $_GET['cari'];
    $where = "AND (nama_produk LIKE '%$keyword%' OR deskripsi LIKE '%$keyword%')";
}

$query = "SELECT products.*, users.nama_toko, users.nama_lengkap 
          FROM products 
          JOIN users ON products.user_id = users.id 
          WHERE 1=1 $where 
          ORDER BY products.id DESC";

$result = mysqli_query($koneksi, $query);
?>

<div class="bg-light py-5 mb-4">
    <div class="container text-center">
        <h1 class="fw-bold" style="color: var(--dark-text);">Katalog Produk UMKM</h1>
        <p class="text-muted">Jelajahi potensi kolaborasi dengan ratusan produk lokal.</p>
        
        <div class="row justify-content-center mt-3">
            <div class="col-md-6">
                <form action="" method="GET">
                    <div class="input-group">
                        <input type="text" name="cari" class="form-control" placeholder="Cari produk..." value="<?php echo isset($_GET['cari']) ? $_GET['cari'] : ''; ?>">
                        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-search"></i> Cari</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-3 col-sm-6">
                    <div class="card h-100 shadow-sm border-0 product-card">
                        <div style="height: 200px; overflow: hidden; background: #eee;" class="d-flex align-items-center justify-content-center">
                            <?php if($row['gambar'] != 'no-image.jpg'): ?>
                                <img src="assets/img/<?php echo $row['gambar']; ?>" class="w-100" style="object-fit: cover; height: 100%;" alt="Produk">
                            <?php else: ?>
                                <i class="fa-solid fa-image fa-3x text-secondary"></i>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <small class="text-uppercase fw-bold text-muted" style="font-size: 0.7rem;">
                                <i class="fa-solid fa-store"></i> <?php echo $row['nama_toko']; ?>
                            </small>
                            <h5 class="card-title mt-1 fw-bold text-truncate"><?php echo $row['nama_produk']; ?></h5>
                            <p class="card-text text-primary fw-bold">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></p>
                        </div>

                        <div class="card-footer bg-white border-0 pb-3">
                            <div class="d-grid">
                                <?php if(!isset($_SESSION['status'])): ?>
                                    <a href="auth/login.php" class="btn btn-outline-secondary btn-sm">Login untuk Kolab</a>
                                <?php elseif($_SESSION['user_id'] == $row['user_id']): ?>
                                    <a href="produk/index.php" class="btn btn-outline-primary btn-sm">Kelola Produk</a>
                                <?php else: ?>
                                    <a href="partner/detail.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-handshake"></i> Ajak Kolaborasi
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <h4 class="text-muted">Belum ada produk.</h4>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include 'layouts/footer.php'; ?>