<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-cyan-500 to-blue-600 rounded-2xl p-8 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">
                    <i class="fa-solid fa-boxes-stacked mr-3"></i>Kelola Postingan
                </h1>
                <p class="text-cyan-50">Kontrol penuh atas semua postingan barang hilang & ditemukan</p>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-xl px-6 py-4 text-center">
                <div class="text-3xl font-bold text-white"><?= number_format($totalItems) ?></div>
                <div class="text-xs text-cyan-50 uppercase font-semibold">Total Postingan</div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-100">
        <!-- Table Header -->
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-slate-50 to-gray-50">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="text-lg font-bold text-slate-800 flex items-center">
                    <i class="fa-solid fa-list mr-2 text-cyan-600"></i>
                    Daftar Semua Postingan
                </h3>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-600">Halaman <?= $page ?> dari <?= $totalPages ?></span>
                </div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Gambar
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Info Barang
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Pelapor
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fa-solid fa-inbox text-6xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 font-medium">Belum ada postingan barang</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($items as $item): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                <!-- Thumbnail -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($item['image_path'])): ?>
                                        <?php 
                                        $imageSrc = (strpos($item['image_path'], 'http://') === 0 || strpos($item['image_path'], 'https://') === 0) 
                                            ? $item['image_path']
                                            : base_url('assets/uploads/items/' . $item['image_path']);
                                        ?>
                                        <img 
                                            src="<?= $imageSrc ?>" 
                                            alt="<?= htmlspecialchars($item['title']) ?>" 
                                            class="w-16 h-16 object-cover rounded-lg shadow-sm border-2 border-gray-200"
                                        >
                                    <?php else: ?>
                                        <div class="w-16 h-16 bg-gradient-to-br from-slate-200 to-slate-300 rounded-lg flex items-center justify-center shadow-sm border-2 border-gray-200">
                                            <i class="fa-solid fa-image text-slate-400 text-2xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Info Barang -->
                                <td class="px-6 py-4">
                                    <div class="space-y-1">
                                        <div class="font-bold text-slate-900 text-sm leading-tight">
                                            <?= htmlspecialchars($item['title']) ?>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-gray-600">
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-md font-medium">
                                                <?= htmlspecialchars($item['category_name']) ?>
                                            </span>
                                            <span class="text-gray-400">•</span>
                                            <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-md font-medium">
                                                <i class="fa-solid fa-map-marker-alt mr-1"></i><?= htmlspecialchars($item['location_name']) ?>
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            <?= htmlspecialchars(substr($item['description'], 0, 60)) . (strlen($item['description']) > 60 ? '...' : '') ?>
                                        </div>
                                    </div>
                                </td>

                                <!-- Pelapor (User Info with Identity Number) -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm">
                                            <?= strtoupper(substr($item['user_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-slate-900">
                                                <?= htmlspecialchars($item['user_name']) ?>
                                            </div>
                                            <div class="text-xs text-gray-500 font-mono">
                                                <?= htmlspecialchars($item['user_identity']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Tanggal -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php
                                        $date = new DateTime($item['incident_date']);
                                        echo $date->format('d M Y');
                                        ?>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?php
                                        $created = new DateTime($item['created_at']);
                                        $now = new DateTime();
                                        $diff = $now->diff($created);
                                        if ($diff->days == 0) {
                                            if ($diff->h == 0) {
                                                echo $diff->i . ' menit lalu';
                                            } else {
                                                echo $diff->h . ' jam lalu';
                                            }
                                        } else {
                                            echo $diff->days . ' hari lalu';
                                        }
                                        ?>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusConfig = [
                                        'open' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'fa-check-circle', 'label' => 'Terbuka'],
                                        'process' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'icon' => 'fa-clock', 'label' => 'Proses'],
                                        'closed' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-lock', 'label' => 'Selesai']
                                    ];
                                    $status = $statusConfig[$item['status']] ?? $statusConfig['open'];
                                    ?>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold <?= $status['bg'] ?> <?= $status['text'] ?> shadow-sm">
                                        <i class="fa-solid <?= $status['icon'] ?> mr-1.5"></i>
                                        <?= $status['label'] ?>
                                    </span>
                                    <div class="mt-1">
                                        <?php if ($item['type'] === 'lost'): ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-600">
                                                <i class="fa-solid fa-exclamation-circle mr-1"></i>Hilang
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-600">
                                                <i class="fa-solid fa-hands-holding mr-1"></i>Ditemukan
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Aksi (Actions) -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- View Button -->
                                        <a 
                                            href="<?= base_url('index.php?page=items&action=show&id=' . $item['id']) ?>" 
                                            target="_blank"
                                            class="inline-flex items-center px-3 py-1.5 bg-cyan-500 hover:bg-cyan-600 text-white text-xs font-semibold rounded-lg transition-all duration-200 shadow-sm hover:shadow-md"
                                            title="Lihat Detail"
                                        >
                                            <i class="fa-solid fa-eye mr-1"></i>
                                            Lihat
                                        </a>

                                        <!-- Delete Button (Danger Zone) -->
                                        <form 
                                            method="POST" 
                                            action="<?= base_url('index.php?page=admin&action=delete_item') ?>" 
                                            class="inline"
                                            onsubmit="return confirm('⚠️ PERINGATAN ADMIN:\n\nApakah Anda yakin ingin menghapus postingan ini secara paksa?\n\nTindakan ini tidak dapat dibatalkan!\n\nJudul: <?= htmlspecialchars($item['title']) ?>\nPelapor: <?= htmlspecialchars($item['user_name']) ?>');"
                                        >
                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                            <button 
                                                type="submit" 
                                                class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-bold rounded-lg transition-all duration-200 shadow-sm hover:shadow-md border-2 border-red-600"
                                                title="Hapus Postingan (Admin)"
                                            >
                                                <i class="fa-solid fa-trash-can mr-1"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="bg-slate-50 px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Menampilkan <span class="font-semibold"><?= min(($page - 1) * 20 + 1, $totalItems) ?></span> 
                        sampai <span class="font-semibold"><?= min($page * 20, $totalItems) ?></span> 
                        dari <span class="font-semibold"><?= $totalItems ?></span> postingan
                    </div>
                    <div class="flex gap-2">
                        <?php if ($page > 1): ?>
                            <a 
                                href="<?= base_url('index.php?page=admin&action=items&p=' . ($page - 1)) ?>" 
                                class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium text-sm shadow-sm"
                            >
                                <i class="fa-solid fa-chevron-left mr-1"></i>
                                Sebelumnya
                            </a>
                        <?php endif; ?>

                        <?php if ($page < $totalPages): ?>
                            <a 
                                href="<?= base_url('index.php?page=admin&action=items&p=' . ($page + 1)) ?>" 
                                class="px-4 py-2 bg-cyan-500 text-white rounded-lg hover:bg-cyan-600 transition-colors font-medium text-sm shadow-sm"
                            >
                                Selanjutnya
                                <i class="fa-solid fa-chevron-right ml-1"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Info Box -->
    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-lg shadow-sm">
        <div class="flex items-start">
            <i class="fa-solid fa-shield-halved text-amber-500 text-xl mr-3 mt-0.5"></i>
            <div>
                <h4 class="text-sm font-bold text-amber-900 mb-1">Kebijakan Post-Moderation</h4>
                <p class="text-xs text-amber-800 leading-relaxed">
                    Semua user dapat posting langsung, namun Admin memiliki kontrol penuh untuk menghapus postingan yang melanggar aturan atau tidak sesuai. 
                    Gunakan tombol <span class="font-bold">"Hapus"</span> dengan bijak untuk menjaga kualitas konten platform.
                </p>
            </div>
        </div>
    </div>
</div>

