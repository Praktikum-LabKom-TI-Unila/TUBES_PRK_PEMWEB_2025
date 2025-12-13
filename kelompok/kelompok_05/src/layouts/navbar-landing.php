<!-- Navbar Landing Page -->
<nav class="navbar navbar-expand-lg navbar-lampung sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="../assets/images/logo-lampung.png" alt="Logo Lampung" class="logo-lampung-navbar"> LampungSmart
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard-publik.php') ? 'active' : ''; ?>" href="dashboard-publik.php">Dashboard Publik</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'faq.php') ? 'active' : ''; ?>" href="faq.php">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'hubungi-kami.php') ? 'active' : ''; ?>" href="hubungi-kami.php">Hubungi Kami</a>
                </li>
                
                <?php if(isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
                    <!-- User Sudah Login - Tampilkan Dashboard & Profile -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo ($_SESSION['role'] == 'admin') ? '../admin/dashboard/index.php' : '../dashboard/dashboard_warga.php'; ?>">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['nama']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="../profile/profile.php">
                                    <i class="bi bi-person"></i> Lihat Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="../auth/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Keluar
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- User Belum Login - Tampilkan Login & Register -->
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-warning text-dark px-3 ms-2 rounded" href="../auth/register.php">
                            <i class="bi bi-person-plus"></i> Daftar
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<script>
// Perbaiki state active navbar yang tidak hilang saat scroll
document.addEventListener('DOMContentLoaded', function() {
    // Pastikan hanya halaman saat ini yang active
    const currentPage = '<?php echo basename($_SERVER['PHP_SELF']); ?>';
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href && href.includes(currentPage)) {
            link.classList.add('active');
        }
    });
    
    // Efek shadow navbar saat scroll
    const navbar = document.querySelector('.navbar-lampung');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            navbar.classList.add('shadow-lampung-lg');
        } else {
            navbar.classList.remove('shadow-lampung-lg');
        }
    });
});
</script>
