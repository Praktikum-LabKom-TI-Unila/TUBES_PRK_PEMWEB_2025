<section class="p-6 md:p-10 space-y-8">
    <!-- Header -->
    <div>
        <p class="text-sm text-slate-500 uppercase tracking-[0.3em]">Dashboard</p>
        <h1 class="text-2xl font-semibold text-slate-800 mt-1">Selamat datang, <?= htmlspecialchars($user['name'] ?? 'User') ?>!</h1>
        <p class="text-sm text-slate-500">Ringkasan sistem inventory Anda hari ini</p>
    </div>

    <!-- Main Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <!-- Total Materials -->
        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg p-6 text-white">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm text-blue-100 mb-2">Total Bahan Baku</p>
                    <p class="text-3xl font-bold"><?= number_format($stats['totalMaterials']) ?></p>
                    <p class="text-xs text-blue-100 mt-2">Item aktif di sistem</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-xl p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 right-0 opacity-10">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </div>
        </article>

        <!-- Stock Value -->
        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-lg p-6 text-white">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm text-emerald-100 mb-2">Total Nilai Stok</p>
                    <p class="text-3xl font-bold">Rp <?= number_format($stats['totalValue'], 0, ',', '.') ?></p>
                    <p class="text-xs text-emerald-100 mt-2">Estimasi total</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-xl p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 right-0 opacity-10">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </article>

        <!-- Low Stock -->
        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 shadow-lg p-6 text-white">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm text-amber-100 mb-2">Stok Menipis</p>
                    <p class="text-3xl font-bold"><?= number_format($stats['lowStockCount']) ?></p>
                    <p class="text-xs text-amber-100 mt-2">Perlu perhatian</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-xl p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 right-0 opacity-10">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
        </article>

        <!-- Out of Stock -->
        <article class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500 to-rose-600 shadow-lg p-6 text-white">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-sm text-rose-100 mb-2">Stok Habis</p>
                    <p class="text-3xl font-bold"><?= number_format($stats['outOfStock']) ?></p>
                    <p class="text-xs text-rose-100 mt-2">Segera restock</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-xl p-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 right-0 opacity-10">
                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
            </div>
        </article>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Stock In This Month -->
        <article class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m0 0l-6-6m6 6l6-6" />
                    </svg>
                </span>
                <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">Bulan Ini</span>
            </div>
            <p class="text-sm text-slate-500 mb-1">Stok Masuk</p>
            <p class="text-2xl font-bold text-slate-900"><?= number_format($stats['stockInCount']) ?></p>
            <p class="text-xs text-slate-400 mt-1">Transaksi</p>
        </article>

        <!-- Stock Out This Month -->
        <article class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20V4m0 0l6 6m-6-6l-6 6" />
                    </svg>
                </span>
                <span class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-full">Bulan Ini</span>
            </div>
            <p class="text-sm text-slate-500 mb-1">Stok Keluar</p>
            <p class="text-2xl font-bold text-slate-900"><?= number_format($stats['stockOutCount']) ?></p>
            <p class="text-xs text-slate-400 mt-1">Transaksi</p>
        </article>

        <!-- Total Categories -->
        <article class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100 text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                    </svg>
                </span>
            </div>
            <p class="text-sm text-slate-500 mb-1">Total Kategori</p>
            <p class="text-2xl font-bold text-slate-900"><?= number_format($stats['totalCategories']) ?></p>
            <p class="text-xs text-slate-400 mt-1">Kategori bahan</p>
        </article>

        <!-- Total Suppliers -->
        <article class="rounded-2xl bg-white border border-slate-100 shadow-sm p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                    </svg>
                </span>
            </div>
            <p class="text-sm text-slate-500 mb-1">Total Supplier</p>
            <p class="text-2xl font-bold text-slate-900"><?= number_format($stats['totalSuppliers']) ?></p>
            <p class="text-xs text-slate-400 mt-1">Supplier aktif</p>
        </article>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Low Stock Warning - Takes 2 columns -->
        <article class="lg:col-span-2 rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">Peringatan Stok Menipis</h2>
                    <p class="text-sm text-slate-500">Bahan yang perlu segera di-restock</p>
                </div>
                <?php if (!empty($lowStock)): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-600">
                        <span class="h-2 w-2 rounded-full bg-rose-500 animate-pulse"></span>
                        <?= count($lowStock) ?> Item
                    </span>
                <?php endif; ?>
            </div>

            <?php if (empty($lowStock)): ?>
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-slate-500 font-medium">Semua stok dalam kondisi baik!</p>
                    <p class="text-sm text-slate-400 mt-1">Tidak ada bahan yang perlu di-restock</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($lowStock as $item): 
                        $percentage = $item['min_stock'] > 0 ? min(100, ($item['current_stock'] / $item['min_stock']) * 100) : 0;
                        $statusColor = $percentage == 0 ? 'rose' : ($percentage <= 50 ? 'amber' : 'emerald');
                    ?>
                        <div class="p-4 rounded-xl border border-slate-100 hover:border-slate-200 hover:shadow-sm transition-all">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3 flex-1">
                                    <?php if (!empty($item['image_url'])): ?>
                                        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-10 h-10 rounded-lg object-cover border border-slate-200">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center text-slate-600 font-semibold text-sm">
                                            <?= strtoupper(substr($item['name'], 0, 2)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-slate-800 truncate"><?= htmlspecialchars($item['name']) ?></p>
                                        <p class="text-xs text-slate-500"><?= htmlspecialchars($item['category_name'] ?? 'Tanpa Kategori') ?></p>
                                    </div>
                                </div>
                                <div class="text-right ml-4">
                                    <p class="text-sm font-semibold text-slate-800"><?= number_format($item['current_stock'], 0, ',', '.') ?> / <?= number_format($item['min_stock'], 0, ',', '.') ?></p>
                                    <p class="text-xs text-slate-500"><?= htmlspecialchars($item['unit']) ?></p>
                                </div>
                            </div>
                            <div class="relative h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-<?= $statusColor ?>-400 to-<?= $statusColor ?>-500 transition-all duration-500" 
                                     style="width: <?= $percentage ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($lowStock) >= 5): ?>
                    <div class="mt-4 text-center">
                        <a href="<?= url('/reports/low-stock') ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700">
                            Lihat Semua
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </article>

        <!-- Recent Activities -->
        <article class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">Aktivitas Terbaru</h2>
                    <p class="text-sm text-slate-500">Log sistem terkini</p>
                </div>
            </div>

            <?php if (empty($recentActivities)): ?>
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-100 text-slate-400 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm text-slate-500">Belum ada aktivitas</p>
                </div>
            <?php else: ?>
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    <?php foreach ($recentActivities as $activity): ?>
                        <div class="flex items-start gap-3 pb-4 border-b border-slate-100 last:border-0 last:pb-0">
                            <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                <?= strtoupper(substr($activity['user_name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-slate-800 font-medium"><?= htmlspecialchars($activity['action']) ?></p>
                                <?php if (!empty($activity['description'])): ?>
                                    <p class="text-xs text-slate-500 mt-0.5 truncate"><?= htmlspecialchars($activity['description']) ?></p>
                                <?php endif; ?>
                                <p class="text-xs text-slate-400 mt-1">
                                    <?= htmlspecialchars($activity['user_name'] ?? 'System') ?> · 
                                    <?= date('d M H:i', strtotime($activity['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    <a href="<?= url('/activity-logs') ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700">
                        Lihat Semua Aktivitas
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            <?php endif; ?>
        </article>
    </div>

    <!-- Recent Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Stock In -->
        <article class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">Stok Masuk Terbaru</h2>
                    <p class="text-sm text-slate-500">Transaksi masuk terakhir</p>
                </div>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m0 0l-6-6m6 6l6-6" />
                    </svg>
                </span>
            </div>

            <?php if (empty($recentStockIn)): ?>
                <div class="text-center py-8">
                    <p class="text-sm text-slate-500">Belum ada transaksi stok masuk</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($recentStockIn as $stock): ?>
                        <div class="p-4 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-semibold text-slate-800"><?= htmlspecialchars($stock['material_name']) ?></p>
                                <span class="text-sm font-bold text-emerald-600">+<?= number_format($stock['quantity'], 0, ',', '.') ?> <?= htmlspecialchars($stock['unit'] ?? '') ?></span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span><?= htmlspecialchars($stock['supplier_name'] ?? 'N/A') ?></span>
                                <span><?= date('d M Y', strtotime($stock['txn_date'])) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    <a href="<?= url('/stock-in') ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-600 hover:text-emerald-700">
                        Lihat Semua Stok Masuk
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            <?php endif; ?>
        </article>

        <!-- Recent Stock Out -->
        <article class="rounded-2xl bg-white border border-slate-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-slate-800">Stok Keluar Terbaru</h2>
                    <p class="text-sm text-slate-500">Transaksi keluar terakhir</p>
                </div>
                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-amber-100 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20V4m0 0l6 6m-6-6l-6 6" />
                    </svg>
                </span>
            </div>

            <?php if (empty($recentStockOut)): ?>
                <div class="text-center py-8">
                    <p class="text-sm text-slate-500">Belum ada transaksi stok keluar</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($recentStockOut as $stock): ?>
                        <div class="p-4 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-semibold text-slate-800"><?= htmlspecialchars($stock['material_name']) ?></p>
                                <span class="text-sm font-bold text-amber-600">-<?= number_format($stock['quantity'], 0, ',', '.') ?> <?= htmlspecialchars($stock['unit'] ?? '') ?></span>
                            </div>
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span><?= htmlspecialchars($stock['usage_type'] ?? 'Penggunaan') ?></span>
                                <span><?= date('d M Y', strtotime($stock['txn_date'])) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100">
                    <a href="<?= url('/stock-out') ?>" class="inline-flex items-center gap-2 text-sm font-semibold text-amber-600 hover:text-amber-700">
                        Lihat Semua Stok Keluar
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            <?php endif; ?>
        </article>
    </div>

    <!-- Quick Actions -->
    <div class="rounded-2xl bg-gradient-to-br from-slate-50 to-slate-100 border border-slate-200 p-6">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Aksi Cepat</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <?php if (has_permission('view_materials')): ?>
            <a href="<?= url('/materials') ?>" class="flex flex-col items-center justify-center p-4 rounded-xl bg-white hover:shadow-md transition-all border border-slate-100 group">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 text-center">Kelola Bahan</p>
            </a>
            <?php endif; ?>

            <?php if (has_permission('view_stock_in')): ?>
            <a href="<?= url('/stock-in') ?>" class="flex flex-col items-center justify-center p-4 rounded-xl bg-white hover:shadow-md transition-all border border-slate-100 group">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m0 0l-6-6m6 6l6-6" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 text-center">Stok Masuk</p>
            </a>
            <?php endif; ?>

            <?php if (has_permission('view_stock_out')): ?>
            <a href="<?= url('/stock-out') ?>" class="flex flex-col items-center justify-center p-4 rounded-xl bg-white hover:shadow-md transition-all border border-slate-100 group">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20V4m0 0l6 6m-6-6l-6 6" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 text-center">Stok Keluar</p>
            </a>
            <?php endif; ?>

            <?php if (has_permission('view_reports')): ?>
            <a href="<?= url('/reports/stock') ?>" class="flex flex-col items-center justify-center p-4 rounded-xl bg-white hover:shadow-md transition-all border border-slate-100 group">
                <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-slate-700 text-center">Laporan</p>
            </a>
            <?php endif; ?>
        </div>
    </div>
</section>
