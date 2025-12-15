<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $siswa_id = intval($_POST['siswa_id']);

        switch ($_POST['action']) {
            case 'delete':
                mysqli_query($conn, "DELETE FROM users WHERE id = $siswa_id AND role = 'siswa'");
                $_SESSION['success'] = "Siswa berhasil dihapus!";
                break;

            case 'update':
                $nama = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
                $username = mysqli_real_escape_string($conn, $_POST['username']);
                // ambil kelas dari POST, kalau kosong set NULL
                $kelas_post = isset($_POST['kelas']) && $_POST['kelas'] !== '' ? intval($_POST['kelas']) : null;
                $kelas_sql = is_null($kelas_post) ? "NULL" : $kelas_post;
                mysqli_query($conn, "UPDATE users SET nama_lengkap = '$nama', username = '$username', kelas = $kelas_sql WHERE id = $siswa_id");
                $_SESSION['success'] = "Data siswa berhasil diperbarui!";
                break;
        }
        header("Location: data_siswa.php");
        exit();
    }
}

// Ambil data siswa
// join ke tabel kelas supaya menampilkan nama kelas
$result_siswa = mysqli_query($conn, "SELECT u.*, k.nama as kelas_nama FROM users u LEFT JOIN kelas k ON u.kelas = k.id WHERE u.role = 'siswa' ORDER BY u.created_at DESC");
$total_siswa = mysqli_num_rows($result_siswa);

