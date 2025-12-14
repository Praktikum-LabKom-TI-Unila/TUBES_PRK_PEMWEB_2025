<?php
// Include pakai __DIR__ biar aman
include __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../layouts/header.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href='../auth/login.php';</script>";
    exit;
}

$id_user = $_SESSION['user_id'];

// Query: Ambil voucher milik bundle yang user ini terlibat (sebagai pembuat atau mitra)
$query = "SELECT v.*, b.nama_bundle 
          FROM vouchers v
          JOIN bundles b ON v.bundle_id = b.id
          WHERE b.pembuat_id = '$id_user' OR b.mitra_id = '$id_user'
          ORDER BY v.id DESC";

$result = mysqli_query($koneksi, $query);
?>

<!-- Panggil CSS Voucher -->
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_voucher.css?v=<?php echo time(); ?>">

<div class="container mt-4 mb-5 page-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="title-section mb-0">Manajemen Voucher</h2>
        <!-- Arahkan ke file create.php -->
        <a href="create.php" class="btn-add text-decoration-none">
            <i class="fa-solid fa-plus me-2"></i> Buat Voucher
        </a>
    </div>

    <div class="card-table">
        <table class="table-voucher">
            <thead>
                <tr>
                    <th width="25%">Kode Unik</th>
                    <th width="30%">Untuk Bundle</th>
                    <th width="15%">Kuota</th>
                    <th width="15%">Expired</th>
                    <th width="15%">Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <!-- PERUBAHAN DI SINI: Menggunakan class voucher-badge -->
                    <td>
                        <span class="voucher-badge">
                            <?= $row['kode_voucher'] ?>
                        </span>
                    </td>
                    
                    <td class="fw-bold text-secondary"><?= $row['nama_bundle'] ?></td>
                    
                    <td>
                        <span class="fw-bold text-dark"><?= $row['kuota_terpakai'] ?></span> 
                        <span class="text-muted">/ <?= $row['kuota_maksimal'] ?></span>
                    </td>
                    
                    <td>
                        <?php 
                            if($row['expired_at']) {
                                // Warna merah jika sudah lewat
                                $tgl = strtotime($row['expired_at']);
                                $color = ($tgl < time()) ? 'text-danger fw-bold' : 'text-muted';
                                echo "<span class='$color'>" . date('d M Y', $tgl) . "</span>";
                            } else {
                                echo '<span class="text-muted">Seumur Hidup</span>';
                            }
                        ?>
                    </td>
                    
                    <td>
                        <?php 
                            if($row['status'] == 'available') {
                                echo '<span class="badge bg-success rounded-pill px-3">Aktif</span>';
                            } elseif($row['status'] == 'expired') {
                                echo '<span class="badge bg-danger rounded-pill px-3">Expired</span>';
                            } else {
                                echo '<span class="badge bg-secondary rounded-pill px-3">Habis</span>';
                            }
                        ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center py-5 text-muted">Belum ada voucher dibuat.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>