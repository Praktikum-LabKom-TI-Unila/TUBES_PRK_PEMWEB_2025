<?php
declare(strict_types=1);
// CATATAN: Pastikan Anda telah mendefinisikan fungsi base_url() dan timeAgo()
// serta variabel $unread_count dan $notifications (berisi 'id', 'title', 'message', 'link', 'is_read', 'type', 'created_at').
// Asumsi variabel $current_filter berisi string tipe filter yang sedang aktif (misal: 'all', 'unread', 'claims').
?>

<div class="min-h-screen gradient-mesh py-8 px-4" id="notification-page">
    <div class="container mx-auto max-w-2xl">
        
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                    <span class="text-primary-600 dark:text-primary-400">Pesan</span> Notifikasi Anda
                </h1>
                <p class="text-slate-600 dark:text-slate-400" id="unread-status">
                    <?php if ($unread_count > 0): ?>
                        <span class="font-bold text-primary-600 dark:text-primary-400" id="unread-count-display"><?= $unread_count ?></span> notifikasi belum dibaca
                    <?php else: ?>
                        Semua notifikasi sudah dibaca
                    <?php endif; ?>
                </p>
            </div>

            <button 
                id="mark-all-read-btn"
                class="px-4 py-2 border-2 border-primary-500 text-primary-600 dark:text-primary-400 rounded-lg hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all font-semibold text-sm flex items-center gap-2 <?= $unread_count > 0 ? '' : 'hidden' ?>"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tandai Semua Dibaca
            </button>
        </div>
        
        <div class="mb-8 overflow-x-auto">
            <div class="flex space-x-3 pb-2">
                <?php
                $filters = [
                    'all' => ['label' => 'Semua', 'count' => count($notifications)],
                    'unread' => ['label' => 'Belum Dibaca', 'count' => $unread_count],
                    'claims' => ['label' => 'Klaim', 'count' => array_reduce($notifications, fn($c, $n) => $c + (str_contains($n['type'] ?? '', 'claim')), 0)],
                    'reports' => ['label' => 'Laporan Saya', 'count' => array_reduce($notifications, fn($c, $n) => $c + (str_contains($n['type'] ?? '', 'item') || str_contains($n['type'] ?? '', 'new_claim')), 0)],
                ];
                $active_filter = $current_filter ?? 'all'; 
                ?>

                <?php foreach ($filters as $key => $filter): ?>
                    <?php 
                    $isActive = ($key === $active_filter);
                    $btnClass = $isActive 
                        ? 'gradient-primary text-white shadow-md' 
                        : 'bg-white dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700';
                    ?>
                    <a href="<?= base_url('index.php?page=notifications&filter=' . $key) ?>" 
                       class="flex-shrink-0 px-4 py-2 rounded-full font-medium text-sm transition-all <?= $btnClass ?>">
                        <?= $filter['label'] ?> 
                        <span class="ml-1 px-2 py-0.5 text-xs rounded-full <?= $isActive ? 'bg-white/30' : 'bg-slate-200 dark:bg-slate-600' ?>"><?= $filter['count'] ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <div id="notification-list-container">
            <?php if (empty($notifications)): ?>
                <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur-md rounded-3xl p-16 text-center border border-white/20 dark:border-slate-700/50 shadow-xl">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-100 dark:bg-slate-700/50 mb-6">
                        <svg class="w-12 h-12 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13.563l-1.406-1.406a2.03 2.03 0 01-.595-1.437V9.282a6.003 6.003 0 00-4-5.659V3a2 2 0 10-4 0v.623a6.003 6.003 0 00-4 5.659v1.438c0 .538-.214 1.055-.595 1.437L4 13.563M15 17v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">
                        Kotak masukmu bersih!
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400 max-w-md mx-auto">
                        Notifikasi akan muncul di sini ketika ada aktivitas terkait laporan atau klaim Anda.
                    </p>
                </div>
            <?php else: ?>
                <div class="space-y-3" id="notification-list">
                    <?php foreach ($notifications as $notification): ?>
                        <?php
                        // (Logic penentuan tipe, ikon, warna, dan $cardClasses/iconBg/iconFg tetap sama seperti revisi sebelumnya)
                        $isUnread = !$notification['is_read'];
                        $type = $notification['type'] ?? 'default';
                        
                        // Menentukan Ikon dan Warna berdasarkan Tipe Notifikasi
                        $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>';
                        $borderColors = 'border-primary-500';

                        switch ($type) {
                            case 'claim_accepted':
                                $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 018.618 3.047 12.007 12.007 0 00-2.396 6.385c.465 4.312 3.822 7.842 8.525 9.873.308.13.626.23.953.305v-12"/>';
                                $borderColors = 'border-teal-500';
                                break;
                            case 'claim_rejected':
                                $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>';
                                $borderColors = 'border-rose-500';
                                break;
                            case 'item_match':
                                $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>';
                                $borderColors = 'border-amber-500';
                                break;
                            case 'new_claim_on_my_item':
                                $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14v6m-3-3h6m-9-3h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>';
                                $borderColors = 'border-indigo-500';
                                break;
                        }

                        $cardClasses = $isUnread 
                            ? "bg-gradient-to-r from-cyan-50/50 to-blue-50/50 dark:from-cyan-900/20 dark:to-blue-900/20 border-l-4 {$borderColors}" 
                            : 'bg-white/40 dark:bg-slate-800/40';

                        $iconBg = $isUnread 
                            ? "bg-gradient-to-br from-cyan-400 to-blue-500" 
                            : "bg-slate-200 dark:bg-slate-700";
                        
                        $iconFg = $isUnread ? 'text-white' : 'text-slate-500 dark:text-slate-400';
                        ?>
                        <a 
                            href="<?= htmlspecialchars($notification['link'] ?? '#') ?>" 
                            id="notification-<?= $notification['id'] ?>"
                            data-notification-id="<?= $notification['id'] ?>"
                            onclick="markAsRead(event, <?= $notification['id'] ?>, '<?= htmlspecialchars($notification['link'] ?? '', ENT_QUOTES) ?>')"
                            class="notification-item block <?= $cardClasses ?> backdrop-blur-md rounded-xl shadow-lg border border-white/20 dark:border-slate-700/50 overflow-hidden hover:shadow-xl transition-all duration-300 group"
                        >
                            <div class="p-5 flex items-start gap-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-full <?= $iconBg ?> flex items-center justify-center shadow-lg">
                                        <svg class="w-6 h-6 <?= $iconFg ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <?= $iconSvg ?>
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

                                    <?php if (!empty($notification['link'])): ?>
                                        <div class="flex items-center gap-3">
                                            <span class="inline-flex items-center gap-1 text-primary-600 dark:text-primary-400 font-semibold text-sm group-hover:gap-2 transition-all">
                                                Lihat Detail
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="flex-shrink-0 self-center">
                                    <?php if ($isUnread): ?>
                                        <span class="unread-dot w-3 h-3 bg-gradient-to-r from-cyan-500 to-blue-500 rounded-full inline-block shadow-lg shadow-cyan-500/50 animate-pulse"></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Variabel untuk menyimpan jumlah notifikasi yang belum dibaca (Global state)
