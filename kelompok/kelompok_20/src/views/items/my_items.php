
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-8">
    <div class="container mx-auto px-4">
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                    <span class="gradient-text">Laporan Saya</span>
                </h1>
                <p class="text-slate-600 dark:text-slate-400">
                    Kelola semua laporan barang hilang dan temuan Anda
                </p>
            </div>
            <a 
                href="<?= base_url('index.php?page=items&action=create') ?>"
                class="inline-flex items-center justify-center gap-2 px-6 py-3.5 gradient-primary text-white rounded-xl font-semibold shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 transition-all"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Buat Laporan Baru
            </a>
        </div>

        <?php if (empty($items)): ?>
            
            <div class="glass-card rounded-2xl p-12 text-center">
                <div class="max-w-md mx-auto">
                    
                    <div class="mb-6">
                        <svg class="w-32 h-32 mx-auto text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    
                    
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                        Belum Ada Laporan
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400 mb-6">
                        Anda belum membuat laporan barang hilang atau temuan. Mulai sekarang dengan membuat laporan pertama Anda!
                    </p>
                    
                    
                    <a 
                        href="<?= base_url('index.php?page=items&action=create') ?>"
                        class="inline-flex items-center gap-2 px-8 py-4 gradient-primary text-white rounded-xl font-semibold shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 transition-all"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Laporan Pertama
                    </a>
                </div>
            </div>
        <?php else: ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($items as $item): ?>
                    <div class="glass-card rounded-xl overflow-hidden group hover:shadow-xl transition-all duration-300">
                        
                        <a href="<?= base_url('index.php?page=items&action=show&id=' . $item['id']) ?>" class="block relative aspect-video bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 overflow-hidden">
                            <?php if (!empty($item['image_path'])): ?>
                                <img 
                                    src="<?= base_url($item['image_path']) ?>" 
                                    alt="<?= htmlspecialchars($item['title']) ?>"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center\'><svg class=\'w-16 h-16 text-slate-400 dark:text-slate-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg></div>'"
                                >
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            
                            
                            <div class="absolute top-3 right-3">
                                <?php if ($item['status'] === 'closed'): ?>
                                    <span class="px-3 py-1.5 bg-green-500 text-white rounded-full text-xs font-semibold shadow-lg flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Selesai
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1.5 bg-amber-500 text-white rounded-full text-xs font-semibold shadow-lg">
                                        Aktif
                                    </span>
                                <?php endif; ?>
                            </div>

                            
                            <div class="absolute top-3 left-3">
                                <?php if ($item['type'] === 'lost'): ?>
                                    <span class="px-3 py-1.5 bg-rose-500 text-white rounded-full text-xs font-semibold shadow-lg">
                                        Hilang
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1.5 bg-teal-500 text-white rounded-full text-xs font-semibold shadow-lg">
                                        Ditemukan
                                    </span>
                                <?php endif; ?>
                            </div>
                        </a>

                        
                        <div class="p-5">
                            
                            <a href="<?= base_url('index.php?page=items&action=show&id=' . $item['id']) ?>" class="block mb-3">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors line-clamp-2">
                                    <?= htmlspecialchars($item['title']) ?>
                                </h3>
                            </a>

                            
                            <div class="space-y-2 mb-4">
                                
                                <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span><?= date('d M Y', strtotime($item['incident_date'])) ?></span>
                                </div>

                                
                                <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="truncate"><?= htmlspecialchars($item['location_name'] ?? 'Tidak diketahui') ?></span>
                                </div>

                                
                                <div class="flex items-center gap-4 text-sm text-slate-600 dark:text-slate-400">
                                    
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span><?= $item['views'] ?? 0 ?></span>
                                    </div>

                                    
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span><?= $item['comments_count'] ?? 0 ?></span>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="border-t border-slate-200 dark:border-slate-700 pt-4 space-y-2">
                                
                                <?php if (!empty($item['claims_count']) && $item['claims_count'] > 0): ?>
                                    <a 
                                        href="<?= base_url('index.php?page=claims&action=index&item_id=' . $item['id']) ?>"
                                        class="w-full flex items-center justify-between px-4 py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-lg font-semibold transition-all shadow-lg shadow-amber-500/30 hover:shadow-xl"
                                    >
                                        <span class="flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Lihat Klaim
                                        </span>
                                        <span class="px-2.5 py-1 bg-white/20 rounded-full text-xs font-bold">
                                            <?= $item['claims_count'] ?>
                                        </span>
                                    </a>
                                <?php endif; ?>

                                
                                <div class="grid grid-cols-2 gap-2">
                                    
                                    <a 
                                        href="<?= base_url('index.php?page=items&action=edit&id=' . $item['id']) ?>"
                                        class="flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 dark:bg-primary-600 dark:hover:bg-primary-700 text-white rounded-lg font-medium transition-all"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>

                                    
                                    <form 
                                        action="<?= base_url('index.php?page=items&action=delete') ?>" 
                                        method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini? Tindakan ini tidak dapat dibatalkan.')"
                                        class="w-full"
                                    >
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <button 
                                            type="submit"
                                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-medium transition-all"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                
                <div class="glass-card rounded-xl p-5 text-center">
                    <div class="text-3xl font-bold text-slate-900 dark:text-white mb-1">
                        <?= count($items) ?>
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        Total Laporan
                    </div>
                </div>

                
                <div class="glass-card rounded-xl p-5 text-center">
                    <div class="text-3xl font-bold text-amber-600 dark:text-amber-400 mb-1">
                        <?= count(array_filter($items, function($item) { return $item['status'] === 'open'; })) ?>
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        Laporan Aktif
                    </div>
                </div>

                
                <div class="glass-card rounded-xl p-5 text-center">
                    <div class="text-3xl font-bold text-green-600 dark:text-green-400 mb-1">
                        <?= count(array_filter($items, function($item) { return $item['status'] === 'closed'; })) ?>
                    </div>
                    <div class="text-sm text-slate-600 dark:text-slate-400">
                        Laporan Selesai
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
