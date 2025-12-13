<?php
session_start();

if (!isset($_SESSION['admin_status']) || $_SESSION['admin_status'] != "login") {
    echo "<script>alert('Silakan login sebagai admin!'); window.location='../../admin/login.php';</script>";
    exit;
}

require_once '../../backend/config.php';

$stats = [
    'total_users' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'],
    'total_voted' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE has_voted = 1"))['count'],
    'total_candidates' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM candidates"))['count'],
    'total_votes' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM votes"))['count']
];

$stats['not_voted'] = $stats['total_users'] - $stats['total_voted'];
$stats['percentage'] = $stats['total_users'] > 0 ? round(($stats['total_voted'] / $stats['total_users']) * 100, 2) : 0;

$query_candidates = mysqli_query($conn, "
    SELECT 
        c.*,
        COUNT(v.id) as vote_count
    FROM candidates c
    LEFT JOIN votes v ON c.id = v.candidate_id
    GROUP BY c.id
    ORDER BY c.id ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - E-Voting Himatro</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #1e40af; --sidebar-bg: #1e293b; --bg-light: #f1f5f9; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg-light); display: flex; min-height: 100vh; }
        
        .sidebar { width: 280px; background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); color: white; position: fixed; height: 100vh; padding: 0; box-shadow: 4px 0 15px rgba(0,0,0,0.1); overflow-y: auto; }
        .sidebar-header { padding: 30px 25px; background: rgba(255,255,255,0.05); border-bottom: 1px solid rgba(255,255,255,0.1); }
        .logo-container { display: flex; align-items: center; gap: 15px; }
        .logo-img { width: 50px; height: 50px; object-fit: contain; }
        .logo-text h3 { font-size: 20px; font-weight: 700; color: white; margin-bottom: 5px; }
        .logo-text p { font-size: 12px; color: #94a3b8; }
        
        .sidebar-menu { padding: 20px 15px; }
        .menu-label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 600; padding: 0 10px; margin: 20px 0 10px; }
        .sidebar-menu a { display: flex; align-items: center; padding: 14px 15px; color: #cbd5e1; text-decoration: none; margin-bottom: 5px; border-radius: 10px; transition: 0.3s; font-size: 14px; font-weight: 500; }
        .sidebar-menu a i { width: 25px; font-size: 16px; margin-right: 12px; }
        .sidebar-menu a:hover { background: rgba(255,255,255,0.1); color: white; transform: translateX(5px); }
        .sidebar-menu a.active { background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%); color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
        
        .logout-section { padding: 20px 25px; border-top: 1px solid rgba(255,255,255,0.1); }
        .logout-section a { background: rgba(248, 113, 113, 0.1); color: #fca5a5; border: 1px solid rgba(248, 113, 113, 0.2); }
        
        .main-content { margin-left: 280px; flex: 1; padding: 30px; }
        .header { background: white; padding: 25px 30px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { font-size: 28px; color: #1e293b; }
        .header p { color: #64748b; margin-top: 5px; }
   
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border-left: 4px solid var(--primary); }
        .stat-card h3 { font-size: 14px; color: #64748b; margin-bottom: 10px; text-transform: uppercase; font-weight: 600; }
        .stat-card .stat-value { font-size: 36px; font-weight: 700; color: #1e293b; margin-bottom: 5px; }
        .stat-card .stat-label { font-size: 13px; color: #94a3b8; }

        .section-title { font-size: 22px; font-weight: 700; color: #1e293b; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0; }
        .btn-primary { padding: 12px 24px; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(30, 64, 175, 0.4); }
        
        table { width: 100%; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        thead { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; }
        th, td { padding: 15px; text-align: left; }
        tbody tr:hover { background: #f8fafc; }
        tbody tr { border-bottom: 1px solid #e2e8f0; }
        
        .btn-edit { padding: 8px 16px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; margin-right: 5px; }
        .btn-delete { padding: 8px 16px; background: #ef4444; color: white; border: none; border-radius: 6px; cursor: pointer; }
        
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <img src="../../assets/logo-himatro.png" alt="Logo Himatro" class="logo-img" onerror="this.style.display='none'">
                <div class="logo-text">
                    <h3>Admin Panel</h3>
                    <p>E-Voting Himatro</p>
                </div>
            </div>
        </div>
        
        <div class="sidebar-menu">
            <div class="menu-label">Main Menu</div>
            <a href="dashboard.php" class="active">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="users.php">
                <i class="fas fa-user-friends"></i> Data Pemilih
            </a>
            <a href="votes.php">
                <i class="fas fa-chart-bar"></i> Hasil Voting
            </a>
        </div>
        
        <div class="logout-section">
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <div>
                <h1>Dashboard Admin</h1>
                <p>Selamat datang, <?php echo $_SESSION['admin_name']; ?>!</p>
            </div>
            <div style="color: var(--primary); font-weight: 600;">
                <i class="far fa-calendar"></i> <?php echo date('d F Y'); ?>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Pemilih</h3>
                <div class="stat-value"><?php echo $stats['total_users']; ?></div>
                <div class="stat-label">Terdaftar</div>
            </div>
            <div class="stat-card">
                <h3>Sudah Memilih</h3>
                <div class="stat-value"><?php echo $stats['total_voted']; ?></div>
                <div class="stat-label"><?php echo $stats['percentage']; ?>% Partisipasi</div>
            </div>
            <div class="stat-card">
                <h3>Belum Memilih</h3>
                <div class="stat-value"><?php echo $stats['not_voted']; ?></div>
                <div class="stat-label">Pemilih</div>
            </div>
            <div class="stat-card">
                <h3>Total Kandidat</h3>
                <div class="stat-value"><?php echo $stats['total_candidates']; ?></div>
                <div class="stat-label">Pasangan</div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="section-title" style="margin: 0; padding: 0; border: none;">Data Kandidat</h2>
            <a href="kandidat_add.php" class="btn-primary">
                <i class="fas fa-plus"></i> Tambah Kandidat
            </a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Foto</th>
                    <th>Nama Ketua</th>
                    <th>Nama Wakil</th>
                    <th>Jumlah Suara</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($kandidat = mysqli_fetch_assoc($query_candidates)) : 
                    $chairman_initial = strtoupper(substr($kandidat['chairman_name'], 0, 1));
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td>
                        <?php if ($kandidat['chairman_photo']) : ?>
                            <img src="../../assets/img/kandidat/<?php echo $kandidat['chairman_photo']; ?>" 
                                 alt="<?php echo $kandidat['chairman_name']; ?>" 
                                 style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">
                        <?php else : ?>
                            <div style="width: 50px; height: 50px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #64748b;">
                                <?php echo $chairman_initial; ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $kandidat['chairman_name']; ?></td>
                    <td><?php echo $kandidat['vice_chairman_name']; ?></td>
                    <td><strong><?php echo $kandidat['vote_count']; ?></strong> suara</td>
                    <td>
                        <button class="btn-edit" onclick="window.location='kandidat_edit.php?id=<?php echo $kandidat['id']; ?>'">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn-delete" onclick="if(confirm('Yakin hapus kandidat ini?')) window.location='kandidat_delete.php?id=<?php echo $kandidat['id']; ?>'">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        setTimeout(() => location.reload(), 30000);
    </script>
</body>
</html>