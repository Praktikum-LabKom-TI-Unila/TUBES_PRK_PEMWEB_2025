<?php
require_once '../../koneksi/database.php'; 
require_once '../../backend/manajemenUser/auth_middleware.php'; 

checkAuthorization([]); 

$id_user_login = $_SESSION['user']['id'];

$stmt = $pdo->prepare("SELECT id, username, email, full_name, phone, role FROM users WHERE id = ?");
$stmt->execute([$id_user_login]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: ../login/login.php");
    exit();
}

$error_msg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$status = isset($_GET['status']) ? htmlspecialchars($_GET['status']) : '';
$current_page = 'profile.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - NPC System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50 font-[inter] text-slate-800 flex">
    
    <?php include '../../sidebar/sidebar.php'; ?> 

    <div class="flex-1 flex flex-col ">
        <?php include '../../header/header.php'; ?> 

        <main class="flex-1 p-6 flex items-center justify-center">

            <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-100 w-full max-w-lg md:ml-64">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <i data-lucide="user" class="text-blue-500 w-6 h-6"></i>
                    <h2 class="text-xl font-bold text-slate-800">Pengaturan Profil Anda</h2>
                </div>

                <?php if ($status == 'success'): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">✅ Profil berhasil diperbarui!</span>
                    </div>
                <?php elseif ($error_msg): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">❌ <?= $error_msg ?></span>
                    </div>
                <?php endif; ?>

                <form action="../../backend/profile/process_profile.php" method="POST" class="space-y-5">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    <input type="hidden" name="action_type" value="edit_profile_self">
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">No. Telepon/WhatsApp</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>


                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Username</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <p class="text-xs text-slate-500 mt-1">Role Anda saat ini: <span class="font-bold uppercase text-blue-600"><?= $user['role'] ?></span> (tidak dapat diubah)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Password Baru</label>
                        <input type="password" name="password" placeholder="(Kosongkan jika tidak diubah)" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <p class="text-xs text-slate-500 mt-1">Minimal 6 karakter.</p>
                    </div>
                    
                    <button type="submit" name="update_profile" class="w-full bg-blue-600 text-white font-bold py-2.5 rounded-lg hover:bg-blue-700 transition-colors mt-6">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </main>
    </div>

    <script>lucide.createIcons();</script>
    
</body>
</html>