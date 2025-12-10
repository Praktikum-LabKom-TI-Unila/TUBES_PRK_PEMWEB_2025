<?php
session_set_cookie_params(0, '/'); 
session_start();

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header("Location: ../auth/login.php");
    exit;
}

$configPath = dirname(__DIR__, 3) . '/config/database.php';
if (file_exists($configPath)) {
    require_once $configPath;
}

$page = $_GET['page'] ?? 'home'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - ScholarBridge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body { overflow-x: hidden; background-color: #f4f6f9; }
        
        #wrapper { display: flex; width: 100%; height: 100vh; overflow: hidden; }
        
        #sidebar-wrapper {
            min-width: 250px; max-width: 250px;
            height: 100vh;
            background-color: #212529; color: #fff;
            overflow-y: auto;
            position: relative; 
            z-index: 1; 
        }
        
        #sidebar-wrapper .list-group-item {
            background-color: #212529; color: #ccc; border: none; padding: 1rem 1.5rem;
        }
        #sidebar-wrapper .list-group-item:hover { background-color: #343a40; color: #fff; }
        #sidebar-wrapper .list-group-item.active { background-color: #0d6efd; color: #fff; font-weight: bold; }

        #page-content-wrapper {
            width: 100%;
            height: 100vh;
            overflow-y: auto; 
            position: relative;
        }
    </style>
</head>
<body>

<div class="d-flex" id="wrapper">
    <div id="sidebar-wrapper">
        <div class="sidebar-heading text-center py-4 border-bottom border-secondary fs-4 fw-bold">
            ScholarBridge
        </div>
            <div class="list-group list-group-flush">
                <a href="?page=home" class="list-group-item list-group-item-action <?= $page == 'home' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
                <a href="?page=tutor" class="list-group-item list-group-item-action <?= $page == 'tutor' ? 'active' : '' ?>">
                <i class="fas fa-chalkboard-teacher me-2"></i> Data Tutor
                </a>
                <a href="?page=siswa" class="list-group-item list-group-item-action <?= $page == 'siswa' ? 'active' : '' ?>">
                <i class="fas fa-user-graduate me-2"></i> Data Siswa
                </a>
                <a href="?page=verifikasi" class="list-group-item list-group-item-action <?= $page == 'verifikasi' ? 'active' : '' ?>">
                <i class="fas fa-check-circle me-2"></i> Verifikasi
                </a>
                <a href="../auth/logout.php" class="list-group-item list-group-item-action text-danger mt-5">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </div>

    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4 sticky-top">
            <div class="container-fluid">
                <span class="navbar-text">Halaman: <strong><?= ucfirst($page) ?></strong></span>
                <span class="navbar-text ms-auto">Halo, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong></span>
            </div>
        </nav>

        <div class="container-fluid px-4 mt-4 mb-5">
            <?php 
            $viewDir = 'views/';
            $file = $viewDir . $page . '.php';
            
            if (file_exists($file)) {
                include $file;
            } else {
                echo '<div class="alert alert-danger">File View tidak ditemukan: '.$file.'</div>';
            }
            ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmAction(message, icon = 'warning') {
        return Swal.fire({
            title: 'Yakin?',
            text: message,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        });
    }

    function showToast(message, icon = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        Toast.fire({ icon: icon, title: message });
    }
</script>

</body>
</html>