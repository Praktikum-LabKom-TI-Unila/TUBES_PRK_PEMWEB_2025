<?php
global $conn;

$qSiswa = mysqli_query($conn, "SELECT COUNT(*) as total FROM siswa");
$totalSiswa = mysqli_fetch_assoc($qSiswa)['total'];

$qTutor = mysqli_query($conn, "SELECT COUNT(*) as total FROM tutor");
$totalTutor = mysqli_fetch_assoc($qTutor)['total'];

$qPending = mysqli_query($conn, "SELECT COUNT(*) as total FROM tutor WHERE status != 'Aktif'");
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

$logQuery = "
(SELECT nama_lengkap, 'Mendaftar sebagai Siswa' as aksi, created_at, 'primary' as warna FROM siswa)
UNION
(SELECT nama_lengkap, 'Mendaftar sebagai Tutor' as aksi, created_at, 'success' as warna FROM tutor)
ORDER BY created_at DESC LIMIT 5
";
$logResult = mysqli_query($conn, $logQuery);
?>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 text-primary">
                    <i class="fas fa-user-graduate fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-bold">Total Siswa</h6>
                    <h3 class="mb-0 fw-bold"><?= $totalSiswa ?></h3>
                    <small class="text-success"><i class="fas fa-check"></i> Data Realtime</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3 text-success">
                    <i class="fas fa-chalkboard-teacher fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-bold">Total Tutor</h6>
                    <h3 class="mb-0 fw-bold"><?= $totalTutor ?></h3>
                    <small class="text-success"><i class="fas fa-check"></i> Data Realtime</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3 text-warning">
                    <i class="fas fa-book-open fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-bold">Kelas Aktif</h6>
                    <h3 class="mb-0 fw-bold"><?= $totalKelas ?></h3>
                    <small class="text-muted">Sedang berjalan</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle me-3 text-danger">
                    <i class="fas fa-bell fa-2x"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small text-uppercase fw-bold">Status Non-Aktif</h6>
                    <h3 class="mb-0 fw-bold"><?= $totalPending ?></h3>
                    <small class="text-danger fw-bold">Butuh Tindakan</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">Tren Pendaftaran (Simulasi)</h5>
            </div>
            <div class="card-body">
                <canvas id="registrationChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0 fw-bold">Sebaran Keahlian Tutor</h5>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="width: 100%;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0 fw-bold">Pendaftaran Terbaru</h5>
    
    <div class="dropdown">
        <button class="btn btn-sm btn-outline-primary rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Lihat Semua
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
            <li><h6 class="dropdown-header text-uppercase small text-muted">Pilih Data</h6></li>
            <li>
                <a class="dropdown-item" href="?page=siswa">
                    <i class="fas fa-user-graduate me-2 text-primary"></i> Data Siswa
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="?page=tutor">
                    <i class="fas fa-chalkboard-teacher me-2 text-success"></i> Data Tutor
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" href="?page=verifikasi">
                    <i class="fas fa-check-circle me-2 text-warning"></i> Cek Verifikasi
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
                    <?php while($log = mysqli_fetch_assoc($logResult)): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($log['nama_lengkap']) ?>&background=random" class="rounded-circle me-2" width="32" height="32">
                                <strong><?= htmlspecialchars($log['nama_lengkap']) ?></strong>
                            </div>
                        </td>
                        <td><?= $log['aksi'] ?></td>
                        <td class="text-muted"><?= date('d M Y H:i', strtotime($log['created_at'])) ?></td>
                        <td><span class="badge bg-<?= $log['warna'] ?>">Baru</span></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center">Belum ada aktivitas.</td></tr>
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