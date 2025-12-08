<?php
declare(strict_types=1);
?>

<div class="min-h-screen gradient-mesh py-8 px-4">
    <div class="container mx-auto max-w-2xl">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                    Notifikasi Anda
                </h1>
                <p class="text-slate-600 dark:text-slate-400">
                    <?php if ($unread_count > 0): ?>
                        <span class="font-semibold text-primary-600 dark:text-primary-400"><?= $unread_count ?></span> notifikasi belum dibaca
                    <?php else: ?>
                        Semua notifikasi sudah dibaca
                    <?php endif; ?>
                </p>
            </div>

            <?php if ($unread_count > 0): ?>
                <a 
                    href="<?= base_url('index.php?page=notifications&action=mark-all-read') ?>" 
                    class="px-4 py-2 border-2 border-primary-500 text-primary-600 dark:text-primary-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all font-semibold text-sm flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Tandai Semua Dibaca
                </a>
            <?php endif; ?>
        </div>

        <?php if (empty($notifications)): ?>
            <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur-md rounded-3xl p-16 text-center border border-white/20 dark:border-slate-700/50 shadow-xl">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-700/50 mb-6">
                    <svg class="w-12 h-12 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13.563l-1.406-1.406a2.03 2.03 0 01-.595-1.437V9.282a6.003 6.003 0 00-4-5.659V3a2 2 0 10-4 0v.623a6.003 6.003 0 00-4 5.659v1.438c0 .538-.214 1.055-.595 1.437L4 13.563M15 17v1a3 3 0 11-6 0v-1m6 0H9"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">
                    Belum ada notifikasi baru
                </h3>
                <p class="text-slate-600 dark:text-slate-400 max-w-md mx-auto">
                    Notifikasi akan muncul di sini ketika ada aktivitas terkait laporan atau klaim Anda.
                </p>
            </div>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($notifications as $notification): ?>
                    <?php
                    $isUnread = !$notification['is_read'];
                    $cardClasses = $isUnread 
                        ? 'bg-gradient-to-r from-cyan-50/50 to-blue-50/50 dark:from-cyan-900/20 dark:to-blue-900/20 border-l-4 border-cyan-500' 
                        : 'bg-white/40 dark:bg-slate-800/40';
                    ?>
                    <a 
                        href="<?= htmlspecialchars($notification['link'] ?? '#') ?>" 
                        onclick="markAsRead(event, <?= $notification['id'] ?>, '<?= htmlspecialchars($notification['link'] ?? '', ENT_QUOTES) ?>')"
                        class="block <?= $cardClasses ?> backdrop-blur-md rounded-xl shadow-lg border border-white/20 dark:border-slate-700/50 overflow-hidden hover:shadow-xl transition-all duration-300 group"
                    >
                        <div class="p-5 flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full <?= $isUnread ? 'bg-gradient-to-br from-cyan-400 to-blue-500' : 'bg-slate-200 dark:bg-slate-700' ?> flex items-center justify-center shadow-lg">
                                    <svg class="w-6 h-6 <?= $isUnread ? 'text-white' : 'text-slate-500 dark:text-slate-400' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <h4 class="<?= $isUnread ? 'font-bold' : 'font-semibold' ?> text-slate-900 dark:text-white text-base group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                        <?= htmlspecialchars($notification['title']) ?>
                                    </h4>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap flex-shrink-0">
                                        <?= timeAgo($notification['created_at']) ?>
                                    </span>
                                </div>
                                
                                <p class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed mb-3 <?= $isUnread ? 'font-medium' : '' ?>">
                                    <?= htmlspecialchars($notification['message']) ?>
                                </p>

                                <div class="flex items-center gap-3">
                                    <?php if (!empty($notification['link'])): ?>
                                        <span class="inline-flex items-center gap-1 text-primary-600 dark:text-primary-400 font-semibold text-sm group-hover:gap-2 transition-all">
                                            Lihat Detail
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($isUnread): ?>
                                <div class="flex-shrink-0 self-center">
                                    <span class="w-3 h-3 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full inline-block shadow-lg shadow-cyan-500/50 animate-pulse"></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function markAsRead(e, id, url) {
    e.preventDefault();
    
    fetch('<?= base_url('index.php?page=notifications&action=markRead&id=') ?>' + id, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (url && url !== '#' && url !== '') {
            window.location.href = '<?= base_url('') ?>' + url;
        } else {
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (url && url !== '#' && url !== '') {
            window.location.href = '<?= base_url('') ?>' + url;
        }
    });
}
</script>