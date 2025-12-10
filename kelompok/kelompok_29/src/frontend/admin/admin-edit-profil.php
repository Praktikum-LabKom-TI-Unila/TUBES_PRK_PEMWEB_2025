<?php
// Dummy data untuk simulasi Admin yang sedang login
$currentUser = [
    'id' => 'A001',
    'name' => 'Admin System',
    'email' => 'admin@infra.go.id',
    'phone' => '021-12345678',
    'role' => 'Administrator',
    'createdAt' => '2023-01-01',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile Admin</title>
    <link rel="stylesheet" href="css/style.css"> 
    <link rel="stylesheet" href="css/admin-edit-profil.css"> 
</head>
<body>
    <main class="form-main-content">
        <div class="container max-w-4xl mx-auto p-4">
            
            <header class="page-header sticky top-0 bg-white z-10">
                <a href="admin-dashboard.php" class="back-link">&larr;</a>
                <div>
                    <h1>Edit Profile Admin</h1>
                    <p class="text-muted">Perbarui informasi akun admin</p>
                </div>
            </header>
            
            <form id="formEditProfile" class="space-y-4">
                
                <section class="card form-section profile-info-card">
                    <div class="profile-display">
                        <div class="profile-icon-wrapper">
                            🛡️
                        </div>
                        <div>
                            <h3>Admin Profile</h3>
                            <p class="text-muted">Administrator System</p>
                        </div>
                    </div>
                </section>

                <section class="card form-section">
                    <h3 class="mb-4">Informasi Admin</h3>
                    
                    <div class="space-y-4">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="input-with-icon">
                                <span class="icon">👤</span>
                                <input type="text" id="nama" name="name" value="<?php echo htmlspecialchars($currentUser['name']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <div class="input-with-icon">
                                <span class="icon">✉️</span>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="telepon">No. Telepon <span class="text-danger">*</span></label>
                            <div class="input-with-icon">
                                <span class="icon">📞</span>
                                <input type="tel" id="telepon" name="phone" value="<?php echo htmlspecialchars($currentUser['phone']); ?>" required>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card form-section">
                    <h3 class="mb-4">Informasi Akun</h3>
                    <div class="info-read-only space-y-2">
                        <p>User ID: <span class="text-dark"><?php echo htmlspecialchars($currentUser['id']); ?></span></p>
                        <p>Role: <span class="text-danger-status">Administrator</span></p>
                        <p>Tanggal Bergabung: <span class="text-dark"><?php echo htmlspecialchars($currentUser['createdAt']); ?></span></p>
                    </div>
                </section>

                <div class="alert alert-warning-custom">
                    <p>Untuk mengganti password atau pengaturan keamanan, hubungi super administrator.</p>
                </div>
                
                <div class="form-actions flex gap-3">
                    <button type="button" class="btn btn-secondary flex-1" onclick="window.location.href='admin-dashboard.php'">Batal</button>
                    <button type="submit" class="btn btn-primary flex-1">
                        <span class="icon">💾</span> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script src="js/admin-edit-profil.js"></script>
</body>
</html>