<?php
/**
 * Dashboard Mahasiswa
 * Dikerjakan oleh: Anggota 2
 * Updated oleh: Anggota 4 (Widget Pengumuman)
 * 
 * Dashboard mahasiswa menampilkan statistik akademik dan akses ke materi, tugas, dan nilai
 */

session_start();

// Check if logged in and is mahasiswa
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

// Query statistik tambahan
if ($pdo) {
    try {
        // Total mata kuliah diambil
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM enrollment WHERE mahasiswa_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $total_mata_kuliah = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total materi
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM materi");
        $total_materi_downloaded = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Total tugas
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM tugas");
        $total_tugas = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Dummy jika kosong
        if ($total_tugas == 0) $total_tugas = 5;
        if ($total_materi_downloaded == 0) $total_materi_downloaded = 10;

        // Tugas dikumpulkan
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM submission WHERE mahasiswa_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $tugas_dikumpulkan = (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Rata-rata nilai
        $stmt = $pdo->prepare("SELECT AVG(nilai) as rata FROM submission WHERE mahasiswa_id = ? AND nilai IS NOT NULL");
        $stmt->execute([$_SESSION['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $rata_nilai = $result['rata'] ? round($result['rata'], 2) : 0;

    } catch (PDOException $e) {
        // Dummy data
        $total_mata_kuliah = 3;
        $total_materi_downloaded = 10;
        $total_tugas = 5;
        $tugas_dikumpulkan = 2;
        $rata_nilai = 85;
    }
} else {
    // Dummy data jika koneksi gagal
    $total_mata_kuliah = 3;
    $total_materi_downloaded = 10;
    $total_tugas = 5;
    $tugas_dikumpulkan = 2;
    $rata_nilai = 85;
}

$page_title = "Dashboard Mahasiswa";
include '../components/header.php';
include '../components/navbar.php';

// Dummy lainnya
$ips = 3.45;
$kehadiran = 88;

// Fix division zero
$progress_tugas = ($total_tugas > 0) ? round(($tugas_dikumpulkan / $total_tugas) * 100) : 0;
$belum_tugas = max($total_tugas - $tugas_dikumpulkan, 0);

$last_updated = date('d M Y, H:i') . ' WIB';
?>

<!-- Dashboard Header -->
<div class="dashboard-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 2rem 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1"><i class="fas fa-graduation-cap me-2"></i>Dashboard Mahasiswa</h2>
                <p class="mb-0 text-light">EduPortal - Portal Akademik Mahasiswa</p>
            </div>
            <div class="col-md-4 text-end">
                <p class="mb-1 small"><i class="fas fa-clock me-1"></i>Terakhir diperbarui: <?= $last_updated ?></p>
                <button class="btn btn-light btn-sm"><i class="fas fa-sync-alt me-1"></i>Update Data</button>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container mt-4 mb-5">

    <!-- Statistik Cards -->
    <div class="row g-4 mb-4">

        <!-- IPS -->
        <div class="col-lg-3">
            <div class="card shadow-sm h-100 border-primary">
                <div class="card-body text-center">
                    <div class="mb-3"><i class="fas fa-star fa-3x text-primary"></i></div>
                    <h2 class="text-primary mb-1"><?= $ips ?></h2>
                    <p class="text-muted mb-3">Indeks Prestasi Semester</p>
                    <small class="badge bg-primary">Sangat Memuaskan</small>
                </div>
            </div>
        </div>

        <!-- Mata Kuliah -->
        <div class="col-lg-3">
            <div class="card shadow-sm h-100 border-info">
                <div class="card-body text-center">
                    <div class="mb-3"><i class="fas fa-book fa-3x text-info"></i></div>
                    <h2 class="text-info mb-1"><?= $total_mata_kuliah ?></h2>
                    <p class="text-muted mb-3">Mata Kuliah Diambil</p>
                    <a class="btn btn-outline-info btn-sm w-100" href="../mahasiswa/materi.php">Materi</a>
                </div>
            </div>
        </div>

        <!-- Tugas -->
        <div class="col-lg-3">
            <div class="card shadow-sm h-100 border-success">
                <div class="card-body text-center">
                    <div class="mb-3"><i class="fas fa-tasks fa-3x text-success"></i></div>
                    <h2 class="text-success mb-1"><?= $tugas_dikumpulkan ?>/<?= $total_tugas ?></h2>
                    <p class="text-muted mb-3">Tugas Dikumpulkan</p>
                    <a class="btn btn-outline-success btn-sm w-100" href="../mahasiswa/tugas.php">Lihat Tugas</a>
                </div>
            </div>
        </div>

        <!-- Nilai -->
        <div class="col-lg-3">
            <div class="card shadow-sm h-100 border-warning">
                <div class="card-body text-center">
                    <div class="mb-3"><i class="fas fa-chart-line fa-3x text-warning"></i></div>
                    <h2 class="text-warning mb-1"><?= $rata_nilai ?></h2>
                    <p class="text-muted mb-3">Rata-rata Nilai</p>
                    <a class="btn btn-outline-warning btn-sm w-100" href="../mahasiswa/nilai.php">Lihat Nilai</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Classes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white"><h5><i class="fas fa-plus-circle me-2 text-success"></i>Bergabung ke Kelas Tersedia</h5></div>
                <div class="card-body">
                    <div id="availableClassesContainer" class="row g-3">
                        <div class="col-12 text-center"><p class="text-muted">Loading...</p></div>
                    </div>
                    <a href="../mahasiswa/matakuliah.php" class="btn btn-outline-primary btn-sm mt-3">
                        <i class="fas fa-eye me-1"></i> Lihat Semua Mata Kuliah
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cards Row 2 -->
    <div class="row g-4 mb-4">
        <!-- Card Akademik -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>Performa Akademik
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="nilaiChart" style="max-height: 200px;"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>IPK:</strong>
                                        <strong class="text-primary"><?php echo $ips; ?></strong>
                                    </div>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-2"><strong>Nilai per Mata Kuliah</strong></small>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Pemrograman Web</span>
                                            <span class="badge bg-success">A</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 95%;"></div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Basis Data</span>
                                            <span class="badge bg-success">A</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 92%;"></div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Algoritma</span>
                                            <span class="badge bg-info">B+</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 88%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="../mahasiswa/nilai.php" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-eye me-1"></i>Lihat Detail Nilai
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Kehadiran dan Progress -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2 text-success"></i>Kehadiran & Progress Tugas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <canvas id="kehadiranChart" style="max-width: 150px; max-height: 150px; margin: 0 auto;"></canvas>
                            </div>
                            <h4 class="text-success"><?php echo $kehadiran; ?>%</h4>
                            <p class="text-muted mb-0">Kehadiran Semester Ini</p>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <canvas id="tugasChart" style="max-width: 150px; max-height: 150px; margin: 0 auto;"></canvas>
                            </div>
                            <h4 class="text-info"><?php echo round(($tugas_dikumpulkan / $total_tugas) * 100); ?>%</h4>
                            <p class="text-muted mb-0">Progress Tugas</p>
                        </div>
                    </div>
                    <hr>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        <small><strong>Tips:</strong> Tingkatkan kehadiran dan kumpulkan semua tugas tepat waktu untuk mendapatkan nilai terbaik.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tugas Mendatang -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-hourglass-end me-2 text-warning"></i>Tugas Mendatang
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mata Kuliah</th>
                                    <th>Judul Tugas</th>
                                    <th>Dosen</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Pemrograman Web</strong></td>
                                    <td>Project Akhir Semester</td>
                                    <td>Dr. Budi Santoso, S.Kom</td>
                                    <td>15 Jan 2025</td>
                                    <td><span class="badge bg-warning">Belum Dikumpulkan</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">
                                            <i class="fas fa-upload me-1"></i>Submit
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Basis Data</strong></td>
                                    <td>Desain Database UML</td>
                                    <td>Hendra Wijaya, S.Kom, M.T.</td>
                                    <td>18 Jan 2025</td>
                                    <td><span class="badge bg-warning">Belum Dikumpulkan</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">
                                            <i class="fas fa-upload me-1"></i>Submit
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Algoritma</strong></td>
                                    <td>Analisis Kompleksitas</td>
                                    <td>Prof. Ir. Bambang Riyanto</td>
                                    <td>10 Jan 2025</td>
                                    <td><span class="badge bg-success">Sudah Dikumpulkan</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="fas fa-check me-1"></i>Selesai
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pengumuman Terbaru -->
    <div class="row g-4 mb-4">
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

<script>
// Kelas load
function bergabungKelas(id, kode) {
    alert("Dummy: Bergabung ke kelas " + kode);
}

// Load kelas dummy
$(document).ready(function() {
    setTimeout(() => {
        $("#availableClassesContainer").html(`
            <div class="col-md-4">
                <div class="card border-info h-100">
                    <div class="card-body">
                        <h6>KLS101</h6>
                        <p class="small text-muted">Pemrograman Web</p>
                        <button class="btn btn-sm btn-success w-100" onclick="bergabungKelas(1,'KLS101')">Bergabung</button>
                    </div>
                </div>
            </div>
        `);
    }, 600);
});
</script>

<?php include '../components/footer.php'; ?>