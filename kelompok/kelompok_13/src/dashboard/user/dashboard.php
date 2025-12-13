<?php
session_start();

if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    echo "<script>window.location='../../login.php';</script>";
    exit;
}

if (file_exists('../../backend/config.php')) {
    require_once '../../backend/config.php';
} else {
    die("Error Critical: File koneksi database tidak ditemukan!");
}

if (isset($_POST['vote_candidate'])) {
    $candidate_id = intval($_POST['candidate_id']);
    $user_id = $_SESSION['user_id'];
    
    $checkVote = mysqli_query($conn, "SELECT has_voted FROM users WHERE id = $user_id");
    $userCheck = mysqli_fetch_assoc($checkVote);
    
    if ($userCheck['has_voted'] == 0) {
        $conn->begin_transaction();
        
        try {
            $insertVote = $conn->prepare("INSERT INTO votes (user_id, candidate_id) VALUES (?, ?)");
            $insertVote->bind_param("ii", $user_id, $candidate_id);
            
            if (!$insertVote->execute()) {
                throw new Exception("Gagal insert vote: " . $conn->error);
            }
            
            $updateUser = $conn->prepare("UPDATE users SET has_voted = 1 WHERE id = ?");
            $updateUser->bind_param("i", $user_id);
            
            if (!$updateUser->execute()) {
                throw new Exception("Gagal update user: " . $conn->error);
            }
            
            $conn->commit();
            
            $_SESSION['has_voted'] = 1;
            $_SESSION['vote_success'] = true;
            
            error_log("VOTE SUCCESS: User $user_id voted for candidate $candidate_id");
            
        } catch (Exception $e) {
            $conn->rollback();
            
            error_log("VOTE ERROR: " . $e->getMessage());
            
            $_SESSION['vote_error'] = "Gagal melakukan voting: " . $e->getMessage();
        }
        
        header("Location: dashboard.php");
        exit;
    }
}

$nim_user = $_SESSION['nim'];
$query_user = mysqli_query($conn, "SELECT * FROM users WHERE nim = '$nim_user'");

if (mysqli_num_rows($query_user) == 0) {
    session_destroy();
    echo "<script>alert('Data user tidak ditemukan!'); window.location='../../login.php';</script>";
    exit;
}

$user = mysqli_fetch_assoc($query_user);
$nama_lengkap = $user['first_name'] . ' ' . $user['last_name'];
$status_voting = ($user['has_voted'] == 1) ? 'sudah' : 'belum';

$vote_success = isset($_SESSION['vote_success']) ? $_SESSION['vote_success'] : false;
$vote_error = isset($_SESSION['vote_error']) ? $_SESSION['vote_error'] : false;

if ($vote_success) {
    unset($_SESSION['vote_success']);
}
if ($vote_error) {
    unset($_SESSION['vote_error']);
}

