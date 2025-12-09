<?php
// Memulai session
session_start();

// TEMPORARY: Disable login check for testing
// Set dummy session untuk testing
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Dummy user ID untuk testing
    $_SESSION['username'] = 'test_user';
    $_SESSION['nama'] = 'Pengguna';
    $_SESSION['email'] = 'user@silatium.com';
    $_SESSION['role'] = 'user';
}

// Include file koneksi database
require_once('../config/koneksi.php');

// Ambil user_id dari session
$user_id = $_SESSION['user_id'];

// Query untuk mengambil laporan milik user
$query = "SELECT id, judul_laporan, isi_laporan, status, tanggal, tanggal_update 
          FROM laporan 
          WHERE user_id = ? 
          ORDER BY tanggal DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Laporan Pengaduan - SILATIUM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 50%, #fef3f7 100%);
            background-attachment: fixed;
            min-height: 100vh;
            color: #1e293b;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 20%, rgba(59, 130, 246, 0.08) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(16, 185, 129, 0.08) 0%, transparent 50%);
            z-index: -1;
            animation: bgFloat 20s ease-in-out infinite;
        }

        @keyframes bgFloat {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(5deg); }
            66% { transform: translate(-20px, 20px) rotate(-5deg); }
        }

        /* Navbar Styles */
        .navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 18px 48px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06), 0 1px 4px rgba(0,0,0,0.04);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            position: sticky;
            top: 0;
            z-index: 100;
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            box-shadow: 0 3px 8px rgba(59, 130, 246, 0.25);
        }

        .brand-text h1 {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .brand-text p {
            font-size: 11px;
            color: #64748b;
            margin-top: 1px;
            font-weight: 500;
        }

        .navbar-menu {
            display: flex;
            gap: 32px;
            align-items: center;
        }

        .navbar-menu a {
            text-decoration: none;
            color: #475569;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            position: relative;
            padding: 6px 0;
        }

        .navbar-menu a:hover {
            color: #3b82f6;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-left: 24px;
            border-left: 1px solid #e2e8f0;
            margin-left: 8px;
        }

        .user-info-nav {
            text-align: right;
            line-height: 1.3;
        }

        .user-info-nav strong {
            display: block;
            font-size: 13px;
            color: #0f172a;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .user-info-nav small {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
            border: 2px solid white;
        }

        .btn-logout {
            padding: 8px 16px;
            background: #0f172a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
            letter-spacing: 0.2px;
        }

        .btn-logout:hover {
            background: #1e293b;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 48px 48px 64px 48px;
            animation: fadeInUp 0.6s ease-out 0.2s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #3b82f6;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
            transition: all 0.2s ease;
        }

        .back-link:hover {
            color: #2563eb;
            gap: 10px;
        }

        .back-link::before {
            content: '←';
            transition: transform 0.2s ease;
        }

        .back-link:hover::before {
            transform: translateX(-3px);
        }

        .page-header {
            margin-bottom: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .page-header-content {
            flex: 1;
        }

        .page-header h2 {
            font-size: 24px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .page-header p {
            font-size: 14px;
            color: #64748b;
            line-height: 1.5;
            font-weight: 400;
        }

        .btn-create {
            padding: 10px 20px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
            letter-spacing: 0.2px;
            white-space: nowrap;
        }

        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.4);
        }

        .form-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
            border: 1px solid rgba(241, 245, 249, 0.8);
            margin-bottom: 24px;
        }

        .form-card-header {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #334155;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
            background: white;
        }

        .form-control::placeholder {
            color: #94a3b8;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
            line-height: 1.6;
        }

        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn {
            padding: 11px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        }

        .btn-secondary {
            background: white;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .list-header {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin: 36px 0 16px 0;
            letter-spacing: -0.2px;
        }

        .laporan-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 16px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(15, 23, 42, 0.02);
            border: 1px solid rgba(241, 245, 249, 0.8);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            animation: cardSlideIn 0.5s ease-out both;
        }

        .laporan-card:hover {
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(59, 130, 246, 0.1);
            transform: translateY(-4px) scale(1.01);
            border-color: rgba(59, 130, 246, 0.2);
        }

        @keyframes cardSlideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
            gap: 16px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.4;
            letter-spacing: -0.2px;
            flex: 1;
            margin-bottom: 2px;
        }

        .card-body {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 16px;
            line-height: 1.6;
            font-weight: 400;
        }

        .card-footer {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
            margin-top: 4px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.3px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
            transition: all 0.2s ease;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .status-pending {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid #fcd34d;
            box-shadow: 0 2px 8px rgba(252, 211, 77, 0.4);
        }

        .status-diproses {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border: 1px solid #93c5fd;
            box-shadow: 0 2px 8px rgba(147, 197, 253, 0.4);
        }

        .status-selesai {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1px solid #6ee7b7;
            box-shadow: 0 2px 8px rgba(110, 231, 183, 0.4);
        }

        .status-ditolak {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
            border: 1px solid #fca5a5;
            box-shadow: 0 2px 8px rgba(252, 165, 165, 0.4);
        }

        .empty-state {
            text-align: center;
            padding: 64px 40px;
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(249,250,251,0.95) 100%);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(229, 231, 235, 0.6);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        }

        .empty-state-icon {
            font-size: 64px;
            display: inline-block;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin: 16px 0 8px 0;
            letter-spacing: -0.3px;
        }

        .empty-state p {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 24px;
            line-height: 1.5;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-brand">
            <div class="logo">📋</div>
            <div class="brand-text">
                <h1>SILATIUM</h1>
                <p>Sistem Informasi Transportasi Umum</p>
            </div>
        </div>
        <div class="navbar-menu">
            <a href="#">Dashboard</a>
            <a href="#">Lihat Transportasi</a>
            <div class="user-menu">
                <div class="user-info-nav">
                    <strong>Pengguna</strong>
                    <small>user@silatium.com</small>
                </div>
                <div class="user-avatar">P</div>
                <button class="btn-logout">Keluar</button>
            </div>
        </div>
    </nav>

    <div class="container">
        <a href="list_laporan_user.php" class="back-link">Kembali ke Dashboard</a>
        
        <div class="page-header">
            <div class="page-header-content">
                <h2>Laporan Pengaduan</h2>
                <p>Buat dan kelola laporan pengaduan Anda</p>
            </div>
            <a href="list_laporan_user.php" class="btn-create">
                <span>+</span>
                Buat Laporan Baru
            </a>
        </div>

        <div class="form-card">
            <div class="form-card-header">Form Laporan Baru</div>

            <form id="formLaporan" method="POST" action="proses_laporan.php">
                <div class="form-group">
                    <label for="judul_laporan">Judul Laporan</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="judul_laporan" 
                        name="judul_laporan" 
                        placeholder="Masukkan judul laporan" 
                        required
                        maxlength="255"
                    >
                </div>

                <div class="form-group">
                    <label for="isi_laporan">Isi Laporan</label>
                    <textarea 
                        class="form-control" 
                        id="isi_laporan" 
                        name="isi_laporan" 
                        placeholder="Jelaskan detail masalah atau pengaduan Anda" 
                        required
                        maxlength="2000"
                    ></textarea>
                </div>

                <div class="btn-group">
                    <a href="list_laporan_user.php" class="btn btn-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="22" y1="2" x2="11" y2="13"></line>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                        </svg>
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>

        <div class="list-header">Daftar Laporan Saya</div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): 
                $status_class = 'status-' . $row['status'];
                $isi_preview = strlen($row['isi_laporan']) > 100 ? substr($row['isi_laporan'], 0, 100) . '...' : $row['isi_laporan'];
                $tanggal = date('d F Y', strtotime($row['tanggal']));
            ?>
                <div class="laporan-card">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo htmlspecialchars($row['judul_laporan']); ?></h3>
                        <span class="status-badge <?php echo $status_class; ?>">
                            <?php 
                                $status_display = $row['status'];
                                if ($status_display == 'pending') echo 'Pending';
                                elseif ($status_display == 'diproses') echo 'Diproses';
                                elseif ($status_display == 'selesai') echo 'Selesai';
                                elseif ($status_display == 'ditolak') echo 'Ditolak';
                            ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <?php echo htmlspecialchars($isi_preview); ?>
                    </div>
                    <div class="card-footer">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        Dilaporkan pada: <?php echo $tanggal; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📭</div>
                <h3>Belum Ada Laporan</h3>
                <p>Anda belum membuat laporan pengaduan. Silakan isi form di atas untuk membuat laporan baru.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Form validation
        const form = document.getElementById('formLaporan');

        form.addEventListener('submit', function(e) {
            const judul = document.getElementById('judul_laporan').value.trim();
            const isi = document.getElementById('isi_laporan').value.trim();

            // Validasi judul
            if (judul.length < 5) {
                e.preventDefault();
                alert('Judul laporan minimal 5 karakter');
                return false;
            }

            // Validasi isi
            if (isi.length < 10) {
                e.preventDefault();
                alert('Isi laporan minimal 10 karakter');
                return false;
            }
        });
    </script>
</body>
</html>
