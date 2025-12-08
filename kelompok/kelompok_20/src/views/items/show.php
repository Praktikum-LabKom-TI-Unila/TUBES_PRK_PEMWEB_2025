<!-- Item Detail View -->
<div class="min-h-screen bg-slate-50 dark:bg-slate-900 py-8">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm mb-6" aria-label="Breadcrumb">
            <a href="<?= base_url('index.php') ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition">
                Beranda
            </a>
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="<?= base_url('index.php?page=items') ?>" class="text-slate-600 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition">
                Laporan
            </a>
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-slate-900 dark:text-white font-medium truncate max-w-xs">
                <?= htmlspecialchars($item['title']) ?>
            </span>
        </nav>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Left: Image -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="aspect-square bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 flex items-center justify-center">
                    <?php if (!empty($item['image_path'])): ?>
                        <img 
                            src="<?= base_url($item['image_path']) ?>" 
                            alt="<?= htmlspecialchars($item['title']) ?>"
                            class="w-full h-full object-cover"
                            onerror="this.parentElement.innerHTML='<svg class=\'w-32 h-32 text-slate-400 dark:text-slate-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\'/></svg>'"
                        >
                    <?php else: ?>
                        <svg class="w-32 h-32 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Information -->
            <div class="space-y-6">
                <!-- Title -->
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                        <?= htmlspecialchars($item['title']) ?>
                    </h1>
                    
                    <!-- Badges -->
                    <div class="flex flex-wrap gap-2">
                        <!-- Type Badge -->
                        <?php if ($item['type'] === 'lost'): ?>
                            <span class="px-4 py-2 bg-rose-100 dark:bg-rose-950/50 text-rose-700 dark:text-rose-400 rounded-full text-sm font-semibold flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Kehilangan
                            </span>
                        <?php else: ?>
                            <span class="px-4 py-2 bg-teal-100 dark:bg-teal-950/50 text-teal-700 dark:text-teal-400 rounded-full text-sm font-semibold flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Ditemukan
                            </span>
                        <?php endif; ?>

                        <!-- Category Badge -->
                        <span class="px-4 py-2 bg-primary-100 dark:bg-primary-950/50 text-primary-700 dark:text-primary-400 rounded-full text-sm font-semibold">
                            <?= htmlspecialchars($item['category_name'] ?? 'Lainnya') ?>
                        </span>

                        <!-- Status Badge -->
                        <?php if ($item['status'] === 'closed'): ?>
                            <span class="px-4 py-2 bg-green-100 dark:bg-green-950/50 text-green-700 dark:text-green-400 rounded-full text-sm font-semibold flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Selesai
                            </span>
                        <?php else: ?>
                            <span class="px-4 py-2 bg-amber-100 dark:bg-amber-950/50 text-amber-700 dark:text-amber-400 rounded-full text-sm font-semibold">
                                Masih Dicari
                            </span>
                        <?php endif; ?>

                        <!-- Safe Claim Badge -->
                        <?php if (!empty($item['is_safe_claim'])): ?>
                            <span class="px-4 py-2 bg-purple-100 dark:bg-purple-950/50 text-purple-700 dark:text-purple-400 rounded-full text-sm font-semibold flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Safe Claim
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Meta Information Card -->
                <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <div class="space-y-4">
                        <!-- Reported By -->
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-gradient-primary rounded-full flex items-center justify-center text-white font-bold shadow-lg">
                                <?= strtoupper(substr($item['user_name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Dilaporkan oleh</p>
                                <p class="font-semibold text-slate-900 dark:text-white">
                                    <?= htmlspecialchars($item['user_name'] ?? 'Anonymous') ?>
                                </p>
                            </div>
                        </div>

                        <div class="border-t border-slate-200 dark:border-slate-700 pt-4 space-y-3">
                            <!-- Date -->
                            <div class="flex items-center gap-3 text-slate-600 dark:text-slate-400">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-500">Tanggal Kejadian</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                                        <?= date('d F Y', strtotime($item['incident_date'])) ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="flex items-center gap-3 text-slate-600 dark:text-slate-400">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-500">Lokasi</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                                        <?= htmlspecialchars($item['location_name'] ?? 'Tidak diketahui') ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Created At -->
                            <div class="flex items-center gap-3 text-slate-600 dark:text-slate-400">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="text-xs text-slate-500 dark:text-slate-500">Diposting</p>
                                    <p class="text-sm font-medium text-slate-900 dark:text-white">
                                        <?= timeAgo($item['created_at']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-3">Deskripsi</h3>
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                        <?= htmlspecialchars($item['description']) ?>
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <?php if ($item['status'] === 'closed'): ?>
                        <!-- Item Closed -->
                        <div class="flex-1 px-6 py-4 bg-green-50 dark:bg-green-950/30 border-2 border-green-200 dark:border-green-800 rounded-xl text-center">
                            <p class="text-green-700 dark:text-green-400 font-semibold flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Barang Sudah Dikembalikan
                            </p>
                        </div>
                    <?php elseif ($isOwner): ?>
                        <!-- Owner Actions -->
                        <a 
                            href="<?= base_url('index.php?page=items&action=edit&id=' . $item['id']) ?>"
                            class="flex-1 px-6 py-3.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-semibold text-center transition-all shadow-lg shadow-primary-500/30 flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Laporan
                        </a>
                        <button 
                            onclick="confirmDelete(<?= $item['id'] ?>)"
                            class="px-6 py-3.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-semibold transition-all flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus
                        </button>
                    <?php elseif ($isLoggedIn): ?>
                        <!-- Visitor Can Claim -->
                        <button 
                            onclick="openClaimModal()"
                            class="flex-1 px-6 py-3.5 gradient-primary text-white rounded-xl font-semibold shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 transition-all flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ajukan Klaim
                        </button>
                    <?php else: ?>
                        <!-- Not Logged In -->
                        <a 
                            href="<?= base_url('index.php?page=auth&action=login') ?>"
                            class="flex-1 px-6 py-3.5 gradient-primary text-white rounded-xl font-semibold text-center shadow-lg shadow-primary-500/30 hover:shadow-xl transition-all"
                        >
                            Login untuk Mengklaim
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl p-6 md:p-8 border border-slate-200 dark:border-slate-700">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Komentar (<?= count($comments) ?>)
            </h2>

            <!-- Comment Form (Only if logged in) -->
            <?php if ($isLoggedIn): ?>
                <form action="<?= base_url('index.php?page=comments&action=store') ?>" method="POST" class="mb-8">
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                    <div class="flex gap-3">
                        <div class="w-10 h-10 bg-gradient-primary rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                            <?= strtoupper(substr($_SESSION['user']['name'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="flex-1">
                            <textarea 
                                name="comment" 
                                required
                                rows="3"
                                placeholder="Tulis komentar Anda..."
                                class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none transition-all"
                            ></textarea>
                            <button 
                                type="submit"
                                class="mt-3 px-5 py-2.5 gradient-primary text-white rounded-lg font-medium hover:shadow-lg transition-all flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                Kirim Komentar
                            </button>
                        </div>
                    </div>
                </form>
            <?php endif; ?>

            <!-- Comments List -->
            <?php if (!empty($comments)): ?>
                <div class="space-y-6">
                    <?php foreach ($comments as $comment): ?>
                        <div class="flex gap-3 pb-6 border-b border-slate-200 dark:border-slate-700 last:border-0">
                            <div class="w-10 h-10 bg-gradient-to-br from-slate-300 to-slate-400 dark:from-slate-600 dark:to-slate-700 rounded-full flex items-center justify-center text-white font-bold flex-shrink-0">
                                <?= strtoupper(substr($comment['user_name'] ?? 'U', 0, 1)) ?>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <p class="font-semibold text-slate-900 dark:text-white">
                                        <?= htmlspecialchars($comment['user_name'] ?? 'Anonymous') ?>
                                    </p>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                        • <?= timeAgo($comment['created_at']) ?>
                                    </span>
                                </div>
                                <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                                    <?= nl2br(htmlspecialchars($comment['comment'])) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto mb-4 text-slate-300 dark:text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="text-slate-500 dark:text-slate-400">Belum ada komentar. Jadilah yang pertama!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Claim Modal -->
<div id="claimModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background Overlay -->
        <div class="fixed inset-0 transition-opacity bg-slate-900/75 backdrop-blur-sm" onclick="closeClaimModal()"></div>

        <!-- Modal Content -->
        <div class="inline-block align-bottom bg-white dark:bg-slate-800 rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="<?= base_url('index.php?page=claims&action=store') ?>" method="POST">
                <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                
                <!-- Modal Header -->
                <div class="bg-gradient-primary px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ajukan Klaim Barang
                        </h3>
                        <button type="button" onclick="closeClaimModal()" class="text-white/80 hover:text-white transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-6 space-y-5">
                    <!-- Message -->
                    <div>
                        <label for="claim_message" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Pesan untuk Pemilik <span class="text-rose-500">*</span>
                        </label>
                        <textarea 
                            id="claim_message" 
                            name="message" 
                            required
                            rows="4"
                            placeholder="Jelaskan mengapa Anda mengklaim barang ini..."
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 resize-none"
                        ></textarea>
                    </div>

                    <!-- Safe Claim Question (Conditional) -->
                    <?php if (!empty($item['is_safe_claim']) && !empty($item['security_question'])): ?>
                        <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/30 rounded-xl p-4">
                            <div class="flex items-start gap-3 mb-3">
                                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <div class="flex-1">
                                    <p class="font-semibold text-amber-900 dark:text-amber-200 text-sm mb-1">Pertanyaan Keamanan</p>
                                    <p class="text-amber-800 dark:text-amber-300 text-sm">
                                        <?= htmlspecialchars($item['security_question']) ?>
                                    </p>
                                </div>
                            </div>
                            <input 
                                type="text" 
                                name="security_answer" 
                                required
                                placeholder="Masukkan jawaban Anda"
                                class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-amber-300 dark:border-amber-700 rounded-lg text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500"
                            >
                            <p class="mt-2 text-xs text-amber-700 dark:text-amber-400">
                                💡 Jawaban akan diverifikasi oleh sistem untuk memastikan kepemilikan yang sah
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Modal Footer -->
                <div class="bg-slate-50 dark:bg-slate-900/50 px-6 py-4 flex gap-3">
                    <button 
                        type="button" 
                        onclick="closeClaimModal()"
                        class="flex-1 px-4 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-semibold hover:bg-slate-300 dark:hover:bg-slate-600 transition-all"
                    >
                        Batal
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 px-4 py-3 gradient-primary text-white rounded-xl font-semibold shadow-lg shadow-primary-500/30 hover:shadow-xl transition-all"
                    >
                        Kirim Klaim
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
// Open Claim Modal
function openClaimModal() {
    document.getElementById('claimModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

// Close Claim Modal
function closeClaimModal() {
    document.getElementById('claimModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeClaimModal();
    }
});

// Confirm Delete
function confirmDelete(itemId) {
    if (confirm('Apakah Anda yakin ingin menghapus laporan ini? Tindakan ini tidak dapat dibatalkan.')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('index.php?page=items&action=delete') ?>';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = itemId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
