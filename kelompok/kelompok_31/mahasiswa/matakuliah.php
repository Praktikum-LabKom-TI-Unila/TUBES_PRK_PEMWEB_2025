<?php
/**
 * Daftar Mata Kuliah Mahasiswa
 * Dikerjakan oleh: Anggota 2
 * 
 * Menampilkan semua mata kuliah yang tersedia dan yang sudah diambil
 */

session_start();

// Check if logged in and is mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

$page_title = "Mata Kuliah";
include '../components/header.php';
include '../components/navbar.php';

// Koneksi database menggunakan Database class
require_once '../config/database.php';
$database = new Database();
$pdo = $database->getConnection();

if (!$pdo) {
    die('Error: Koneksi database gagal');
}

// Ambil semua mata kuliah
$all_mata_kuliah = [];
$enrolled_ids = [];

try {
    // Ambil semua mata kuliah dengan nama dosen
    $stmt = $pdo->prepare("
        SELECT mk.id, mk.kode, mk.nama, mk.sks, mk.dosen_id, u.nama as nama_dosen
        FROM mata_kuliah mk
        LEFT JOIN users u ON mk.dosen_id = u.id
        ORDER BY mk.kode ASC
    ");
    $stmt->execute();
    $all_mata_kuliah = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Cek apakah tabel enrollment ada, jika ada ambil mata kuliah yang sudah diambil
    try {
        $stmt = $pdo->prepare("SELECT mata_kuliah_id FROM enrollment WHERE mahasiswa_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $enrolled = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $enrolled_ids = array_column($enrolled, 'mata_kuliah_id');
    } catch (PDOException $e) {
        // Tabel enrollment tidak ada, semua mata kuliah dianggap tersedia
        $enrolled_ids = [];
    }
    
} catch (PDOException $e) {
    error_log("Error loading mata kuliah: " . $e->getMessage());
    $all_mata_kuliah = [];
}
?>

<!-- Dashboard Header -->
<div class="dashboard-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 2rem 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1">
                    <i class="fas fa-book me-2"></i>Daftar Mata Kuliah
                </h2>
                <p class="mb-0 text-light">Pilih dan lihat semua mata kuliah yang tersedia</p>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container mt-4 mb-5">
    <!-- Mata Kuliah yang Sudah Diambil -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2 text-success"></i>Mata Kuliah yang Sudah Diambil
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Mata Kuliah</th>
                                    <th>SKS</th>
                                    <th>Dosen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($enrolled_ids) > 0 && count($all_mata_kuliah) > 0): ?>
                                    <?php foreach ($all_mata_kuliah as $mk): ?>
                                        <?php if (in_array($mk['id'], $enrolled_ids)): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($mk['kode']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($mk['nama']); ?></td>
                                            <td><span class="badge bg-info"><?php echo $mk['sks']; ?> SKS</span></td>
                                            <td><?php echo htmlspecialchars($mk['nama_dosen'] ?? 'Belum ditentukan'); ?></td>
                                            <td>
                                                <a href="../mahasiswa/materi.php" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-folder-open me-1"></i>Akses
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                            Anda belum mengambil mata kuliah apapun
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mata Kuliah Tersedia -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2 text-warning"></i>Mata Kuliah Tersedia untuk Bergabung
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <?php if (empty($all_mata_kuliah)): ?>
                            <div class="col-12 text-center py-4">
                                <i class="fas fa-book fa-2x text-muted mb-2 d-block"></i>
                                <p class="text-muted mb-0">Tidak ada mata kuliah tersedia</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($all_mata_kuliah as $mk): ?>
                                <?php if (!in_array($mk['id'], $enrolled_ids)): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-info">
                                        <div class="card-body">
                                            <h6 class="card-title"><?php echo htmlspecialchars($mk['kode']); ?></h6>
                                            <p class="card-text small text-muted"><?php echo htmlspecialchars($mk['nama']); ?></p>
                                            <p class="card-text small"><i class="fas fa-book me-1"></i><?php echo $mk['sks']; ?> SKS</p>
                                            <?php if ($mk['nama_dosen']): ?>
                                                <p class="card-text small"><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($mk['nama_dosen']); ?></p>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-success w-100" onclick="bergabungKelas(<?php echo $mk['id']; ?>, '<?php echo htmlspecialchars($mk['kode']); ?>')">
                                                <i class="fas fa-plus me-1"></i>Bergabung
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function bergabungKelas(kelasId, kodesKelas) {
    if (confirm('Apakah Anda yakin ingin bergabung ke kelas ' + kodesKelas + '?')) {
        $.ajax({
            url: '../api/enrollment.php',
            method: 'POST',
            data: {
                action: 'join',
                mata_kuliah_id: kelasId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat bergabung ke kelas');
            }
        });
    }
}
</script>

<?php include '../components/footer.php'; ?>
