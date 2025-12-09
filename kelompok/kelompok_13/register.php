<?php ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun Baru - E-Voting Himatro 2026</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px 0; }
        
        .container { display: flex; width: 90%; max-width: 1100px; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        
        .form-section { flex: 1; padding: 50px 40px; max-height: 90vh; overflow-y: auto; }
        .logo-container { display: flex; align-items: center; gap: 15px; margin-bottom: 30px; }
        .logo-img { width: 50px; height: 50px; object-fit: contain; }
        .logo-text { font-size: 24px; font-weight: 700; color: #1e40af; }
        .logo-subtitle { color: #64748b; font-size: 14px; }
        
        h2 { font-size: 28px; color: #1e293b; margin-bottom: 10px; }
        .subtitle { color: #64748b; margin-bottom: 25px; }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        
        .form-group { margin-bottom: 18px; }
        .form-group.full-width { grid-column: 1 / -1; }
        
        label { display: block; color: #475569; font-weight: 500; margin-bottom: 6px; font-size: 14px; }
        
        input[type="text"], input[type="email"], input[type="password"] { 
            width: 100%; 
            padding: 12px 14px; 
            border: 2px solid #e2e8f0; 
            border-radius: 10px; 
            font-size: 14px; 
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
        }
        input:focus { 
            outline: none; 
            border-color: #3b82f6; 
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .checkbox-group { display: flex; align-items: flex-start; margin: 15px 0; }
        .checkbox-group input { width: auto; margin-right: 8px; margin-top: 4px; }
        .checkbox-group label { margin: 0; font-weight: 400; color: #64748b; font-size: 13px; line-height: 1.5; }
        .checkbox-group a { color: #3b82f6; text-decoration: none; }
        .checkbox-group a:hover { text-decoration: underline; }
        
        .btn-register { 
            width: 100%; 
            padding: 15px; 
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); 
            color: white; 
            border: none; 
            border-radius: 10px; 
            font-size: 16px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
        }
        .btn-register:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4); 
        }
        
        .login-link { text-align: center; margin-top: 15px; color: #64748b; font-size: 14px; }
        .login-link a { color: #3b82f6; font-weight: 600; text-decoration: none; }
        .login-link a:hover { text-decoration: underline; }
        
        .back-link { display: inline-flex; align-items: center; color: #3b82f6; text-decoration: none; font-size: 14px; margin-bottom: 20px; }
        .back-link:hover { text-decoration: underline; }
        
        .info-section { 
            flex: 1; 
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%); 
            color: white; 
            padding: 60px 50px; 
            display: flex; 
            flex-direction: column; 
            justify-content: center;
        }
        .info-section h3 { font-size: 32px; margin-bottom: 20px; line-height: 1.3; }
        .info-section p { font-size: 16px; color: #dbeafe; margin-bottom: 30px; line-height: 1.6; }
        
        .feature-list { list-style: none; }
        .feature-list li { 
            padding: 12px 0; 
            display: flex; 
            align-items: center; 
            color: #dbeafe;
        }
        .feature-list li::before { 
            content: '✓'; 
            background: rgba(255,255,255,0.2); 
            width: 24px; 
            height: 24px; 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin-right: 12px;
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .info-section { display: none; }
            .form-section { padding: 30px 25px; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-section">
            <a href="index.php" class="back-link">← Kembali ke Beranda</a>
            
            <div class="logo-container">
                <img src="assets/logo-himatro.png" alt="Logo Himatro" class="logo-img">
                <div>
                    <div class="logo-text">Himatro</div>
                    <div class="logo-subtitle">E-Voting 2026</div>
                </div>
            </div>
            
            <h2>Buat Akun Baru</h2>
            <p class="subtitle">Daftarkan diri Anda untuk berpartisipasi dalam pemilihan</p>
            
            <form action="backend/register.php" method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nama Depan</label>
                        <input type="text" name="firstName" placeholder="Muhammad" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Belakang</label>
                        <input type="text" name="lastName" placeholder="Gumay" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>NIM</label>
                    <input type="text" name="nim" placeholder="i234554" required>
                </div>
                
                <div class="form-group">
                    <label>Email Institusional</label>
                    <input type="email" name="email" placeholder="gumay@himatro.ac.id" required>
                </div>
                
                <div class="form-group">
                    <label>Kata Sandi</label>
                    <input type="password" name="password" placeholder="Minimal 8 karakter" minlength="8" required>
                </div>
                
                <div class="form-group">
                    <label>Konfirmasi Kata Sandi</label>
                    <input type="password" name="confirmPassword" placeholder="Ulangi kata sandi" minlength="8" required>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="terms" required>
                    <label for="terms">Saya setuju dengan <a href="#">Syarat & Ketentuan</a></label>
                </div>
                
                <button type="submit" class="btn-register">✨ Daftar Akun</button>
            </form>
            
            <div class="login-link">
                Sudah punya akun? <a href="login.php">Masuk di sini</a>
            </div>
        </div>
        
        <div class="info-section">
            <h3>Bergabung dengan E-Voting</h3>
            <p>Dengan mendaftar, Anda dapat berpartisipasi dalam pemilihan Ketua Himatro 2026</p>
            
            <ul class="feature-list">
                <li>Mahasiswa aktif Teknik Elektro</li>
                <li>Memiliki NIM valid</li>
                <li>Email institusional aktif</li>
                <li>Belum memberikan suara</li>
            </ul>
        </div>
    </div>
    
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const confirm = document.querySelector('input[name="confirmPassword"]').value;
            
            if (password !== confirm) {
                e.preventDefault();
                alert('Password dan konfirmasi password tidak cocok!');
            }
        });
    </script>
</body>
</html>