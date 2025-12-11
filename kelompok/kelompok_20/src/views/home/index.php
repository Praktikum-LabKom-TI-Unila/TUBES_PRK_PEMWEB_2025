<section class="gradient-mesh min-h-[85vh] flex items-center relative">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-400/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-sky-400/20 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container mx-auto px-4 py-20 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-sm font-medium mb-6 animate-fade-in">
                <span class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></span>
                Platform Resmi Universitas Lampung
            </div>
            
            
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-slate-900 dark:text-white mb-8 leading-relaxed">
                Temukan Barang Hilang
                <span class="gradient-text block">di Kampus</span>
            </h1>
            
            
            <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 mb-10 max-w-2xl mx-auto leading-relaxed">
                Platform digital untuk membantu civitas akademika Universitas Lampung dalam melaporkan dan menemukan barang hilang dengan mudah, cepat, dan aman.
            </p>
            
            
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


<section class="py-16 bg-white dark:bg-slate-800/50 border-y border-slate-200 dark:border-slate-700/50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            
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

<section class="py-20 bg-white dark:bg-slate-800/50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                Kategori Barang Paling Dicari
            </h2>
            <p class="text-slate-600 dark:text-slate-400 max-w-3xl mx-auto">
                Lihat jenis-jenis barang yang paling sering dilaporkan hilang atau ditemukan di sekitar kampus.
            </p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            
            <div class="bg-slate-50 dark:bg-slate-800 p-6 rounded-2xl text-center group hover:bg-primary-50 dark:hover:bg-primary-900 transition-colors duration-300 bento-shadow">
                <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 dark:bg-primary-700/50 rounded-full flex items-center justify-center group-hover:bg-primary-500 transition-colors">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-1">
                    Elektronik
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Laptop, Ponsel, Tablet.
                </p>
            </div>
            
            <div class="bg-slate-50 dark:bg-slate-800 p-6 rounded-2xl text-center group hover:bg-primary-50 dark:hover:bg-primary-900 transition-colors duration-300 bento-shadow">
                <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 dark:bg-primary-700/50 rounded-full flex items-center justify-center group-hover:bg-primary-500 transition-colors">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-1">
                    Dokumen
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    KTM, KTP, SIM, Ijazah.
                </p>
            </div>
            
            <div class="bg-slate-50 dark:bg-slate-800 p-6 rounded-2xl text-center group hover:bg-primary-50 dark:hover:bg-primary-900 transition-colors duration-300 bento-shadow">
                <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 dark:bg-primary-700/50 rounded-full flex items-center justify-center group-hover:bg-primary-500 transition-colors">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h.01M3 7h.01M3 3h.01M21 7h.01M21 3h.01M17 7h.01M17 3h.01M3 21h.01M3 17h.01M7 21h.01M7 17h.01M21 21h.01M21 17h.01M17 21h.01M17 17h.01M12 12V3M12 12l-4 4m4-4l4 4"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-1">
                    Aksesoris
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Kacamata, Jam, Tas.
                </p>
            </div>
            
            <div class="bg-slate-50 dark:bg-slate-800 p-6 rounded-2xl text-center group hover:bg-primary-50 dark:hover:bg-primary-900 transition-colors duration-300 bento-shadow">
                <div class="w-16 h-16 mx-auto mb-4 bg-primary-100 dark:bg-primary-700/50 rounded-full flex items-center justify-center group-hover:bg-primary-500 transition-colors">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-300 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5s3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18s-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-1">
                    Kuliah
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400">
                    Buku, Catatan, Alat Tulis.
                </p>
            </div>
            
        </div>
        
        <div class="text-center mt-12">
            <a 
                href="<?= base_url('index.php?page=items') ?>" 
                class="inline-flex items-center gap-2 px-8 py-3.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-semibold hover:bg-primary-600 dark:hover:bg-primary-500 hover:text-white transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5"
            >
                Lihat Semua Kategori
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>
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
                        
                        <div class="aspect-video bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 relative overflow-hidden">
                            <?php if (!empty($item['image_path'])): ?>
                                <?php 
                                // Check if image_path is external URL or local path
                                $imageSrc = (strpos($item['image_path'], 'http://') === 0 || strpos($item['image_path'], 'https://') === 0) 
                                    ? $item['image_path'] 
                                    : base_url('assets/uploads/items/' . $item['image_path']);
                                ?>
                                <img 
                                    src="<?= $imageSrc ?>" 
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
                        
                        
                        <div class="p-5">
                            <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition line-clamp-1">
                                <?= htmlspecialchars($item['title']) ?>
                            </h3>
                            
                            <p class="text-slate-600 dark:text-slate-400 text-sm mb-4 line-clamp-2">
                                <?= htmlspecialchars($item['description'] ?? 'Tidak ada deskripsi') ?>
                            </p>
                            
                            
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

<section class="py-20 bg-slate-50 dark:bg-slate-900/50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                Mengapa Menggunakan Lost & Found Unila?
            </h2>
            <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                Platform resmi dan terpercaya untuk menemukan kembali barang berharga Anda.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            
            <div class="flex items-start p-6 bg-white dark:bg-slate-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex-shrink-0 w-12 h-12 mr-4 mt-1 bg-teal-500 rounded-full flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-1">
                        Proses Cepat & Efisien
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">
                        Laporkan kehilangan atau temuan dalam hitungan menit. Proses pencarian dan verifikasi yang disederhanakan.
                    </p>
                </div>
            </div>
            
            <div class="flex items-start p-6 bg-white dark:bg-slate-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex-shrink-0 w-12 h-12 mr-4 mt-1 bg-amber-500 rounded-full flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-1">
                        Jangkauan Seluruh Kampus
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">
                        Semua laporan terpusat, memastikan barang hilang di fakultas mana pun dapat ditemukan.
                    </p>
                </div>
            </div>
            
            <div class="flex items-start p-6 bg-white dark:bg-slate-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex-shrink-0 w-12 h-12 mr-4 mt-1 bg-red-500 rounded-full flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.047 12.007 12.007 0 00-2.396 6.385c.465 4.312 3.822 7.842 8.525 9.873.308.13.626.23.953.305v-12"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-1">
                        Verifikasi Keamanan
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">
                        Kami memastikan barang hanya diserahkan kepada pemilik yang sah melalui proses verifikasi yang ketat.
                    </p>
                </div>
            </div>
            
            <div class="flex items-start p-6 bg-white dark:bg-slate-800 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <div class="flex-shrink-0 w-12 h-12 mr-4 mt-1 bg-blue-500 rounded-full flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm-6-2h6m6 0v1h3v-1m-3-6h.01M18 10h.01M18 14h.01"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-1">
                        Mendukung Komunitas
                    </h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">
                        Meningkatkan budaya jujur dan saling membantu di lingkungan civitas akademika Universitas Lampung.
                    </p>
                </div>
            </div>
            
        </div>
    </div>
</section>