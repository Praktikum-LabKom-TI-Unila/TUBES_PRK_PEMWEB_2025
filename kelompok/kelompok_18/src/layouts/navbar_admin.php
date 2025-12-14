<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// Proteksi Admin: Cek apakah user yang login punya role 'admin'
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    // Jika bukan admin, tendang ke halaman depan
    // Kita gunakan ../index.php karena asumsi file ini di-include oleh file dalam folder admin/
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | X-Bundle</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS KHUSUS ADMIN (Penting: Pastikan file ini ada di assets/css/) -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style_admin.css">
</head>
<body class="bg-light">

<!-- NAVBAR ADMIN -->
<!-- Class 'navbar-admin' ini yang bikin warnanya jadi Coklat Tua (Earth Tone) -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm navbar-admin">
  <div class="container">
    
    <!-- Logo & Judul Admin -->
    <a class="navbar-brand" href="<?php echo $base_url; ?>/admin/index.php">
        <i class="fa-solid fa-shield-halved me-2"></i> ADMIN PANEL
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        
        <!-- Menu Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $base_url; ?>/admin/index.php">
                <i class="fa-solid fa-gauge me-1"></i> Dashboard
            </a>
        </li>
        
        <!-- Menu Kelola User -->
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $base_url; ?>/admin/users.php">
                <i class="fa-solid fa-users me-1"></i> Kelola User
            </a>
        </li>
        
        <!-- Menu Laporan -->
        <li class="nav-item">
            <a class="nav-link" href="<?php echo $base_url; ?>/admin/laporan.php">
                <i class="fa-solid fa-file-invoice me-1"></i> Laporan Global
            </a>
        </li>
        
        <!-- Tombol Logout (Class 'btn-logout' ada di style_admin.css) -->
        <li class="nav-item ms-lg-3">
            <a class="nav-link btn-logout px-4" href="<?php echo $base_url; ?>/auth/logout.php">
                Logout <i class="fa-solid fa-right-from-bracket ms-1"></i>
            </a>
        </li>

      </ul>
    </div>
  </div>
</nav>

<!-- Container Utama (Memberi jarak agar konten tidak tertutup navbar fixed) -->
<div class="container" style="min-height: 80vh; margin-top: 100px;">