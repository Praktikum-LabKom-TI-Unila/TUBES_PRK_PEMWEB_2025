<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        
        
        $check_username = mysqli_query($conn, "SELECT id FROM users WHERE username = '$username' AND id != $user_id");
        
        if (mysqli_num_rows($check_username) > 0) {
            $_SESSION['error'] = "Username sudah digunakan oleh user lain!";
        } else {
            $query = "UPDATE users SET nama_lengkap = '$nama_lengkap', username = '$username' WHERE id = $user_id";
            
            if (mysqli_query($conn, $query)) {
                $_SESSION['nama_lengkap'] = $nama_lengkap;
                $_SESSION['username'] = $username;
                $_SESSION['success'] = "Profile berhasil diperbarui!";
            } else {
                $_SESSION['error'] = "Gagal memperbarui profile: " . mysqli_error($conn);
            }
        }
        
        header("Location: profile.php");
        exit();
    }
    
    if (isset($_POST['change_password'])) {
        $password_lama = $_POST['password_lama'];
        $password_baru = $_POST['password_baru'];
        $konfirmasi_password = $_POST['konfirmasi_password'];
        

        $user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT password FROM users WHERE id = $user_id"));
        
    
        if (!password_verify($password_lama, $user_data['password'])) {
            $_SESSION['error'] = "Password lama tidak sesuai!";
        } elseif ($password_baru !== $konfirmasi_password) {
            $_SESSION['error'] = "Konfirmasi password tidak cocok!";
        } elseif (strlen($password_baru) < 6) {
            $_SESSION['error'] = "Password baru minimal 6 karakter!";
        } else {
            $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);
            $query = "UPDATE users SET password = '$hashed_password' WHERE id = $user_id";
            
            if (mysqli_query($conn, $query)) {
                $_SESSION['success'] = "Password berhasil diubah!";
            } else {
                $_SESSION['error'] = "Gagal mengubah password: " . mysqli_error($conn);
            }
        }
        
        header("Location: profile.php");
        exit();
    }
}


$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id"));


