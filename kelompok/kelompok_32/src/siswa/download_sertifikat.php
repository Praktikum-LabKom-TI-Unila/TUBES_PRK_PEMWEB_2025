<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../auth/login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];
$user_kelas = isset($_SESSION['kelas']) ? intval($_SESSION['kelas']) : null;

$query = "SELECT ru.*, u.judul, u.kelas as kelas_id, COALESCE(k.nama, u.kelas) as kelas_nama, mp.nama as mata_pelajaran, usr.nama_lengkap
          FROM riwayat_ujian ru 
          JOIN ujian u ON ru.ujian_id = u.id 
          JOIN mata_pelajaran mp ON u.mata_pelajaran_id = mp.id 
          JOIN users usr ON ru.user_id = usr.id
          LEFT JOIN kelas k ON u.kelas = k.id
          WHERE ru.id = $id AND ru.user_id = $user_id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    die("Data tidak ditemukan");
}

$data = mysqli_fetch_assoc($result);


if (!is_null($user_kelas) && intval($data['kelas_id']) !== $user_kelas) {
    die("Akses ditolak: ujian bukan untuk kelas Anda.");
}

$persentase = ($data['total_soal'] > 0) ? ($data['jawaban_benar'] / $data['total_soal']) * 100 : 0;


if ($data['skor'] < 70) {
    die("Maaf, sertifikat hanya tersedia untuk skor minimal 70");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Ujian</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Georgia', serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .certificate {
            width: 100%;
            max-width: 900px;
            background: #fff;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            position: relative;
            border: 15px solid #f8f9fa;
        }
        .certificate::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            bottom: 15px;
            border: 3px solid #3182ce;
            border-radius: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }
        .logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #fff;
        }
        .title {
            font-size: 2.5rem;
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .subtitle {
            font-size: 1rem;
            color: #718096;
            font-style: italic;
        }
        .content {
            text-align: center;
            margin: 3rem 0;
        }
        .intro-text {
            font-size: 1.125rem;
            color: #4a5568;
            margin-bottom: 2rem;
        }
        .name {
            font-size: 2.5rem;
            color: #3182ce;
            font-weight: 700;
            margin: 1rem 0;
            text-decoration: underline;
            text-decoration-color: #3182ce;
            text-decoration-thickness: 3px;
        }
        .description {
            font-size: 1.125rem;
            color: #4a5568;
            line-height: 1.8;
            margin: 1.5rem 0;
        }
        .subject {
            font-size: 1.5rem;
            color: #2d3748;
            font-weight: 700;
            margin: 1rem 0;
        }
        .score-section {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin: 2rem 0;
        }
        .score-box {
            text-align: center;
        }
        .score-label {
            font-size: 0.875rem;
            color: #718096;
            margin-bottom: 0.5rem;
        }
        .score-value {
            font-size: 2rem;
            font-weight: 700;
            color: #3182ce;
        }
        .footer {
            display: flex;
            justify-content: space-between;
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 2px solid #e2e8f0;
        }
        .signature {
            text-align: center;
            flex: 1;
        }
        .signature-line {
            border-bottom: 2px solid #2d3748;
            width: 200px;
            margin: 3rem auto 0.5rem;
        }
        .signature-name {
            font-weight: 600;
            color: #2d3748;
        }
        .signature-title {
            font-size: 0.875rem;
            color: #718096;
        }
        .date {
            text-align: center;
            margin-top: 2rem;
            color: #718096;
            font-size: 0.875rem;
        }
        .decoration {
            position: absolute;
            width: 100px;
            height: 100px;
            opacity: 0.1;
        }
        .decoration-1 {
            top: 20px;
            left: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
        }
        .decoration-2 {
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border-radius: 50%;
        }
        .download-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 2rem;
            background: #3182ce;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }
        .download-btn:hover {
            background: #2c5aa0;
            transform: translateY(-2px);
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .download-btn {
                display: none;
            }
            .certificate {
                box-shadow: none;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="download-btn">📥 Download PDF</button>
    
    <div class="certificate">
        <div class="decoration decoration-1"></div>
        <div class="decoration decoration-2"></div>
        
        <div class="header">
            <div class="logo">🎓</div>
            <h1 class="title">CERTIFICATE</h1>
            <p class="subtitle">of Achievement</p>
        </div>
        
        <div class="content">
            <p class="intro-text">This certificate is proudly presented to</p>
            
            <div class="name"><?php echo strtoupper($data['nama_lengkap']); ?></div>
            
            <p class="description">
                Has successfully completed the examination
            </p>
            
            <div class="subject"><?php echo $data['mata_pelajaran']; ?></div>
            <p style="color: #718096; font-size: 1rem;"><?php echo $data['judul']; ?></p>
            <p style="color: #718096; font-size: 1rem;">Kelas: <?php echo htmlspecialchars($data['kelas_nama'] ?? $data['kelas_id']); ?></p>
            
            <div class="score-section">
                <div class="score-box">
                    <div class="score-label">Score</div>
                    <div class="score-value"><?php echo $data['skor']; ?></div>
                </div>
                <div class="score-box">
                    <div class="score-label">Percentage</div>
                    <div class="score-value"><?php echo number_format($persentase, 1); ?>%</div>
                </div>
                <div class="score-box">
                    <div class="score-label">Correct Answers</div>
                    <div class="score-value"><?php echo $data['jawaban_benar']; ?>/<?php echo $data['total_soal']; ?></div>
                </div>
            </div>
            
            <p class="date">
                Issued on <?php echo date('F d, Y', strtotime($data['created_at'])); ?>
            </p>
        </div>
        
        <div class="footer">
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-name">Administrator</div>
                <div class="signature-title">System Director</div>
            </div>
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-name">Head Teacher</div>
                <div class="signature-title">Academic Supervisor</div>
            </div>
        </div>
    </div>
</body>
</html>