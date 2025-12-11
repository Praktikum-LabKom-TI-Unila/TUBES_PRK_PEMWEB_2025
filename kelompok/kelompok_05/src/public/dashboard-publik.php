<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Dashboard Publik LampungSmart - Pantau aktivitas pengaduan secara real-time dan bantu prioritaskan pengaduan mendesak">
    <title>Dashboard Publik - LampungSmart</title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- LampungSmart Theme -->
    <link href="../assets/css/lampung-theme.css" rel="stylesheet">
    <link href="../assets/css/landing-page.css" rel="stylesheet">
    <link href="../assets/css/logo-navbar.css" rel="stylesheet">
    <link href="../assets/css/dashboard-voting.css" rel="stylesheet">
    <link href="../assets/css/dashboard-publik-custom.css" rel="stylesheet">
</head>
<body <?php echo (isset($_SESSION['login']) && $_SESSION['login'] === true) ? 'data-logged-in="true"' : ''; ?>>

    <?php include '../layouts/navbar-landing.php'; ?>

    <!-- Hero Section -->
    <section class="hero-lampung" style="padding: 80px 0;">
        <div class="container">
            <div class="hero-content text-center">
                <div class="mb-4">
                    <span class="badge bg-lampung-gold text-dark px-4 py-2 fs-6">
                        <i class="bi bi-broadcast"></i> Dashboard Real-Time
                    </span>
                </div>
                <h1 class="hero-title" style="font-size: 2.5rem;">Dashboard Publik LampungSmart</h1>
                <p class="hero-subtitle">
                    Pantau aktivitas pengaduan secara transparan dan bantu prioritaskan penanganan yang paling mendesak
                </p>
            </div>
        </div>
    </section>

    <!-- Public Dashboard Section -->
    <section class="py-5 bg-white">
        <div class="container">
            <div class="row mb-4">
                <div class="col-12 text-center">
                    <h2 class="section-title">Metrik Real-Time</h2>
                    <p class="section-subtitle">
                        Pantau aktivitas platform secara transparan berdasarkan data aktual
                        <span class="badge bg-success text-white ms-2">
                            <i class="bi bi-database-check"></i> Data Real Database
                        </span>
                    </p>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Live Processing Counter -->
                <div class="col-md-6">
                    <div class="card shadow-lampung-md border-0 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title text-lampung-blue-dark mb-1">
                                        <i class="bi bi-hourglass-split"></i> Sedang Diproses
                                    </h5>
                                    <p class="text-muted small mb-0">Pengaduan dalam antrian</p>
                                </div>
                                <span class="badge bg-lampung-blue-light text-lampung-blue">
                                    <i class="bi bi-arrow-clockwise"></i> Live
                                </span>
                            </div>
                            <div class="text-center py-4">
                                <div id="live-counter" class="display-2 fw-bold text-lampung-blue mb-2" 
                                     aria-live="polite" aria-atomic="true">47</div>
                                <div class="text-muted">pengaduan aktif</div>
                            </div>
                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="bi bi-clock-history"></i> Update setiap 5 detik
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Total UMKM Aktif -->
                <div class="col-md-6">
                    <div class="card shadow-lampung-md border-0 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title text-lampung-green-dark mb-1">
                                        <i class="bi bi-shop-window"></i> Total Pendaftaran UMKM Aktif
                                    </h5>
                                    <p class="text-muted small mb-0">Status: Approved</p>
                                </div>
                                <span class="badge bg-lampung-green-light text-lampung-green">
                                    <i class="bi bi-arrow-clockwise"></i> Live
                                </span>
                            </div>
                            <div class="text-center py-4">
                                <div id="umkm-counter" class="display-2 fw-bold text-lampung-green mb-2" 
                                     aria-live="polite" aria-atomic="true">0</div>
                                <div class="text-muted">UMKM terdaftar</div>
                            </div>
                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="bi bi-clock-history"></i> Update setiap 5 detik
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Priority Voting Widget -->
    <section class="voting-section py-5">
        <div class="container">
            <div class="row mb-4">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="section-title">Pengaduan Teratas Berdasarkan Vote Warga</h2>
                    <p class="section-subtitle">
                        Ini adalah pengaduan yang paling banyak di-vote oleh warga. 
                        <?php if (isset($_SESSION['login']) && $_SESSION['login'] === true): ?>
                            Vote pengaduan yang menurut Anda paling mendesak untuk membantu prioritas penanganan.
                        <?php else: ?>
                            <a href="../auth/login.php" class="text-lampung-blue fw-bold">Login</a> untuk ikut vote dan prioritaskan pengaduan mendesak.
                        <?php endif; ?>
                    </p>
                    <div class="mt-3">
                        <span class="badge bg-lampung-blue-light text-lampung-blue px-3 py-2">
                            <i class="bi bi-trophy"></i> Transparansi Prioritas Publik
                        </span>
                    </div>
                </div>
            </div>
            
            <?php
            // Load pengaduan dari database (bukan mock)
            require '../config/config.php';
            
            // Query pengaduan dengan join upvotes
            // HANYA TAMPILKAN PENGADUAN YANG PUNYA VOTES (transparansi prioritas)
            $query = "
                SELECT 
                    p.id,
                    p.judul,
                    p.deskripsi,
                    p.lokasi,
                    p.status,
                    p.created_at,
                    COUNT(DISTINCT uv.id) as total_upvotes,
                    CASE 
                        WHEN EXISTS (
                            SELECT 1 FROM pengaduan_upvotes 
                            WHERE pengaduan_id = p.id 
                            AND user_id = " . (isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0) . "
                        ) THEN 1 
                        ELSE 0 
                    END as user_upvoted
                FROM pengaduan p
                LEFT JOIN pengaduan_upvotes uv ON p.id = uv.pengaduan_id
                WHERE p.status IN ('pending', 'proses')
                GROUP BY p.id
                HAVING total_upvotes > 0
                ORDER BY total_upvotes DESC, p.created_at DESC
                LIMIT 6
            ";
            
            $result = mysqli_query($conn, $query);
            $realComplaints = [];
            
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    // Determine icon and color based on keywords
                    $icon = 'exclamation-triangle';
                    $color = 'blue';
                    
                    if (stripos($row['judul'], 'jalan') !== false || stripos($row['judul'], 'lubang') !== false) {
                        $icon = 'cone-striped';
                        $color = 'red';
                    } elseif (stripos($row['judul'], 'lampu') !== false || stripos($row['judul'], 'listrik') !== false) {
                        $icon = 'lightbulb';
                        $color = 'gold';
                    } elseif (stripos($row['judul'], 'sampah') !== false || stripos($row['judul'], 'kebersihan') !== false) {
                        $icon = 'trash';
                        $color = 'green';
                    }
                    
                    // Calculate urgency based on upvotes and age
                    $days_old = (time() - strtotime($row['created_at'])) / (60 * 60 * 24);
                    $urgency = min(100, ($row['total_upvotes'] * 5) + (max(0, 10 - $days_old) * 3));
                    
                    $realComplaints[] = [
                        'id' => $row['id'],
                        'title' => htmlspecialchars($row['judul']),
                        'description' => htmlspecialchars(substr($row['deskripsi'], 0, 100) . '...'),
                        'urgency' => round($urgency),
                        'votes' => intval($row['total_upvotes']),
                        'location' => htmlspecialchars($row['lokasi']),
                        'icon' => $icon,
                        'color' => $color,
                        'user_upvoted' => $row['user_upvoted'] == 1
                    ];
                }
            }
            
            // Jika tidak ada data, tampilkan pesan
            if (empty($realComplaints)) {
                echo '<div class="col-12"><div class="alert alert-info text-center">';
                echo '<i class="bi bi-info-circle"></i> Belum ada pengaduan dengan vote dari warga saat ini.';
                echo '<br><small class="text-muted">Vote akan muncul di sini setelah warga memberikan vote pada pengaduan.</small>';
                if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
                    echo '<br><a href="../auth/login.php" class="btn btn-primary mt-3">Login untuk Vote & Buat Pengaduan</a>';
                }
                echo '</div></div>';
            }
            ?>
            
            <div class="row g-4" id="pengaduan-list">
                <?php foreach ($realComplaints as $index => $complaint): ?>
                <div class="col-lg-4">
                    <div class="complaint-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="complaint-icon bg-lampung-<?php echo $complaint['color']; ?>-light">
                                <i class="bi bi-<?php echo $complaint['icon']; ?> text-lampung-<?php echo $complaint['color']; ?>"></i>
                            </div>
                            <span class="badge bg-lampung-gold-light text-lampung-gold">
                                Urgensi: <?php echo $complaint['urgency']; ?>%
                            </span>
                        </div>
                        
                        <h5 class="complaint-title text-lampung-blue-dark mb-2">
                            <?php echo $complaint['title']; ?>
                        </h5>
                        
                        <p class="complaint-description text-muted small mb-3">
                            <?php echo $complaint['description']; ?>
                        </p>
                        
                        <div class="complaint-location text-muted small mb-3">
                            <i class="bi bi-geo-alt"></i> <?php echo $complaint['location']; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <button class="vote-btn btn <?php echo $complaint['user_upvoted'] ? 'btn-success' : 'btn-primary'; ?> btn-sm" 
                                    data-pengaduan-id="<?php echo $complaint['id']; ?>"
                                    data-upvoted="<?php echo $complaint['user_upvoted'] ? '1' : '0'; ?>"
                                    <?php if (!isset($_SESSION['login']) || $_SESSION['login'] !== true): ?>
                                    title="Login untuk vote pengaduan ini"
                                    data-bs-toggle="tooltip"
                                    <?php endif; ?>
                                    aria-label="Vote untuk pengaduan ini">
                                <i class="bi bi-hand-thumbs-up<?php echo $complaint['user_upvoted'] ? '-fill' : ''; ?>"></i> 
                                <?php echo $complaint['user_upvoted'] ? 'Voted' : 'Vote'; ?>
                            </button>
                            <span class="vote-count-badge badge bg-lampung-gold text-dark" 
                                  id="vote-count-<?php echo $complaint['id']; ?>">
                                <i class="bi bi-people-fill"></i> <span class="vote-number"><?php echo $complaint['votes']; ?></span>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (!empty($realComplaints)): ?>
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <div class="alert alert-lampung-info border-left-lampung-blue">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Data Real-Time:</strong> Pengaduan diurutkan berdasarkan jumlah vote dan tingkat urgensi.
                        <?php if (!isset($_SESSION['login']) || !$_SESSION['login']): ?>
                        <br><a href="../auth/login.php" class="btn btn-sm btn-primary mt-2">Login untuk Vote</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- ARIA Live Region for Accessibility -->
    <div id="vote-announcer" class="visually-hidden" role="status" aria-live="polite" aria-atomic="true"></div>

        <!-- CTA Section -->
    <section class="py-5 bg-lampung-gradient-primary text-white">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-5 fw-bold mb-4">Ingin Laporkan Pengaduan Sendiri?</h2>
                    <p class="lead mb-4">
                        Daftar sekarang dan mulai laporkan masalah infrastruktur di sekitar Anda
                    </p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="../auth/register.php" class="btn btn-warning btn-lg shadow-lampung-lg">
                            <i class="bi bi-person-plus-fill"></i> Daftar Sekarang
                        </a>
                        <a href="../auth/login.php" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-box-arrow-in-right"></i> Sudah Punya Akun? Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../layouts/footer-landing.php'; ?>

    <!-- Rate Limit Modal -->
    <div class="modal fade" id="rateLimitModal" tabindex="-1" aria-labelledby="rateLimitModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-lampung-red text-white">
                    <h5 class="modal-title" id="rateLimitModalLabel">
                        <i class="bi bi-exclamation-triangle"></i> Batas Voting Tercapai
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        <strong>Anda telah menggunakan 3 vote yang tersedia.</strong>
                    </p>
                    <p class="mb-0 text-muted">
                        Untuk mencegah penyalahgunaan sistem voting, setiap pengunjung dibatasi maksimal 3 vote per sesi.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Initialize Bootstrap Tooltips -->
    <script>
        // Enable Bootstrap tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    
    <!-- Dashboard Live Updates -->
    <script src="../assets/js/dashboard-live.js"></script>
    
    <!-- Upvote Handler (Real Data) -->
    <script src="../assets/js/upvote-handler.js"></script>
    
    <!-- Voting Widget -->
    <script src="../assets/js/voting-widget.js"></script>

</body>
</html>
