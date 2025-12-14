<?php
$user = current_user();
?>

<nav class="fixed top-0 left-0 right-0 bg-white border-b border-slate-200 h-16 flex items-center px-6 justify-between shadow-sm z-20">
    <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-semibold">IM</div>
        <div>
            <p class="text-sm font-semibold text-slate-700">Inventory Manager</p>
            <p class="text-xs text-slate-500">Sistem Manajemen Stok Bahan Baku</p>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <div class="text-right">
            <p class="text-sm font-semibold text-slate-700"><?= e($user['name'] ?? 'User') ?></p>
            <p class="text-xs text-slate-500"><?= e($user['role_name'] ?? 'Staff') ?></p>
        </div>
        <div class="h-10 w-10 rounded-full bg-purple-500 text-white flex items-center justify-center font-semibold">
            <?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?>
        </div>
    </div>
</nav>
