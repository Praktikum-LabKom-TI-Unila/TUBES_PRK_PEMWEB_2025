<?php

// Mulai session
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../backend/auth/login.php');
    exit;
}

// Cek role user (hanya warga yang bisa akses)
if ($_SESSION['role'] !== 'warga') {
    header('Location: ../frontend/index.php');
    exit;
}

// Koneksi database
require_once '../backend/config.php';

// Variabel filter dan pencarian
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Query dasar
$query = "SELECT p.*, COUNT(t.id) as jumlah_tanggapan 
          FROM pengaduan p 
          LEFT JOIN tanggapan t ON p.id = t.pengaduan_id 
          WHERE p.user_id = ?";
$params = [$_SESSION['user_id']];
$types = 'i';

// Tambahkan filter pencarian
if (!empty($search)) {
    $query .= " AND (p.judul LIKE ? OR p.deskripsi LIKE ? OR p.lokasi LIKE ?)";
    $searchTerm = '%' . $search . '%';
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
    $types .= 'sss';
}

// Tambahkan filter status
if (!empty($filter_status) && in_array($filter_status, ['pending', 'proses', 'selesai', 'ditolak'])) {
    $query .= " AND p.status = ?";
    $params[] = $filter_status;
    $types .= 's';
}

// Urutkan berdasarkan tanggal terbaru
$query .= " GROUP BY p.id ORDER BY p.created_at DESC";

// Eksekusi query
$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$pengaduan_list = [];

while ($row = $result->fetch_assoc()) {
    $pengaduan_list[] = $row;
}
$stmt->close();

// Fungsi untuk format tanggal
function format_tanggal($date) {
    $timestamp = strtotime($date);
    setlocale(LC_TIME, 'id_ID.UTF-8');
    return strftime('%d %B %Y %H:%M', $timestamp);
}

// Fungsi untuk menghitung waktu yang lalu
function time_ago($date) {
    $timestamp = strtotime($date);
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return "baru saja";
    } elseif ($diff < 3600) {
        return floor($diff / 60) . " menit lalu";
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . " jam lalu";
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . " hari lalu";
    } else {
        return date('d M Y', $timestamp);
    }
}

