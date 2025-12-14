<?php
/**
 * Pengaturan Sistem
 * Dikerjakan oleh: Anggota 2
 * 
 * Halaman pengaturan sistem untuk admin
 */

session_start();

// Check if logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';
$database = new Database();
$pdo = $database->getConnection();

if (!$pdo) {
    die('Error: Koneksi database gagal');
}

// Handle form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Buat tabel settings jika belum ada
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) UNIQUE NOT NULL,
                setting_value TEXT,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        // Update settings
        $settings = [
            'system_name' => $_POST['system_name'] ?? 'EduPortal',
            'system_email' => $_POST['system_email'] ?? '',
            'max_file_size' => intval($_POST['max_file_size'] ?? 5242880), // 5MB default
            'allowed_extensions' => $_POST['allowed_extensions'] ?? 'pdf,doc,docx,txt,zip,rar'
        ];
        
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("
                INSERT INTO settings (setting_key, setting_value) 
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()
            ");
            $stmt->execute([$key, $value, $value]);
        }
        
        $message = 'Pengaturan berhasil disimpan!';
        $message_type = 'success';
    } catch (PDOException $e) {
        error_log("Settings Error: " . $e->getMessage());
        $message = 'Gagal menyimpan pengaturan: ' . $e->getMessage();
        $message_type = 'danger';
    }
}

// Get current settings
$current_settings = [
    'system_name' => 'EduPortal',
    'system_email' => '',
    'max_file_size' => 5242880,
    'allowed_extensions' => 'pdf,doc,docx,txt,zip,rar'
];

try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($settings_data as $setting) {
        $current_settings[$setting['setting_key']] = $setting['setting_value'];
    }
} catch (PDOException $e) {
    // Tabel belum ada, gunakan default
}

$page_title = "Pengaturan Sistem";
include '../components/header.php';
include '../components/navbar.php';
?>

<div class="dashboard-header" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 2rem 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1"><i class="fas fa-cog me-2"></i>Pengaturan Sistem</h2>
                <p class="mb-0 text-light">Kelola konfigurasi sistem EduPortal</p>
            </div>
        </div>
    </div>
</div>

<div class="container mt-4 mb-5">
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i>Konfigurasi Sistem</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="system_name" class="form-label">Nama Sistem</label>
                            <input type="text" class="form-control" id="system_name" name="system_name" 
                                   value="<?php echo htmlspecialchars($current_settings['system_name']); ?>" required>
                            <small class="text-muted">Nama yang ditampilkan di header dan footer sistem</small>
                        </div>

                        <div class="mb-3">
                            <label for="system_email" class="form-label">Email Sistem</label>
                            <input type="email" class="form-control" id="system_email" name="system_email" 
                                   value="<?php echo htmlspecialchars($current_settings['system_email']); ?>">
                            <small class="text-muted">Email untuk notifikasi dan komunikasi sistem</small>
                        </div>

                        <div class="mb-3">
                            <label for="max_file_size" class="form-label">Max File Size (bytes)</label>
                            <input type="number" class="form-control" id="max_file_size" name="max_file_size" 
                                   value="<?php echo $current_settings['max_file_size']; ?>" min="1048576" step="1048576" required>
                            <small class="text-muted">
                                Ukuran maksimal file upload dalam bytes. 
                                Default: 5MB (5242880 bytes). 
                                Saat ini: <?php echo number_format($current_settings['max_file_size'] / 1048576, 2); ?> MB
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="allowed_extensions" class="form-label">Allowed File Extensions</label>
                            <input type="text" class="form-control" id="allowed_extensions" name="allowed_extensions" 
                                   value="<?php echo htmlspecialchars($current_settings['allowed_extensions']); ?>" required>
                            <small class="text-muted">Ekstensi file yang diizinkan, pisahkan dengan koma (contoh: pdf,doc,docx,txt,zip,rar)</small>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="../dashboard/admin.php" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi</h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        <strong>Nama Sistem:</strong> Digunakan di header, footer, dan title halaman.
                    </p>
                    <p class="small text-muted">
                        <strong>Email Sistem:</strong> Digunakan untuk notifikasi dan komunikasi.
                    </p>
                    <p class="small text-muted">
                        <strong>Max File Size:</strong> Batas ukuran file yang bisa diupload oleh user.
                    </p>
                    <p class="small text-muted">
                        <strong>Allowed Extensions:</strong> Format file yang diizinkan untuk upload.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../components/footer.php'; ?>

