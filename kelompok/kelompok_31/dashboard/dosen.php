<?php
/**
 * Dashboard Dosen
 * Dikerjakan oleh: Anggota 2
 * Updated oleh: Anggota 4 (Widget Pengumuman)
 */

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'dosen') {
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

// Get mata kuliah yang diampu
$mata_kuliah_list = [];
if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id, kode, nama FROM mata_kuliah WHERE dosen_id = ? ORDER BY kode ASC");
        $stmt->execute([$_SESSION['user_id']]);
        $mata_kuliah_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error loading mata kuliah: " . $e->getMessage());
    }
}

$page_title = "Dashboard Dosen";
include '../components/header.php';
include '../components/navbar.php';
?>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 2rem 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1"><i class="fas fa-chalkboard-teacher me-2"></i>Dashboard Dosen</h2>
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
    <div class="row g-4">
        <!-- Mata Kuliah yang Diampu -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2 text-primary"></i>Mata Kuliah yang Diampu
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($mata_kuliah_list)): ?>
                        <p class="text-muted mb-0">Belum ada mata kuliah yang diampu</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($mata_kuliah_list as $mk): ?>
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($mk['kode']); ?></h6>
                                            <p class="mb-0 small text-muted"><?php echo htmlspecialchars($mk['nama']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-3">
                            <a href="dosen/upload_materi.php" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-upload me-1"></i>Upload Materi
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2 text-success"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="dosen/upload_materi.php" class="btn btn-outline-primary">
                            <i class="fas fa-upload me-2"></i>Upload Materi
                        </a>
                        <a href="dosen/buat_tugas.php" class="btn btn-outline-info">
                            <i class="fas fa-tasks me-2"></i>Buat Tugas
                        </a>
                        <a href="dosen/input_nilai.php" class="btn btn-outline-success">
                            <i class="fas fa-edit me-2"></i>Input Nilai
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="col-lg-4">
            <div class="card shadow-sm h-100 border-primary">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-graduation-cap fa-3x text-primary"></i>
                    </div>
                    <h2 class="text-primary mb-1"><?php echo count($mata_kuliah_list); ?></h2>
                    <p class="text-muted mb-3">Mata Kuliah</p>
                    <p class="small text-muted">Total mata kuliah yang Anda ampu</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pengumuman Terbaru -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
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
