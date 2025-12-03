<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

require '../frontend/layout/header.html';
require '../frontend/layout/sidebar.php';
?>

<div class="card shadow-sm">
    <div class="card-body">
        <h2 class="card-title">Selamat Datang, <?php echo $_SESSION['nama']; ?>!</h2>
        <p class="text-muted">Anda login sebagai <strong><?php echo ucfirst($_SESSION['role']); ?></strong></p>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Silakan pilih menu di samping untuk mulai menggunakan layanan SigerHub.
        </div>

        <div class="row mt-4">
            <<div class="col-md-6">
                <div class="card card-dashboard text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Status Akun</h5>
                        <p class="card-text">Aktif</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

require '../frontend/layout/footer.html';
?>