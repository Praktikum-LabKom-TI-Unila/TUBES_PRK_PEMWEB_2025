<?php


declare(strict_types=1);
?>

<div class="container mx-auto px-4 py-8">
    
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                🔔 Notifikasi
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                <?php if ($unreadCount > 0): ?>
                    Anda memiliki <span class="font-semibold text-cyan-500"><?= $unreadCount ?></span> notifikasi belum dibaca
                <?php else: ?>
                    Semua notifikasi sudah dibaca
                <?php endif; ?>
            </p>
        </div>

        <?php if ($unreadCount > 0): ?>
        <form action="<?= base_url('index.php?page=notifications&action=mark-all-read') ?>" method="POST">
            <button type="submit" 
                    class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition-colors text-sm">
                Tandai Semua Dibaca
            </button>
        </form>
        <?php endif; ?>
    </div>

    <?php if (empty($notifications)): ?>
    
    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-12 text-center">
        <div class="text-6xl mb-4">🔕</div>
        <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">
            Tidak Ada Notifikasi
        </h3>
        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
            Anda belum memiliki notifikasi. Notifikasi akan muncul saat ada aktivitas terkait laporan Anda.
        </p>
    </div>

    <?php else: ?>
    
    <div class="space-y-3">
        <?php foreach ($notifications as $notification): ?>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden
            <?= !$notification['is_read'] ? 'border-l-4 border-cyan-500' : '' ?>">
            <div class="p-4 flex items-start gap-4">
                
                <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center
                    <?= !$notification['is_read'] 
                        ? 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400' 
                        : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' ?>">
                    <?php

                    $icon = '🔔';
                    if (str_contains($notification['title'], 'Komentar')) $icon = '💬';
                    elseif (str_contains($notification['title'], 'Klaim')) $icon = '📋';
                    elseif (str_contains($notification['title'], 'Diverifikasi')) $icon = '✅';
                    elseif (str_contains($notification['title'], 'Ditolak')) $icon = '❌';
                    echo $icon;
                    ?>
                </div>

                
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <h4 class="font-semibold text-gray-900 dark:text-white text-sm">
                            <?= htmlspecialchars($notification['title']) ?>
                        </h4>
                        <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                            <?= timeAgo($notification['created_at']) ?>
                        </span>
                    </div>
                    
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                        <?= htmlspecialchars($notification['message']) ?>
                    </p>

                    
                    <div class="flex items-center gap-3 mt-3">
                        <?php if (!empty($notification['link'])): ?>
                        <a href="<?= base_url($notification['link']) ?>" 
                           class="text-cyan-500 hover:text-cyan-600 text-sm font-medium">
                            Lihat Detail →
                        </a>
                        <?php endif; ?>

                        <?php if (!$notification['is_read']): ?>
                        <form action="<?= base_url('index.php?page=notifications&action=mark-read') ?>" method="POST" class="inline">
                            <input type="hidden" name="id" value="<?= $notification['id'] ?>">
                            <button type="submit" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 text-sm">
                                Tandai Dibaca
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>

                
                <?php if (!$notification['is_read']): ?>
                <div class="flex-shrink-0">
                    <span class="w-2 h-2 bg-cyan-500 rounded-full inline-block"></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
