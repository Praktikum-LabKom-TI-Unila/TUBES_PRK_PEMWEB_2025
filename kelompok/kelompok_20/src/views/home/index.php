<!-- Hero Section with Search -->
<section class="gradient-mesh min-h-[85vh] flex items-center relative">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-400/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-sky-400/20 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container mx-auto px-4 py-20 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-sm font-medium mb-6 animate-fade-in">
                <span class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></span>
                Platform Resmi Universitas Lampung
            </div>
            
            <!-- Hero Title -->
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-slate-900 dark:text-white mb-8 leading-relaxed">
                Temukan Barang Hilang
                <span class="gradient-text block">di Kampus</span>
            </h1>
            
            <!-- Hero Description -->
            <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 mb-10 max-w-2xl mx-auto leading-relaxed">
                Platform digital untuk membantu civitas akademika Universitas Lampung dalam melaporkan dan menemukan barang hilang dengan mudah, cepat, dan aman.
            </p>
            
            <!-- Search Bar -->
            <form action="<?= base_url('index.php?page=items') ?>" method="GET" class="max-w-2xl mx-auto mb-8">
                <input type="hidden" name="page" value="items">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                        <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Cari barang hilang atau ditemukan..." 
                        class="w-full pl-16 pr-6 py-5 text-lg bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 rounded-2xl focus:outline-none focus:border-primary-500 dark:focus:border-primary-500 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 shadow-lg transition-all"
                    >
                </div>
            </form>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= base_url('index.php?page=items&action=create') ?>" class="inline-flex items-center justify-center gap-2 px-8 py-4 gradient-primary text-white rounded-2xl font-semibold shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 hover:-translate-y-1 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Laporkan Kehilangan
                </a>
                <a href="<?= base_url('index.php?page=items') ?>" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-2xl font-semibold border-2 border-slate-200 dark:border-slate-700 hover:border-primary-300 dark:hover:border-primary-600 hover:-translate-y-1 transition-all bento-shadow">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari Barang
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Live Statistics Section -->
<section class="py-16 bg-white dark:bg-slate-800/50 border-y border-slate-200 dark:border-slate-700/50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Lost Items Stat -->
            <div class="text-center p-6 group hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-rose-500 to-red-500 rounded-2xl flex items-center justify-center shadow-lg shadow-rose-500/20 group-hover:shadow-xl group-hover:shadow-rose-500/30 transition-all">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-4xl font-bold text-slate-900 dark:text-white mb-1">
                    <?= number_format($stats['total_lost'] ?? 0) ?>
                </h3>
                <p class="text-slate-600 dark:text-slate-400 font-medium">Barang Hilang</p>
            </div>
            
            <!-- Found Items Stat -->
            <div class="text-center p-6 group hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-teal-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-teal-500/20 group-hover:shadow-xl group-hover:shadow-teal-500/30 transition-all">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-4xl font-bold text-slate-900 dark:text-white mb-1">
                    <?= number_format($stats['total_found'] ?? 0) ?>
                </h3>
                <p class="text-slate-600 dark:text-slate-400 font-medium">Barang Ditemukan</p>
            </div>
            
            <!-- Returned Items Stat -->
            <div class="text-center p-6 group hover:-translate-y-2 transition-all duration-300">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:shadow-xl group-hover:shadow-amber-500/30 transition-all">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-4xl font-bold text-slate-900 dark:text-white mb-1">
                    <?= number_format($stats['total_returned'] ?? 0) ?>
                </h3>
                <p class="text-slate-600 dark:text-slate-400 font-medium">Berhasil Dikembalikan</p>
            </div>
        </div>
    </div>
</section>

