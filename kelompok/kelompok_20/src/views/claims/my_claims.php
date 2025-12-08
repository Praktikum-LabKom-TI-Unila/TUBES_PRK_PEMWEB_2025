<?php
/**
 * My Claims View
 * 
 * Available variables:
 * - $claims: Array of user's claims with item info
 */

declare(strict_types=1);
?>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            📋 Klaim Saya
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Daftar klaim yang telah Anda ajukan
        </p>
    </div>

    <?php if (empty($claims)): ?>
    <!-- Empty State -->
    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-12 text-center">
        <div class="text-6xl mb-4">📭</div>
        <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">
            Belum Ada Klaim
        </h3>
        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6">
            Anda belum mengajukan klaim untuk barang apapun. 
            Cari barang yang mungkin milik Anda dan ajukan klaim.
        </p>
        <a href="<?= base_url('index.php?page=items') ?>" 
           class="inline-flex items-center px-6 py-3 bg-cyan-500 hover:bg-cyan-600 text-white rounded-xl transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Cari Barang
        </a>
    </div>

    <?php else: ?>
    <!-- Claims List -->
    <div class="space-y-4">
        <?php foreach ($claims as $claim): ?>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
            <div class="flex flex-col md:flex-row">
                <!-- Image -->
                <div class="md:w-48 h-48 md:h-auto bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                    <?php if (!empty($claim['item_image'])): ?>
                    <img src="<?= base_url('assets/uploads/items/' . $claim['item_image']) ?>" 
                         alt="<?= htmlspecialchars($claim['item_title']) ?>"
                         class="w-full h-full object-cover">
                    <?php else: ?>
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Content -->
                <div class="flex-1 p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                        <div>
                            <!-- Type Badge -->
                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full mb-2
                                <?= $claim['item_type'] === 'lost' 
                                    ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' 
                                    : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' ?>">
                                <?= $claim['item_type'] === 'lost' ? 'Hilang' : 'Ditemukan' ?>
                            </span>
                            
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                <a href="<?= base_url('index.php?page=items&action=show&id=' . $claim['item_id']) ?>" 
                                   class="hover:text-cyan-500 transition-colors">
                                    <?= htmlspecialchars($claim['item_title']) ?>
                                </a>
                            </h3>
                            
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Dilaporkan oleh: <?= htmlspecialchars($claim['item_owner_name']) ?>
                            </p>
                        </div>

                        <!-- Status Badge -->
                        <span class="px-4 py-2 text-sm font-semibold rounded-full
                            <?php 
                            switch ($claim['status']) {
                                case 'pending':
                                    echo 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400';
                                    break;
                                case 'verified':
                                    echo 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
                                    break;
                                case 'rejected':
                                    echo 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
                                    break;
                            }
                            ?>">
                            <?php 
                            switch ($claim['status']) {
                                case 'pending': echo '⏳ Menunggu'; break;
                                case 'verified': echo '✅ Diverifikasi'; break;
                                case 'rejected': echo '❌ Ditolak'; break;
                            }
                            ?>
                        </span>
                    </div>

                    <!-- Info -->
                    <div class="flex flex-wrap gap-4 text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Diajukan: <?= date('d M Y, H:i', strtotime($claim['created_at'])) ?>
                        </span>
                    </div>

                    <?php if (!empty($claim['admin_notes'])): ?>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-semibold">Catatan:</span> <?= htmlspecialchars($claim['admin_notes']) ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <!-- Actions -->
                    <div class="flex flex-wrap gap-3">
                        <a href="<?= base_url('index.php?page=items&action=show&id=' . $claim['item_id']) ?>" 
                           class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors text-sm">
                            Lihat Detail Item
                        </a>
                        
                        <?php if ($claim['status'] === 'pending'): ?>
                        <form action="<?= base_url('index.php?page=claims&action=cancel') ?>" method="POST" 
                              onsubmit="return confirm('Yakin ingin membatalkan klaim ini?');">
                            <input type="hidden" name="claim_id" value="<?= $claim['id'] ?>">
                            <button type="submit" 
                                    class="px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors text-sm">
                                Batalkan Klaim
                            </button>
                        </form>
                        <?php endif; ?>

                        <?php if ($claim['status'] === 'verified'): ?>
                        <span class="px-4 py-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg text-sm">
                            🎉 Selamat! Hubungi pemilik untuk pengambilan
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
