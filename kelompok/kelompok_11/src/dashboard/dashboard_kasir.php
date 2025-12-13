<?php
$pageTitle = 'Dashboard Kasir';
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../config/database.php';

// Ambil data statistik kasir
$sql_total_transaksi = "SELECT COUNT(*) as total, SUM(grand_total) as omzet FROM transactions WHERE DATE(created_at) = CURDATE() AND status = 'paid'";
$stats_transaksi = fetchOne($sql_total_transaksi);
$total_transaksi = $stats_transaksi['total'] ?? 0;
$omzet = $stats_transaksi['omzet'] ?? 0;

$sql_draft = "SELECT COUNT(*) as total FROM transactions WHERE status = 'draft'";
$total_draft = fetchOne($sql_draft)['total'] ?? 0;

// Ambil transaksi hari ini
$sql_transaksi_list = "SELECT * FROM transactions WHERE DATE(created_at) = CURDATE() ORDER BY created_at DESC LIMIT 10";
$transaksi_list = fetchAll($sql_transaksi_list);

// Ambil draft transaksi
$sql_draft_list = "SELECT * FROM transactions WHERE status = 'draft' ORDER BY created_at DESC LIMIT 5";
$draft_list = fetchAll($sql_draft_list);
?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    
    <!-- Card 1 - Omzet -->
    <div class="glass-panel p-6 rounded-3xl shadow-glass hover:-translate-y-1 transition duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-2xl bg-green-100 flex items-center justify-center text-green-600">
                <i class="fas fa-wallet text-xl"></i>
            </div>
            <span class="bg-green-100 text-green-600 py-1 px-3 rounded-full text-xs font-bold"><?= $total_transaksi ?></span>
        </div>
        <h3 class="text-3xl font-bold text-brand-dark">Rp <?= number_format($omzet, 0, ',', '.') ?></h3>
        <p class="text-sm font-medium text-brand-gray mt-1">Omzet Hari Ini</p>
    </div>

    <!-- Card 2 - Transaksi -->
    <div class="glass-panel p-6 rounded-3xl shadow-glass hover:-translate-y-1 transition duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center text-brand-blue">
                <i class="fas fa-receipt text-xl"></i>
            </div>
            <span class="bg-brand-blue/10 text-brand-blue py-1 px-3 rounded-full text-xs font-bold">Paid</span>
        </div>
        <h3 class="text-3xl font-bold text-brand-dark"><?= $total_transaksi ?></h3>
        <p class="text-sm font-medium text-brand-gray mt-1">Transaksi Selesai</p>
    </div>

    <!-- Card 3 - Draft -->
    <div class="glass-panel p-6 rounded-3xl shadow-glass hover:-translate-y-1 transition duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center text-yellow-600">
                <i class="fas fa-file-invoice text-xl"></i>
            </div>
            <span class="bg-yellow-100 text-yellow-600 py-1 px-3 rounded-full text-xs font-bold">Pending</span>
        </div>
        <h3 class="text-3xl font-bold text-brand-dark"><?= $total_draft ?></h3>
        <p class="text-sm font-medium text-brand-gray mt-1">Transaksi Draft</p>
    </div>
</div>

<!-- Two Column Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    
    <!-- Transaksi Hari Ini -->
    <div class="glass-panel rounded-3xl shadow-glass overflow-hidden">
        <div class="px-6 py-4 border-b border-white/50 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-brand-dark">Transaksi Hari Ini</h3>
                <p class="text-sm text-brand-gray">Riwayat pembayaran</p>
            </div>
            <a href="../pos/pos.php" class="text-brand-blue hover:underline text-sm font-medium">
                <i class="fas fa-plus mr-1"></i>Buat Baru
            </a>
        </div>
        <div class="p-6">
            <?php if (empty($transaksi_list)): ?>
            <div class="text-center py-8 text-brand-gray">
                <i class="fas fa-inbox text-4xl mb-3 text-gray-300"></i>
                <p>Belum ada transaksi</p>
            </div>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($transaksi_list as $item): 
                    $status_class = [
                        'paid' => 'bg-green-100 text-green-800',
                        'draft' => 'bg-yellow-100 text-yellow-800',
                        'canceled' => 'bg-red-100 text-red-800'
                    ][$item['status']] ?? 'bg-gray-100 text-gray-800';
                ?>
                <div class="flex items-center justify-between p-3 bg-white/40 rounded-xl hover:bg-white/60 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-brand-blue/10 rounded-xl flex items-center justify-center">
                            <i class="fas fa-file-invoice text-brand-blue"></i>
                        </div>
                        <div>
                            <p class="font-medium text-brand-dark text-sm"><?= htmlspecialchars($item['kode']) ?></p>
                            <p class="text-xs text-brand-gray"><?= htmlspecialchars($item['pelanggan_nama'] ?? 'Walk-in') ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-brand-dark text-sm">Rp <?= number_format($item['grand_total'], 0, ',', '.') ?></p>
                        <span class="px-2 py-0.5 rounded-lg text-xs font-medium <?= $status_class ?>">
                            <?= ucfirst($item['status']) ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Draft Transaksi -->
    <div class="glass-panel rounded-3xl shadow-glass overflow-hidden">
        <div class="px-6 py-4 border-b border-white/50">
            <h3 class="text-lg font-bold text-brand-dark">Draft Transaksi</h3>
            <p class="text-sm text-brand-gray">Menunggu pembayaran</p>
        </div>
        <div class="p-6">
            <?php if (empty($draft_list)): ?>
            <div class="text-center py-8 text-brand-gray">
                <i class="fas fa-check-circle text-4xl mb-3 text-green-300"></i>
                <p>Tidak ada draft</p>
            </div>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($draft_list as $item): ?>
                <div class="flex items-center justify-between p-3 bg-white/40 rounded-xl hover:bg-white/60 transition cursor-pointer">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-clock text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-brand-dark text-sm"><?= htmlspecialchars($item['kode']) ?></p>
                            <p class="text-xs text-brand-gray"><?= htmlspecialchars($item['pelanggan_nama'] ?? 'Walk-in') ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-brand-dark text-sm">Rp <?= number_format($item['grand_total'], 0, ',', '.') ?></p>
                        <p class="text-xs text-brand-gray"><?= date('H:i', strtotime($item['created_at'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
