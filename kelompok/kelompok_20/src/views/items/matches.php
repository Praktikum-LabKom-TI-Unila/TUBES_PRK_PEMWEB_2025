<?php


declare(strict_types=1);

$targetItem = $matchData['target_item'];
$matches = $matchData['matches'];
$matchesCount = $matchData['matches_count'];
$oppositeType = $matchData['opposite_type'];

$typeLabel = $targetItem['type'] === 'lost' ? 'Hilang' : 'Ditemukan';
$oppositeLabel = $oppositeType === 'lost' ? 'Hilang' : 'Ditemukan';
?>

<div class="container mx-auto px-4 py-8">
    
    <div class="mb-8">
        <a href="<?= base_url('index.php?page=items&action=show&id=' . $targetItem['id']) ?>" 
           class="inline-flex items-center text-cyan-500 hover:text-cyan-400 mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Detail Item
        </a>
        
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
            🔍 Smart Match
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Menampilkan barang <span class="font-semibold text-cyan-500"><?= $oppositeLabel ?></span> 
            yang mungkin cocok dengan barang <span class="font-semibold"><?= $typeLabel ?></span> Anda
        </p>
    </div>

    
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-8 border-l-4 border-cyan-500">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">
            📌 Barang Anda
        </h2>
        <div class="flex flex-col md:flex-row gap-6">
            <?php if (!empty($targetItem['image_path'])): ?>
            <div class="w-full md:w-48 h-48 rounded-xl overflow-hidden flex-shrink-0">
                <img src="<?= base_url('assets/uploads/items/' . $targetItem['image_path']) ?>" 
                     alt="<?= htmlspecialchars($targetItem['title']) ?>"
                     class="w-full h-full object-cover">
            </div>
            <?php endif; ?>
            
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-3 py-1 text-xs font-semibold rounded-full 
                        <?= $targetItem['type'] === 'lost' 
                            ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' 
                            : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' ?>">
                        <?= $typeLabel ?>
                    </span>
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                        <?= htmlspecialchars($targetItem['category_name']) ?>
                    </span>
                </div>
                
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    <?= htmlspecialchars($targetItem['title']) ?>
                </h3>
                
                <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                    <?= htmlspecialchars($targetItem['description']) ?>
                </p>
                
                <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <?= htmlspecialchars($targetItem['location_name']) ?>
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <?= date('d M Y', strtotime($targetItem['incident_date'])) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
            ✨ Kecocokan Potensial
        </h2>
        <p class="text-gray-600 dark:text-gray-400">
            Ditemukan <span class="font-bold text-cyan-500"><?= $matchesCount ?></span> barang 
            <?= $oppositeLabel ?> yang mungkin cocok
        </p>
    </div>

    <?php if (empty($matches)): ?>
    
    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-12 text-center">
        <div class="text-6xl mb-4">🔎</div>
        <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">
            Belum Ada Kecocokan
        </h3>
        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
            Saat ini belum ada barang <?= $oppositeLabel ?> dengan kategori 
            <strong><?= htmlspecialchars($targetItem['category_name']) ?></strong> yang cocok.
            Coba periksa kembali nanti!
        </p>
    </div>

    <?php else: ?>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($matches as $match): ?>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300
            <?= $match['location_match'] ? 'ring-2 ring-cyan-500 ring-opacity-50' : '' ?>">
            
            <?php if ($match['location_match']): ?>
            <div class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white text-center py-1 text-xs font-semibold">
                📍 Lokasi Sama!
            </div>
            <?php endif; ?>
            
            
            <div class="relative h-48 bg-gray-100 dark:bg-gray-700">
                <?php if (!empty($match['image_path'])): ?>
                <img src="<?= base_url('assets/uploads/items/' . $match['image_path']) ?>" 
                     alt="<?= htmlspecialchars($match['title']) ?>"
                     class="w-full h-full object-cover">
                <?php else: ?>
                <div class="w-full h-full flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <?php endif; ?>
                
                
                <span class="absolute top-3 left-3 px-3 py-1 text-xs font-semibold rounded-full 
                    <?= $match['type'] === 'lost' 
                        ? 'bg-red-500 text-white' 
                        : 'bg-green-500 text-white' ?>">
                    <?= $match['type'] === 'lost' ? 'Hilang' : 'Ditemukan' ?>
                </span>
            </div>
            
            
            <div class="p-5">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-1">
                    <?= htmlspecialchars($match['title']) ?>
                </h3>
                
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-2">
                    <?= htmlspecialchars($match['description']) ?>
                </p>
                
                <div class="flex flex-wrap gap-2 mb-4 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        </svg>
                        <?= htmlspecialchars($match['location_name']) ?>
                    </span>
                    <span class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <?= date('d M Y', strtotime($match['incident_date'])) ?>
                    </span>
                </div>
                
                
                <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center text-white text-xs font-bold overflow-hidden">
                            <?php if (!empty($match['user_avatar'])): ?>
                            <img src="<?= base_url('assets/uploads/profiles/' . $match['user_avatar']) ?>" 
                                 alt="" class="w-full h-full object-cover">
                            <?php else: ?>
                            <?= strtoupper(substr($match['user_name'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            <?= htmlspecialchars($match['user_name']) ?>
                        </span>
                    </div>
                    
                    <a href="<?= base_url('index.php?page=items&action=show&id=' . $match['id']) ?>" 
                       class="px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white text-sm font-medium rounded-lg transition-colors">
                        Lihat Detail
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    
    <div class="mt-10 flex flex-wrap justify-center gap-4">
        <a href="<?= base_url('index.php?page=items&type=' . $oppositeType . '&category=' . $targetItem['category_id']) ?>" 
           class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            Lihat Semua Barang <?= $oppositeLabel ?> di Kategori Ini
        </a>
        <a href="<?= base_url('index.php?page=items') ?>" 
           class="px-6 py-3 bg-cyan-500 hover:bg-cyan-600 text-white rounded-xl transition-colors">
            Cari di Semua Barang
        </a>
    </div>
</div>