// Fungsi untuk badge status
function get_status_badge($status) {
    switch ($status) {
        case 'pending':
            return '<span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Menunggu Verifikasi</span>';
        case 'proses':
            return '<span class="badge bg-info text-white"><i class="bi bi-arrow-repeat"></i> Sedang Diproses</span>';
        case 'selesai':
            return '<span class="badge bg-success text-white"><i class="bi bi-check-circle"></i> Selesai</span>';
        case 'ditolak':
            return '<span class="badge bg-danger text-white"><i class="bi bi-x-circle"></i> Ditolak</span>';
        default:
            return '<span class="badge bg-secondary">Unknown</span>';
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengaduan Warga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* Bagian Styling akan ada disini ntar */
    </style>
</head>
<body>
        <?php include 'layout/header.html'; ?>
    
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="bi bi-chat-dots-fill"></i> Riwayat Pengaduan</h1>
                    <p>Lihat status dan tanggapan dari pengaduan yang telah Anda ajukan</p>
                </div>
                <div class="col-md-4 text-md-end" style="margin-top: 15px;">
                    <a href="pengaduan_form.php" class="btn-new-pengaduan">
                        <i class="bi bi-plus-circle"></i> Ajukan Pengaduan Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Cari Pengaduan</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="search" 
                        name="search" 
                        placeholder="Cari judul, deskripsi, atau lokasi..."
                        value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div class="col-md-4">
                    <label for="status" class="form-label">Filter Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Menunggu Verifikasi</option>
                        <option value="proses" <?php echo $filter_status === 'proses' ? 'selected' : ''; ?>>Sedang Diproses</option>
                        <option value="selesai" <?php echo $filter_status === 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                        <option value="ditolak" <?php echo $filter_status === 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-filter flex-grow-1">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="pengaduan_list.php" class="btn btn-reset-filter">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Daftar Pengaduan -->
        <?php if (count($pengaduan_list) > 0): ?>
            <div class="pengaduan-list">
                <?php foreach ($pengaduan_list as $pengaduan): ?>
                    <div class="pengaduan-card <?php echo $pengaduan['status']; ?>">
                        
                        <!-- Header -->
                        <div class="pengaduan-header">
                            <h5><?php echo htmlspecialchars($pengaduan['judul']); ?></h5>
                            <?php echo get_status_badge($pengaduan['status']); ?>
                        </div>
                        
                        <!-- Meta Info -->
                        <div class="pengaduan-meta">
                            <div class="pengaduan-meta-item">
                                <i class="bi bi-calendar"></i>
                                <span><?php echo format_tanggal($pengaduan['created_at']); ?></span>
                            </div>
                            <div class="pengaduan-meta-item">
                                <i class="bi bi-clock"></i>
                                <span><?php echo time_ago($pengaduan['created_at']); ?></span>
                            </div>
                            <div class="pengaduan-meta-item">
                                <i class="bi bi-chat-dots"></i>
                                <span><?php echo $pengaduan['jumlah_tanggapan']; ?> Tanggapan</span>
                            </div>
                        </div>
                        
                        <!-- Deskripsi -->
                        <div class="pengaduan-deskripsi">
                            <?php echo htmlspecialchars(substr($pengaduan['deskripsi'], 0, 200)); ?>
                            <?php if (strlen($pengaduan['deskripsi']) > 200): ?>
                                ...
                            <?php endif; ?>
                        </div>
                        
                        <!-- Lokasi -->
                        <div class="pengaduan-lokasi">
                            <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($pengaduan['lokasi']); ?>
                        </div>
                        
                        <!-- Foto -->
                        <?php if (!empty($pengaduan['foto'])): ?>
                            <div class="pengaduan-foto">
                                <img src="../../uploads/pengaduan/<?php echo htmlspecialchars($pengaduan['foto']); ?>" alt="Foto Pengaduan">
                            </div>
                        <?php endif; ?>
                        
                        <!-- Action Buttons -->
                        <div class="pengaduan-action">
                            <a href="#" class="btn-detail" onclick="showDetail(<?php echo $pengaduan['id']; ?>); return false;">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>
                            <?php if ($pengaduan['jumlah_tanggapan'] > 0): ?>
                                <button type="button" class="btn-lihat-tanggapan" onclick="toggleTanggapan(this)">
                                    <i class="bi bi-chat"></i> Lihat Tanggapan 
                                    <span class="badge-count"><?php echo $pengaduan['jumlah_tanggapan']; ?></span>
                                </button>
                            <?php else: ?>
                                <p style="margin: 0; color: #999; font-size: 13px;">
                                    <i class="bi bi-info-circle"></i> Belum ada tanggapan
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Tanggapan Section (Hidden by default) -->
                        <?php if ($pengaduan['jumlah_tanggapan'] > 0): ?>
                            <div class="tanggapan-section" data-pengaduan-id="<?php echo $pengaduan['id']; ?>">
                                <?php 
                                // Query untuk mendapatkan tanggapan
                                $stmt_tanggapan = $conn->prepare(
                                    "SELECT t.*, u.nama 
                                     FROM tanggapan t 
                                     JOIN users u ON t.admin_id = u.id 
                                     WHERE t.pengaduan_id = ? 
                                     ORDER BY t.created_at DESC"
                                );
                                $stmt_tanggapan->bind_param('i', $pengaduan['id']);
                                $stmt_tanggapan->execute();
                                $tanggapan_result = $stmt_tanggapan->get_result();
                                
                                while ($tanggapan = $tanggapan_result->fetch_assoc()):
                                ?>
                                    <div class="tanggapan-item">
                                        <div class="tanggapan-header">
                                            Admin: <?php echo htmlspecialchars($tanggapan['nama']); ?> - <?php echo format_tanggal($tanggapan['created_at']); ?>
                                        </div>
                                        <div class="tanggapan-content">
                                            <?php echo nl2br(htmlspecialchars($tanggapan['isi_tanggapan'])); ?>
                                        </div>
                                    </div>
                                <?php 
                                endwhile;
                                $stmt_tanggapan->close();
                                ?>
                            </div>
                        <?php endif; ?>
                        
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h4>Belum Ada Pengaduan</h4>
                <p>Anda belum pernah mengajukan pengaduan. Mulai dengan mengajukan pengaduan baru untuk menyampaikan keluhan atau saran Anda.</p>
                <a href="pengaduan_form.php" class="btn-new-pengaduan" style="margin-top: 20px;">
                    <i class="bi bi-plus-circle"></i> Ajukan Pengaduan Pertama Anda
                </a>
            </div>
        <?php endif; ?>
        
    </div>
    
    <?php include 'layout/footer.html'; ?>
    
    <script>
        // Fungsi fungsi bakalan ada disini ntar
    </script>
</body>

