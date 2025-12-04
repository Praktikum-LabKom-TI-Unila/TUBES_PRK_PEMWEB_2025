<?php
$role = $_SESSION['role'] ?? 'guest'; 
?>

<div class="sidebar p-3" style="width: 250px;">
    <h4 class="text-center mb-4"><i class="fas fa-landmark"></i> SigerHub</h4>
    
    <a href="../backend/dashboard_warga.php"><i class="fas fa-home me-2"></i> Dashboard</a>

    <?php if ($role == 'warga'): ?>
        <small class="text-muted ms-3 mt-3 d-block">LAYANAN WARGA</small>
        <a href="../backend/pengaduan_form.php"><i class="fas fa-bullhorn me-2"></i> Lapor Jalan/Sampah</a>
        <a href="../backend/pengaduan_riwayat.php"><i class="fas fa-history me-2"></i> Riwayat Laporan</a>
        
        <a href="../backend/umkm_daftar.php"><i class="fas fa-store me-2"></i> Daftar UMKM</a>
        <a href="../backend/umkm_status.php"><i class="fas fa-file-contract me-2"></i> Status Izin</a>
    
    <?php elseif ($role == 'admin'): ?>
        <small class="text-muted ms-3 mt-3 d-block">PANEL ADMIN</small>
        <a href="../backend/admin_pengaduan.php"><i class="fas fa-check-double me-2"></i> Validasi Laporan</a>
        
        <a href="../backend/admin_umkm.php"><i class="fas fa-user-check me-2"></i> Validasi UMKM</a>
        <a href="../backend/kelola_user.php"><i class="fas fa-users me-2"></i> Kelola User</a>
    <?php endif; ?>

    <hr>
    <a href="../backend/logout.php" class="text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<div class="flex-grow-1 p-4">