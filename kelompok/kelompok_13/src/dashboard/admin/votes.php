<?php
session_start();
if (!isset($_SESSION['admin_status']) || $_SESSION['admin_status'] != "login") {
    header("Location: ../../admin/login.php");
    exit;
}

require_once '../../backend/config.php';

$query_results = mysqli_query($conn, "
    SELECT 
        c.id,
        c.chairman_name,
        c.vice_chairman_name,
        c.chairman_photo,
        COUNT(v.id) as vote_count
    FROM candidates c
    LEFT JOIN votes v ON c.id = v.candidate_id
    GROUP BY c.id
    ORDER BY vote_count DESC, c.id ASC
");

$total_votes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM votes"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Voting - Admin E-Voting</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .logout-section a { display: flex; align-items: center; justify-content: center; padding: 10px 20px; font-size: 16px; color: white; text-decoration: none; }
        .logout-section i { margin-right: 8px; }
        
        .main-content { margin-left: 280px; flex: 1; padding: 30px; }
        .header { background: white; padding: 25px 30px; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; border-left: 4px solid var(--primary); }
        
        .chart-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .results-table {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        table { width: 100%; border-collapse: collapse; }
        thead { background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; }
        th, td { padding: 15px; text-align: left; }
        tbody tr { border-bottom: 1px solid #e2e8f0; }
        tbody tr:hover { background: #f8fafc; }
        
        .candidate-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .progress-bar {
            height: 30px;
            background: #e2e8f0;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
            transition: width 0.5s ease;
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
            <a href="dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="users.php">
                <i class="fas fa-user-friends"></i> Data Pemilih
            </a>
            <a href="votes.php" class="active">
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
            <h1><i class="fas fa-chart-pie"></i> Hasil Voting Real-Time</h1>
            <p style="color: #64748b; margin-top: 5px;">Total Suara Masuk: <strong><?php echo $total_votes; ?></strong></p>
        </div>

        <div class="chart-container">
            <h3 style="margin-bottom: 20px; color: #1e293b;">Grafik Perolehan Suara</h3>
            <canvas id="voteChart" width="400" height="150"></canvas>
        </div>

        <div class="results-table">
            <h3 style="margin-bottom: 20px; color: #1e293b;">Detail Perolehan Suara</h3>
            <table>
                <thead>
                    <tr>
                        <th>Ranking</th>
                        <th>Foto</th>
                        <th>Nama Kandidat</th>
                        <th>Jumlah Suara</th>
                        <th>Persentase</th>
                        <th>Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    mysqli_data_seek($query_results, 0);
                    while ($result = mysqli_fetch_assoc($query_results)) :
                        $percentage = $total_votes > 0 ? round(($result['vote_count'] / $total_votes) * 100, 2) : 0;
                        $chairman_initial = strtoupper(substr($result['chairman_name'], 0, 1));
                    ?>
                    <tr>
                        <td><strong style="font-size: 20px; color: #1e40af;">#<?php echo $rank; ?></strong></td>
                        <td>
                            <?php if ($result['chairman_photo']) : ?>
                                <img src="../../assets/img/kandidat/<?php echo $result['chairman_photo']; ?>" 
                                     alt="Foto" class="candidate-photo">
                            <?php else : ?>
                                <div style="width: 50px; height: 50px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #64748b;">
                                    <?php echo $chairman_initial; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo $result['chairman_name']; ?></strong><br>
                            <small style="color: #64748b;"><?php echo $result['vice_chairman_name']; ?></small>
                        </td>
                        <td><strong style="font-size: 18px; color: #10b981;"><?php echo $result['vote_count']; ?></strong> suara</td>
                        <td><strong style="color: #1e40af;"><?php echo $percentage; ?>%</strong></td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $percentage; ?>%">
                                    <?php echo $percentage; ?>%
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        $rank++;
                    endwhile; 
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const candidates = [];
        const votes = [];
        const colors = [
            'rgba(30, 64, 175, 0.8)',
            'rgba(59, 130, 246, 0.8)',
            'rgba(147, 197, 253, 0.8)',
            'rgba(191, 219, 254, 0.8)'
        ];
        
        <?php 
        mysqli_data_seek($query_results, 0);
        while ($row = mysqli_fetch_assoc($query_results)) : 
        ?>
            candidates.push('<?php echo addslashes($row['chairman_name']); ?>');
            votes.push(<?php echo $row['vote_count']; ?>);
        <?php endwhile; ?>
        
        const ctx = document.getElementById('voteChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: candidates,
                datasets: [{
                    label: 'Jumlah Suara',
                    data: votes,
                    backgroundColor: colors,
                    borderColor: colors.map(c => c.replace('0.8', '1')),
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Perolehan Suara Kandidat',
                        font: { size: 18, weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    </script>

</body>
</html>
