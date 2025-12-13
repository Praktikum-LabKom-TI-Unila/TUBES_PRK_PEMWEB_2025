<?php
$userName = htmlspecialchars($user['name'] ?? 'User');
$userEmail = htmlspecialchars($user['email'] ?? '');
$userPhone = htmlspecialchars($user['phone'] ?? '');
$userCreatedAt = isset($user['created_at']) ? date('d F Y', strtotime($user['created_at'])) : date('d F Y');
$userInitial = strtoupper(substr($userName, 0, 1));
?>

<section class="p-6 md:p-10 space-y-8">

<!-- ================= HEADER PROFIL ================= -->

<div class="bg-gradient-to-r from-indigo-600 to-indigo-500 text-white rounded-2xl p-8 shadow-lg">
    <div class="flex items-center gap-6">
        <!-- Avatar -->
        <div class="relative group">
            <?php if (!empty($user['avatar_url'])): ?>
                <img id="headerAvatar" src="<?= htmlspecialchars($user['avatar_url']) ?>" alt="Avatar" class="w-24 h-24 rounded-full object-cover shadow-lg border-4 border-white">
            <?php else: ?>
                <div id="headerAvatar" class="w-24 h-24 rounded-full bg-white text-indigo-600 flex items-center justify-center text-4xl font-bold shadow-lg">
                    <?= $userInitial ?>
                </div>
            <?php endif; ?>
            <button onclick="document.getElementById('avatarInput').click()" class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all cursor-pointer">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </button>
            <input type="file" id="avatarInput" accept="image/*" class="hidden">
        </div>

        <div class="flex-1">
            <h1 class="text-2xl font-semibold"><?= $userName ?></h1>
            <p class="opacity-90"><?= $userEmail ?></p>

            <p class="opacity-90 mt-2">
                Bergabung sejak  
                <span class="font-semibold"><?= $userCreatedAt ?></span>
            </p>
        </div>
    </div>
</div>

<!-- ================= NAVIGATION TAB ================= -->

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
    <div class="flex gap-8">
        <button id="tab-profil" class="tab-active text-indigo-600 font-semibold border-b-2 border-indigo-600 pb-2">Edit Profil</button>
        <button id="tab-password" class="text-slate-600 hover:text-indigo-600 pb-2">Ganti Password</button>
        <button id="tab-log" class="text-slate-600 hover:text-indigo-600 pb-2">Aktivitas Log</button>
    </div>
</div>

<!-- ============================================================= -->
<!-- ===================== EDIT PROFIL (DEFAULT) ================= -->
<!-- ============================================================= -->

<div id="content-profil" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">

    <h2 class="text-xl font-semibold text-slate-800 mb-6">Edit Profil</h2>

    <!-- Avatar Upload Section -->
    <div class="mb-6 p-4 bg-slate-50 rounded-lg">
        <label class="block text-sm font-medium text-slate-700 mb-3">Foto Profil</label>
        <div class="flex items-center gap-4">
            <?php if (!empty($user['avatar_url'])): ?>
                <img id="profileAvatar" src="<?= htmlspecialchars($user['avatar_url']) ?>" alt="Avatar" class="w-20 h-20 rounded-full object-cover border-2 border-slate-300">
            <?php else: ?>
                <div id="profileAvatar" class="w-20 h-20 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-2xl font-bold border-2 border-slate-300">
                    <?= $userInitial ?>
                </div>
            <?php endif; ?>
            <div>
                <button type="button" onclick="document.getElementById('avatarInput').click()" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    Upload Foto
                </button>
                <p class="text-xs text-slate-500 mt-2">JPG, PNG max 2MB</p>
            </div>
        </div>
    </div>

    <form id="profileForm" class="grid grid-cols-1 md:grid-cols-2 gap-5">

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap</label>
            <input type="text" id="name" value="<?= $userName ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
            <input type="email" id="email" value="<?= $userEmail ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">No. Telepon</label>
            <input type="text" id="phone" value="<?= $userPhone ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Role</label>
            <input type="text" value="<?= htmlspecialchars($user['role_name'] ?? 'User') ?>" class="w-full px-4 py-2 border border-slate-300 rounded-lg bg-slate-50" disabled>
        </div>

    </form>

    <div class="mt-6 flex gap-3">
        <button onclick="Profile.updateProfile()" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
            Simpan Perubahan
        </button>
        <button onclick="Profile.resetForm()" class="px-6 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-lg transition">
            Reset
        </button>
    </div>

