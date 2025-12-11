<?php
global $conn;

$qSiswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa");
$totalSiswa = mysqli_fetch_assoc($qSiswa)['total'];

$qTutor = mysqli_query($conn, "SELECT COUNT(*) as total FROM tutor");
$totalTutor = mysqli_fetch_assoc($qTutor)['total'];

$qPending = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'tutor' AND status = 'pending'");
$totalPending = mysqli_fetch_assoc($qPending)['total'];

$totalKelas = 12; 

$chartQuery = mysqli_query($conn, "SELECT keahlian, COUNT(*) as jumlah FROM tutor GROUP BY keahlian");

$labels = [];
$dataChart = [];

while($row = mysqli_fetch_assoc($chartQuery)) {
    $labels[] = $row['keahlian'];
    $dataChart[] = $row['jumlah'];
}

$jsonLabels = json_encode($labels);
$jsonData = json_encode($dataChart);

$logQuery = "SELECT id, name, email, role, status, created_at 
             FROM users 
             WHERE role IN ('learner', 'tutor')
             ORDER BY created_at DESC 
             LIMIT 10";
$logResult = mysqli_query($conn, $logQuery);
?>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #0C4A60 0%, #0A5A70 100%); color: white;">
            <div class="card-body d-flex align-items-center">
                <div class="bg-white bg-opacity-20 p-3 rounded-circle me-3">
                    <i class="fas fa-user-graduate fa-2x text-white"></i>
                </div>
                <div>
                    <h6 class="mb-1 small text-uppercase fw-bold opacity-75">Total Siswa</h6>
                    <h2 class="mb-0 fw-bold"><?= $totalSiswa ?></h2>
                    <small class="opacity-75"><i class="fas fa-check-circle"></i> Data Realtime</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #9AD4D6 0%, #B5E5E7 100%);">
            <div class="card-body d-flex align-items-center">
                <div class="bg-white bg-opacity-50 p-3 rounded-circle me-3" style="color: #0C4A60;">
                    <i class="fas fa-chalkboard-teacher fa-2x"></i>
                </div>
                <div style="color: #0C4A60;">
                    <h6 class="mb-1 small text-uppercase fw-bold opacity-75">Total Tutor</h6>
                    <h2 class="mb-0 fw-bold"><?= $totalTutor ?></h2>
                    <small class="opacity-75"><i class="fas fa-check-circle"></i> Data Realtime</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #F7DC6F 0%, #F9E79F 100%);">
            <div class="card-body d-flex align-items-center">
                <div class="bg-white bg-opacity-50 p-3 rounded-circle me-3" style="color: #856404;">
                    <i class="fas fa-book-open fa-2x"></i>
                </div>
                <div style="color: #856404;">
                    <h6 class="mb-1 small text-uppercase fw-bold opacity-75">Kelas Aktif</h6>
                    <h2 class="mb-0 fw-bold"><?= $totalKelas ?></h2>
                    <small class="opacity-75">Sedang berjalan</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #FF6B35 0%, #FF8C61 100%); color: white;">
            <div class="card-body d-flex align-items-center">
                <div class="bg-white bg-opacity-20 p-3 rounded-circle me-3">
                    <i class="fas fa-bell fa-2x text-white"></i>
                </div>
                <div>
                    <h6 class="mb-1 small text-uppercase fw-bold opacity-75">Perlu Verifikasi</h6>
                    <h2 class="mb-0 fw-bold"><?= $totalPending ?></h2>
                    <small class="opacity-75 fw-bold">Butuh Tindakan</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #0C4A60 !important;">
            <div class="card-header py-3" style="background: linear-gradient(135deg, #0C4A60 0%, #0A5A70 100%); color: white;">
                <h5 class="card-title mb-0 fw-bold"><i class="fas fa-chart-line me-2"></i>Tren Pendaftaran</h5>
            </div>
            <div class="card-body">
                <canvas id="registrationChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #9AD4D6 !important;">
            <div class="card-header py-3" style="background: linear-gradient(135deg, #9AD4D6 0%, #B5E5E7 100%); color: #0C4A60;">
                <h5 class="card-title mb-0 fw-bold"><i class="fas fa-graduation-cap me-2"></i>Sebaran Keahlian</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="width: 100%;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-left: 4px solid #F7DC6F !important;">
    <div class="card-header py-3 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, rgba(247, 220, 111, 0.15) 0%, rgba(249, 231, 159, 0.15) 100%);">
    <h5 class="mb-0 fw-bold" style="color: #0C4A60;"><i class="fas fa-history me-2"></i>Pendaftaran Terbaru</h5>
    
    <div class="dropdown">
        <button class="btn btn-sm rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" 
                style="background: #0C4A60; color: white; border: none;">
            <i class="fas fa-eye me-1"></i>Lihat Semua
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
            <li><h6 class="dropdown-header text-uppercase small" style="color: #0C4A60;">Pilih Data</h6></li>
            <li>
                <a class="dropdown-item" href="?page=siswa">
                    <i class="fas fa-user-graduate me-2" style="color: #0C4A60;"></i> Data Siswa
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="?page=tutor">
                    <i class="fas fa-chalkboard-teacher me-2" style="color: #9AD4D6;"></i> Data Tutor
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="?page=verifikasi">
                    <i class="fas fa-check-circle me-2" style="color: #FF6B35;"></i> Cek Verifikasi
                </a>
            </li>
        </ul>
    </div>