$query_kandidat = mysqli_query($conn, "SELECT * FROM candidates");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemilih | E-Voting Himatro</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --primary: #1e40af; 
            --primary-light: #3b82f6;
            --sidebar-bg: #1e293b; 
            --bg-light: #f1f5f9; 
            --text-dark: #1e293b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background-color: var(--bg-light); display: flex; min-height: 100vh; font-family: 'Poppins', sans-serif; }
        

        .sidebar { 
            width: 280px; 
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: white; 
            position: fixed; 
            height: 100vh; 
            padding: 0;
            box-shadow: 4px 0 15px rgba(0,0,0,0.1);
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 30px 25px;
            background: rgba(255,255,255,0.05);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }
        
        .logo-text {
            flex: 1;
        }
        
        .logo-text h3 { 
            font-size: 20px; 
            font-weight: 700; 
            color: white; 
            margin-bottom: 5px;
        }
        
        .logo-text p {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 400;
        }
        
        .sidebar-menu { 
            padding: 20px 15px;
            flex: 1;
        }
        
        .menu-section {
            margin-bottom: 30px;
        }
        
        .menu-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 1px;
            padding: 0 10px;
            margin-bottom: 10px;
        }
        
        .sidebar-menu a { 
            display: flex;
            align-items: center;
            padding: 14px 15px; 
            color: #cbd5e1; 
            text-decoration: none; 
            margin-bottom: 5px; 
            border-radius: 10px; 
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
            position: relative;
        }
        
        .sidebar-menu a i { 
            width: 25px;
            font-size: 16px;
            margin-right: 12px;
        }
        
        .sidebar-menu a:hover { 
            background: rgba(255,255,255,0.1);
            color: white; 
            transform: translateX(5px);
        }
        
        .sidebar-menu a.active { 
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .sidebar-menu a.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 70%;
            background: white;
            border-radius: 0 4px 4px 0;
        }
        
        .logout-section {
            padding: 20px 25px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .logout-section a {
            background: rgba(248, 113, 113, 0.1);
            color: #fca5a5;
            border: 1px solid rgba(248, 113, 113, 0.2);
        }
        
        .logout-section a:hover {
            background: rgba(248, 113, 113, 0.2);
            color: #f87171;
        }


        .main-content { 
            margin-left: 280px; 
            flex: 1; 
            padding: 30px;
            min-height: 100vh;
        }
        
        .header-welcome { 
            background: white; 
            padding: 25px 30px; 
            border-radius: 15px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05); 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px;
            border-left: 4px solid var(--primary);
        }
        
        .user-info h4 { 
            margin: 0; 
            color: var(--text-dark); 
            font-size: 24px;
            font-weight: 700;
        }
        
        .user-info p { 
            margin: 8px 0 0; 
            color: #64748b; 
            font-size: 14px; 
        }
        
        .user-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            padding: 6px 12px;
            border-radius: 20px;
            color: var(--primary);
            font-weight: 600;
            font-size: 13px;
            margin-top: 8px;
        }
        
        .user-badge i {
            font-size: 14px;
        }
        
        .date-now {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            padding: 10px 20px;
            border-radius: 10px;
            color: var(--primary);
            font-weight: 600;
            font-size: 14px;
        }
        

        .alert-status { 
            padding: 18px 25px; 
            border-radius: 12px; 
            margin-bottom: 30px; 
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-status i {
            font-size: 20px;
        }
        
        .alert-belum { 
            background: #fef2f2; 
            color: #991b1b; 
            border-left: 4px solid #dc2626;
        }
        
        .alert-sudah { 
            background: #f0fdf4; 
            color: #166534; 
            border-left: 4px solid #16a34a;
        }


        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e2e8f0;
        }


        .candidates-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr)); 
            gap: 30px; 
        }
        
        .candidate-card { 
            background: white; 
            border-radius: 20px; 
            overflow: hidden; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            text-align: center; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid transparent;
            position: relative;
        }
        
        .candidate-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 40px rgba(30, 64, 175, 0.2);
            border-color: var(--primary);
        }
        

        .candidate-number {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(30, 64, 175, 0.3);
            z-index: 10;
        }
        
        .candidate-img { 
            width: 100%; 
            height: 320px; 
            object-fit: contain;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 20px;
            border-bottom: 3px solid #e2e8f0;
        }
        
        .card-body { 
            padding: 30px; 
        }
        
        .card-body h2 {
            font-size: 14px;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        

        .kandidat-names {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border-left: 4px solid var(--primary);
        }
        
        .kandidat-item {
            margin-bottom: 15px;
            text-align: left;
        }
        
        .kandidat-item:last-child {
            margin-bottom: 0;
        }
        
        .kandidat-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 1px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .kandidat-label i {
            color: var(--primary);
        }
        
        .kandidat-name {
            font-size: 18px;
            color: var(--text-dark);
            font-weight: 700;
            line-height: 1.3;
        }
        
        .visi-misi {
            background: #f8fafc;
            padding: 18px;
            border-radius: 12px;
            margin: 15px 0;
            text-align: left;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        
        .visi-misi:hover {
            background: #f1f5f9;
            border-color: var(--primary);
        }
        
        .visi-misi-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .visi-misi-title i {
            font-size: 14px;
        }
        
        .visi-misi p {
            color: #475569;
            font-size: 13px;
            line-height: 1.8;
            margin: 0;
        }
        
        .btn-vote { 
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white; 
            padding: 16px 35px; 
            border-radius: 12px; 
            text-decoration: none; 
            display: inline-flex;
            align-items: center;
            gap: 12px;
            margin-top: 20px;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
        }
        
        .btn-vote:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(30, 64, 175, 0.5);
        }
        
        .btn-vote.disabled { 
            background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
            cursor: not-allowed; 
            pointer-events: none;
            opacity: 0.7;
        }
        
        @media (max-width: 768px) {
            .sidebar { width: 100%; height: auto; position: relative; }
            .main-content { margin-left: 0; }
            .candidates-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo-container">
                <img src="../../assets/logo-himatro.png" alt="Logo Himatro" class="logo-img" onerror="this.style.display='none'">
                <div class="logo-text">
                    <h3>E-Voting Himatro</h3>
                    <p>Pemilihan Kahim 2026</p>
                </div>
            </div>
        </div>
        
        <div class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-label">Menu Utama</div>
                <a href="dashboard.php" class="active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="../../index.php">
                    <i class="fas fa-arrow-left"></i> Halaman Utama
                </a>
            </div>
        </div>
        
        <div class="logout-section">
            <a href="../../logout.php">
                <i class="fas fa-sign-out-alt"></i> Keluar
            </a>
        </div>
    </div>

    <div class="main-content">
        
        <div class="header-welcome">
            <div class="user-info">
                <h4>Selamat Datang, <?php echo $nama_lengkap; ?>!</h4>
                <div class="user-badge">
                    <i class="fas fa-id-card"></i>
                    <span>NIM: <?php echo $user['nim']; ?> | Teknik Elektro</span>
                </div>
                <p style="margin-top: 12px;">
                    <?php echo $status_voting == 'sudah' ? '✓ Sudah Voting' : '⚠ Belum Voting'; ?>
                </p>
            </div>
            <div class="date-now">
                <i class="far fa-calendar"></i> <?php echo date('d F Y'); ?>
            </div>
        </div>


        <?php if ($vote_success) : ?>

            <div class="alert-status" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; border-left: 4px solid #065f46; animation: slideDown 0.6s ease;">
                <i class="fas fa-check-circle" style="font-size: 28px;"></i>
                <div>
                    <strong style="font-size: 20px; display: block; margin-bottom: 8px;">🎉 Selamat! Suara Mu Telah Masuk</strong>
                    <span style="font-size: 15px; opacity: 0.95; line-height: 1.6;">
                        Terima kasih telah berpartisipasi dalam pemilihan. Pilihan Anda telah tersimpan dengan aman dan akan dihitung dalam hasil akhir.
                    </span>
                </div>
            </div>
            <style>
                @keyframes slideDown {
                    0% { opacity: 0; transform: translateY(-30px); }
                    100% { opacity: 1; transform: translateY(0); }
                }
            </style>
            
        <?php elseif ($vote_error) : ?>

            <div class="alert-status" style="background: #fef2f2; color: #991b1b; border-left: 4px solid #dc2626;">
                <i class="fas fa-exclamation-circle"></i>
                <span><strong>Error:</strong> <?php echo htmlspecialchars($vote_error); ?></span>
            </div>
            
        <?php elseif ($status_voting == 'sudah') : ?>

            <div class="alert-status alert-sudah">
                <i class="fas fa-check-circle"></i>
                <span>Terimakasih! Anda sudah menggunakan hak suara Anda pada pemilihan ini.</span>
            </div>
            
        <?php else : ?>

            <div class="alert-status alert-belum">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Perhatian: Anda belum melakukan voting. Silakan pilih kandidat di bawah ini.</span>
            </div>
        <?php endif; ?>


        <?php if ($status_voting == 'belum') : ?>
            <h3 class="section-title">
                <i class="fas fa-vote-yea"></i> Kertas Suara Pemilihan
            </h3>
            
            <div class="candidates-grid">
                <?php 
                if(mysqli_num_rows($query_kandidat) > 0) {
                    $nomor_urut = 1;
                    while($kandidat = mysqli_fetch_assoc($query_kandidat)) { 
                        $chairman_initial = strtoupper(substr($kandidat['chairman_name'], 0, 1));
                        $photo_path = "../../assets/img/kandidat/" . $kandidat['chairman_photo'];
                        $photo_exists = !empty($kandidat['chairman_photo']) && file_exists($photo_path);
                ?>
                    <div class="candidate-card">
                        <div class="candidate-number"><?php echo $nomor_urut; ?></div>
                        
                        <?php if ($photo_exists) : ?>
                            <img src="<?php echo $photo_path; ?>" class="candidate-img" alt="Foto Pasangan Kandidat">
                        <?php else : ?>
                            <div class="candidate-img" style="display: flex; align-items: center; justify-content: center; font-size: 80px; font-weight: 700; color: #cbd5e1;">
                                <?php echo $chairman_initial; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-body">
                            <h2><i class="fas fa-users"></i> Pasangan Kandidat No. <?php echo $nomor_urut; ?></h2>
                            
                            <div class="kandidat-names">
                                <div class="kandidat-item">
                                    <div class="kandidat-label">
                                        <i class="fas fa-user-tie"></i> Calon Ketua
                                    </div>
                                    <div class="kandidat-name"><?php echo $kandidat['chairman_name']; ?></div>
                                </div>
                                <div class="kandidat-item">
                                    <div class="kandidat-label">
                                        <i class="fas fa-user-shield"></i> Calon Wakil Ketua
                                    </div>
                                    <div class="kandidat-name"><?php echo $kandidat['vice_chairman_name']; ?></div>
                                </div>
                            </div>
                            
                            <div class="visi-misi">
                                <div class="visi-misi-title">
                                    <i class="fas fa-lightbulb"></i> Visi
                                </div>
                                <p><?php echo substr($kandidat['vision'], 0, 120) . (strlen($kandidat['vision']) > 120 ? '...' : ''); ?></p>
                            </div>
                            
                            <div class="visi-misi">
                                <div class="visi-misi-title">
                                    <i class="fas fa-tasks"></i> Misi
                                </div>
                                <p><?php echo substr($kandidat['mission'], 0, 120) . (strlen($kandidat['mission']) > 120 ? '...' : ''); ?></p>
                            </div>
                            

                            <form method="POST" action="dashboard.php" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin memilih:\n\nKetua: <?php echo $kandidat['chairman_name']; ?>\nWakil: <?php echo $kandidat['vice_chairman_name']; ?>\n\nPilihan tidak dapat diubah!')">
                                <input type="hidden" name="candidate_id" value="<?php echo $kandidat['id']; ?>">
                                <button type="submit" name="vote_candidate" class="btn-vote">
                                    <i class="fas fa-check-circle"></i> Pilih Pasangan Ini
                                </button>
                            </form>
                        </div>
                    </div>
                <?php 
                        $nomor_urut++;
                    }
                } else {
                    echo "<div style='grid-column: 1 / -1; text-align: center; padding: 60px; background: white; border-radius: 15px;'>";
                    echo "<i class='fas fa-inbox' style='font-size: 60px; color: #cbd5e1; margin-bottom: 20px;'></i>";
                    echo "<h3 style='color: #64748b;'>Belum ada data kandidat</h3>";
                    echo "<p style='color: #94a3b8;'>Kandidat akan ditampilkan saat periode pemilihan dibuka</p>";
                    echo "</div>";
                }
                ?>
            </div>
        <?php else : ?>

            <div style="text-align: center; padding: 80px 20px; background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08);">
                <div style="width: 150px; height: 150px; margin: 0 auto 30px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);">
                    <i class="fas fa-check" style="font-size: 80px; color: white;"></i>
                </div>
                <h2 style="font-size: 32px; color: #1e293b; margin-bottom: 15px; font-weight: 700;">Voting Selesai</h2>
                <p style="font-size: 18px; color: #64748b; margin-bottom: 30px;">Terima kasih telah menggunakan hak suara Anda!</p>
                <div style="background: #f8fafc; padding: 25px; border-radius: 15px; border-left: 5px solid #10b981; max-width: 600px; margin: 0 auto;">
                    <p style="color: #475569; font-size: 15px; line-height: 1.8; margin: 0;">
                        <i class="fas fa-info-circle" style="color: #10b981; margin-right: 8px;"></i>
                        Pilihan Anda telah tersimpan dengan aman. Hasil pemilihan akan diumumkan setelah periode voting berakhir.
                    </p>
                </div>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>