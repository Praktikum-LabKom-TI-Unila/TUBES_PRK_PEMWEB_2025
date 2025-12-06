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

<!-- Panggil CSS Voucher (Pastikan filenya ada di assets/css) -->
<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_voucher.css">

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
                    <th>Kode Unik</th>
                    <th>Untuk Bundle</th>
                    <th>Kuota</th>
                    <th>Expired</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td class="fw-bold text-primary"><?= $row['kode_voucher'] ?></td>
                    <td><?= $row['nama_bundle'] ?></td>
                    <td>
                        <?= $row['kuota_terpakai'] ?> / <?= $row['kuota_maksimal'] ?>
                    </td>
                    <td>
                        <?php 
                            if($row['expired_at']) {
                                echo date('d M Y', strtotime($row['expired_at']));
                            } else {
                                echo '<span class="text-muted">Seumur Hidup</span>';
                            }
                        ?>
                    </td>
                    <td>
                        <?php 
                            if($row['status'] == 'available') {
                                echo '<span class="badge bg-success">Aktif</span>';
                            } elseif($row['status'] == 'expired') {
                                echo '<span class="badge bg-warning text-dark">Expired</span>';
                            } else {
                                echo '<span class="badge bg-secondary">Habis</span>';
                            }
                        ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada voucher dibuat.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>