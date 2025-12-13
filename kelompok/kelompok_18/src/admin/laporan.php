<?php
// Gunakan __DIR__ agar path include aman
include __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../layouts/navbar_admin.php';

// Query Data
$query = "SELECT v.*, b.nama_bundle, u1.nama_toko as toko_1, u2.nama_toko as toko_2
          FROM vouchers v
          JOIN bundles b ON v.bundle_id = b.id
          JOIN users u1 ON b.pembuat_id = u1.id
          JOIN users u2 ON b.mitra_id = u2.id
          ORDER BY v.created_at DESC";

$result = mysqli_query($koneksi, $query);
?>

<div class="container mt-4 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-brown">Laporan Transaksi</h3>
            <p class="text-muted">Rekapitulasi penggunaan voucher kolaborasi sistem global.</p>
        </div>
        
        <!-- TOMBOL CETAK (Panggil fungsi printLaporanLocal) -->
        <button onclick="printLaporanLocal()" class="btn btn-admin-action shadow-sm">
            <i class="fa-solid fa-print me-2"></i> Cetak Laporan
        </button>
    </div>

    <!-- Statistik Ringkas -->
    <div class="row mb-4 d-print-none">
        <div class="col-md-3">
            <div class="card bg-terracotta text-white border-0 shadow-sm p-3 rounded-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 opacity-75">Total Voucher</h6>
                        <h2 class="fw-bold mb-0"><?php echo mysqli_num_rows($result); ?></h2>
                    </div>
                    <i class="fa-solid fa-ticket fa-2x opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="card table-card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-admin align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Kode Voucher</th>
                            <th>Kolaborasi</th>
                            <th>Nama Bundle</th>
                            <th>Diskon</th>
                            <th>Kuota</th>
                            <th class="text-center">Status</th>
                            <th class="text-center d-print-none">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark border px-3 py-2 fw-bold" style="font-family: monospace;">
                                        <?php echo htmlspecialchars($row['kode_voucher']); ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="fw-bold text-terracotta"><?php echo htmlspecialchars($row['toko_1']); ?></small> 
                                    <span class="text-muted mx-1">&</span> 
                                    <small class="fw-bold text-brown"><?php echo htmlspecialchars($row['toko_2']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($row['nama_bundle']); ?></td>
                                <td class="fw-bold text-danger">Rp <?php echo number_format($row['potongan_harga']); ?></td>
                                <td style="min-width: 150px;">
                                    <?php 
                                        $kuota_max = $row['kuota_maksimal'];
                                        $terpakai  = $row['kuota_terpakai'];
                                        $persen    = ($kuota_max > 0) ? ($terpakai / $kuota_max) * 100 : 0;
                                        $warna_bar = $persen >= 100 ? 'bg-secondary' : ($persen > 80 ? 'bg-danger' : 'bg-success');
                                    ?>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px; background-color: #eee;">
                                            <div class="progress-bar <?php echo $warna_bar; ?>" role="progressbar" style="width: <?php echo $persen; ?>%"></div>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.7rem;"><?php echo $terpakai; ?>/<?php echo $kuota_max; ?></small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php if($row['status'] == 'available'): ?>
                                        <span class="badge bg-success rounded-pill px-3">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill px-3">Non-Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center d-print-none">
                                    <a href="proses_admin.php?aksi=hapus_voucher&id=<?php echo $row['id']; ?>" class="btn btn-sm text-danger hover-bg-light rounded-circle p-2" onclick="return confirm('Hapus voucher ini?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">Belum ada transaksi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- === SCRIPT KHUSUS HALAMAN INI === -->
<!-- Kita taruh JS Print langsung di sini biar DIJAMIN JALAN -->
<script>
    function printLaporanLocal() {
        window.print();
    }
</script>

<!-- CSS Khusus Cetak (Sembunyikan Navbar & Tombol saat Print) -->
<style>
@media print {
    .btn-admin-action, .navbar, footer, .d-print-none { display: none !important; }
    body { background-color: white; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
    .container { margin-top: 0 !important; max-width: 100%; }
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>