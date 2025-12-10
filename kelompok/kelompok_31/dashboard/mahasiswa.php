<?php
/**
 * Dashboard Mahasiswa
 * Dikerjakan oleh: Anggota 2
 * Updated oleh: Anggota 4 (Widget Pengumuman)
 */

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// Koneksi database untuk pengumuman
require_once '../config/database.php';
$database = new Database();
$pdo = $database->getConnection();

// Get pengumuman terbaru
$pengumuman_list = [];
if ($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT p.*, u.nama as author_name 
            FROM pengumuman p
            JOIN users u ON p.created_by = u.id
            ORDER BY p.created_at DESC
            LIMIT 5
        ");
        $stmt->execute();
        $pengumuman_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error loading pengumuman: " . $e->getMessage());
    }
}

// Get statistik tugas dan nilai
$statistik = [
    'total_tugas' => 0,
    'tugas_dinilai' => 0,
    'tugas_belum_dinilai' => 0,
    'rata_rata_nilai' => 0
];

if ($pdo) {
    try {
        // Total tugas yang sudah di-submit
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total,
                   SUM(CASE WHEN nilai IS NOT NULL THEN 1 ELSE 0 END) as dinilai,
                   SUM(CASE WHEN nilai IS NULL THEN 1 ELSE 0 END) as belum_dinilai,
                   AVG(nilai) as rata_rata
            FROM submission
            WHERE mahasiswa_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $stat = $stmt->fetch();
        
        if ($stat) {
            $statistik['total_tugas'] = intval($stat['total']);
            $statistik['tugas_dinilai'] = intval($stat['dinilai']);
            $statistik['tugas_belum_dinilai'] = intval($stat['belum_dinilai']);
            $statistik['rata_rata_nilai'] = $stat['rata_rata'] ? number_format($stat['rata_rata'], 2) : '0.00';
        }
    } catch (PDOException $e) {
        error_log("Error loading statistik: " . $e->getMessage());
    }
}

$page_title = "Dashboard Mahasiswa";
include '../components/header.php';
include '../components/navbar.php';
?>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 2rem 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1"><i class="fas fa-user-graduate me-2"></i>Dashboard Mahasiswa</h2>
                <p class="mb-0 text-light">Selamat datang, <?php echo htmlspecialchars($_SESSION['nama'] ?? $_SESSION['username']); ?>!</p>
            </div>
            <div class="col-md-4 text-end">
                <p class="mb-1 small">
                    <i class="fas fa-calendar-alt me-1"></i> <?php echo date('d M Y'); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4 mb-5">
    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm text-center border-primary">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-tasks fa-3x text-primary"></i>
                    </div>
                    <h3 class="text-primary mb-1"><?php echo $statistik['total_tugas']; ?></h3>
                    <p class="text-muted mb-0">Total Tugas</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center border-success">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-check-circle fa-3x text-success"></i>
                    </div>
                    <h3 class="text-success mb-1"><?php echo $statistik['tugas_dinilai']; ?></h3>
                    <p class="text-muted mb-0">Sudah Dinilai</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center border-warning">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-clock fa-3x text-warning"></i>
                    </div>
                    <h3 class="text-warning mb-1"><?php echo $statistik['tugas_belum_dinilai']; ?></h3>
                    <p class="text-muted mb-0">Belum Dinilai</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm text-center border-info">
                <div class="card-body">
                    <div class="mb-3">
                        <i class="fas fa-chart-line fa-3x text-info"></i>
                    </div>
                    <h3 class="text-info mb-1"><?php echo $statistik['rata_rata_nilai']; ?></h3>
                    <p class="text-muted mb-0">Rata-rata Nilai</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2 text-primary"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="mahasiswa/materi.php" class="btn btn-outline-primary">
                            <i class="fas fa-book me-2"></i>Lihat Materi
                        </a>
                        <a href="mahasiswa/tugas.php" class="btn btn-outline-info">
                            <i class="fas fa-tasks me-2"></i>Lihat Tugas
                        </a>
                        <a href="mahasiswa/nilai.php" class="btn btn-outline-success">
                            <i class="fas fa-chart-line me-2"></i>Lihat Nilai
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pengumuman Terbaru -->
        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bullhorn me-2"></i>Pengumuman Terbaru
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($pengumuman_list)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Belum ada pengumuman</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($pengumuman_list as $pengumuman): 
                                $created_date = date('d M Y, H:i', strtotime($pengumuman['created_at']));
                                $excerpt = strlen($pengumuman['isi']) > 150 ? substr($pengumuman['isi'], 0, 150) . '...' : $pengumuman['isi'];
                            ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($pengumuman['judul']); ?></h6>
                                            <p class="mb-2 text-muted small"><?php echo htmlspecialchars($excerpt); ?></p>
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($pengumuman['author_name']); ?>
                                                <span class="ms-2">
                                                    <i class="fas fa-calendar me-1"></i><?php echo $created_date; ?>
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../components/footer.php'; ?>
