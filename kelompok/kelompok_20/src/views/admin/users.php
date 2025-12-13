<div id="section-users" class=" space-y-6">
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-lg font-bold text-slate-800">Manajemen Pengguna</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User Profile</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada pengguna</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-9 w-9 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                        <?= strtoupper(substr($user['name'] ?? '', 0, 1)) ?>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($user['name'] ?? '') ?></div>
                                        <div class="text-xs text-gray-500">NPM: <?= htmlspecialchars($user['npm'] ?? '') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($user['email'] ?? '') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php $uid = (int)($user['id'] ?? 0); ?>
                                <?php $isActive = isset($user['is_active']) ? (int)$user['is_active'] : 0; ?>
                                <span id="status-badge-<?= $uid ?>" class="px-2.5 py-1 text-xs font-bold rounded-full <?= $isActive ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                    <?= $isActive ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <?php if (isset($_SESSION['user']['id']) && $_SESSION['user']['id'] == $uid): ?>
                                    <button class="text-gray-400 border border-gray-200 px-3 py-1.5 rounded-md text-xs font-medium shadow-sm w-24" disabled>--</button>
                                <?php else: ?>
                                    <?php if ($isActive): ?>
                                        <button data-admin-action="toggle_active" data-id="<?= $uid ?>" data-confirm="Apakah Anda yakin ingin menonaktifkan user ini?" class="text-amber-600 hover:text-white hover:bg-amber-500 border border-amber-200 px-3 py-1.5 rounded-md transition text-xs font-medium shadow-sm w-24">
                                            <i class="fa-solid fa-ban mr-1"></i> Ban User
                                        </button>
                                    <?php else: ?>
                                        <button data-admin-action="toggle_active" data-id="<?= $uid ?>" data-confirm="Aktifkan kembali user ini?" class="text-green-600 hover:text-white hover:bg-green-500 border border-green-200 px-3 py-1.5 rounded-md transition text-xs font-medium shadow-sm w-24">
                                            <i class="fa-solid fa-check mr-1"></i> Unban
                                        </button>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
