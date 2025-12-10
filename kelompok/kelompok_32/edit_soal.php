<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['step']) && $_POST['step'] == 'update_ujian') {
        $ujian_id = intval($_POST['ujian_id']);
        $mata_pelajaran_id = intval($_POST['mata_pelajaran_id']);
        $judul = mysqli_real_escape_string($conn, $_POST['judul']);
        $jumlah_soal = intval($_POST['jumlah_soal']);
        $waktu = intval($_POST['waktu_pengerjaan']);
        
        $kelas = isset($_POST['kelas']) && $_POST['kelas'] !== '' ? intval($_POST['kelas']) : 0;
        
        $query = "UPDATE ujian SET mata_pelajaran_id = $mata_pelajaran_id, judul = '$judul', jumlah_soal = $jumlah_soal, waktu_pengerjaan = $waktu, kelas = $kelas WHERE id = $ujian_id";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Detail ujian berhasil diperbarui!";
        } else {
            $_SESSION['error'] = "Gagal memperbarui: " . mysqli_error($conn);
        }
        header("Location: edit_soal.php?ujian_id=$ujian_id");
        exit();
    }
    
    if (isset($_POST['step']) && $_POST['step'] == 'update_soal') {
        $soal_id = intval($_POST['soal_id']);
        $ujian_id = intval($_POST['ujian_id']);
        $pertanyaan = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
        $jawaban_benar = $_POST['jawaban_benar'];
        
        $pilihan = [];
        foreach ($_POST['pilihan'] as $p) {
            $pilihan[] = mysqli_real_escape_string($conn, $p);
        }
        while (count($pilihan) < 4) $pilihan[] = '';
        
        $query = "UPDATE soal SET pertanyaan = '$pertanyaan', pilihan_a = '{$pilihan[0]}', pilihan_b = '{$pilihan[1]}', pilihan_c = '{$pilihan[2]}', pilihan_d = '{$pilihan[3]}', jawaban_benar = '$jawaban_benar' WHERE id = $soal_id";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Soal berhasil diperbarui!";
        } else {
            $_SESSION['error'] = "Gagal memperbarui soal: " . mysqli_error($conn);
        }
        header("Location: edit_soal.php?ujian_id=$ujian_id&mode=soal");
        exit();
    }
    
    if (isset($_POST['step']) && $_POST['step'] == 'add_soal') {
        $ujian_id = intval($_POST['ujian_id']);
        $pertanyaan = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
        $jawaban_benar = $_POST['jawaban_benar'];
        
        $pilihan = [];
        foreach ($_POST['pilihan'] as $p) {
            if (!empty($p)) $pilihan[] = mysqli_real_escape_string($conn, $p);
        }
        while (count($pilihan) < 4) $pilihan[] = '';
        
        $query = "INSERT INTO soal (ujian_id, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar) VALUES ($ujian_id, '$pertanyaan', '{$pilihan[0]}', '{$pilihan[1]}', '{$pilihan[2]}', '{$pilihan[3]}', '$jawaban_benar')";
        
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Soal baru berhasil ditambahkan!";
        } else {
            $_SESSION['error'] = "Gagal menambahkan soal: " . mysqli_error($conn);
        }
        header("Location: edit_soal.php?ujian_id=$ujian_id&mode=soal");
        exit();
    }
    
}

$ujian_id = isset($_GET['ujian_id']) ? intval($_GET['ujian_id']) : 0;
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'ujian';
$edit_soal_id = isset($_GET['edit_soal_id']) ? intval($_GET['edit_soal_id']) : 0;

if ($ujian_id == 0) {
    header("Location: dashboard_admin.php");
    exit();
}

$ujian_query = mysqli_query($conn, "SELECT u.*, mp.nama as mapel_nama FROM ujian u JOIN mata_pelajaran mp ON u.mata_pelajaran_id = mp.id WHERE u.id = $ujian_id");
$ujian = mysqli_fetch_assoc($ujian_query);

$ujian = mysqli_fetch_assoc(mysqli_query($conn, "SELECT u.*, mp.nama as mapel_nama, COALESCE(k.nama, u.kelas) as kelas_nama, u.kelas as kelas_id FROM ujian u JOIN mata_pelajaran mp ON u.mata_pelajaran_id = mp.id LEFT JOIN kelas k ON u.kelas = k.id WHERE u.id = $ujian_id"));

