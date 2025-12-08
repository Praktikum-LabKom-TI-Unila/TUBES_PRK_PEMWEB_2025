
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-cyan-50/30 to-slate-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-900 py-8">
    <div class="container mx-auto px-4">
        
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-2">
                        <span class="gradient-text">Daftar Laporan</span>
                    </h1>
                    <p class="text-slate-600 dark:text-slate-400">
                        Total <?= number_format($pagination['total_items']) ?> laporan ditemukan
                    </p>
                </div>
                
                <?php if (isLoggedIn()): ?>
                <a 
                    href="<?= base_url('index.php?page=items&action=create') ?>" 
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 to-cyan-600 hover:from-primary-700 hover:to-cyan-700 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30 transition-all duration-300 hover:scale-105"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Laporan
                </a>
                <?php endif; ?>
            </div>

            
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-md rounded-2xl shadow-xl border border-slate-200/50 dark:border-slate-700/50 overflow-hidden">
                <form method="GET" action="<?= base_url('index.php') ?>">
                    <input type="hidden" name="page" value="items">
                    
                    
                    <div class="px-6 py-4 bg-gradient-to-r from-slate-100 to-slate-50 dark:from-slate-800 dark:to-slate-900 border-b border-slate-200 dark:border-slate-700">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                            Filter Pencarian
                        </h2>
                    </div>

                    
                    <div class="p-6">
                        
                        <div class="mb-5">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input 
                                    type="text" 
                                    name="q" 
                                    value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                                    placeholder="Cari barang berdasarkan nama atau deskripsi..."
                                    class="w-full pl-12 pr-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                >
                            </div>
                        </div>

                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-5">
                            
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 uppercase tracking-wide">
                                    Jenis Laporan
                                </label>
                                <select 
                                    name="type"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                >
                                    <option value="">Semua Jenis</option>
                                    <option value="lost" <?= ($_GET['type'] ?? '') === 'lost' ? 'selected' : '' ?>>Kehilangan</option>
                                    <option value="found" <?= ($_GET['type'] ?? '') === 'found' ? 'selected' : '' ?>>Ditemukan</option>
                                </select>
                            </div>

                            
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 uppercase tracking-wide">
                                    Status
                                </label>
                                <select 
                                    name="status"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                >
                                    <option value="">Semua Status</option>
                                    <option value="open" <?= ($_GET['status'] ?? '') === 'open' ? 'selected' : '' ?>>Terbuka</option>
                                    <option value="process" <?= ($_GET['status'] ?? '') === 'process' ? 'selected' : '' ?>>Diproses</option>
                                    <option value="closed" <?= ($_GET['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Selesai</option>
                                </select>
                            </div>

                            
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 uppercase tracking-wide">
                                    Kategori
                                </label>
                                <select 
                                    name="category"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                >
                                    <option value="">Semua Kategori</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= ($_GET['category'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 uppercase tracking-wide">
                                    Lokasi
                                </label>
                                <select 
                                    name="location"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                >
                                    <option value="">Semua Lokasi</option>
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?= $loc['id'] ?>" <?= ($_GET['location'] ?? '') == $loc['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($loc['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2 uppercase tracking-wide">
                                    Urutkan
                                </label>
                                <select 
                                    name="sort"
                                    class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-600 rounded-lg text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                >
                                    <option value="newest" <?= ($_GET['sort'] ?? 'newest') === 'newest' ? 'selected' : '' ?>>Terbaru</option>
                                    <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Terlama</option>
                                </select>
                            </div>
                        </div>

                        
                        <div class="flex gap-3">
                            <button 
                                type="submit"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-primary-600 to-cyan-600 hover:from-primary-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg shadow-primary-500/20 transition-all duration-200 hover:shadow-xl hover:shadow-primary-500/30 flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                Terapkan Filter
                            </button>
                            <a 
                                href="<?= base_url('index.php?page=items') ?>"
                                class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600 transition-all flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        
        <?php if (empty($items)): ?>
            <div class="text-center py-20">
                <svg class="w-20 h-20 text-slate-400 dark:text-slate-600 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Tidak ada laporan ditemukan</h3>
                <p class="text-slate-600 dark:text-slate-400 mb-6">Coba ubah filter atau buat laporan baru</p>
                <?php if (isLoggedIn()): ?>
                <a 
                    href="<?= base_url('index.php?page=items&action=create') ?>" 
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-primary-600 to-cyan-600 hover:from-primary-700 hover:to-cyan-700 text-white font-semibold rounded-lg shadow-lg transition-colors duration-200"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Laporan Pertama
                </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                <?php foreach ($items as $item): ?>
                    <a 
                        href="<?= base_url('index.php?page=items&action=show&id=' . $item['id']) ?>"
                        class="group bg-white/80 dark:bg-slate-800/80 backdrop-blur-md rounded-lg shadow-lg hover:shadow-xl border border-white/20 dark:border-slate-700/50 overflow-hidden transition-all duration-200"
                    >
                        
                        <div class="aspect-square bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 relative overflow-hidden">
                            <?php if (!empty($item['image_path'])): ?>
                                <img 
                                    src="<?= base_url($item['image_path']) ?>" 
                                    alt="<?= htmlspecialchars($item['title']) ?>"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    onerror="this.parentElement.innerHTML='<svg class=\'w-20 h-20 text-slate-400 dark:text-slate-600 absolute inset-0 m-auto\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg>'"
                                >
                            <?php else: ?>
                                <svg class="w-20 h-20 text-slate-400 dark:text-slate-600 absolute inset-0 m-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            <?php endif; ?>
                            
                            
                            <div class="absolute top-3 left-3">
                                <?php if ($item['type'] === 'lost'): ?>
                                    <span class="px-3 py-1 bg-rose-500 text-white text-xs font-semibold rounded-lg shadow-md">
                                        Kehilangan
                                    </span>
                                <?php else: ?>
                                    <span class="px-3 py-1 bg-teal-500 text-white text-xs font-semibold rounded-lg shadow-md">
                                        Ditemukan
                                    </span>
                                <?php endif; ?>
                            </div>

                            
                            <div class="absolute top-3 right-3">
                                <?php if ($item['status'] === 'closed'): ?>
                                    <span class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-lg shadow-md">
                                        Selesai
                                    </span>
                                <?php elseif ($item['status'] === 'process'): ?>
                                    <span class="px-3 py-1 bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-md">
                                        Diproses
                                    </span>
                                <?php endif; ?>
                            </div>

                            
                            <?php if (!empty($item['is_safe_claim'])): ?>
                                <div class="absolute bottom-3 right-3">
                                    <span class="px-3 py-1 bg-purple-500 text-white text-xs font-semibold rounded-lg shadow-md flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Safe
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        
                        <div class="p-4">
                            <h3 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                <?= htmlspecialchars($item['title']) ?>
                            </h3>
                            
                            <div class="space-y-1.5 text-sm text-slate-600 dark:text-slate-400">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                    <span class="truncate"><?= htmlspecialchars($item['category_name'] ?? 'Lainnya') ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="truncate"><?= htmlspecialchars($item['location_name'] ?? 'Tidak diketahui') ?></span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span><?= date('d M Y', strtotime($item['incident_date'])) ?></span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            
            <?php if ($pagination['total_pages'] > 1): ?>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 bg-white/60 dark:bg-slate-800/60 backdrop-blur-md rounded-lg p-6 shadow-lg border border-white/20 dark:border-slate-700/50">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Halaman <span class="font-semibold text-slate-900 dark:text-white"><?= $pagination['current_page'] ?></span> dari <span class="font-semibold text-slate-900 dark:text-white"><?= $pagination['total_pages'] ?></span>
                    </p>
                    
                    <div class="flex gap-2">
                        <?php
                        $currentQueryParams = $_GET;
                        unset($currentQueryParams['page']);
                        $queryString = http_build_query($currentQueryParams);
                        $baseUrl = 'index.php?' . ($queryString ? $queryString . '&' : '') . 'page=items&';
                        ?>

                        
                        <?php if ($pagination['has_prev']): ?>
                            <a 
                                href="<?= base_url($baseUrl . 'page=' . ($pagination['current_page'] - 1)) ?>"
                                class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors"
                            >
                                ← Prev
                            </a>
                        <?php endif; ?>

                        
                        <?php
                        $start = max(1, $pagination['current_page'] - 2);
                        $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                        
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                            <a 
                                href="<?= base_url($baseUrl . 'page=' . $i) ?>"
                                class="px-4 py-2 font-semibold rounded-lg transition-colors <?= $i === $pagination['current_page'] ? 'bg-gradient-to-r from-primary-600 to-cyan-600 text-white shadow-md' : 'bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-600' ?>"
                            >
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        
                        <?php if ($pagination['has_next']): ?>
                            <a 
                                href="<?= base_url($baseUrl . 'page=' . ($pagination['current_page'] + 1)) ?>"
                                class="px-4 py-2 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-semibold rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors"
                            >
                                Next →
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