// ambil daftar kelas untuk option select di modal edit (jika tabel kelas ada)
$kelas_exists = mysqli_num_rows(mysqli_query($conn, "SHOW TABLES LIKE 'kelas'")) > 0;
if ($kelas_exists) {
    $kelas_list = mysqli_query($conn, "SELECT id, nama FROM kelas ORDER BY nama");
} else {
    $kelas_list = false;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            color: #2d3748;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: #fff;
            border-right: 1px solid #e2e8f0;
            padding: 2rem 0;
            transition: transform 0.3s;
            position: relative;
        }

        .sidebar.closed {
            transform: translateX(-260px);
        }

        .toggle-btn {
            position: absolute;
            right: -40px;
            top: 20px;
            width: 40px;
            height: 40px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-left: none;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .sidebar-header {
            padding: 0 1.5rem 2rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .sidebar-header h3 {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-header p {
            font-size: 0.875rem;
            color: #718096;
        }

        .menu {
            list-style: none;
            padding: 1.5rem 0;
        }

        .menu a {
            display: block;
            padding: 0.75rem 1.5rem;
            color: #4a5568;
            text-decoration: none;
            transition: all 0.2s;
        }

        .menu a:hover,
        .menu a.active {
            background: #edf2f7;
            color: #2b6cb0;
            border-left: 3px solid #3182ce;
        }

        .main {
            flex: 1;
            padding: 2rem;
            transition: margin-left 0.3s;
        }

        .main.expanded {
            margin-left: -260px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            font-size: 1.875rem;
            font-weight: 600;
        }

        .header p {
            color: #718096;
            font-size: 0.875rem;
        }

        .stat-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #fff;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            font-weight: 600;
            color: #3182ce;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #c6f6d5;
            color: #22543d;
        }

        .search-box {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-box input {
            width: 100%;
            max-width: 400px;
            padding: 0.625rem 0.625rem 0.625rem 2.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .search-box::before {
            content: "🔍";
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
        }

        .table-wrapper {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f7fafc;
        }

        th {
            padding: 1rem;
            text-align: left;
            font-size: 0.875rem;
            font-weight: 600;
            color: #4a5568;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid #f7fafc;
            font-size: 0.875rem;
        }

        tbody tr:hover {
            background: #f7fafc;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            padding: 0.5rem 0.75rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .btn-view {
            background: #ebf8ff;
            color: #2c5aa0;
        }

        .btn-view:hover {
            background: #bee3f8;
        }

        .btn-edit {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-edit:hover {
            background: #fde68a;
        }

        .btn-delete {
            background: #fee;
            color: #c53030;
        }

        .btn-delete:hover {
            background: #fed7d7;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: #fff;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            font-size: 1.25rem;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #718096;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .btn-primary {
            padding: 0.75rem 1.5rem;
            background: #3182ce;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: #2c5aa0;
        }

        .btn-secondary {
            padding: 0.75rem 1.5rem;
            background: #e2e8f0;
            color: #4a5568;
            border: none;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f7fafc;
        }

        .detail-label {
            color: #718096;
            font-size: 0.875rem;
        }

        .detail-value {
            font-weight: 600;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #718096;
        }

        .empty-state h3 {
            margin: 1rem 0 0.5rem;
            color: #2d3748;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                z-index: 100;
                height: 100vh;
            }

            .main {
                padding: 1.5rem;
            }

            .main.expanded {
                margin-left: 0;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
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
                <li><a href="data_siswa.php" class="active">Data Siswa</a></li>
                <li><a href="daftar_ujian.php">Daftar Ujian</a></li>
                <li><a href="tambah_mata_pelajaran.php">Tambah Mata Pelajaran</a></li>
                <li><a href="tambah_soal.php">Tambah Soal</a></li>
                <li><a href="../siswa/profile.php">Profile</a></li>
                <li><a href="../auth/logout.php">Logout</a></li>
            </ul>
        </aside>

        <main class="main" id="main">
            <div class="header">
                <div>
                    <h1>Data Siswa</h1>
                    <p>Kelola data siswa yang terdaftar</p>
                </div>
                <div class="stat-badge">
                    👥 <?php echo $total_siswa; ?> Siswa
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    ✓ <?php echo $_SESSION['success'];
                        unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <div class="search-box">
                <input type="text" id="searchInput" placeholder="Cari siswa...">
            </div>

            <div class="table-wrapper">
                <table id="siswaTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Kelas</th>
                            <th>Tanggal Daftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($total_siswa > 0):
                            $no = 1;
                            while ($siswa = mysqli_fetch_assoc($result_siswa)):
                        ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><strong><?php echo htmlspecialchars($siswa['nama_lengkap']); ?></strong></td>
                                    <td>@<?php echo htmlspecialchars($siswa['username']); ?></td>
                                    <td><?php echo $siswa['kelas_nama'] ? htmlspecialchars($siswa['kelas_nama']) : '—'; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($siswa['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view" onclick="viewSiswa(<?php echo $siswa['id']; ?>, '<?php echo addslashes($siswa['nama_lengkap']); ?>', '@<?php echo $siswa['username']; ?>', '<?php echo date('d/m/Y H:i', strtotime($siswa['created_at'])); ?>', '<?php echo addslashes($siswa['kelas_nama'] ?? ''); ?>')">
                                                👁️ Detail
                                            </button>
                                            <button class="btn-action btn-edit" onclick="editSiswa(<?php echo $siswa['id']; ?>, '<?php echo addslashes($siswa['nama_lengkap']); ?>', '<?php echo $siswa['username']; ?>', <?php echo is_null($siswa['kelas']) ? 'null' : intval($siswa['kelas']); ?>)">
                                                ✏️ Edit
                                            </button>
                                            <button class="btn-action btn-delete" onclick="deleteSiswa(<?php echo $siswa['id']; ?>, '<?php echo addslashes($siswa['nama_lengkap']); ?>')">
                                                🗑️ Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile;
                        else: ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <div style="font-size: 3rem;">👥</div>
                                        <h3>Belum ada siswa terdaftar</h3>
                                        <p>Siswa yang mendaftar akan muncul di sini</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Modal View -->
    <div class="modal" id="viewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Detail Siswa</h3>
                <button class="close-btn" onclick="closeModal('viewModal')">×</button>
            </div>
            <div class="modal-body" id="viewContent"></div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('viewModal')">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Data Siswa</h3>
                <button class="close-btn" onclick="closeModal('editModal')">×</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="siswa_id" id="editId">
                    <input type="hidden" name="action" value="update">

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" id="editNama" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" id="editUsername" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Kelas</label>
                        <select name="kelas" id="editKelas" class="form-control">
                            <option value="">-- Pilih Kelas --</option>
                            <?php
                            // reset pointer / re-query saja jika tabel kelas ada
                            if ($kelas_exists) {
                                $kelas_list2 = mysqli_query($conn, "SELECT id, nama FROM kelas ORDER BY nama");
                                while ($k = mysqli_fetch_assoc($kelas_list2)):
                            ?>
                                <option value="<?php echo intval($k['id']); ?>"><?php echo htmlspecialchars($k['nama']); ?></option>
                            <?php
                                endwhile;
                            } else {
                                // fallback: kosongkan select (opsional: tambahkan satu option seperti `-- Tidak ada kelas --`)
                                echo '<option value="">-- Tidak ada kelas --</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('editModal')">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Konfirmasi Hapus</h3>
                <button class="close-btn" onclick="closeModal('deleteModal')">×</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus siswa ini?</p>
                <p><strong id="deleteName"></strong></p>
                <p style="color: #e53e3e; font-size: 0.875rem; margin-top: 1rem;">⚠️ Data yang dihapus tidak dapat dikembalikan!</p>
            </div>
            <div class="modal-footer">
                <form method="POST" style="display: flex; gap: 0.75rem; width: 100%; justify-content: flex-end;">
                    <input type="hidden" name="siswa_id" id="deleteId">
                    <input type="hidden" name="action" value="delete">
                    <button type="button" class="btn-secondary" onclick="closeModal('deleteModal')">Batal</button>
                    <button type="submit" class="btn-delete" style="padding: 0.75rem 1.5rem;">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            sidebar.classList.toggle('closed');
            main.classList.toggle('expanded');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }

        function viewSiswa(id, nama, username, tanggal, kelas) {
            const content = document.getElementById('viewContent');

            
            fetch('get_siswa_stats.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    content.innerHTML = `
                        <div class="detail-row">
                            <span class="detail-label">Nama Lengkap</span>
                            <span class="detail-value">${nama}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Username</span>
                            <span class="detail-value">${username}</span>
                        </div>
                        <div class="detail-row">
                           <span class="detail-label">Kelas</span>
                           <span class="detail-value">${kelas || '—'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Tanggal Daftar</span>
                            <span class="detail-value">${tanggal}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Total Ujian Dikerjakan</span>
                            <span class="detail-value">${data.total_ujian || 0} ujian</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Rata-rata Nilai</span>
                            <span class="detail-value">${data.rata_rata || 0}</span>
                        </div>
                        <div class="detail-row" style="border-bottom: none;">
                            <span class="detail-label">Nilai Tertinggi</span>
                            <span class="detail-value">${data.tertinggi || 0}</span>
                        </div>
                    `;
                })
                .catch(() => {
                    content.innerHTML = `
                        <div class="detail-row">
                            <span class="detail-label">Nama Lengkap</span>
                            <span class="detail-value">${nama}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Username</span>
                            <span class="detail-value">${username}</span>
                        </div>
                       <div class="detail-row">
                           <span class="detail-label">Kelas</span>
                           <span class="detail-value">${kelas || '—'}</span>
                       </div>
                        <div class="detail-row" style="border-bottom: none;">
                            <span class="detail-label">Tanggal Daftar</span>
                            <span class="detail-value">${tanggal}</span>
                        </div>
                    `;
                });

            openModal('viewModal');
        }

        function editSiswa(id, nama, username, kelasId) {
            document.getElementById('editId').value = id;
            document.getElementById('editNama').value = nama;
            document.getElementById('editUsername').value = username;
            document.getElementById('editKelas').value = kelasId === null ? '' : kelasId;
            openModal('editModal');
        }


        function deleteSiswa(id, nama) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteName').textContent = nama;
            openModal('deleteModal');
        }

        searchInput.oninput = function() {
            const term = this.value.toLowerCase();
            [...document.querySelectorAll('#siswaTable tbody tr')].forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(term) ? '' : 'none';
            });
        };

        // Close modal on outside click
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });
    </script>
</body>

</html>