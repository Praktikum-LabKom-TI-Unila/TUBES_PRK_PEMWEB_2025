<section class="gradient-mesh min-h-[85vh] flex items-center relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-400/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-sky-400/20 rounded-full blur-3xl"></div>
    </div>
    
    <div class="container mx-auto px-4 py-20 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-sm font-medium mb-6">
                <span class="w-2 h-2 bg-primary-500 rounded-full animate-pulse"></span>
                Platform Resmi Universitas Lampung
            </div>
            
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-slate-900 dark:text-white mb-6 leading-tight">
                Temukan Barang
                <span class="gradient-text block">Hilang Anda</span>
            </h1>
            
            <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 mb-10 max-w-2xl mx-auto leading-relaxed">
                Platform digital untuk membantu civitas akademika Universitas Lampung dalam melaporkan dan menemukan barang hilang dengan mudah, cepat, dan aman.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= base_url('index.php?page=items&action=create') ?>" class="inline-flex items-center justify-center gap-2 px-8 py-4 gradient-primary text-white rounded-2xl font-semibold shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 hover:-translate-y-1 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Laporkan Barang
                </a>
                <a href="<?= base_url('index.php?page=items') ?>" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-2xl font-semibold border border-slate-200 dark:border-slate-700 hover:border-primary-300 dark:hover:border-primary-600 hover:-translate-y-1 transition-all bento-shadow">
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
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto mb-4 gradient-primary rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/20">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white mb-1">150+</h3>
                <p class="text-slate-600 dark:text-slate-400 font-medium">Total Laporan</p>
            </div>
            
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-teal-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-teal-500/20">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white mb-1">85+</h3>
                <p class="text-slate-600 dark:text-slate-400 font-medium">Barang Ditemukan</p>
            </div>
            
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/20">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-3xl font-bold text-slate-900 dark:text-white mb-1">72+</h3>
                <p class="text-slate-600 dark:text-slate-400 font-medium">Dikembalikan</p>
            </div>
        </div>
    </div>
</section>

<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">Laporan Terbaru</h2>
            <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">Daftar barang hilang dan ditemukan terbaru di lingkungan Universitas Lampung</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <article class="bg-white dark:bg-slate-800 rounded-2xl overflow-hidden bento-shadow hover:shadow-xl transition-shadow group">
                <div class="aspect-video bg-slate-100 dark:bg-slate-700 relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1611532736597-de2d4265fba3?w=400&h=250&fit=crop" alt="Dompet" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <span class="absolute top-3 left-3 px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">Hilang</span>
                </div>
                <div class="p-5">
                    <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition">Dompet Kulit Coklat</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm mb-3 line-clamp-2">Dompet kulit berwarna coklat berisi KTM dan beberapa kartu penting.</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            Perpustakaan
                        </span>
                        <span class="text-slate-400 dark:text-slate-500">2 jam lalu</span>
                    </div>
                </div>
            </article>
            
            <article class="bg-white dark:bg-slate-800 rounded-2xl overflow-hidden bento-shadow hover:shadow-xl transition-shadow group">
                <div class="aspect-video bg-slate-100 dark:bg-slate-700 relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1585771724684-38269d6639fd?w=400&h=250&fit=crop" alt="Kunci" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <span class="absolute top-3 left-3 px-3 py-1 bg-teal-500 text-white text-xs font-semibold rounded-full">Ditemukan</span>
                </div>
                <div class="p-5">
                    <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition">Kunci Motor Honda</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm mb-3 line-clamp-2">Kunci motor Honda dengan gantungan karakter kartun ditemukan di parkiran.</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            FMIPA
                        </span>
                        <span class="text-slate-400 dark:text-slate-500">5 jam lalu</span>
                    </div>
                </div>
            </article>
            
            <article class="bg-white dark:bg-slate-800 rounded-2xl overflow-hidden bento-shadow hover:shadow-xl transition-shadow group">
                <div class="aspect-video bg-slate-100 dark:bg-slate-700 relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=400&h=250&fit=crop" alt="Laptop" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <span class="absolute top-3 left-3 px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full">Hilang</span>
                </div>
                <div class="p-5">
                    <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition">Charger Laptop Asus</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm mb-3 line-clamp-2">Charger laptop Asus 65W warna hitam tertinggal di ruang kelas.</p>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            FT
                        </span>
                        <span class="text-slate-400 dark:text-slate-500">1 hari lalu</span>
                    </div>
                </div>
            </article>
        </div>
        
        <div class="text-center mt-10">
            <a href="<?= base_url('index.php?page=items') ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 rounded-xl font-semibold hover:bg-slate-200 dark:hover:bg-slate-700 transition">
                Lihat Semua Laporan
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<section class="py-20 gradient-mesh">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">Cara Kerja</h2>
            <p class="text-slate-600 dark:text-slate-400 mb-12">Tiga langkah mudah untuk menemukan atau melaporkan barang</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="glass-card rounded-2xl p-6 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 gradient-primary rounded-xl flex items-center justify-center text-white font-bold text-xl">1</div>
                    <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-2">Buat Laporan</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">Isi form laporan dengan detail barang yang hilang atau ditemukan</p>
                </div>
                
                <div class="glass-card rounded-2xl p-6 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 gradient-primary rounded-xl flex items-center justify-center text-white font-bold text-xl">2</div>
                    <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-2">Verifikasi</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">Tim kami akan memverifikasi laporan untuk keamanan</p>
                </div>
                
                <div class="glass-card rounded-2xl p-6 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 gradient-primary rounded-xl flex items-center justify-center text-white font-bold text-xl">3</div>
                    <h3 class="font-semibold text-lg text-slate-900 dark:text-white mb-2">Terhubung</h3>
                    <p class="text-slate-600 dark:text-slate-400 text-sm">Pemilik dan penemu dapat terhubung dengan aman</p>
                </div>
            </div>
        </div>
    </div>
</section>