</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>User</th>
                    <th>Aktivitas</th>
                    <th>Waktu Daftar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($logResult) > 0): ?>
                    <?php while($log = mysqli_fetch_assoc($logResult)): 
                        // Hitung waktu lalu
                        $createdTime = strtotime($log['created_at']);
                        $now = time();
                        $diff = $now - $createdTime;
                        $minutes = floor($diff / 60);
                        $hours = floor($diff / 3600);
                        $days = floor($diff / 86400);
                        
                        if ($minutes < 1) {
                            $timeAgo = 'Baru saja';
                        } elseif ($minutes < 60) {
                            $timeAgo = $minutes . ' menit lalu';
                        } elseif ($hours < 24) {
                            $timeAgo = $hours . ' jam lalu';
                        } else {
                            $timeAgo = $days . ' hari lalu';
                        }
                        
                        // Tentukan warna status
                        $statusClass = 'secondary';
                        $statusText = 'Baru';
                        if ($log['role'] == 'tutor' && $log['status'] == 'pending') {
                            $statusClass = 'warning';
                            $statusText = 'Menunggu Verifikasi';
                        } elseif ($log['status'] == 'active') {
                            $statusClass = 'success';
                            $statusText = 'Aktif';
                        } elseif ($log['status'] == 'banned') {
                            $statusClass = 'danger';
                            $statusText = 'Ditolak';
                        }
                    ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($log['name']) ?>&background=random" class="rounded-circle me-2" width="32" height="32">
                                <div>
                                    <strong><?= htmlspecialchars($log['name']) ?></strong>
                                    <br><small class="text-muted"><?= htmlspecialchars($log['email']) ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php if ($log['role'] == 'learner'): ?>
                                <span class="badge bg-primary-subtle text-primary">
                                    <i class="fas fa-user-graduate me-1"></i> Siswa Baru
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="fas fa-chalkboard-teacher me-1"></i> Tutor Baru
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small">
                            <i class="far fa-clock me-1"></i><?= $timeAgo ?>
                        </td>
                        <td><span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                        Belum ada aktivitas registrasi
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctxReg = document.getElementById('registrationChart').getContext('2d');
    new Chart(ctxReg, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
            datasets: [{
                label: 'Siswa Baru',
                data: [12, 19, 3, 5, 2, 3, 20, 45, 30, 55, 40, <?= $totalSiswa ?>], 
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                borderWidth: 2, fill: true, tension: 0.4
            },
            {
                label: 'Tutor Baru',
                data: [2, 3, 1, 0, 1, 2, 5, 10, 8, 12, 5, <?= $totalTutor ?>], 
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                borderWidth: 2, fill: true, tension: 0.4
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'top' } } }
    });

    const dbLabels = <?= $jsonLabels ?>; 
    const dbData = <?= $jsonData ?>;

    const ctxCat = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: dbLabels,
            datasets: [{
                data: dbData,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                borderWidth: 1
            }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });
</script>