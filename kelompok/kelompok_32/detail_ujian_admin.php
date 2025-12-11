<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['role'] !== 'admin') {
    header("Location: daftar_ujian.php");
    exit();
}

$ujian_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$query_ujian = "SELECT u.*, mp.nama as mata_pelajaran, COALESCE(k.nama, u.kelas) as kelas_nama
                FROM ujian u 
                JOIN mata_pelajaran mp ON u.mata_pelajaran_id = mp.id 
                LEFT JOIN kelas k ON u.kelas = k.id
                WHERE u.id = $ujian_id";
$result_ujian = mysqli_query($conn, $query_ujian);

if (mysqli_num_rows($result_ujian) == 0) {
    header("Location: dashboard_admin.php");
    exit();
}

$ujian = mysqli_fetch_assoc($result_ujian);

// Ambil daftar peserta
$query_peserta = "SELECT ru.*, u.nama_lengkap, u.username, u.kelas, COALESCE(k.nama, u.kelas) as kelas_nama
                  FROM riwayat_ujian ru
                  JOIN users u ON ru.user_id = u.id
                  LEFT JOIN kelas k ON u.kelas = k.id
                  WHERE ru.ujian_id = $ujian_id
                  ORDER BY ru.skor DESC, ru.created_at ASC";
$result_peserta = mysqli_query($conn, $query_peserta);
$total_peserta = mysqli_num_rows($result_peserta);

// Hitung statistik
$passing_grade = 70;
$lulus = 0;
$total_skor = 0;
$skor_tertinggi = 0;
$skor_terendah = 100;

$peserta_data = [];
while ($p = mysqli_fetch_assoc($result_peserta)) {
    $peserta_data[] = $p;
    $total_skor += $p['skor'];
    if ($p['skor'] >= $passing_grade) $lulus++;
    if ($p['skor'] > $skor_tertinggi) $skor_tertinggi = $p['skor'];
    if ($p['skor'] < $skor_terendah) $skor_terendah = $p['skor'];
}

$rata_rata = $total_peserta > 0 ? $total_skor / $total_peserta : 0;
$tingkat_kelulusan = $total_peserta > 0 ? ($lulus / $total_peserta * 100) : 0;

// Ambil analisis soal
$query_soal = "SELECT 
                s.id, s.pertanyaan,
                COUNT(*) as total_jawaban,
                SUM(CASE WHEN dj.status = 'benar' THEN 1 ELSE 0 END) as benar,
                SUM(CASE WHEN dj.status = 'salah' THEN 1 ELSE 0 END) as salah
               FROM soal s
               LEFT JOIN detail_jawaban dj ON s.id = dj.soal_id
               LEFT JOIN riwayat_ujian ru ON dj.riwayat_id = ru.id AND ru.ujian_id = $ujian_id
               WHERE s.ujian_id = $ujian_id
               GROUP BY s.id
               ORDER BY s.id";
