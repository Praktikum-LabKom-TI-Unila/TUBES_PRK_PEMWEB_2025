<?php
// Gunakan __DIR__ agar path include aman (Anti Error Path)
include __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../layouts/navbar_admin.php';

// --- LOGIC PENCARIAN ---
$where = "WHERE role='umkm'";
if (isset($_GET['cari'])) {
    $keyword = mysqli_real_escape_string($koneksi, $_GET['cari']);
    $where .= " AND (nama_toko LIKE '%$keyword%' OR nama_lengkap LIKE '%$keyword%' OR kategori_bisnis LIKE '%$keyword%')";
}

// Ambil data user terbaru
$query = "SELECT * FROM users $where ORDER BY id DESC";
$result = mysqli_query($koneksi, $query);
?>

<div class="container mt-4 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-brown">Manajemen User</h3>
            <p class="text-muted">Kelola data mitra UMKM yang terdaftar.</p>
        </div>
        
        <!-- Form Cari -->
        <form action="" method="GET" class="d-flex shadow-sm rounded-pill overflow-hidden bg-white">
            <input type="text" name="cari" class="form-control border-0 ps-4" placeholder="Cari Toko / Pemilik..." value="<?php echo isset($_GET['cari']) ? htmlspecialchars($_GET['cari']) : ''; ?>">
            <button class="btn btn-admin-action px-4" type="submit"><i class="fa-solid fa-search"></i></button>
        </form>
    </div>

    <!-- Alert Notifikasi -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-3 fade show">
            <i class="fa-solid fa-check-circle me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="card table-card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-admin align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4" width="5%">No</th>
                            <th width="25%">Identitas Toko</th>
                            <th width="20%">Pemilik</th>
                            <th width="25%">Kontak</th>
                            <th width="15%">Bergabung</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no=1; 
                        if(mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)): 
                        ?>
                        <tr>
                            <td class="ps-4 text-muted fw-bold"><?php echo $no++; ?></td>
                            <td>
                                <div class="fw-bold text-dark fs-5"><?php echo htmlspecialchars($row['nama_toko']); ?></div>
                                <!-- Menampilkan Kategori Bisnis -->
                                <span class="badge badge-role mt-1">
                                    <i class="fa-solid fa-tag me-1"></i> <?php echo htmlspecialchars($row['kategori_bisnis'] ?? 'Umum'); ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($row['nama_lengkap']); ?></div>
                                <small class="text-muted">ID: #<?php echo $row['id']; ?></small>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <small><i class="fa-solid fa-envelope text-muted me-2"></i> <?php echo htmlspecialchars($row['email']); ?></small>
                                    <?php if(!empty($row['no_hp'])): ?>
                                        <small class="text-success fw-bold"><i class="fa-brands fa-whatsapp me-2"></i> <?php echo htmlspecialchars($row['no_hp']); ?></small>
                                    <?php else: ?>
                                        <small class="text-muted fst-italic ms-4">- No HP Kosong -</small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                            <td class="text-center">
                                <!-- PERBAIKAN TOMBOL HAPUS: Menggunakan Link langsung agar lebih stabil -->
                                <a href="proses_admin.php?aksi=hapus_user&id=<?php echo $row['id']; ?>" 
                                   class="btn btn-sm btn-outline-danger rounded-pill px-3" 
                                   onclick="return confirm('⚠️ PERINGATAN PENTING!\n\nApakah Anda yakin ingin menghapus Toko ini?\n\nSemua data (Produk, Chat, Bundle) milik toko ini akan hilang permanen.')">
                                    <i class="fa-solid fa-trash"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; 
                        else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-regular fa-folder-open fa-3x mb-3 opacity-25"></i><br>
                                    Tidak ada data user ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Load Script Admin (Opsional, untuk fitur lain) -->
<script src="<?php echo $base_url; ?>/assets/js/script_admin.js"></script>
<?php include __DIR__ . '/../layouts/footer.php'; ?>