</div>

<!-- ============================================================= -->
<!-- ===================== GANTI PASSWORD ========================= -->
<!-- ============================================================= -->

<div id="content-password" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hidden">

    <h2 class="text-xl font-semibold text-slate-800 mb-6">Ganti Password</h2>

    <form id="passwordForm" class="grid grid-cols-1 gap-5 max-w-md">

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Password Lama</label>
            <input type="password" id="oldPassword" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Password Baru</label>
            <input type="password" id="newPassword" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <p class="text-xs text-slate-500 mt-1">Minimal 6 karakter</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi Password Baru</label>
            <input type="password" id="confirmPassword" class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

    </form>

    <div class="mt-6 flex gap-3">
        <button onclick="Profile.updatePassword()" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
            Simpan Password Baru
        </button>
        <button onclick="document.getElementById('passwordForm').reset()" class="px-6 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-lg transition">
            Reset
        </button>
    </div>

</div>

<!-- ============================================================= -->
<!-- ======================== AKTIVITAS LOG ======================= -->
<!-- ============================================================= -->

<div id="content-log" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hidden">

    <h2 class="text-xl font-semibold text-slate-800 mb-6">Aktivitas Terakhir</h2>

    <div class="overflow-x-auto">
        <table class="w-full">

            <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Aktivitas</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">IP Address</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100">
                <?php if (!empty($activities)): ?>
                    <?php foreach ($activities as $activity): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-4 text-sm text-slate-600">
                            <?= date('d M Y H:i', strtotime($activity['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-800">
                            <?= htmlspecialchars($activity['action']) ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            <?= htmlspecialchars($activity['ip_address'] ?? '-') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-400">
                            Belum ada aktivitas
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>
    </div>

</div>

</section>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 z-50 hidden">
    <div class="bg-white rounded-lg shadow-lg border border-slate-200 p-4 max-w-sm">
        <div class="flex items-start gap-3">
            <div id="toastIcon" class="flex-shrink-0"></div>
            <div class="flex-1">
                <h4 id="toastTitle" class="font-semibold text-slate-800"></h4>
                <p id="toastMessage" class="text-sm text-slate-600 mt-1"></p>
            </div>
            <button onclick="Profile.hideToast()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<script src="/assets/js/modules/profile.js"></script>
<script>
    const tabProfil = document.getElementById("tab-profil");
    const tabPassword = document.getElementById("tab-password");
    const tabLog = document.getElementById("tab-log");

    const contentProfil = document.getElementById("content-profil");
    const contentPassword = document.getElementById("content-password");
    const contentLog = document.getElementById("content-log");

    function resetTabs() {
        // Reset tab styling
        [tabProfil, tabPassword, tabLog].forEach(tab => {
            tab.classList.remove("tab-active", "text-indigo-600", "font-semibold", "border-b-2", "border-indigo-600");
            tab.classList.add("text-slate-600");
        });

        // Hide all content
        contentProfil.classList.add("hidden");
        contentPassword.classList.add("hidden");
        contentLog.classList.add("hidden");
    }

    tabProfil.onclick = () => {
        resetTabs();
        tabProfil.classList.add("tab-active", "text-indigo-600", "font-semibold", "border-b-2", "border-indigo-600");
        tabProfil.classList.remove("text-slate-600");
        contentProfil.classList.remove("hidden");
    };

    tabPassword.onclick = () => {
        resetTabs();
        tabPassword.classList.add("tab-active", "text-indigo-600", "font-semibold", "border-b-2", "border-indigo-600");
        tabPassword.classList.remove("text-slate-600");
        contentPassword.classList.remove("hidden");
    };

    tabLog.onclick = () => {
        resetTabs();
        tabLog.classList.add("tab-active", "text-indigo-600", "font-semibold", "border-b-2", "border-indigo-600");
        tabLog.classList.remove("text-slate-600");
        contentLog.classList.remove("hidden");
    };
</script>
