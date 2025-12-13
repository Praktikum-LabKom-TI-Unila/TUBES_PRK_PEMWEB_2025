<?php
session_start();
if (!isset($_SESSION['admin_status']) || $_SESSION['admin_status'] != "login") {
    header("Location: ../../admin/login.php");
    exit;
}

require_once '../../backend/config.php';

$query_users = mysqli_query($conn, "
    SELECT id, first_name, last_name, nim, email, has_voted 
    FROM users 
    ORDER BY id DESC
");

$total_users = mysqli_num_rows($query_users);
$total_voted = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE has_voted = 1"))['count'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pemilih - Admin E-Voting</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #1e40af; --sidebar-bg: #1e293b; --bg-light: #f1f5f9; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg-light); display: flex; min-height: 100vh; }
        
        .sidebar { width: 280px; background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%); color: white; position: fixed; height: 100vh; overflow-y: auto; }
        
        .sidebar-header { padding: 30px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .logo-container { display: flex; align-items: center; justify-content: center; }
        .logo-img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
        .logo-text { margin-left: 10px; text-align: left; }
        .logo-text h3 { margin-bottom: 5px; font-size: 18px; font-weight: 600; }
        .logo-text p { font-size: 14px; color: rgba(255,255,255,0.7); }
        
        .sidebar-menu { padding: 20px 0; }
        .menu-label { padding: 10px 20px; font-size: 14px; color: rgba(255,255,255,0.7); }
        .sidebar-menu a { display: flex; align-items: center; padding: 10px 20px; font-size: 16px; color: white; text-decoration: none; transition: background 0.3s; }
        .sidebar-menu a:hover { background: rgba(255,255,255,0.1); }
        .sidebar-menu a.active { background: rgba(255,255,255,0.2); }
        .sidebar-menu i { margin-right: 10px; }
        
        .logout-section { position: absolute; bottom: 20px; left: 0; right: 0; text-align: center; }
        .logout-section a { display: flex; align-items: center; justify-content: center; padding: 10px 20px; font-size: 16px; color: white; text-decoration: none; transition: background 0.3s; }
        .logout-section a:hover { background: rgba(255,255,255,0.1); }
        .logout-section i { margin-right: 8px; }
        
        .main-content { margin-left: 280px; flex: 1; padding: 30px; }
        .header { background: white; padding: 25px 30px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; border-left: 4px solid var(--primary); }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .stat-card h3 { font-size: 14px; color: #64748b; margin-bottom: 8px; }
        .stat-card .stat-value { font-size: 32px; font-weight: 700; color: #1e293b; }
        
        table { width: 100%; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        thead { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; }
        th, td { padding: 12px; text-align: left; }
        tbody tr:hover { background: #f8fafc; }
        tbody tr { border-bottom: 1px solid #e2e8f0; }
        
        .badge-voted { background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .badge-not-voted { background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }
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
            <a href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="users.php" class="active">
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
            <h1><i class="fas fa-user-friends"></i> Data Pemilih</h1>
            <p style="color: #64748b; margin-top: 5px;">Daftar mahasiswa yang terdaftar sebagai pemilih</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Terdaftar</h3>
                <div class="stat-value"><?php echo $total_users; ?></div>
            </div>
            <div class="stat-card">
                <h3>Sudah Memilih</h3>
                <div class="stat-value" style="color: #16a34a;"><?php echo $total_voted; ?></div>
            </div>
            <div class="stat-card">
                <h3>Belum Memilih</h3>
                <div class="stat-value" style="color: #dc2626;"><?php echo $total_users - $total_voted; ?></div>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 20px; color: #1e293b; font-weight: 700;">Daftar Pemilih</h3>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Status Voting</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    mysqli_data_seek($query_users, 0); // Reset pointer
                    $no = 1;
                    while ($user = mysqli_fetch_assoc($query_users)) : 
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><strong><?php echo $user['nim']; ?></strong></td>
                        <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td>
                            <?php if ($user['has_voted'] == 1) : ?>
                                <span class="badge-voted"><i class="fas fa-check"></i> Sudah Memilih</span>
                            <?php else : ?>
                                <span class="badge-not-voted"><i class="fas fa-times"></i> Belum Memilih</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>