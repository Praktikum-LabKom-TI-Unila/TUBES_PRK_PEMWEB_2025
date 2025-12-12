<?php
/**
 * Dashboard Dosen
 * Dikerjakan oleh: Anggota 2
 * Updated oleh: Anggota 4 (Widget Pengumuman)
 * 
 * Dashboard dosen menampilkan statistik dan manajemen materi, tugas, dan nilai
 */

session_start();

// Check if logged in and is dosen
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

// Query statistik dari database
try {
    $dosen_id = $_SESSION['user_id'];
    
    // Total mata kuliah diampu oleh dosen ini
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM mata_kuliah WHERE dosen_id = ?");
    $stmt->execute([$dosen_id]);
    $total_mata_kuliah = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total mahasiswa (semua mahasiswa di sistem)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'mahasiswa'");
    $total_mahasiswa = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total materi yang diupload
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM materi WHERE uploaded_by = ?");
    $stmt->execute([$dosen_id]);
    $total_materi_uploaded = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total tugas yang dibuat
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM tugas WHERE created_by = ?");
    $stmt->execute([$dosen_id]);
    $total_tugas_dibuat = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Rata-rata nilai untuk tugas yang dibuat dosen ini
    $stmt = $pdo->prepare("
        SELECT AVG(s.nilai) as rata 
        FROM submission s
        JOIN tugas t ON s.tugas_id = t.id
        WHERE t.created_by = ? AND s.nilai IS NOT NULL
    ");
    $stmt->execute([$dosen_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $rata_nilai = $result['rata'] ? round($result['rata'], 2) : 0;
    
    // Distribusi nilai (A, B, C)
    $stmt = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN s.nilai >= 80 THEN 1 ELSE 0 END) as nilai_a,
            SUM(CASE WHEN s.nilai >= 70 AND s.nilai < 80 THEN 1 ELSE 0 END) as nilai_b,
            SUM(CASE WHEN s.nilai >= 60 AND s.nilai < 70 THEN 1 ELSE 0 END) as nilai_c
        FROM submission s
        JOIN tugas t ON s.tugas_id = t.id
        WHERE t.created_by = ? AND s.nilai IS NOT NULL
    ");
    $stmt->execute([$dosen_id]);
    $distribusi = $stmt->fetch(PDO::FETCH_ASSOC);
    $nilai_a = intval($distribusi['nilai_a'] ?? 0);
    $nilai_b = intval($distribusi['nilai_b'] ?? 0);
    $nilai_c = intval($distribusi['nilai_c'] ?? 0);
    $total_nilai = $nilai_a + $nilai_b + $nilai_c;
    
    // Total submission yang belum dinilai
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM submission s
        JOIN tugas t ON s.tugas_id = t.id
        WHERE t.created_by = ? AND s.nilai IS NULL
    ");
    $stmt->execute([$dosen_id]);
    $submission_belum_dinilai = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total submission yang sudah dinilai
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total 
        FROM submission s
        JOIN tugas t ON s.tugas_id = t.id
        WHERE t.created_by = ? AND s.nilai IS NOT NULL
    ");
    $stmt->execute([$dosen_id]);
    $submission_sudah_dinilai = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Hitung submission rate
    $total_submission = $submission_belum_dinilai + $submission_sudah_dinilai;
    $submission_rate = $total_submission > 0 ? round(($submission_sudah_dinilai / $total_submission) * 100) : 0;
    
} catch (PDOException $e) {
    error_log("Error loading statistik dosen: " . $e->getMessage());
    $total_mata_kuliah = 0;
    $total_mahasiswa = 0;
    $total_materi_uploaded = 0;
    $total_tugas_dibuat = 0;
    $rata_nilai = 0;
    $nilai_a = 0;
    $nilai_b = 0;
    $nilai_c = 0;
    $total_nilai = 0;
    $submission_rate = 0;
}

// Data untuk lainnya
$kehadiran_rata = 85; // Dummy data, bisa dikembangkan dengan tabel kehadiran
$last_updated = date('d M Y, H:i') . ' WIB';
?>

<!-- Dashboard Header -->
<div class="dashboard-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 2rem 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1"><i class="fas fa-chalkboard-teacher me-2"></i>Dashboard Dosen</h2>
                <p class="mb-0 text-light">EduPortal - Kelola Mata Kuliah dan Penilaian Mahasiswa</p>
            </div>
            <div class="col-md-4 text-end">
                <p class="mb-1 small">
                    <i class="fas fa-clock me-1"></i>Terakhir diperbarui: <?php echo $last_updated; ?>
                </p>
                <button class="btn btn-light btn-sm" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-1"></i>Update Data
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container mt-4 mb-5">
    <!-- Statistik Cards Row 1 -->
    <div class="row g-4 mb-4">
        <!-- Card Mata Kuliah -->
        <div class="col-lg-3">
            <div class="card shadow-sm h-100 border-primary">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-book fa-3x text-primary"></i>
                    </div>
                    <h2 class="text-primary mb-1"><?php echo $total_mata_kuliah; ?></h2>
                    <p class="text-muted mb-3">Mata Kuliah Diampu</p>
                    <a href="../admin/mata_kuliah.php?dosen_id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-outline-primary btn-sm w-100">
                        <i class="fas fa-list me-1"></i>Lihat Semua
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Mahasiswa -->
        <div class="col-lg-3">
            <div class="card shadow-sm h-100 border-info">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-users fa-3x text-info"></i>
                    </div>
                    <h2 class="text-info mb-1"><?php echo $total_mahasiswa; ?></h2>
                    <p class="text-muted mb-3">Total Mahasiswa</p>
                    <a href="../admin/users.php?role=mahasiswa" class="btn btn-outline-info btn-sm w-100">
                        <i class="fas fa-eye me-1"></i>Lihat Detail
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Materi -->
        <div class="col-lg-3">
            <div class="card shadow-sm h-100 border-success">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-file-pdf fa-3x text-success"></i>
                    </div>
                    <h2 class="text-success mb-1"><?php echo $total_materi_uploaded; ?></h2>
                    <p class="text-muted mb-3">Materi Diupload</p>
                    <a href="../dosen/upload_materi.php" class="btn btn-outline-success btn-sm w-100">
                        <i class="fas fa-upload me-1"></i>Upload Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- Card Tugas -->
        <div class="col-lg-3">
            <div class="card shadow-sm h-100 border-warning">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-tasks fa-3x text-warning"></i>
                    </div>
                    <h2 class="text-warning mb-1"><?php echo $total_tugas_dibuat; ?></h2>
                    <p class="text-muted mb-3">Tugas Dibuat</p>
                    <a href="../dosen/buat_tugas.php" class="btn btn-outline-warning btn-sm w-100">
                        <i class="fas fa-plus me-1"></i>Buat Tugas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cards Row 2 -->
    <div class="row g-4 mb-4">
        <!-- Card Rata-rata Nilai -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>Rata-rata Nilai Mahasiswa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="nilaiChart" style="max-height: 200px;"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-3">
                                    <span><strong>Nilai Rata-rata:</strong></span>
                                    <strong class="text-primary"><?php echo $rata_nilai; ?></strong>
                                </div>
                                <hr>
                                <div class="mb-3">
                                    <small class="text-muted d-block mb-2">Distribusi Nilai</small>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span><i class="fas fa-circle text-success me-2"></i>A (80-100)</span>
                                            <span><?php echo $nilai_a; ?> siswa</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $total_nilai > 0 ? round(($nilai_a / $total_nilai) * 100) : 0; ?>%;"></div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span><i class="fas fa-circle text-info me-2"></i>B (70-79)</span>
                                            <span><?php echo $nilai_b; ?> siswa</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $total_nilai > 0 ? round(($nilai_b / $total_nilai) * 100) : 0; ?>%;"></div>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span><i class="fas fa-circle text-warning me-2"></i>C (60-69)</span>
                                            <span><?php echo $nilai_c; ?> siswa</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $total_nilai > 0 ? round(($nilai_c / $total_nilai) * 100) : 0; ?>%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="../dosen/input_nilai.php" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-edit me-1"></i>Input Nilai
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Kehadiran dan Submission -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2 text-success"></i>Kehadiran & Pengumpulan Tugas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <canvas id="kehadiranChart" style="max-width: 150px; max-height: 150px; margin: 0 auto;"></canvas>
                            </div>
                            <h4 class="text-success"><?php echo $kehadiran_rata; ?>%</h4>
                            <p class="text-muted mb-0">Kehadiran Rata-rata</p>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <canvas id="submissionChart" style="max-width: 150px; max-height: 150px; margin: 0 auto;"></canvas>
                            </div>
                            <h4 class="text-info"><?php echo $submission_rate; ?>%</h4>
                            <p class="text-muted mb-0">Rate Pengumpulan Tugas</p>
                        </div>
                    </div>
                    <hr>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <small><strong>Tips:</strong> Monitor kehadiran dan pengumpulan tugas mahasiswa secara berkala untuk memastikan proses pembelajaran berjalan optimal.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2 text-primary"></i>Aktivitas Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    <?php
                    // Get aktivitas terbaru dari database
                    $aktivitas_list = [];
                    if ($pdo) {
                        try {
                            $dosen_id = $_SESSION['user_id'];
                            
                            // Get materi terbaru
                            $stmt = $pdo->prepare("
                                SELECT 'materi' as type, m.judul as title, m.created_at, mk.nama as mata_kuliah
                                FROM materi m
                                JOIN mata_kuliah mk ON m.mata_kuliah_id = mk.id
                                WHERE m.uploaded_by = ?
                                ORDER BY m.created_at DESC
                                LIMIT 3
                            ");
                            $stmt->execute([$dosen_id]);
                            $materi_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            // Get tugas terbaru
                            $stmt = $pdo->prepare("
                                SELECT 'tugas' as type, t.judul as title, t.created_at, mk.nama as mata_kuliah
                                FROM tugas t
                                JOIN mata_kuliah mk ON t.mata_kuliah_id = mk.id
                                WHERE t.created_by = ?
                                ORDER BY t.created_at DESC
                                LIMIT 3
                            ");
                            $stmt->execute([$dosen_id]);
                            $tugas_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            // Get submission terbaru yang belum dinilai
                            $stmt = $pdo->prepare("
                                SELECT 'submission' as type, 
                                       CONCAT('Submission tugas: ', t.judul) as title,
                                       s.submitted_at as created_at,
                                       mk.nama as mata_kuliah,
                                       COUNT(*) as count
                                FROM submission s
                                JOIN tugas t ON s.tugas_id = t.id
                                JOIN mata_kuliah mk ON t.mata_kuliah_id = mk.id
                                WHERE t.created_by = ? AND s.nilai IS NULL
                                GROUP BY s.tugas_id, t.judul, mk.nama, s.submitted_at
                                ORDER BY s.submitted_at DESC
                                LIMIT 2
                            ");
                            $stmt->execute([$dosen_id]);
                            $submission_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            // Combine all activities
                            foreach ($materi_list as $m) {
                                $aktivitas_list[] = $m;
                            }
                            foreach ($tugas_list as $t) {
                                $aktivitas_list[] = $t;
                            }
                            foreach ($submission_list as $s) {
                                $aktivitas_list[] = $s;
                            }
                            
                            // Sort by created_at descending
                            usort($aktivitas_list, function($a, $b) {
                                return strtotime($b['created_at']) - strtotime($a['created_at']);
                            });
                            
                            // Limit to 5 most recent
                            $aktivitas_list = array_slice($aktivitas_list, 0, 5);
                            
                        } catch (PDOException $e) {
                            error_log("Error loading aktivitas: " . $e->getMessage());
                        }
                    }
                    
                    function timeAgo($datetime) {
                        $time = time() - strtotime($datetime);
                        if ($time < 60) return 'Baru saja';
                        if ($time < 3600) return floor($time/60) . ' menit yang lalu';
                        if ($time < 86400) return floor($time/3600) . ' jam yang lalu';
                        if ($time < 2592000) return floor($time/86400) . ' hari yang lalu';
                        return date('d M Y', strtotime($datetime));
                    }
                    ?>
                    <div class="timeline">
                        <?php if (empty($aktivitas_list)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-history fa-2x text-muted mb-2 d-block"></i>
                                <p class="text-muted mb-0">Belum ada aktivitas</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($aktivitas_list as $index => $aktivitas): 
                                $badge_class = '';
                                $icon = '';
                                if ($aktivitas['type'] === 'materi') {
                                    $badge_class = 'bg-success';
                                    $icon = 'fa-file-upload';
                                } elseif ($aktivitas['type'] === 'tugas') {
                                    $badge_class = 'bg-primary';
                                    $icon = 'fa-tasks';
                                } else {
                                    $badge_class = 'bg-info';
                                    $icon = 'fa-check';
                                }
                                $time_ago = timeAgo($aktivitas['created_at']);
                                $is_last = ($index === count($aktivitas_list) - 1);
                            ?>
                                <div class="timeline-item mb-4 pb-4 <?php echo $is_last ? '' : 'border-bottom'; ?>">
                                    <div class="d-flex gap-3">
                                        <div>
                                            <span class="badge <?php echo $badge_class; ?> rounded-pill">
                                                <i class="fas <?php echo $icon; ?>"></i>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <?php 
                                                if ($aktivitas['type'] === 'materi') {
                                                    echo 'Materi "' . htmlspecialchars($aktivitas['title']) . '" diupload';
                                                } elseif ($aktivitas['type'] === 'tugas') {
                                                    echo 'Tugas "' . htmlspecialchars($aktivitas['title']) . '" dibuat';
                                                } else {
                                                    $count = isset($aktivitas['count']) ? $aktivitas['count'] : 0;
                                                    echo $count . ' submission tugas menunggu penilaian';
                                                }
                                                ?>
                                            </h6>
                                            <?php if (isset($aktivitas['mata_kuliah'])): ?>
                                                <small class="text-muted d-block mb-1">
                                                    <i class="fas fa-book me-1"></i><?php echo htmlspecialchars($aktivitas['mata_kuliah']); ?>
                                                </small>
                                            <?php endif; ?>
                                            <small class="text-muted"><?php echo $time_ago; ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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

    <!-- Quick Actions -->
    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2 text-primary"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="../dosen/upload_materi.php" class="btn btn-outline-success w-100">
                                <i class="fas fa-upload me-2"></i>Upload Materi
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="../dosen/buat_tugas.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-plus me-2"></i>Buat Tugas
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="../dosen/input_nilai.php" class="btn btn-outline-info w-100">
                                <i class="fas fa-edit me-2"></i>Input Nilai
                            </a>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-outline-secondary w-100">
                                <i class="fas fa-download me-2"></i>Export Data
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Charts
document.addEventListener('DOMContentLoaded', function() {
    // Bar Chart - Nilai
    const ctxNilai = document.getElementById('nilaiChart').getContext('2d');
    new Chart(ctxNilai, {
        type: 'bar',
        data: {
            labels: ['A', 'B', 'C', 'D', 'E'],
            datasets: [{
                label: 'Jumlah Mahasiswa',
                data: [<?php echo $nilai_a; ?>, <?php echo $nilai_b; ?>, <?php echo $nilai_c; ?>, 0, 0],
                backgroundColor: [
                    '#198754',
                    '#0dcaf0',
                    '#ffc107',
                    '#dc3545',
                    '#6c757d'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Progress Gauge - Kehadiran
    const ctxKehadiran = document.getElementById('kehadiranChart').getContext('2d');
    new Chart(ctxKehadiran, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Tidak Hadir'],
            datasets: [{
                data: [<?php echo $kehadiran_rata; ?>, <?php echo 100 - $kehadiran_rata; ?>],
                backgroundColor: ['#198754', '#e9ecef'],
                borderWidth: 0,
                cutout: '75%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Progress Gauge - Submission
    const ctxSubmission = document.getElementById('submissionChart').getContext('2d');
    new Chart(ctxSubmission, {
        type: 'doughnut',
        data: {
            labels: ['Dikumpulkan', 'Belum Dikumpulkan'],
            datasets: [{
                data: [<?php echo $submission_rate; ?>, <?php echo 100 - $submission_rate; ?>],
                backgroundColor: ['#0dcaf0', '#e9ecef'],
                borderWidth: 0,
                cutout: '75%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>

<?php include '../components/footer.php'; ?>