let unreadCount = <?= $unread_count ?>;

// --- MARK AS READ (Single Item) ---
function markAsRead(e, id, url) {
    e.preventDefault();
    const fullUrl = '<?= base_url('index.php?page=notifications&action=markRead&id=') ?>' + id;
    const notificationElement = document.getElementById(`notification-${id}`);
    
    // Perubahan visual sementara (sebelum fetch selesai)
    if (notificationElement && notificationElement.classList.contains('border-l-4')) {
        const dot = notificationElement.querySelector('.unread-dot');
        if (dot) dot.remove();
        
        // Ubah styling dari unread ke read
        notificationElement.classList.remove('border-l-4');
        const iconContainer = notificationElement.querySelector('.w-12.h-12');
        if (iconContainer) {
            // Ubah gradien unread menjadi warna read (default styling)
            iconContainer.classList.remove('bg-gradient-to-br', 'from-cyan-400', 'to-blue-500');
            iconContainer.classList.add('bg-slate-200', 'dark:bg-slate-700'); 
            const svgIcon = iconContainer.querySelector('svg');
            if (svgIcon) svgIcon.classList.remove('text-white');
        }

        // Update Counter
        if (unreadCount > 0) {
            unreadCount--;
            updateUnreadCountDisplay();
        }
    }

    fetch(fullUrl, { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(response => {
        // Setelah sukses, navigasi
        if (url && url !== '#' && url !== '') {
            const finalUrl = url.startsWith('http') ? url : '<?= base_url('') ?>' + url;
            window.location.href = finalUrl;
        } else {
             // Jika tidak ada link, biarkan di halaman ini (karena sudah diupdate visualnya)
        }
    })
    .catch(error => {
        console.error('Error marking as read:', error);
        // Jika gagal, bisa dikembalikan visualnya atau tetap navigasi
        if (url && url !== '#' && url !== '') {
            const finalUrl = url.startsWith('http') ? url : '<?= base_url('') ?>' + url;
            window.location.href = finalUrl;
        }
    });
}

// --- LIVE UPDATE LOGIC ---

// Fungsi untuk memperbarui tampilan jumlah notifikasi yang belum dibaca
function updateUnreadCountDisplay() {
    const display = document.getElementById('unread-count-display');
    const statusText = document.getElementById('unread-status');
    const markAllBtn = document.getElementById('mark-all-read-btn');

    if (display) display.textContent = unreadCount.toString();

    if (unreadCount > 0) {
        statusText.innerHTML = `<span class="font-bold text-primary-600 dark:text-primary-400" id="unread-count-display">${unreadCount}</span> notifikasi belum dibaca`;
        if (markAllBtn) markAllBtn.classList.remove('hidden');
    } else {
        statusText.textContent = 'Semua notifikasi sudah dibaca';
        if (markAllBtn) markAllBtn.classList.add('hidden');
    }
}


// --- MARK ALL READ (AJAX Update) ---
document.getElementById('mark-all-read-btn')?.addEventListener('click', function(e) {
    e.preventDefault();
    
    // Perubahan visual instan (optimasi UX)
    const items = document.querySelectorAll('.notification-item');
    items.forEach(item => {
        if (item.classList.contains('border-l-4')) {
            item.classList.remove('border-l-4');
            const dot = item.querySelector('.unread-dot');
            if (dot) dot.remove();

            const iconContainer = item.querySelector('.w-12.h-12');
            if (iconContainer) {
                // Ubah styling dari unread ke read
                iconContainer.classList.remove('bg-gradient-to-br', 'from-cyan-400', 'to-blue-500');
                iconContainer.classList.add('bg-slate-200', 'dark:bg-slate-700'); 
                const svgIcon = iconContainer.querySelector('svg');
                if (svgIcon) svgIcon.classList.remove('text-white');
            }
        }
    });

    unreadCount = 0;
    updateUnreadCountDisplay();
    
    // Kirim permintaan ke backend
    fetch('<?= base_url('index.php?page=notifications&action=mark-all-read') ?>', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            console.error('Failed to mark all as read on server.');
            // Jika ada error, mungkin perlu menampilkan pesan atau reload
        }
    })
    .catch(error => {
        console.error('Error during mark all as read AJAX:', error);
    });
});
</script>