if (!$ujian) {
    header("Location: dashboard_admin.php");
    exit();
}

$result_mapel = mysqli_query($conn, "SELECT * FROM mata_pelajaran ORDER BY nama");
$result_soal = mysqli_query($conn, "SELECT * FROM soal WHERE ujian_id = $ujian_id ORDER BY id");
$soal_list = [];
while ($soal = mysqli_fetch_assoc($result_soal)) {
    $soal_list[] = $soal;
}
$total_soal = count($soal_list);

$edit_soal = null;
if ($edit_soal_id > 0) {
    $edit_soal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM soal WHERE id = $edit_soal_id"));
}

$kelas_list = mysqli_query($conn, "SELECT id, nama FROM kelas ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Soal - <?php echo $ujian['judul']; ?></title>
    <link rel="stylesheet" href="edit_soal_style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f7fa; color: #2d3748; }
.container { display: flex; min-height: 100vh; }
.sidebar { width: 260px; background: #fff; border-right: 1px solid #e2e8f0; padding: 2rem 0; transition: transform 0.3s; position: relative; }
.sidebar.closed { transform: translateX(-260px); }
.toggle-btn { position: absolute; right: -40px; top: 20px; width: 40px; height: 40px; background: #fff; border: 1px solid #e2e8f0; border-left: none; border-radius: 0 8px 8px 0; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; }
.sidebar-header { padding: 0 1.5rem 2rem; border-bottom: 1px solid #e2e8f0; }
.sidebar-header h3 { font-size: 1.25rem; margin-bottom: 0.5rem; }
.sidebar-header p { font-size: 0.875rem; color: #718096; }
.menu { list-style: none; padding: 1.5rem 0; }
.menu a { display: block; padding: 0.75rem 1.5rem; color: #4a5568; text-decoration: none; transition: all 0.2s; }
.menu a:hover, .menu a.active { background: #edf2f7; color: #2b6cb0; border-left: 3px solid #3182ce; }
.main { flex: 1; padding: 2rem; transition: margin-left 0.3s; }
.main.expanded { margin-left: -260px; }
.header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.header h1 { font-size: 1.875rem; font-weight: 600; }
.header p { color: #718096; font-size: 0.875rem; }
.back-btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem; background: #e2e8f0; color: #4a5568; text-decoration: none; border-radius: 8px; font-weight: 600; transition: background 0.2s; }
.back-btn:hover { background: #cbd5e0; }
.alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
.alert-success { background: #c6f6d5; color: #22543d; }
.alert-error { background: #fed7d7; color: #c53030; }
.tab-container { display: flex; gap: 1rem; margin-bottom: 2rem; }
.tab { padding: 0.875rem 1.5rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.2s; text-decoration: none; color: #4a5568; }
.tab:hover { background: #f7fafc; }
.tab.active { background: #3182ce; color: #fff; border-color: #3182ce; }
.info-card { background: #fff; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.info-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
.info-item { text-align: center; padding: 1rem; background: #f7fafc; border-radius: 8px; }
.info-item .label { font-size: 0.75rem; color: #718096; margin-bottom: 0.5rem; }
.info-item .value { font-size: 1.25rem; font-weight: 700; color: #2d3748; }
.form-card { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 800px; }
.form-group { margin-bottom: 1.5rem; }
.form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem; color: #2d3748; }
.form-control { width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; transition: border 0.2s; }
.form-control:focus { outline: none; border-color: #3182ce; }
textarea.form-control { resize: vertical; min-height: 100px; }
.form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
.pilihan-container { margin-bottom: 1rem; }
.pilihan-item { display: flex; gap: 0.75rem; margin-bottom: 0.75rem; align-items: center; }
.pilihan-item input { flex: 1; }
.btn-remove { padding: 0.5rem 0.75rem; background: #fed7d7; color: #c53030; border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem; }
.btn-remove:hover { background: #fc8181; }
.btn-add-pilihan { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; background: #e6fffa; color: #234e52; border: none; border-radius: 8px; cursor: pointer; font-size: 0.875rem; font-weight: 500; margin-bottom: 1rem; }
.btn-add-pilihan:hover { background: #b2f5ea; }
.radio-group { display: flex; gap: 1rem; flex-wrap: wrap; }
.radio-item { display: flex; align-items: center; gap: 0.5rem; }
.radio-item input { width: auto; }
.btn-primary { width: 100%; padding: 0.875rem; background: #3182ce; color: #fff; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.btn-primary:hover { background: #2c5aa0; }
.btn-secondary { width: 100%; padding: 0.875rem; background: #e2e8f0; color: #4a5568; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: background 0.2s; margin-top: 1rem; }
.btn-secondary:hover { background: #cbd5e0; }
.btn-danger { padding: 0.875rem; background: #fc8181; color: #fff; border: none; border-radius: 8px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
.btn-danger:hover { background: #f56565; }
.alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; }
.alert-success { background: #c6f6d5; color: #22543d; }
.alert-error { background: #fed7d7; color: #c53030; }
.soal-list { display: flex; flex-direction: column; gap: 1rem; }
.soal-card { background: #fff; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0; transition: all 0.2s; }
.soal-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.soal-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem; }
.soal-number { font-weight: 700; color: #3182ce; font-size: 1.125rem; }
.soal-actions { display: flex; gap: 0.5rem; }
.btn-edit, .btn-delete { padding: 0.5rem 0.75rem; border: none; border-radius: 6px; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s; }
.btn-edit { background: #bee3f8; color: #2c5aa0; }
.btn-edit:hover { background: #90cdf4; }
.btn-delete { background: #fed7d7; color: #c53030; }
.btn-delete:hover { background: #fc8181; }
.soal-text { font-size: 0.9rem; line-height: 1.6; margin-bottom: 1rem; color: #2d3748; }
.soal-options { display: grid; gap: 0.5rem; }
.option-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f7fafc; border-radius: 6px; font-size: 0.85rem; }
.option-item.correct { background: #c6f6d5; font-weight: 600; }
.option-letter { width: 28px; height: 28px; background: #e2e8f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #4a5568; font-size: 0.75rem; }
.option-item.correct .option-letter { background: #48bb78; color: #fff; }
.empty-state { text-align: center; padding: 3rem; color: #718096; }
.empty-state h3 { font-size: 1.25rem; margin-bottom: 0.5rem; }
.modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
.modal.active { display: flex; }
.modal-content { background: #fff; border-radius: 12px; padding: 2rem; max-width: 500px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.modal-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; }
.modal-icon { width: 48px; height: 48px; background: #fed7d7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
.modal-content h3 { margin-bottom: 1rem; color: #2d3748; font-size: 1.25rem; }
.modal-content p { color: #718096; margin-bottom: 0.75rem; line-height: 1.6; font-size: 0.875rem; }
.modal-list { background: #fef2f2; border-left: 3px solid #fc8181; padding: 1rem; margin: 1rem 0; border-radius: 6px; }
.modal-list li { color: #c53030; font-size: 0.875rem; margin-bottom: 0.5rem; }
.modal-actions { display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1.5rem; }
.btn-cancel { padding: 0.75rem 1.5rem; background: #e2e8f0; color: #4a5568; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.875rem; }
.btn-cancel:hover { background: #cbd5e0; }
.btn-confirm-delete { padding: 0.75rem 1.5rem; background: #fc8181; color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.875rem; }
.btn-confirm-delete:hover { background: #f56565; }

@media (max-width: 768px) {
    .sidebar { position: fixed; z-index: 100; height: 100vh; }
    .main { padding: 1.5rem; }
    .main.expanded { margin-left: 0; }
    .form-row { grid-template-columns: 1fr; }
    .info-grid { grid-template-columns: repeat(2, 1fr); }
    .tab-container { overflow-x: auto; }
}
</style>
</head>
<body>
    <div class="container">
        <aside class="sidebar" id="sidebar">
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
            <div class="sidebar-header">
                <h3>Ujian Online</h3>
                <p>Admin: <?php echo $_SESSION['nama_lengkap']; ?></p>
            </div>
            <ul class="menu">
                <li><a href="dashboard_admin.php">Dashboard</a></li>
                <li><a href="data_siswa.php">Data Siswa</a></li>
                <li><a href="tambah_soal.php">Tambah Soal</a></li>
                <li><a href="daftar_ujian.php" class="active">Daftar Ujian</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </aside>
        
        <main class="main" id="main">
            <div class="header">
                <div>
                    <h1>Edit Ujian</h1>
                    <p><?php echo $ujian['judul']; ?> - <?php echo $ujian['mapel_nama']; ?></p>
                </div>
                
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">✓ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">✗ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
            <div class="info-card">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Mata Pelajaran</div>
                        <div class="value"><?php echo $ujian['mapel_nama']; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label">Total Soal</div>
                        <div class="value"><?php echo $total_soal; ?>/<?php echo $ujian['jumlah_soal']; ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label">Durasi</div>
                        <div class="value"><?php echo $ujian['waktu_pengerjaan']; ?> Min</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Kelas</div>
                        <div class="value"><?php echo htmlspecialchars($ujian['kelas_nama'] ?? $ujian['kelas_id']); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="tab-container">
                <a href="edit_soal.php?ujian_id=<?php echo $ujian_id; ?>&mode=ujian" class="tab <?php echo $mode == 'ujian' ? 'active' : ''; ?>">📝 Detail Ujian</a>
                <a href="edit_soal.php?ujian_id=<?php echo $ujian_id; ?>&mode=soal" class="tab <?php echo $mode == 'soal' ? 'active' : ''; ?>">📋 Daftar Soal (<?php echo $total_soal; ?>)</a>
                <a href="edit_soal.php?ujian_id=<?php echo $ujian_id; ?>&mode=add" class="tab <?php echo $mode == 'add' ? 'active' : ''; ?>">➕ Tambah Soal Baru</a>
            </div>
            
            <?php if ($mode == 'ujian'): ?>
                <div class="form-card">
                    <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Edit Detail Ujian</h2>
                    <form method="POST">
                        <input type="hidden" name="step" value="update_ujian">
                        <input type="hidden" name="ujian_id" value="<?php echo $ujian_id; ?>">
                        
                        <div class="form-group">
                            <label>Mata Pelajaran</label>
                            <select name="mata_pelajaran_id" class="form-control" required>
                                <?php mysqli_data_seek($result_mapel, 0); while ($mapel = mysqli_fetch_assoc($result_mapel)): ?>
                                    <option value="<?php echo $mapel['id']; ?>" <?php echo $mapel['id'] == $ujian['mata_pelajaran_id'] ? 'selected' : ''; ?>><?php echo $mapel['nama']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Judul Ujian</label>
                            <input type="text" name="judul" class="form-control" value="<?php echo $ujian['judul']; ?>" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label>Jumlah Soal Target</label>
                                <input type="number" name="jumlah_soal" class="form-control" min="1" value="<?php echo $ujian['jumlah_soal']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Waktu Pengerjaan (menit)</label>
                                <input type="number" name="waktu_pengerjaan" class="form-control" min="1" value="<?php echo $ujian['waktu_pengerjaan']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Kelas</label>
                            <select name="kelas" class="form-control">
                                <option value="">-- Pilih Kelas --</option>
                                <?php mysqli_data_seek($kelas_list, 0); while ($k = mysqli_fetch_assoc($kelas_list)): ?>
                                    <option value="<?php echo intval($k['id']); ?>" <?php echo isset($ujian['kelas_id']) && $k['id']==$ujian['kelas_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($k['nama']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-primary">💾 Simpan Perubahan</button>
                    </form>
                </div>
                
            <?php elseif ($mode == 'soal'): ?>
                <div class="soal-list">
                    <?php if (empty($soal_list)): ?>
                        <div class="empty-state">
                            <h3>📝 Belum Ada Soal</h3>
                            <p>Silakan tambahkan soal baru untuk ujian ini</p>
                        </div>
                    <?php else: ?>
                        <?php $no = 1; foreach ($soal_list as $soal): ?>
                            <div class="soal-card">
                                <div class="soal-header">
                                    <span class="soal-number">Soal #<?php echo $no++; ?></span>
                                    <div class="soal-actions">
                                        <button class="btn-edit" onclick="window.location.href='edit_soal.php?ujian_id=<?php echo $ujian_id; ?>&mode=edit&edit_soal_id=<?php echo $soal['id']; ?>'">✏️ Edit</button>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus soal ini?')">
                                            <input type="hidden" name="soal_id" value="<?php echo $soal['id']; ?>">
                                            <input type="hidden" name="ujian_id" value="<?php echo $ujian_id; ?>">
                                            <button type="submit" name="delete_soal" class="btn-delete">🗑️ Hapus</button>
                                        </form>
                                    </div>
                                </div>
                                <div class="soal-text"><?php echo nl2br($soal['pertanyaan']); ?></div>
                                <div class="soal-options">
                                    <?php 
                                    $options = ['a' => $soal['pilihan_a'], 'b' => $soal['pilihan_b'], 'c' => $soal['pilihan_c'], 'd' => $soal['pilihan_d']];
                                    foreach ($options as $key => $value): 
                                        if (empty($value)) continue;
                                    ?>
                                        <div class="option-item <?php echo $key == $soal['jawaban_benar'] ? 'correct' : ''; ?>">
                                            <span class="option-letter"><?php echo strtoupper($key); ?></span>
                                            <span><?php echo $value; ?></span>
                                            <?php if ($key == $soal['jawaban_benar']): ?>
                                                <span style="margin-left: auto; color: #48bb78; font-weight: 600;">✓ Jawaban Benar</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
            <?php elseif ($mode == 'edit' && $edit_soal): ?>
                <div class="form-card">
                    <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Edit Soal</h2>
                    <form method="POST">
                        <input type="hidden" name="step" value="update_soal">
                        <input type="hidden" name="soal_id" value="<?php echo $edit_soal['id']; ?>">
                        <input type="hidden" name="ujian_id" value="<?php echo $ujian_id; ?>">
                        
                        <div class="form-group">
                            <label>Pertanyaan</label>
                            <textarea name="pertanyaan" class="form-control" required><?php echo $edit_soal['pertanyaan']; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Pilihan Jawaban</label>
                            <div class="pilihan-container" id="pilihanContainer">
                                <?php 
                                $pilihan_edit = [$edit_soal['pilihan_a'], $edit_soal['pilihan_b'], $edit_soal['pilihan_c'], $edit_soal['pilihan_d']];
                                $labels = ['A', 'B', 'C', 'D'];
                                for ($i = 0; $i < count($pilihan_edit); $i++): 
                                    if (empty($pilihan_edit[$i])) continue;
                                ?>
                                    <div class="pilihan-item">
                                        <input type="text" name="pilihan[]" class="form-control" placeholder="Pilihan <?php echo $labels[$i]; ?>" value="<?php echo $pilihan_edit[$i]; ?>" required>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Jawaban Benar</label>
                            <div class="radio-group">
                                <div class="radio-item">
                                    <input type="radio" name="jawaban_benar" value="a" id="j_a" <?php echo $edit_soal['jawaban_benar'] == 'a' ? 'checked' : ''; ?> required>
                                    <label for="j_a">A</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" name="jawaban_benar" value="b" id="j_b" <?php echo $edit_soal['jawaban_benar'] == 'b' ? 'checked' : ''; ?>>
                                    <label for="j_b">B</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" name="jawaban_benar" value="c" id="j_c" <?php echo $edit_soal['jawaban_benar'] == 'c' ? 'checked' : ''; ?>>
                                    <label for="j_c">C</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" name="jawaban_benar" value="d" id="j_d" <?php echo $edit_soal['jawaban_benar'] == 'd' ? 'checked' : ''; ?>>
                                    <label for="j_d">D</label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-primary">💾 Simpan Perubahan</button>
                        <button type="button" class="btn-secondary" onclick="window.location.href='edit_soal.php?ujian_id=<?php echo $ujian_id; ?>&mode=soal'">Batal</button>
                    </form>
                </div>
                
            <?php elseif ($mode == 'add'): ?>
                <div class="form-card">
                    <h2 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Tambah Soal Baru</h2>
                    <form method="POST" id="soalForm">
                        <input type="hidden" name="step" value="add_soal">
                        <input type="hidden" name="ujian_id" value="<?php echo $ujian_id; ?>">
                        
                        <div class="form-group">
                            <label>Pertanyaan</label>
                            <textarea name="pertanyaan" class="form-control" placeholder="Tuliskan pertanyaan di sini..." required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Pilihan Jawaban (minimal 2, maksimal 5)</label>
                            <div class="pilihan-container" id="pilihanContainer">
                                <div class="pilihan-item">
                                    <input type="text" name="pilihan[]" class="form-control" placeholder="Pilihan A" required>
                                    <button type="button" class="btn-remove" onclick="removePilihan(this)" style="visibility: hidden;">✕</button>
                                </div>
                                <div class="pilihan-item">
                                    <input type="text" name="pilihan[]" class="form-control" placeholder="Pilihan B" required>
                                    <button type="button" class="btn-remove" onclick="removePilihan(this)">✕</button>
                                </div>
                            </div>
                            <button type="button" class="btn-add-pilihan" onclick="addPilihan()" id="btnAddPilihan">
                                <span style="font-size: 1.25rem;">+</span> Tambah Pilihan
                            </button>
                        </div>
                        
                        <div class="form-group">
                            <label>Jawaban Benar</label>
                            <div class="radio-group" id="jawabanGroup">
                                <div class="radio-item">
                                    <input type="radio" name="jawaban_benar" value="a" id="jawaban_a" required>
                                    <label for="jawaban_a">A</label>
                                </div>
                                <div class="radio-item">
                                    <input type="radio" name="jawaban_benar" value="b" id="jawaban_b">
                                    <label for="jawaban_b">B</label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-primary">Tambah Soal</button>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-icon">⚠️</div>
                <h3>Hapus Ujian?</h3>
            </div>
            <p><strong>Anda akan menghapus ujian: "<?php echo $ujian['judul']; ?>"</strong></p>
            <p>Tindakan ini akan menghapus secara permanen:</p>
            <ul class="modal-list">
                <li>📝 <?php echo $total_soal; ?> soal dalam ujian ini</li>
                <li>📊 Semua riwayat ujian siswa</li>
                <li>✏️ Semua jawaban yang pernah dikerjakan siswa</li>
            </ul>
            <p style="color: #c53030; font-weight: 600;">⚠️ Tindakan ini tidak dapat dibatalkan!</p>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Batal</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="ujian_id" value="<?php echo $ujian_id; ?>">
                    <button type="submit" name="delete_ujian" class="btn-confirm-delete">Ya, Hapus Ujian</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('closed');
            document.getElementById('main').classList.toggle('expanded');
        }
        
        function showDeleteModal() {
            document.getElementById('deleteModal').classList.add('active');
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
        }
        
       
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        let pilihanCount = 2;
        const maxPilihan = 5;
        const labels = ['A', 'B', 'C', 'D', 'E'];

        function addPilihan() {
            if (pilihanCount >= maxPilihan) {
                alert('Maksimal 5 pilihan jawaban');
                return;
            }
            
            const container = document.getElementById('pilihanContainer');
            const newItem = document.createElement('div');
            newItem.className = 'pilihan-item';
            newItem.innerHTML = `
                <input type="text" name="pilihan[]" class="form-control" placeholder="Pilihan ${labels[pilihanCount]}" required>
                <button type="button" class="btn-remove" onclick="removePilihan(this)">✕</button>
            `;
            container.appendChild(newItem);
            
            const jawabanGroup = document.getElementById('jawabanGroup');
            const newRadio = document.createElement('div');
            newRadio.className = 'radio-item';
            newRadio.innerHTML = `
                <input type="radio" name="jawaban_benar" value="${labels[pilihanCount].toLowerCase()}" id="jawaban_${labels[pilihanCount].toLowerCase()}">
                <label for="jawaban_${labels[pilihanCount].toLowerCase()}">${labels[pilihanCount]}</label>
            `;
            jawabanGroup.appendChild(newRadio);
            
            pilihanCount++;
            
            if (pilihanCount >= maxPilihan) {
                document.getElementById('btnAddPilihan').style.display = 'none';
            }
        }

        function removePilihan(btn) {
            if (pilihanCount <= 2) {
                alert('Minimal 2 pilihan jawaban');
                return;
            }
            
            btn.parentElement.remove();
            const jawabanGroup = document.getElementById('jawabanGroup');
            jawabanGroup.removeChild(jawabanGroup.lastElementChild);
            
            pilihanCount--;
            document.getElementById('btnAddPilihan').style.display = 'inline-flex';
        }
    </script>
</body>
</html>