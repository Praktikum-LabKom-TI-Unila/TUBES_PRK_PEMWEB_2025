<?php
// src/views/profile/user_profile.php
// User Profile Page - Astral Psychologist

require_once dirname(__DIR__, 2) . "/config/database.php";
require_once dirname(__DIR__, 2) . "/models/User.php";

if (!isset($_SESSION['user'])) {
    echo "<script>window.location='index.php?p=login';</script>";
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['user_id'] ?? $user['id'] ?? null;

$userModel = new User($conn);

// Refresh user data from database to get latest profile picture
$freshUser = $userModel->getUserById($user_id);
if ($freshUser) {
    $user = array_merge($user, $freshUser);
    $_SESSION['user'] = $user;
}

// Fetch user stats
$total_sessions = 0;
$tableCheckResult = $conn->query("SHOW TABLES LIKE 'chat_session'");
if ($tableCheckResult && $tableCheckResult->num_rows > 0) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM chat_session WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $total_sessions = $stmt->get_result()->fetch_assoc()['cnt'] ?? 0;
    }
}

// Fetch recent activity
$activities = [];
$tableCheckResult = $conn->query("SHOW TABLES LIKE 'activity_log'");
if ($tableCheckResult && $tableCheckResult->num_rows > 0) {
    $stmt = $conn->prepare("SELECT * FROM activity_log WHERE actor_type = 'user' AND actor_id = ? ORDER BY created_at DESC LIMIT 10");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $activities[] = $row;
            }
        }
    }
}

// Helper function to format activity
function formatActivity($activity) {
    $actions = [
        'login' => '🔓 Login',
        'update_profile' => '✏️ Update Profil',
        'upload_profile_picture' => '📷 Upload Foto',
        'change_password' => '🔐 Ubah Password',
        'start_chat' => '💬 Mulai Chat',
        'end_chat' => '🏁 Akhiri Chat',
        'survey_completed' => '📋 Selesaikan Survey'
    ];
    return $actions[$activity['action']] ?? ucfirst(str_replace('_', ' ', $activity['action']));
}
?>

