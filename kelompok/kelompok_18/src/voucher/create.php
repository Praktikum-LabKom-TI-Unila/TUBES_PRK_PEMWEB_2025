<?php
include __DIR__ . '/../config/koneksi.php';
include __DIR__ . '/../layouts/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href='../auth/login.php';</script>";
    exit;
}

$id_user = $_SESSION['user_id'];

// Ambil Bundle Aktif milik user ini buat di dropdown
$query_bundle = mysqli_query($koneksi, "SELECT * FROM bundles WHERE status='active' AND (pembuat_id='$id_user' OR mitra_id='$id_user')");
?>

<link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_voucher.css">

<div class="container mt-4 mb-5">
    <div class="form-container">
        <h2>Generate Voucher Baru</h2>

        <form action="proses_voucher.php" method="POST" class="form-voucher">

            <!-- Dropdown Bundle (Lebih User Friendly) -->
            <div class="form-group">
                <label>Pilih Bundle (Kolaborasi):</label>
                <select name="bundle_id" class="form-control" required>
                    <option value="" disabled selected>-- Pilih Paket --</option>
                    <?php while($row = mysqli_fetch_assoc($query_bundle)): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['nama_bundle'] ?> (Rp <?= number_format($row['harga_bundle']) ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Kode Unik Voucher:</label>
                <input type="text" name="kode_unik" class="form-control" placeholder="Contoh: RAMADHAN2025" style="text-transform:uppercase" required>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Potongan Harga (Rp):</label>
                    <input type="number" name="potongan" class="form-control" placeholder="5000" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>Kuota Maksimal:</label>
                    <input type="number" name="kuota" class="form-control" value="100" required>
                </div>
            </div>

            <!-- Input Tanggal Expired (Yang tadinya hilang) -->
            <div class="form-group">
                <label>Berlaku Sampai:</label>
                <input type="date" name="expired_at" class="form-control" required>
            </div>

            <div class="btn-center">
                <button type="submit" class="btn-add w-100">Simpan Voucher</button>
            </div>
            
            <div class="text-center mt-3">
                <a href="index.php" class="text-muted text-decoration-none">Kembali</a>
            </div>

        </form>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>