<!-- Recent Items Section -->
<section class="py-20 bg-slate-50 dark:bg-slate-900/50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                Laporan Terbaru
            </h2>
            <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                Daftar barang hilang dan ditemukan terbaru di lingkungan Universitas Lampung
            </p>
        </div>
        
        <?php if (!empty($recentItems)): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($recentItems as $item): ?>
                    <article class="bg-white dark:bg-slate-800 rounded-2xl overflow-hidden bento-shadow hover:shadow-xl transition-all duration-300 group">
                        <!-- Item Image -->
                        <div class="aspect-video bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 relative overflow-hidden">
                            <?php if (!empty($item['image_path'])): ?>
                                <img 
                                    src="<?= base_url($item['image_path']) ?>" 
                                    alt="<?= htmlspecialchars($item['title']) ?>" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                    onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center\'><svg class=\'w-16 h-16 text-slate-400 dark:text-slate-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg></div>'"
                                >
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Type Badge -->
                            <?php if ($item['type'] === 'lost'): ?>
                                <span class="absolute top-3 left-3 px-3 py-1.5 bg-rose-500 text-white text-xs font-semibold rounded-full shadow-lg">
                                    Kehilangan
                                </span>
                            <?php else: ?>
                                <span class="absolute top-3 left-3 px-3 py-1.5 bg-teal-500 text-white text-xs font-semibold rounded-full shadow-lg">
                                    Ditemukan
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Item Details -->
                        <div class="p-5">
                            <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition line-clamp-1">
                                <?= htmlspecialchars($item['title']) ?>
                            </h3>
                            
                            <p class="text-slate-600 dark:text-slate-400 text-sm mb-4 line-clamp-2">
                                <?= htmlspecialchars($item['description'] ?? 'Tidak ada deskripsi') ?>
                            </p>
                            
                            <!-- Meta Information -->
                            <div class="flex items-center justify-between text-sm mb-4">
                                <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <?= htmlspecialchars($item['location_name'] ?? 'Tidak diketahui') ?>
                                </span>
                                <span class="text-slate-400 dark:text-slate-500 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <?= timeAgo($item['created_at']) ?>
                                </span>
                            </div>
                            
                            <!-- View Detail Button -->
                            <a 
                                href="<?= base_url('index.php?page=items&action=show&id=' . $item['id']) ?>" 
                                class="block w-full text-center px-4 py-2.5 bg-slate-100 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300 rounded-xl font-medium hover:bg-primary-500 hover:text-white dark:hover:bg-primary-500 transition-all"
                            >
                                Lihat Detail
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <!-- View All Button -->
            <div class="text-center mt-12">
                <a 
                    href="<?= base_url('index.php?page=items') ?>" 
                    class="inline-flex items-center gap-2 px-8 py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-semibold hover:bg-primary-600 dark:hover:bg-primary-500 hover:text-white transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5"
                >
                    Lihat Semua Laporan
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="max-w-md mx-auto text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">
                    Belum Ada Laporan
                </h3>
                <p class="text-slate-600 dark:text-slate-400 mb-6">
                    Jadilah yang pertama melaporkan barang hilang atau ditemukan
                </p>
                <a 
                    href="<?= base_url('index.php?page=items&action=create') ?>" 
                    class="inline-flex items-center gap-2 px-6 py-3 gradient-primary text-white rounded-xl font-semibold hover:shadow-lg transition-all"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Laporan Pertama
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-20 gradient-mesh">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                Cara Kerja
            </h2>
            <p class="text-slate-600 dark:text-slate-400 mb-12">
                Tiga langkah mudah untuk menemukan atau melaporkan barang hilang
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="glass-card rounded-2xl p-8 text-center hover:-translate-y-2 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-6 gradient-primary rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-primary-500/30">
                        1
                    </div>
                    <h3 class="font-semibold text-xl text-slate-900 dark:text-white mb-3">
                        Lapor atau Cari
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                        Buat laporan barang hilang atau cari barang yang Anda temukan di database kami
                    </p>
                </div>
                
                <!-- Step 2 -->
                <div class="glass-card rounded-2xl p-8 text-center hover:-translate-y-2 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-6 gradient-primary rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-primary-500/30">
                        2
                    </div>
                    <h3 class="font-semibold text-xl text-slate-900 dark:text-white mb-3">
                        Verifikasi
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                        Sistem kami akan memverifikasi kepemilikan dengan pertanyaan keamanan
                    </p>
                </div>
                
                <!-- Step 3 -->
                <div class="glass-card rounded-2xl p-8 text-center hover:-translate-y-2 transition-all duration-300">
                    <div class="w-16 h-16 mx-auto mb-6 gradient-primary rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-primary-500/30">
                        3
                    </div>
                    <h3 class="font-semibold text-xl text-slate-900 dark:text-white mb-3">
                        Barang Kembali
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                        Koordinasi pengambilan barang dengan aman melalui platform kami
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>