<?php ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Akun - E-Voting Himatro 2026</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        
        .container { display: flex; width: 90%; max-width: 1000px; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        
        .form-section { flex: 1; padding: 60px 50px; }
        .logo-container { display: flex; align-items: center; gap: 15px; margin-bottom: 40px; }
        .logo-img { width: 50px; height: 50px; object-fit: contain; }
        .logo-text { font-size: 24px; font-weight: 700; color: #1e40af; }
        .logo-subtitle { color: #64748b; font-size: 14px; }
        
        h2 { font-size: 28px; color: #1e293b; margin-bottom: 10px; }
        .subtitle { color: #64748b; margin-bottom: 30px; }
        
        .form-group { margin-bottom: 20px; }
        label { display: flex; align-items: center; color: #475569; font-weight: 500; margin-bottom: 8px; font-size: 14px; }
        label i { margin-right: 8px; color: #3b82f6; }
        
        input[type="text"], input[type="email"], input[type="password"] { 
            width: 100%; 
            padding: 14px 16px; 
            border: 2px solid #e2e8f0; 
            border-radius: 10px; 
            font-size: 15px; 
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
        }
        input:focus { 
            outline: none; 
            border-color: #3b82f6; 
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .input-hint { font-size: 12px; color: #94a3b8; margin-top: 5px; }
        
        .checkbox-group { display: flex; align-items: center; margin: 20px 0; }
        .checkbox-group input { width: auto; margin-right: 8px; }
        .checkbox-group label { margin: 0; font-weight: 400; color: #64748b; }
        
        .btn-login { 
            width: 100%; 
            padding: 16px; 
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
        .btn-login:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4); 
        }
        
        .divider { text-align: center; margin: 25px 0; color: #94a3b8; position: relative; }
        .divider::before, .divider::after { content: ''; position: absolute; top: 50%; width: 45%; height: 1px; background: #e2e8f0; }
        .divider::before { left: 0; }
        .divider::after { right: 0; }
        
        .btn-google { 
            width: 100%; 
            padding: 14px; 
            background: white; 
            color: #1e293b; 
            border: 2px solid #e2e8f0; 
            border-radius: 10px; 
            font-size: 15px; 
            font-weight: 500; 
            cursor: pointer; 
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .btn-google:hover { background: #f8fafc; border-color: #cbd5e1; }
        .btn-google img { width: 20px; margin-right: 10px; }
        
        .register-link { text-align: center; margin-top: 20px; color: #64748b; }
        .register-link a { color: #3b82f6; font-weight: 600; text-decoration: none; }
        .register-link a:hover { text-decoration: underline; }
        
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
        
        select {
            width: 100%; 
            padding: 14px 16px; 
            border: 2px solid #e2e8f0; 
            border-radius: 10px; 
            font-size: 15px; 
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
            background: white;
            cursor: pointer;
        }
        
        select:focus { 
            outline: none; 
            border-color: #3b82f6; 
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .role-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
        }
        
        .role-admin {
            background: #fef3c7;
            color: #92400e;
        }
        
        .role-user {
            background: #dbeafe;
            color: #1e40af;
        }
        
        @media (max-width: 768px) {
            .container { flex-direction: column; }
            .info-section { display: none; }
            .form-section { padding: 40px 30px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-section">
            <div class="logo-container">
                <img src="assets/logo-himatro.png" alt="Logo Himatro" class="logo-img">
                <div>
                    <div class="logo-text">Himatro</div>
                    <div class="logo-subtitle">E-Voting 2026</div>
                </div>
            </div>
            
            <h2>Masuk ke Akun</h2>
            <p class="subtitle">Gunakan kredensial Anda untuk mengakses sistem voting</p>
            
            <form action="backend/login.php" method="POST">
                <div class="form-group">
                    <label><i>👤</i> Login Sebagai</label>
                    <select name="role" id="roleSelect" required onchange="updatePlaceholder()">
                        <option value="user">Mahasiswa/Pemilih <span class="role-badge role-user">USER</span></option>
                        <option value="admin">Administrator <span class="role-badge role-admin">ADMIN</span></option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i>✉</i> <span id="emailLabel">Email atau NIM</span></label>
                    <input type="text" name="email" id="emailInput" placeholder="farei@himatro.ac.id atau NIM" required>
                    <div class="input-hint" id="emailHint">Masukkan email institusi atau NIM Anda</div>
                </div>
                
                <div class="form-group">
                    <label><i>🔒</i> Kata Sandi</label>
                    <input type="password" name="password" placeholder="••••••••••" required>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="remember">
                    <label for="remember">Ingat saya</label>
                </div>
                
                <button type="submit" class="btn-login">👉 Masuk</button>
                
                <div class="divider">atau</div>
                
                <button type="button" class="btn-google">
                    <img src="https://www.google.com/favicon.ico" alt="Google">
                    Lanjutkan dengan Google
                </button>
            </form>
            
            <div class="register-link">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </div>
        </div>
        
        <div class="info-section">
            <h3>E-Voting Himatro 2026</h3>
            <p>Sistem voting digital untuk Pemilihan Ketua Himpunan Mahasiswa Teknik Elektro</p>
            
            <ul class="feature-list">
                <li>Keamanan terjamin</li>
                <li>Satu anggota, satu suara</li>
                <li>Email institusional aktif</li>
                <li>Belum memberikan suara</li>
                <li>Hasil real-time</li>
            </ul>
        </div>
    </div>
    
    <script>
        function updatePlaceholder() {
            const role = document.getElementById('roleSelect').value;
            const emailInput = document.getElementById('emailInput');
            const emailLabel = document.getElementById('emailLabel');
            const emailHint = document.getElementById('emailHint');
            
            if (role === 'admin') {
                emailLabel.textContent = 'Email Admin';
                emailInput.placeholder = 'admin@himatro.ac.id';
                emailHint.textContent = 'Gunakan email admin yang terdaftar';
            } else {
                emailLabel.textContent = 'Email atau NIM';
                emailInput.placeholder = 'farei@himatro.ac.id atau NIM';
                emailHint.textContent = 'Masukkan email institusi atau NIM Anda';
            }
        }
    </script>
</body>
</html>