if ($role == 'siswa') {
    $stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
        COUNT(DISTINCT ujian_id) as ujian_selesai,
        IFNULL(AVG(skor), 0) as rata_rata,
        IFNULL(MAX(skor), 0) as tertinggi,
        COUNT(*) as total_percobaan
        FROM riwayat_ujian WHERE user_id = $user_id"));
    
    $recent_activity = mysqli_query($conn, "SELECT ru.*, u.judul as ujian_judul, mp.nama as mata_pelajaran 
        FROM riwayat_ujian ru
        JOIN ujian u ON ru.ujian_id = u.id
        JOIN mata_pelajaran mp ON u.mata_pelajaran_id = mp.id
        WHERE ru.user_id = $user_id
        ORDER BY ru.created_at DESC
        LIMIT 5");
} else {
    $stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
        (SELECT COUNT(*) FROM users WHERE role = 'siswa') as total_siswa,
        (SELECT COUNT(*) FROM ujian) as total_ujian,
        (SELECT COUNT(*) FROM soal) as total_soal,
        (SELECT COUNT(*) FROM riwayat_ujian) as total_pengerjaan"));
}

$dashboard_url = $role == 'admin' ? 'dashboard_admin.php' : 'dashboard_siswa.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - <?php echo $user['nama_lengkap']; ?></title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        :root {
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --bg-hover: #f1f5f9;
            --border: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --shadow: 0 1px 3px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.04);
            --shadow-lg: 0 4px 20px rgba(0,0,0,0.08), 0 8px 32px rgba(0,0,0,0.06);
            --accent: #6366f1;
            --accent-light: #eef2ff;
            --gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #a855f7 100%);
            --success: #10b981;
            --danger: #ef4444;
        }
        
        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-card: #1e293b;
            --bg-hover: #334155;
            --border: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #94a3b8;
            --shadow: 0 1px 3px rgba(0,0,0,0.3), 0 4px 12px rgba(0,0,0,0.2);
            --shadow-lg: 0 4px 20px rgba(0,0,0,0.4), 0 8px 32px rgba(0,0,0,0.3);
            --accent-light: rgba(99, 102, 241, 0.15);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .header h1 { font-size: 1.875rem; font-weight: 800; }
        .header p { color: var(--text-secondary); font-size: 0.875rem; margin-top: 0.25rem; }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }
        
        .back-btn:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
            transform: translateX(-4px);
        }
        
        .theme-toggle-btn {
            width: 44px;
            height: 44px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 1.25rem;
            margin-left: 0.5rem;
        }
        
        .theme-toggle-btn:hover {
            background: var(--accent);
            color: #fff;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .profile-layout {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 2rem;
        }
        

        .profile-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            height: fit-content;
            position: sticky;
            top: 2rem;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            margin: 0 auto 1.5rem;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 800;
            color: #fff;
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.3);
        }
        
        .profile-info {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .profile-name {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }
        
        .profile-role {
            display: inline-block;
            padding: 0.375rem 0.875rem;
            background: var(--accent-light);
            color: var(--accent);
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        
        .profile-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
        }
        
        .stat-box {
            text-align: center;
            padding: 1rem;
            background: var(--bg-hover);
            border-radius: 12px;
        }
        
        .stat-box .value {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }
        
        .stat-box .label {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }
        
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .edit-btn {
            padding: 0.625rem 1.25rem;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .edit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--text-primary);
        }
        
        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        .form-control:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        
        .btn-primary {
            padding: 0.875rem 1.5rem;
            background: var(--gradient);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
        }
        
        .btn-secondary {
            padding: 0.875rem 1.5rem;
            background: var(--bg-hover);
            color: var(--text-primary);
            border: none;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-secondary:hover {
            background: var(--border);
        }
        
        .form-section {
            display: none;
        }
        
        .form-section.active {
            display: block;
        }
        
        .activity-item {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            background: var(--bg-hover);
            border-radius: 12px;
            margin-bottom: 1rem;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            background: var(--accent-light);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        
        .activity-content {
            flex: 1;
        }
        
        .activity-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .activity-meta {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }
        
        .activity-score {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--accent);
        }
        
        @media (max-width: 968px) {
            .profile-layout {
                grid-template-columns: 1fr;
            }
            
            .profile-card {
                position: static;
            }
        }
        
        @media (max-width: 640px) {
            .container {
                padding: 1.5rem;
            }
            
            .profile-stats {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>Profile Saya 👤</h1>
                <p>Kelola informasi akun Anda</p>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <a href="<?php echo $dashboard_url; ?>" class="back-btn">
                    <span>←</span> Kembali
                </a>
                <div class="theme-toggle-btn" onclick="toggleTheme()">🌙</div>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <span>✓</span>
                <span><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <span>✗</span>
                <span><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></span>
            </div>
        <?php endif; ?>
        
        <div class="profile-layout">
            <div class="profile-card">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user['nama_lengkap'], 0, 1)); ?>
                </div>
                <div class="profile-info">
                    <h2 class="profile-name"><?php echo $user['nama_lengkap']; ?></h2>
                    <span class="profile-role">
                        <?php echo $role == 'admin' ? '👨‍💼 Administrator' : '👨‍🎓 Siswa'; ?>
                    </span>
                </div>
                
                <div class="profile-stats">
                    <?php if ($role == 'siswa'): ?>
                        <div class="stat-box">
                            <div class="value"><?php echo $stats['ujian_selesai']; ?></div>
                            <div class="label">Ujian Selesai</div>
                        </div>
                        <div class="stat-box">
                            <div class="value"><?php echo number_format($stats['rata_rata'], 0); ?></div>
                            <div class="label">Rata-rata</div>
                        </div>
                        <div class="stat-box">
                            <div class="value"><?php echo number_format($stats['tertinggi'], 0); ?></div>
                            <div class="label">Tertinggi</div>
                        </div>
                        <div class="stat-box">
                            <div class="value"><?php echo $stats['total_percobaan']; ?></div>
                            <div class="label">Total Percobaan</div>
                        </div>
                    <?php else: ?>
                        <div class="stat-box">
                            <div class="value"><?php echo $stats['total_siswa']; ?></div>
                            <div class="label">Total Siswa</div>
                        </div>
                        <div class="stat-box">
                            <div class="value"><?php echo $stats['total_ujian']; ?></div>
                            <div class="label">Total Ujian</div>
                        </div>
                        <div class="stat-box">
                            <div class="value"><?php echo $stats['total_soal']; ?></div>
                            <div class="label">Total Soal</div>
                        </div>
                        <div class="stat-box">
                            <div class="value"><?php echo $stats['total_pengerjaan']; ?></div>
                            <div class="label">Pengerjaan</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="main-content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Informasi Profile</h3>
                        <button class="edit-btn" onclick="toggleEditProfile()">
                            <span id="editProfileBtn">✏️ Edit Profile</span>
                        </button>
                    </div>
                    
                    <div id="viewProfile" class="form-section active">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <div class="form-control" style="background: var(--bg-hover);">
                                <?php echo $user['nama_lengkap']; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <div class="form-control" style="background: var(--bg-hover);">
                                <?php echo $user['username']; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <div class="form-control" style="background: var(--bg-hover);">
                                <?php echo $role == 'admin' ? 'Administrator' : 'Siswa'; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Terdaftar Sejak</label>
                            <div class="form-control" style="background: var(--bg-hover);">
                                <?php echo date('d F Y', strtotime($user['created_at'])); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div id="editProfile" class="form-section">
                        <form method="POST">
                            <div class="form-group">
                                <label>Nama Lengkap *</label>
                                <input type="text" name="nama_lengkap" class="form-control" value="<?php echo $user['nama_lengkap']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Username *</label>
                                <input type="text" name="username" class="form-control" value="<?php echo $user['username']; ?>" required>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn-secondary" onclick="toggleEditProfile()">Batal</button>
                                <button type="submit" name="update_profile" class="btn-primary">💾 Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ubah Password</h3>
                        <button class="edit-btn" onclick="toggleChangePassword()">
                            <span id="changePasswordBtn">🔒 Ubah Password</span>
                        </button>
                    </div>
                    
                    <div id="changePassword" class="form-section">
                        <form method="POST">
                            <div class="form-group">
                                <label>Password Lama *</label>
                                <input type="password" name="password_lama" class="form-control" placeholder="Masukkan password lama" required>
                            </div>
                            <div class="form-group">
                                <label>Password Baru * (min. 6 karakter)</label>
                                <input type="password" name="password_baru" class="form-control" placeholder="Masukkan password baru" required minlength="6">
                            </div>
                            <div class="form-group">
                                <label>Konfirmasi Password Baru *</label>
                                <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ulangi password baru" required>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn-secondary" onclick="toggleChangePassword()">Batal</button>
                                <button type="submit" name="change_password" class="btn-primary">🔐 Ubah Password</button>
                            </div>
                        </form>
                    </div>
                    
                    <div id="passwordInfo" class="form-section active">
                        <p style="color: var(--text-secondary); font-size: 0.875rem; line-height: 1.6;">
                            🔒 Untuk keamanan akun Anda, pastikan password memiliki minimal 6 karakter. 
                            Disarankan menggunakan kombinasi huruf besar, huruf kecil, angka, dan simbol.
                        </p>
                    </div>
                </div>
                
                <?php if ($role == 'siswa'): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Aktivitas Terakhir</h3>
                    </div>
                    
                    <?php if (mysqli_num_rows($recent_activity) > 0): ?>
                        <?php while ($activity = mysqli_fetch_assoc($recent_activity)): ?>
                            <div class="activity-item">
                                <div class="activity-icon">📝</div>
                                <div class="activity-content">
                                    <div class="activity-title"><?php echo $activity['ujian_judul']; ?></div>
                                    <div class="activity-meta">
                                        <?php echo $activity['mata_pelajaran']; ?> • 
                                        <?php echo date('d M Y, H:i', strtotime($activity['created_at'])); ?>
                                    </div>
                                </div>
                                <div class="activity-score"><?php echo number_format($activity['skor'], 0); ?></div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                            Belum ada aktivitas ujian
                        </p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>

        function toggleTheme() {
            const body = document.body;
            
            if (body.getAttribute('data-theme') === 'dark') {
                body.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
            } else {
                body.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
            }
        }
        
        if (localStorage.getItem('theme') === 'dark') {
            document.body.setAttribute('data-theme', 'dark');
        }
        
        function toggleEditProfile() {
            const viewProfile = document.getElementById('viewProfile');
            const editProfile = document.getElementById('editProfile');
            const editBtn = document.getElementById('editProfileBtn');
            
            if (viewProfile.classList.contains('active')) {
                viewProfile.classList.remove('active');
                editProfile.classList.add('active');
                editBtn.textContent = '❌ Batal Edit';
            } else {
                viewProfile.classList.add('active');
                editProfile.classList.remove('active');
                editBtn.textContent = '✏️ Edit Profile';
            }
        }
        
        
        function toggleChangePassword() {
            const passwordInfo = document.getElementById('passwordInfo');
            const changePassword = document.getElementById('changePassword');
            const changeBtn = document.getElementById('changePasswordBtn');
            
            if (passwordInfo.classList.contains('active')) {
                passwordInfo.classList.remove('active');
                changePassword.classList.add('active');
                changeBtn.textContent = '❌ Batal';
            } else {
                passwordInfo.classList.add('active');
                changePassword.classList.remove('active');
                changeBtn.textContent = '🔒 Ubah Password';
            
                changePassword.querySelector('form').reset();
            }
        }
    </script>
</body>
</html>