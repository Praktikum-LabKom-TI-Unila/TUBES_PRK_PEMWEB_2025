<?php
$shouldBlurImage = !empty($item['is_safe_claim']) && !$isOwner && !isAdmin();
?>

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-cyan-50/30 to-slate-50 dark:from-slate-900 dark:via-slate-900 dark:to-slate-900 py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        
        <nav class="flex items-center gap-2 text-sm mb-8 bg-white/60 dark:bg-slate-800/60 backdrop-blur-md px-4 py-3 rounded-xl shadow-sm" aria-label="Breadcrumb">
            <a href="<?= base_url('index.php') ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition font-medium">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
            </a>
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="<?= base_url('index.php?page=items') ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition font-medium">
                Laporan
            </a>
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-slate-900 dark:text-white font-semibold truncate max-w-xs">
                <?= htmlspecialchars($item['title']) ?>
            </span>
        </nav>

        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            
            <div class="space-y-4">
                <div class="relative bg-white/80 dark:bg-slate-800/80 backdrop-blur-md rounded-3xl shadow-2xl overflow-hidden border border-white/20 dark:border-slate-700/50">
                    <div class="aspect-square bg-gradient-to-br from-slate-100 via-slate-50 to-cyan-50 dark:from-slate-800 dark:via-slate-900 dark:to-slate-800 flex items-center justify-center relative overflow-hidden">
                        <?php if (!empty($item['image_path'])): ?>
                            <?php 
                            // Check if image_path is external URL or local path
                            $imageSrc = (strpos($item['image_path'], 'http://') === 0 || strpos($item['image_path'], 'https://') === 0) 
                                ? $item['image_path'] 
                                : base_url('assets/uploads/items/' . $item['image_path']);
                            ?>
                            <img 
                                id="itemImage"
                                src="<?= $imageSrc ?>" 
                                alt="<?= htmlspecialchars($item['title']) ?>"
                                class="w-full h-full object-cover transition-all duration-300 <?= $shouldBlurImage ? 'blur-xl scale-110' : '' ?>"
                                oncontextmenu="<?= $shouldBlurImage ? 'return false;' : '' ?>"
                                onerror="this.parentElement.innerHTML='<svg class=\'w-32 h-32 text-slate-400 dark:text-slate-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg>'"
                            >
                            
                            
                            <?php if ($shouldBlurImage): ?>
                            <div class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900/40 dark:bg-slate-950/60 backdrop-blur-sm">
                                <div class="text-center space-y-4 px-6">
                                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-purple-500/20 border-4 border-purple-400/50 shadow-lg shadow-purple-500/30">
                                        <svg class="w-10 h-10 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-white mb-2">🔒 Gambar Diproteksi</h3>
                                        <p class="text-purple-200 text-sm max-w-xs">
                                            Safe Claim aktif. Jawab pertanyaan keamanan untuk melihat gambar asli.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center">
                                <svg class="w-32 h-32 text-slate-400 dark:text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-slate-500 dark:text-slate-500 font-medium">Tidak ada gambar</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                
                <?php if (!empty($item['is_safe_claim'])): ?>
                <div class="flex items-center gap-2 text-sm text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/30 px-4 py-2 rounded-xl border border-purple-200 dark:border-purple-800/50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span class="font-semibold">Safe Claim Protection Enabled</span>
                </div>
                <?php endif; ?>
            </div>

            
            <div class="space-y-6">
                
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-md rounded-3xl p-6 shadow-xl border border-white/20 dark:border-slate-700/50">
                    <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 dark:text-white mb-4 leading-tight">
                        <?= htmlspecialchars($item['title']) ?>
                    </h1>
                    
                    
                    <div class="flex flex-wrap gap-2 mb-6">
                        
                        <?php if ($item['type'] === 'lost'): ?>
                            <span class="px-4 py-2 bg-gradient-to-r from-rose-500 to-pink-500 text-white rounded-full text-sm font-bold shadow-lg shadow-rose-500/30 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Kehilangan
                            </span>
                        <?php else: ?>
                            <span class="px-4 py-2 bg-gradient-to-r from-teal-500 to-cyan-500 text-white rounded-full text-sm font-bold shadow-lg shadow-teal-500/30 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Ditemukan
                            </span>
                        <?php endif; ?>

                        
                        <span class="px-4 py-2 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded-full text-sm font-bold border border-primary-200 dark:border-primary-800">
                            <?= htmlspecialchars($item['category_name'] ?? 'Lainnya') ?>
                        </span>

                        
                        <?php if ($item['status'] === 'closed'): ?>
                            <span class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-full text-sm font-bold shadow-lg shadow-green-500/30 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Selesai
                            </span>
                        <?php elseif ($item['status'] === 'process'): ?>
                            <span class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-full text-sm font-bold shadow-lg shadow-indigo-500/30">
                                Dalam Proses
                            </span>
                        <?php else: ?>
                            <span class="px-4 py-2 bg-gradient-to-r from-amber-500 to-yellow-500 text-white rounded-full text-sm font-bold shadow-lg shadow-amber-500/30">
                                Masih Dicari
                            </span>
                        <?php endif; ?>
                    </div>

                    
                    <div class="grid grid-cols-2 gap-4">
                        
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg">
                                <svg class="w-5 h-5 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Kategori</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($item['category_name'] ?? 'Lainnya') ?></p>
                            </div>
                        </div>

                        
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-rose-100 dark:bg-rose-900/30 rounded-lg">
                                <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Lokasi</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($item['location_name'] ?? 'Tidak diketahui') ?></p>
                            </div>
                        </div>

                        
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Tanggal</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-white"><?= date('d M Y', strtotime($item['incident_date'])) ?></p>
                            </div>
                        </div>

                        
                        <div class="flex items-start gap-3">
                            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Dilaporkan Oleh</p>
                                <p class="text-sm font-bold text-slate-900 dark:text-white"><?= htmlspecialchars($item['user_name'] ?? 'Anonim') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur-md rounded-3xl p-6 shadow-xl border border-white/20 dark:border-slate-700/50">
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Deskripsi
                    </h2>
                    <div class="prose prose-slate dark:prose-invert max-w-none prose-sm">
                        <p class="text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">
                            <?= nl2br(htmlspecialchars($item['description'])) ?>
                        </p>
                    </div>
                </div>

                
                <div class="flex flex-wrap gap-3">
                    <?php if ($isLoggedIn && !$isOwner && $item['status'] === 'open'): ?>
                        <button 
                            onclick="openClaimModal()" 
                            class="flex-1 px-6 py-4 bg-gradient-to-r from-primary-600 to-cyan-600 hover:from-primary-700 hover:to-cyan-700 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30 transition-all duration-300 hover:scale-105 flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ajukan Klaim
                        </button>
                    <?php endif; ?>

                    <?php if ($isOwner): ?>
                        <a 
                            href="<?= base_url('index.php?page=items&action=edit&id=' . $item['id']) ?>" 
                            class="flex-1 px-6 py-4 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-bold rounded-xl shadow-lg shadow-amber-500/30 transition-all duration-300 hover:scale-105 flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Laporan
                        </a>
                        <button 
                            onclick="confirmDelete()" 
                            class="px-6 py-4 bg-gradient-to-r from-rose-500 to-red-500 hover:from-rose-600 hover:to-red-600 text-white font-bold rounded-xl shadow-lg shadow-rose-500/30 transition-all duration-300 hover:scale-105 flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur-md rounded-3xl p-6 md:p-8 shadow-xl border border-white/20 dark:border-slate-700/50">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-3">
                <svg class="w-7 h-7 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Komentar
                <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">(<?= count($comments) ?>)</span>
            </h2>

            
            <div class="space-y-4 mb-6">
                <?php if (empty($comments)): ?>
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada komentar</p>
                        <p class="text-sm text-slate-400 dark:text-slate-500">Jadilah yang pertama berkomentar</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <?php 
                        $isItemOwnerComment = ($comment['user_id'] === $item['user_id']);
                        $commentBorderClass = $isItemOwnerComment 
                            ? 'border-l-4 border-primary-500 bg-primary-50/50 dark:bg-primary-900/10' 
                            : 'border-l-4 border-slate-200 dark:border-slate-700 bg-white/50 dark:bg-slate-900/30';
                        ?>
                        <div class="<?= $commentBorderClass ?> backdrop-blur-sm rounded-xl p-4 transition-all duration-300 hover:shadow-md">
                            <div class="flex items-start gap-3">
                                
                                <div class="flex-shrink-0">
                                    <?php if (!empty($comment['user_avatar']) && $comment['user_avatar'] !== 'default.jpg'): ?>
                                        <img 
                                            src="<?= base_url('assets/uploads/profiles/' . $comment['user_avatar']) ?>" 
                                            alt="<?= htmlspecialchars($comment['user_name']) ?>"
                                            class="w-10 h-10 rounded-full object-cover border-2 border-white dark:border-slate-700 shadow-md"
                                        >
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-cyan-500 flex items-center justify-center text-white font-bold text-sm border-2 border-white dark:border-slate-700 shadow-md">
                                            <?= strtoupper(substr($comment['user_name'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-bold text-slate-900 dark:text-white">
                                            <?= htmlspecialchars($comment['user_name']) ?>
                                        </span>
                                        <?php if ($isItemOwnerComment): ?>
                                            <span class="px-2 py-0.5 bg-primary-500 text-white text-xs font-semibold rounded-full">
                                                Pemilik
                                            </span>
                                        <?php endif; ?>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">
                                            • <?= date('d M Y, H:i', strtotime($comment['created_at'])) ?>
                                        </span>
                                    </div>
                                    <p class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed">
                                        <?= nl2br(htmlspecialchars($comment['body'])) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            
            <?php if ($isLoggedIn): ?>
                <form action="<?= base_url('index.php?page=comments&action=store') ?>" method="POST" class="mt-6">
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                    <div class="flex gap-3 items-end">
                        
                        <div class="flex-shrink-0">
                            <?php if (!empty($_SESSION['user']['avatar']) && $_SESSION['user']['avatar'] !== 'default.jpg'): ?>
                                <img 
                                    src="<?= base_url('assets/uploads/profiles/' . $_SESSION['user']['avatar']) ?>" 
                                    alt="You"
                                    class="w-10 h-10 rounded-full object-cover border-2 border-primary-400 shadow-md"
                                >
                            <?php else: ?>
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-cyan-500 flex items-center justify-center text-white font-bold text-sm border-2 border-primary-300 shadow-md">
                                    <?= strtoupper(substr($_SESSION['user']['name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        
                        <div class="flex-1">
                            <textarea 
                                name="body" 
                                rows="1"
                                placeholder="Tulis komentar..." 
                                class="w-full px-4 py-3 bg-white/80 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-2xl focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 transition-all"
                                required
                                oninput="this.style.height = 'auto'; this.style.height = this.scrollHeight + 'px'"
                            ></textarea>
                        </div>

                        
                        <button 
                            type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-primary-600 to-cyan-600 hover:from-primary-700 hover:to-cyan-700 text-white font-bold rounded-2xl shadow-lg shadow-primary-500/30 transition-all duration-300 hover:scale-105 flex items-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Kirim
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="text-center py-6 bg-slate-100/50 dark:bg-slate-900/30 rounded-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-slate-600 dark:text-slate-400 mb-3">
                        <a href="<?= base_url('index.php?page=auth&action=login') ?>" class="text-primary-600 dark:text-primary-400 font-bold hover:underline">
                            Login
                        </a> untuk menambahkan komentar
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<div id="claimModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto border border-slate-200 dark:border-slate-700">
        
        <div class="sticky top-0 bg-gradient-to-r from-primary-600 to-cyan-600 text-white px-6 py-5 rounded-t-3xl">
            <h3 class="text-2xl font-bold flex items-center gap-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Verifikasi Kepemilikan
            </h3>
            <p class="text-primary-100 text-sm mt-1">Buktikan bahwa barang ini milik Anda</p>
        </div>

        
        <form action="<?= base_url('index.php?page=claims&action=store') ?>" method="POST" class="p-6 space-y-5">
            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">

            
            <?php if (!empty($item['is_safe_claim']) && !empty($item['security_question'])): ?>
                <div class="bg-purple-50 dark:bg-purple-950/30 border-l-4 border-purple-500 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <label class="block text-sm font-bold text-purple-900 dark:text-purple-300 mb-2">
                                Pertanyaan Keamanan *
                            </label>
                            <p class="text-purple-800 dark:text-purple-200 font-semibold mb-3 text-base">
                                <?= htmlspecialchars($item['security_question']) ?>
                            </p>
                            <input 
                                type="text" 
                                name="security_answer" 
                                placeholder="Ketik jawaban Anda di sini..."
                                class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-purple-300 dark:border-purple-700 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 font-medium"
                                required
                            >
                            <p class="text-xs text-purple-700 dark:text-purple-400 mt-2">
                                ⚠️ Jawab dengan benar untuk membuktikan kepemilikan
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                    Pesan untuk <?= $item['type'] === 'found' ? 'Penemu' : 'Pemilik' ?> *
                </label>
                <textarea 
                    name="message" 
                    rows="4"
                    placeholder="Jelaskan mengapa Anda yakin ini adalah barang Anda, tambahkan detail yang hanya Anda ketahui..."
                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/50 border border-slate-300 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500"
                    required
                ></textarea>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Berikan informasi detail yang dapat membantu verifikasi
                </p>
            </div>

            
            <div class="flex gap-3 pt-4">
                <button 
                    type="button" 
                    onclick="closeClaimModal()"
                    class="flex-1 px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition-all duration-300"
                >
                    Batal
                </button>
                <button 
                    type="submit"
                    class="flex-1 px-6 py-3 bg-gradient-to-r from-primary-600 to-cyan-600 hover:from-primary-700 hover:to-cyan-700 text-white font-bold rounded-xl shadow-lg shadow-primary-500/30 transition-all duration-300 hover:scale-105 flex items-center justify-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Kirim Klaim
                </button>
            </div>
        </form>
    </div>
</div>


<script>

function openClaimModal() {
    document.getElementById('claimModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent body scroll
}

function closeClaimModal() {
    document.getElementById('claimModal').classList.add('hidden');
    document.body.style.overflow = 'auto'; // Restore scroll
}

document.getElementById('claimModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeClaimModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeClaimModal();
    }
});

function confirmDelete() {
    if (confirm('⚠️ Apakah Anda yakin ingin menghapus laporan ini?\n\nTindakan ini tidak dapat dibatalkan!')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('index.php?page=items&action=delete') ?>';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = '<?= $item['id'] ?>';
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

<?php if ($shouldBlurImage): ?>
document.getElementById('itemImage')?.addEventListener('contextmenu', function(e) {
    e.preventDefault();
    alert('🔒 Gambar ini diproteksi oleh Safe Claim.\n\nJawab pertanyaan keamanan untuk melihat gambar asli.');
    return false;
});

document.getElementById('itemImage')?.addEventListener('dragstart', function(e) {
    e.preventDefault();
    return false;
});
<?php endif; ?>

document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.querySelector('textarea[name="body"]');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
});
</script>
