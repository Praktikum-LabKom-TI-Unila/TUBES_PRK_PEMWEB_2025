<?php
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

$message = '';
$edit_mode = false;
$edit_data = null;

// Handle DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM transportasi WHERE id = ?");
    $stmt->execute([$id]);
    $message = "Data berhasil dihapus.";
}

// Handle EDIT fetching
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM transportasi WHERE id = ?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
    if ($edit_data) {
        $edit_mode = true;
    }
}

// Handle POST (Create/Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama_transportasi']);
    $rute = sanitize($_POST['rute']);
    $jadwal = sanitize($_POST['jadwal']);
    $keterangan = sanitize($_POST['keterangan']);

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update
        $stmt = $pdo->prepare("UPDATE transportasi SET nama_transportasi=?, rute=?, jadwal=?, keterangan=? WHERE id=?");
        $stmt->execute([$nama, $rute, $jadwal, $keterangan, $_POST['id']]);
        $message = "Data berhasil diperbarui.";
    } else {
        // Create
        $stmt = $pdo->prepare("INSERT INTO transportasi (nama_transportasi, rute, jadwal, keterangan) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nama, $rute, $jadwal, $keterangan]);
        $message = "Data berhasil ditambahkan.";
    }
    // Redirect to clear query params or just reload list logic
    if ($edit_mode) {
        header("Location: transportasi.php");
        exit;
    }
}

// Get Data
$stmt = $pdo->query("SELECT * FROM transportasi ORDER BY id DESC");
$transportasi = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Transportasi - SILATIUM</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/icons/dashboard.svg" alt="Logo" class="icon" style="color: var(--primary-color);">
                SILATIUM Admin
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-link">
                    <img src="../assets/icons/dashboard.svg" class="icon" alt=""> Dashboard
                </a>
                <a href="transportasi.php" class="nav-link active">
                    <img src="../assets/icons/bus.svg" class="icon" alt=""> Manajemen Transportasi
                </a>
                <a href="laporan.php" class="nav-link">
                    <img src="../assets/icons/file-text.svg" class="icon" alt=""> Daftar Laporan
                </a>
                <a href="../auth/logout.php" class="nav-link logout">
                    <img src="../assets/icons/log-out.svg" class="icon" alt=""> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Wrapper -->
        <div class="main-wrapper">
            <!-- Topbar -->
            <header class="topbar">
                <button id="sidebarToggle">
                    <img src="../assets/icons/menu.svg" class="icon" alt="Menu">
                </button>
                <div class="user-profile">
                    <div class="user-avatar">
                        <img src="../assets/icons/user.svg" class="icon" alt="User">
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?= htmlspecialchars($_SESSION['name']) ?></span>
                        <span class="user-role">Administrator</span>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="main-content">
                <div class="flex justify-between items-center mb-4">
                    <h2>Manajemen Transportasi</h2>
                </div>

                <?php if ($message): ?>
                    <div
                        style="background-color: var(--success-bg); color: var(--success-text); padding: 1rem; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <div class="grid" style="grid-template-columns: 1fr 2fr; gap: 1.5rem; align-items: start;">
                    <!-- Form Card -->
                    <div class="card">
                        <h3 class="mb-4"><?= $edit_mode ? 'Edit Transportasi' : 'Tambah Baru' ?></h3>
                        <form method="POST" action="">
                            <?php if ($edit_mode): ?>
                                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                            <?php endif; ?>

                            <div class="form-group">
                                <label>Nama Transportasi</label>
                                <input type="text" name="nama_transportasi"
                                    value="<?= $edit_mode ? $edit_data['nama_transportasi'] : '' ?>" required
                                    placeholder="Mis: Bus Trans Kota">
                            </div>
                            <div class="form-group">
                                <label>Rute</label>
                                <textarea name="rute" required rows="3"
                                    placeholder="Mis: Terminal A - Pasar B - Stasiun C"><?= $edit_mode ? $edit_data['rute'] : '' ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Jadwal</label>
                                <input type="text" name="jadwal" value="<?= $edit_mode ? $edit_data['jadwal'] : '' ?>"
                                    required placeholder="Mis: 06:00 - 18:00 WIB">
                            </div>
                            <div class="form-group">
                                <label>Keterangan</label>
                                <textarea name="keterangan" rows="2"
                                    placeholder="Catatan tambahan..."><?= $edit_mode ? $edit_data['keterangan'] : '' ?></textarea>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" class="btn btn-primary" style="flex: 1;">
                                    <?php if ($edit_mode): ?>
                                        <img src="../assets/icons/check-circle.svg" class="icon" alt=""> Simpan
                                    <?php else: ?>
                                        <img src="../assets/icons/plus.svg" class="icon" alt=""> Tambah
                                    <?php endif; ?>
                                </button>
                                <?php if ($edit_mode): ?>
                                    <a href="transportasi.php" class="btn btn-secondary">Batal</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>

                    <!-- Table Card -->
                    <div class="card" style="overflow: hidden;">
                        <h3 class="mb-4">Daftar Rute</h3>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="25%">Nama</th>
                                        <th width="30%">Rute</th>
                                        <th width="20%">Jadwal</th>
                                        <th width="20%" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($transportasi as $index => $t): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td style="font-weight: 500;"><?= htmlspecialchars($t['nama_transportasi']) ?>
                                            </td>
                                            <td style="color: var(--text-muted); font-size: 0.85rem;">
                                                <?= htmlspecialchars($t['rute']) ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-received"
                                                    style="font-weight: normal;"><?= htmlspecialchars($t['jadwal']) ?></span>
                                            </td>
                                            <td>
                                                <div class="flex gap-2 justify-center">
                                                    <a href="?edit=<?= $t['id'] ?>" class="btn btn-secondary"
                                                        style="padding: 0.4rem;" title="Edit">
                                                        <img src="../assets/icons/edit.svg" class="icon" alt="Edit">
                                                    </a>
                                                    <a href="?delete=<?= $t['id'] ?>" class="btn btn-danger"
                                                        style="padding: 0.4rem;"
                                                        onclick="return confirm('Yakin ingin menghapus?')" title="Hapus">
                                                        <img src="../assets/icons/trash.svg" class="icon" alt="Hapus">
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if (count($transportasi) == 0): ?>
                                <div class="text-center" style="padding: 2rem; color: var(--text-muted);">
                                    <img src="../assets/icons/bus.svg"
                                        style="width: 48px; opacity: 0.2; margin-bottom: 0.5rem;">
                                    <p>Belum ada data transportasi.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="../assets/js/script.js"></script>
</body>

</html>