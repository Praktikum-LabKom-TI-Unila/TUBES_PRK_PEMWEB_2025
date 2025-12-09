<?php

include __DIR__ . '/backend/config.php';

$query_kandidat = mysqli_query($conn, "SELECT count(*) as total FROM candidates");
$data_kandidat  = mysqli_fetch_assoc($query_kandidat);
$jumlah_kandidat = $data_kandidat['total'];

$query_pemilih = mysqli_query($conn, "SELECT count(*) as total FROM users");
$data_pemilih  = mysqli_fetch_assoc($query_pemilih);
$jumlah_pemilih = $data_pemilih['total']; 

if ($jumlah_pemilih == null) { $jumlah_pemilih = 0; }

$target_selesai = "2026-05-15 17:00:00";
$waktu_selesai  = strtotime($target_selesai);
$waktu_sekarang = time();
$selisih_detik  = $waktu_selesai - $waktu_sekarang;

$sisa_jam = floor($selisih_detik / (60 * 60));

if ($sisa_jam < 0) { $sisa_jam = 0; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Voting Himatro 2026 | Himpunan Mahasiswa Teknik Elektro</title>
    
    <link rel="stylesheet" href="css/style.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <style>
        .kandidat-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: 0.3s;
        }
        
        .kandidat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .kandidat-img {
            width: 100%;
            height: 350px;
            object-fit: contain; 
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            padding: 20px; 
        }
        
        .kandidat-body {
            padding: 25px;
        }
        
        .kandidat-body h3 {
            font-size: 20px;
            color: #1e40af;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .kandidat-body h4 {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        .visi-misi-box {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #1e40af;
        }
        
        .visi-misi-box h5 {
            font-size: 13px;
            color: #1e40af;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .visi-misi-box p {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
            margin: 0;
        }

        .section-kandidat {
            padding: 80px 0;
            background: #f8fafc;
        }
        
        .kandidat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
            margin-top: 50px;
        }
        
        .kandidat-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
            position: relative;
            border: 2px solid transparent;
        }
        
        .kandidat-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 20px 50px rgba(30, 64, 175, 0.25);
            border-color: #1e40af;
        }
        
        .kandidat-number {
            position: absolute;
            top: 20px;
            left: 20px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            font-weight: 800;
            box-shadow: 0 6px 20px rgba(30, 64, 175, 0.4);
            z-index: 10;
            border: 4px solid white;
        }
        
        .kandidat-img {
            width: 100%;
            height: 380px;
            object-fit: contain;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            padding: 30px;
            border-bottom: 4px solid #e2e8f0;
        }
        
        .kandidat-img-placeholder {
            width: 100%;
            height: 380px;
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 100px;
            font-weight: 800;
            color: #cbd5e1;
        }
        
        .kandidat-body {
            padding: 35px 30px;
        }

        .nama-box {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 8px 20px rgba(30, 64, 175, 0.3);
            color: white;
        }
        
        .nama-item {
            margin-bottom: 18px;
        }
        
        .nama-item:last-child {
            margin-bottom: 0;
        }
        
        .nama-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1.5px;
            margin-bottom: 6px;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .nama-label i {
            font-size: 13px;
        }
        
        .nama-value {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.3;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .visi-misi-container {
            margin-top: 20px;
        }
        
        .visi-misi-item {
            background: #f8fafc;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            border-left: 5px solid #1e40af;
            transition: all 0.3s ease;
        }
        
        .visi-misi-item:hover {
            background: #eff6ff;
            border-left-width: 8px;
        }
        
        .visi-misi-label {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 800;
            color: #1e40af;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: 1px;
        }
        
        .visi-misi-label i {
            font-size: 16px;
        }
        
        .visi-misi-text {
            font-size: 14px;
            color: #475569;
            line-height: 1.8;
            margin: 0;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">
                <img src="assets/logo-himatro.png" alt="Logo Himatro">
                <div>
                    <span class="logo-title">Himatro</span>
                    <span class="logo-sub">Teknik Elektro</span>
                </div>
            </a>
            <div class="nav-menu">
                <a href="#home" class="nav-link active">Beranda</a>
                <a href="#timeline" class="nav-link">Timeline</a>
                <a href="#kandidat" class="nav-link">Kandidat</a>
                <a href="#howto" class="nav-link">Cara Voting</a>
                
                <a href="login.php" class="btn btn-outline">Masuk</a>
                <a href="register.php" class="btn btn-primary">Daftar</a>
            </div>
            <button class="menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </nav>

    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Pemilihan Ketua Himatro 2026</h1>
                <p class="hero-desc">Partisipasi aktif dalam menentukan masa depan Himpunan Mahasiswa Teknik Elektro. Suara Anda menentukan arah perkembangan ke depan.</p>
                
                <div class="hero-actions">
                    <a href="register.php" class="btn btn-primary btn-large">Daftar Sekarang</a>
                    <a href="#kandidat" class="btn btn-secondary btn-large">Lihat Kandidat</a>
                </div>

                <div class="hero-stats">
                    
                    <div class="stat">
                        <span class="stat-num">
                            <?php echo number_format($jumlah_pemilih); ?>+
                        </span>
                        <span class="stat-label">Pemilih Terdaftar</span>
                    </div>

                    <div class="stat">
                        <span class="stat-num">
                            <?php echo $jumlah_kandidat; ?>
                        </span>
                        <span class="stat-label">Kandidat</span>
                    </div>

                    <div class="stat">
                        <span class="stat-num">
                            <?php echo $sisa_jam; ?>
                        </span>
                        <span class="stat-label">Jam Tersisa</span>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section id="timeline" class="section timeline">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Timeline Pemilihan</h2>
                <p class="section-sub">Jadwal kegiatan pemilihan Ketua Himatro 2026</p>
            </div>
            <div class="timeline-container">
                <div class="timeline-item">
                    <div class="timeline-date">1 - 15 April 2026</div>
                    <div class="timeline-content">
                        <h3>Pendaftaran Kandidat</h3>
                        <p>Pendaftaran calon ketua dan wakil ketua Himatro</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">16 - 30 April 2026</div>
                    <div class="timeline-content">
                        <h3>Kampanye</h3>
                        <p>Kampanye dan debat kandidat</p>
                    </div>
                </div>
                <div class="timeline-item active">
                    <div class="timeline-date">1 - 15 Mei 2026</div>
                    <div class="timeline-content">
                        <h3>Voting Online</h3>
                        <p>Pemungutan suara melalui sistem e‑voting</p>
                        <span class="badge">Sedang Berlangsung</span>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-date">16 Mei 2026</div>
                    <div class="timeline-content">
                        <h3>Pengumuman Hasil</h3>
                        <p>Pengumuman pemenang pemilihan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="kandidat" class="section-kandidat">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <h2 style="text-align: center; font-size: 38px; color: #1e293b; margin-bottom: 15px; font-weight: 800;">Calon Kandidat</h2>
            <p style="text-align: center; color: #64748b; margin-bottom: 50px; font-size: 17px;">Kenali pasangan calon Ketua & Wakil Ketua Himatro 2026</p>
            
            <div class="kandidat-grid">
                <?php 
                $query_kandidat = mysqli_query($conn, "SELECT * FROM candidates ORDER BY id ASC");
                
                if(mysqli_num_rows($query_kandidat) > 0) {
                    $nomor_urut = 1;
                    while($kandidat = mysqli_fetch_assoc($query_kandidat)) : 
                        $photo_path = "assets/img/kandidat/" . $kandidat['chairman_photo'];
                        $photo_exists = !empty($kandidat['chairman_photo']) && file_exists($photo_path);
                        $chairman_initial = strtoupper(substr($kandidat['chairman_name'], 0, 1));
                ?>
                    <div class="kandidat-card">
                        <div class="kandidat-number"><?php echo $nomor_urut; ?></div>

                        <?php if ($photo_exists) : ?>
                            <img src="<?php echo $photo_path; ?>" 
                                 alt="Pasangan Kandidat No. <?php echo $nomor_urut; ?>" 
                                 class="kandidat-img"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="kandidat-img-placeholder" style="display: none;">
                                <?php echo $chairman_initial; ?>
                            </div>
                        <?php else : ?>
                            <div class="kandidat-img-placeholder">
                                <?php echo $chairman_initial; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="kandidat-body">
                            <div class="nama-box">
                                <div class="nama-item">
                                    <div class="nama-label">
                                        <i class="fas fa-user-tie"></i> Calon Ketua
                                    </div>
                                    <div class="nama-value"><?php echo $kandidat['chairman_name']; ?></div>
                                </div>
                                <div class="nama-item">
                                    <div class="nama-label">
                                        <i class="fas fa-user-shield"></i> Calon Wakil Ketua
                                    </div>
                                    <div class="nama-value"><?php echo $kandidat['vice_chairman_name']; ?></div>
                                </div>
                            </div>
 
                            <div class="visi-misi-container">
                                <div class="visi-misi-item">
                                    <div class="visi-misi-label">
                                        <i class="fas fa-lightbulb"></i> Visi
                                    </div>
                                    <p class="visi-misi-text"><?php echo substr($kandidat['vision'], 0, 180) . (strlen($kandidat['vision']) > 180 ? '...' : ''); ?></p>
                                </div>

                                <div class="visi-misi-item">
                                    <div class="visi-misi-label">
                                        <i class="fas fa-tasks"></i> Misi
                                    </div>
                                    <p class="visi-misi-text"><?php echo substr($kandidat['mission'], 0, 180) . (strlen($kandidat['mission']) > 180 ? '...' : ''); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                        $nomor_urut++;
                    endwhile;
                } else {
                    echo "<div style='grid-column: 1 / -1; text-align: center; padding: 80px 20px;'>";
                    echo "<i class='fas fa-inbox' style='font-size: 80px; color: #cbd5e1; margin-bottom: 25px;'></i>";
                    echo "<h3 style='color: #64748b; font-size: 24px; margin-bottom: 10px;'>Belum ada kandidat</h3>";
                    echo "<p style='color: #94a3b8; font-size: 16px;'>Data kandidat akan ditampilkan saat periode pemilihan dibuka</p>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </section>

    <section id="howto" class="section howto">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Cara Berpartisipasi</h2>
                <p class="section-sub">Ikuti langkah-langkah mudah untuk memberikan suara Anda</p>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="step-icon">1</div>
                    <div class="step-content">
                        <h3>Registrasi Akun</h3>
                        <p>Daftar dengan NIM dan email aktif untuk verifikasi anggota</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-icon">2</div>
                    <div class="step-content">
                        <h3>Verifikasi Identitas</h3>
                        <p>Login dan verifikasi sebagai anggota aktif Himatro</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-icon">3</div>
                    <div class="step-content">
                        <h3>Pilih Kandidat</h3>
                        <p>Masuk ke bilik suara virtual dan pilih calon pimpinan</p>
                    </div>
                </div>
                <div class="step">
                    <div class="step-icon">4</div>
                    <div class="step-content">
                        <h3>Konfirmasi Suara</h3>
                        <p>Verifikasi pilihan Anda sebelum mengirimkan suara</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h2 class="cta-title">Siap Memberikan Suara Anda?</h2>
            <p class="cta-desc">Partisipasi Anda menentukan masa depan Himatro. Mari bersama membangun himpunan yang lebih baik.</p>
            <div class="cta-actions">
                <a href="register.php" class="btn btn-primary btn-large">Daftar Sekarang</a>
                <a href="login.php" class="btn btn-secondary btn-large">Masuk untuk Voting</a>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <a href="index.php" class="logo">
                        <img src="assets/logo-himatro.png" alt="Logo Himatro">
                        <div>
                            <span class="logo-title">Himatro</span>
                            <span class="logo-sub">Himpunan Mahasiswa Teknik Elektro</span>
                        </div>
                    </a>
                    <p>Sistem E‑Voting Pemilihan Ketua Himatro Periode 2026‑2027</p>
                </div>
                <div class="footer-links">
                    <h4>Tautan Cepat</h4>
                    <a href="index.php">Beranda</a>
                    <a href="login.php">Masuk</a>
                    <a href="register.php">Daftar</a>
                    <a href="#timeline">Timeline</a>
                </div>
                <div class="footer-contact">
                    <h4>Kontak</h4>
                    <p><i class="fas fa-envelope"></i> evoting@himatro.ac.id</p>
                    <p><i class="fas fa-phone"></i> (021) 1234-5678</p>
                    <p><i class="fas fa-map-marker-alt"></i> Gedung H Teknik Elektro</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Himatro - Himpunan Mahasiswa Teknik Elektro. Semua hak dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="js/app.js"></script>
</body>
</html>