$result_soal = mysqli_query($conn, $query_soal);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Ujian - <?php echo $ujian['judul']; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f7fa; color: #2d3748; padding: 2rem; }
        .container { max-width: 1200px; margin: 0 auto; }
        .back-btn { display: inline-block; padding: 0.75rem 1.5rem; background: #fff; color: #3182ce; text-decoration: none; border-radius: 8px; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: all 0.2s; }
        .back-btn:hover { background: #3182ce; color: #fff; }
        .header-card { background: #fff; padding: 2rem; border-radius: 12px; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header-card h1 { font-size: 1.875rem; margin-bottom: 0.5rem; }
        .header-card .subtitle { color: #718096; margin-bottom: 1rem; }
        .info-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .info-item { display: flex; justify-content: space-between; padding: 0.75rem; background: #f7fafc; border-radius: 8px; }
        .info-label { color: #718096; font-size: 0.875rem; }
        .info-value { font-weight: 600; }
        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
        .stat-box { background: #fff; padding: 1.25rem; border-radius: 12px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .stat-label { font-size: 0.875rem; color: #718096; margin-bottom: 0.5rem; }
        .stat-value { font-size: 1.875rem; font-weight: 700; color: #3182ce; }
        .section { background: #fff; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .section h2 { font-size: 1.25rem; margin-bottom: 1.5rem; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #f7fafc; }
        th { padding: 1rem; text-align: left; font-size: 0.875rem; font-weight: 600; color: #4a5568; border-bottom: 1px solid #e2e8f0; }
        td { padding: 1rem; border-bottom: 1px solid #f7fafc; font-size: 0.875rem; }
        tbody tr:hover { background: #f7fafc; }
        .badge { display: inline-block; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: #c6f6d5; color: #22543d; }
        .badge-warning { background: #feebc8; color: #7c2d12; }
        .badge-error { background: #fed7d7; color: #742a2a; }
        .soal-item { padding: 1rem; margin-bottom: 0.75rem; background: #f7fafc; border-radius: 8px; }
        .soal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
        .soal-number { font-weight: 600; }
        .soal-stats { font-size: 0.875rem; color: #718096; }
        .soal-question { font-size: 0.875rem; color: #4a5568; margin-bottom: 0.75rem; }
        .progress-bar { width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden; }
        .progress-fill { height: 100%; transition: width 0.3s; }
        .progress-easy { background: linear-gradient(90deg, #48bb78, #38a169); }
        .progress-medium { background: linear-gradient(90deg, #ed8936, #dd6b20); }
        .progress-hard { background: linear-gradient(90deg, #f56565, #e53e3e); }
        .difficulty-tag { display: inline-block; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600; margin-left: 0.5rem; }
        .easy { background: #c6f6d5; color: #22543d; }
        .medium { background: #feebc8; color: #7c2d12; }
        .hard { background: #fed7d7; color: #742a2a; }
        
        @media (max-width: 768px) {
            body { padding: 1rem; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .info-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard_admin.php" class="back-btn">← Kembali ke Dashboard</a>
        
        <div class="header-card">
            <h1><?php echo $ujian['judul']; ?></h1>
            <div class="subtitle"><?php echo $ujian['mata_pelajaran']; ?></div>
            
            <div class="info-row">
                <div class="info-item">
                    <span class="info-label">Jumlah Soal</span>
                    <span class="info-value"><?php echo $ujian['jumlah_soal']; ?> Soal</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Waktu Pengerjaan</span>
                    <span class="info-value"><?php echo $ujian['waktu_pengerjaan']; ?> Menit</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Kelas</span>
                    <span class="info-value"><?php echo htmlspecialchars($ujian['kelas_nama']); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Dibuat Tanggal</span>
                    <span class="info-value"><?php echo date('d/m/Y', strtotime($ujian['created_at'])); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Total Peserta</span>
                    <span class="info-value"><?php echo $total_peserta; ?> Siswa</span>
                </div>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-label">Passing Grade</div>
                <div class="stat-value"><?php echo $passing_grade; ?></div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Rata-rata Nilai</div>
                <div class="stat-value"><?php echo number_format($rata_rata, 1); ?></div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Nilai Tertinggi</div>
                <div class="stat-value"><?php echo $skor_tertinggi; ?></div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Nilai Terendah</div>
                <div class="stat-value"><?php echo $total_peserta > 0 ? $skor_terendah : 0; ?></div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Tingkat Kelulusan</div>
                <div class="stat-value"><?php echo number_format($tingkat_kelulusan, 1); ?>%</div>
            </div>
        </div>
        
        <div class="section">
            <h2>📊 Daftar Peserta Ujian</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Siswa</th>
                        <th>Username</th>
                        <th>Kelas</th>
                        <th>Skor</th>
                        <th>Benar/Total</th>
                        <th>Persentase</th>
                        <th>Waktu</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($peserta_data as $p):
                        $persentase = ($p['jawaban_benar'] / $p['total_soal'] * 100);
                        $status = $p['skor'] >= 80 ? 'Excellent' : ($p['skor'] >= $passing_grade ? 'Good' : 'Need Improvement');
                        $badge_class = $p['skor'] >= 80 ? 'badge-success' : ($p['skor'] >= $passing_grade ? 'badge-warning' : 'badge-error');
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><strong><?php echo $p['nama_lengkap']; ?></strong></td>
                        <td>@<?php echo $p['username']; ?></td>
                        <td><?php echo htmlspecialchars($p['kelas_nama'] ?? $p['kelas'] ?? '—'); ?></td>
                        <td><strong style="font-size: 1.125rem; color: #3182ce;"><?php echo $p['skor']; ?></strong></td>
                        <td><?php echo $p['jawaban_benar']; ?>/<?php echo $p['total_soal']; ?></td>
                        <td><?php echo number_format($persentase, 1); ?>%</td>
                        <td><?php echo floor($p['waktu_pengerjaan'] / 60); ?> menit</td>
                        <td><span class="badge <?php echo $badge_class; ?>"><?php echo $status; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>📈 Analisis Tingkat Kesulitan Soal</h2>
            <p style="color: #718096; font-size: 0.875rem; margin-bottom: 1.5rem;">
                Soal dengan persentase jawaban benar tinggi = mudah, rendah = sulit
            </p>
            
            <?php 
            $no_soal = 1;
            while ($soal = mysqli_fetch_assoc($result_soal)): 
                $persentase_benar = $soal['total_jawaban'] > 0 ? ($soal['benar'] / $soal['total_jawaban'] * 100) : 0;
                
        
                if ($persentase_benar >= 70) {
                    $difficulty = 'Mudah';
                    $difficulty_class = 'easy';
                    $progress_class = 'progress-easy';
                } elseif ($persentase_benar >= 40) {
                    $difficulty = 'Sedang';
                    $difficulty_class = 'medium';
                    $progress_class = 'progress-medium';
                } else {
                    $difficulty = 'Sulit';
                    $difficulty_class = 'hard';
                    $progress_class = 'progress-hard';
                }
            ?>
            <div class="soal-item">
                <div class="soal-header">
                    <div>
                        <span class="soal-number">Soal #<?php echo $no_soal++; ?></span>
                        <span class="difficulty-tag <?php echo $difficulty_class; ?>"><?php echo $difficulty; ?></span>
                    </div>
                    <span class="soal-stats">
                        <?php echo $soal['benar']; ?> benar, <?php echo $soal['salah']; ?> salah 
                        (<?php echo number_format($persentase_benar, 1); ?>% benar)
                    </span>
                </div>
                <div class="soal-question"><?php echo substr($soal['pertanyaan'], 0, 100); ?><?php echo strlen($soal['pertanyaan']) > 100 ? '...' : ''; ?></div>
                <div class="progress-bar">
                    <div class="progress-fill <?php echo $progress_class; ?>" style="width: <?php echo $persentase_benar; ?>%"></div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        
        <div style="text-align: center; margin: 2rem 0;">
            <a href="dashboard_admin.php" class="back-btn">Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>