<div class="min-h-screen px-6 py-20" style="background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 25%, var(--bg-primary) 50%, var(--bg-secondary) 75%, var(--bg-primary) 100%); position: relative; overflow: hidden; transition: background-color 0.3s ease;">
    
    <!-- Decorative Background Elements -->
    <div style="position: fixed; top: -50%; right: -10%; width: 600px; height: 600px; background: radial-gradient(circle, rgba(58, 175, 169, 0.1) 0%, transparent 70%); border-radius: 50%; z-index: 0; pointer-events: none;"></div>
    <div style="position: fixed; bottom: -30%; left: -5%; width: 500px; height: 500px; background: radial-gradient(circle, rgba(23, 37, 42, 0.05) 0%, transparent 70%); border-radius: 50%; z-index: 0; pointer-events: none;"></div>

    <div class="max-w-6xl mx-auto relative z-10">
        
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold" style="color: var(--text-primary);">👤 Profil Saya</h1>
                <p style="color: var(--text-secondary);" class="mt-2">Lihat informasi profil dan riwayat aktivitas Anda</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="index.php?p=user_dashboard" style="background: var(--bg-tertiary); color: var(--text-primary); border: 1px solid var(--border-color);" class="inline-flex items-center px-4 py-2 hover:shadow-md rounded-lg font-semibold transition">
                    ← Kembali ke Dashboard
                </a>
                <button id="theme-toggle" aria-label="Toggle theme" title="Toggle dark mode" style="background: transparent; border:1px solid var(--border-color); color:var(--text-primary); padding:8px 12px; border-radius:8px; font-weight:600;">
                    🌙 Dark
                </button>
            </div>
        </div>

        <!-- Profile Header Card -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color);" class="rounded-2xl soft-shadow p-8 mb-8">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                <!-- Profile Picture -->
                <div class="flex-shrink-0">
                    <img src="<?= isset($user['profile_picture']) && $user['profile_picture'] ? "../uploads/profile/".htmlspecialchars($user['profile_picture']) : 'https://via.placeholder.com/200x200?text=Profile' ?>" 
                         alt="profile" class="w-48 h-48 object-cover rounded-2xl shadow-lg border-4 border-[#3AAFA9]">
                </div>

                <!-- Profile Info -->
                <div class="flex-1">
                    <h2 class="text-4xl font-bold mb-2" style="color: var(--text-primary);"><?= htmlspecialchars($user['name'] ?? $user['email']) ?></h2>
                    <p class="text-lg mb-6" style="color: var(--text-secondary);">📧 <?= htmlspecialchars($user['email']) ?></p>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="p-4 bg-[#F7FBFB] rounded-lg text-center">
                            <p class="text-3xl font-bold text-[#3AAFA9]"><?= intval($total_sessions) ?></p>
                            <p class="text-sm text-gray-600 mt-1">Total Sesi</p>
                        </div>
                        <div class="p-4 bg-[#F7FBFB] rounded-lg text-center">
                            <p class="text-3xl font-bold text-[#17252A]">⭐</p>
                            <p class="text-sm text-gray-600 mt-1">Member Aktif</p>
                        </div>
                        <div class="p-4 bg-[#F7FBFB] rounded-lg text-center">
                            <p class="text-3xl font-bold text-[#3AAFA9]"><?= count($activities) ?></p>
                            <p class="text-sm text-gray-600 mt-1">Aktivitas</p>
                        </div>
                        <div class="p-4 bg-[#F7FBFB] rounded-lg text-center">
                            <p class="text-3xl font-bold text-[#17252A]">✓</p>
                            <p class="text-sm text-gray-600 mt-1">Terverifikasi</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="index.php?p=user_settings" class="px-6 py-2 bg-[#3AAFA9] text-white rounded-lg hover:bg-[#2B8E89] font-semibold transition">
                            ⚙️ Pengaturan Akun
                        </a>
                        <a href="index.php?p=match" class="px-6 py-2 border border-[#3AAFA9] text-[#3AAFA9] rounded-lg hover:bg-[#F7FBFB] font-semibold transition">
                            💬 Cari Konselor
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Sections -->
        <div class="grid md:grid-cols-2 gap-8 mb-8">

            <!-- Account Information -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color);" class="rounded-2xl soft-shadow p-8">
                <h3 class="text-2xl font-bold mb-6" style="color: var(--text-primary);">📋 Informasi Akun</h3>
                
                <div class="space-y-6">
                    <div style="border-left-color: #3AAFA9;" class="border-l-4 pl-4">
                        <p style="color: var(--text-secondary);" class="text-sm uppercase">Email</p>
                        <p class="text-lg font-semibold mt-1" style="color: var(--text-primary);"><?= htmlspecialchars($user['email']) ?></p>
                    </div>

                    <div style="border-left-color: #3AAFA9;" class="border-l-4 pl-4">
                        <p style="color: var(--text-secondary);" class="text-sm uppercase">Nama Lengkap</p>
                        <p class="text-lg font-semibold mt-1" style="color: var(--text-primary);"><?= htmlspecialchars($user['name'] ?? '-') ?></p>
                    </div>

                    <div style="border-left-color: #3AAFA9;" class="border-l-4 pl-4">
                        <p style="color: var(--text-secondary);" class="text-sm uppercase">Role</p>
                        <p class="text-lg font-semibold mt-1" style="color: var(--text-primary);">
                            <?php 
                            $role = $user['role'] ?? 'user';
                            if ($role === 'admin') {
                                echo '👨‍💼 Administrator';
                            } elseif ($role === 'konselor') {
                                echo '👨‍⚕️ Konselor';
                            } else {
                                echo '👤 Pengguna';
                            }
                            ?>
                        </p>
                    </div>

                    <div style="border-left-color: #3AAFA9;" class="border-l-4 pl-4">
                        <p style="color: var(--text-secondary);" class="text-sm uppercase">ID Pengguna</p>
                        <p class="text-lg font-semibold mt-1" style="color: var(--text-primary);">#<?= htmlspecialchars($user_id) ?></p>
                    </div>

                    <div style="border-left-color: #3AAFA9;" class="border-l-4 pl-4">
                        <p style="color: var(--text-secondary);" class="text-sm uppercase">Bergabung Sejak</p>
                        <p class="text-lg font-semibold mt-1" style="color: var(--text-primary);">
                            <?= date('d F Y H:i', strtotime($user['created_at'] ?? date('Y-m-d H:i:s'))) ?>
                        </p>
                    </div>

                    <div style="border-left-color: #3AAFA9;" class="border-l-4 pl-4">
                        <p style="color: var(--text-secondary);" class="text-sm uppercase">Status Akun</p>
                        <p class="text-lg font-semibold mt-1" style="color: var(--text-primary);">✅ Aktif</p>
                    </div>
                </div>
            </div>

            <!-- Account Statistics -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color);" class="rounded-2xl soft-shadow p-8">
                <h3 class="text-2xl font-bold mb-6" style="color: var(--text-primary);">📊 Statistik</h3>
                
                <div class="space-y-6">
                    <div style="background: linear-gradient(135deg, rgba(58, 175, 169, 0.1) 0%, var(--bg-tertiary) 100%);" class="flex items-center justify-between p-4 rounded-lg">
                        <div>
                            <p style="color: var(--text-secondary);" class="text-sm">Total Sesi Chat</p>
                            <p class="text-2xl font-bold mt-1" style="color: var(--text-primary);"><?= intval($total_sessions) ?></p>
                        </div>
                        <div class="text-4xl">💬</div>
                    </div>

                    <div style="background: linear-gradient(135deg, rgba(58, 175, 169, 0.08) 0%, var(--bg-tertiary) 100%);" class="flex items-center justify-between p-4 rounded-lg">
                        <div>
                            <p style="color: var(--text-secondary);" class="text-sm">Riwayat Aktivitas</p>
                            <p class="text-2xl font-bold mt-1" style="color: var(--text-primary);"><?= count($activities) ?></p>
                        </div>
                        <div class="text-4xl">📝</div>
                    </div>

                    <div style="background: linear-gradient(135deg, rgba(58, 175, 169, 0.08) 0%, var(--bg-tertiary) 100%);" class="flex items-center justify-between p-4 rounded-lg">
                        <div>
                            <p style="color: var(--text-secondary);" class="text-sm">Profil Lengkap</p>
                            <p class="text-2xl font-bold mt-1" style="color: var(--text-primary);">
                                <?= isset($user['profile_picture']) && $user['profile_picture'] ? '100%' : '80%' ?>
                            </p>
                        </div>
                        <div class="text-4xl">✅</div>
                    </div>

                    <div style="background: linear-gradient(135deg, rgba(58, 175, 169, 0.08) 0%, var(--bg-tertiary) 100%);" class="flex items-center justify-between p-4 rounded-lg">
                        <div>
                            <p style="color: var(--text-secondary);" class="text-sm">Member Sejak</p>
                            <p class="text-2xl font-bold mt-1" style="color: var(--text-primary);">
                                <?php
                                $createdAt = new DateTime($user['created_at'] ?? date('Y-m-d'));
                                $now = new DateTime();
                                $interval = $createdAt->diff($now);
                                echo $interval->days . ' hari';
                                ?>
                            </p>
                        </div>
                        <div class="text-4xl">📅</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Recent Activities -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color);" class="rounded-2xl soft-shadow p-8">
            <h3 class="text-2xl font-bold mb-6" style="color: var(--text-primary);">🔄 Aktivitas Terbaru</h3>
            
            <?php if (empty($activities)): ?>
                <div class="text-center py-12">
                    <p style="color: var(--text-secondary);" class="text-lg">Belum ada aktivitas</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($activities as $activity): ?>
                        <div style="border-left-color: #3AAFA9; background: var(--bg-secondary); border: 1px solid var(--border-color);" class="flex items-start gap-4 p-4 border-l-4 rounded-lg hover:shadow-md transition">
                            <div class="flex-shrink-0 w-12 h-12 flex items-center justify-center" style="background: #3AAFA9; color: white;" class="rounded-lg text-xl">
                                <?= formatActivity($activity) ?>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold" style="color: var(--text-primary);"><?= formatActivity($activity) ?></p>
                                <p style="color: var(--text-secondary);" class="text-sm mt-1">
                                    <?php
                                    if (!empty($activity['details'])) {
                                        $details = json_decode($activity['details'], true);
                                        if (is_array($details)) {
                                            echo htmlspecialchars(implode(', ', array_values($details)));
                                        }
                                    }
                                    ?>
                                </p>
                                <p style="color: var(--text-tertiary);" class="text-xs mt-2">
                                    <?= date('d M Y H:i', strtotime($activity['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<style>
.soft-shadow { box-shadow: 0 10px 30px rgba(0,0,0,0.06); }

html.dark-mode .bg-gradient-to-r {
    background: linear-gradient(to right, rgba(58, 175, 169, 0.15), rgba(58, 175, 169, 0.05));
}

html.dark-mode .bg-blue-100,
html.dark-mode .bg-green-100,
html.dark-mode .bg-purple-100 {
    background: rgba(58, 175, 169, 0.1);
}

html.dark-mode .border-l-4 {
    border-left-color: #4DBBB0;
}

html.dark-mode .bg-\[\#F7FBFB\] {
    background-color: var(--bg-tertiary);
}
/* small helper styles for this page */
.theme-active { transition: background-color 0.25s ease, color 0.25s ease; }
</style>

<script>
// Theme toggle: persists to localStorage and applies 'dark-mode' on html
(function(){
    const key = 'darkMode';
    const btn = document.getElementById('theme-toggle');
    if (!btn) return;

    function applyDark(dark){
        if(dark) document.documentElement.classList.add('dark-mode');
        else document.documentElement.classList.remove('dark-mode');
        btn.textContent = dark ? '☀️' : '🌙';
    }

    // init from localStorage (align with global init in index.php)
    try{
        const stored = localStorage.getItem(key);
        const isDark = stored === 'true' || (stored === null && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches);
        applyDark(isDark);
    }catch(e){ /* ignore storage errors */ }

    btn.addEventListener('click', function(){
        const isDark = document.documentElement.classList.toggle('dark-mode');
        try{ localStorage.setItem(key, isDark ? 'true' : 'false'); }catch(e){}
        applyDark(isDark);
    });
})();
</script>
<?php
