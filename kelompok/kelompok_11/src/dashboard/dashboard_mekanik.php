<?php
$pageTitle = 'Dashboard Mekanik';
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../config/database.php';

$user_id = $user['id'];

// Ambil reservasi yang ditugaskan ke mekanik ini
$sql_my_reservasi = "SELECT COUNT(*) as total FROM reservations WHERE mekanik_id = $user_id AND status IN ('booked', 'in_progress')";
$total_my_reservasi = fetchOne($sql_my_reservasi)['total'] ?? 0;

$sql_completed_today = "SELECT COUNT(*) as total FROM reservations WHERE mekanik_id = $user_id AND DATE(updated_at) = CURDATE() AND status = 'completed'";
$completed_today = fetchOne($sql_completed_today)['total'] ?? 0;

// Ambil list reservasi mekanik ini
$sql_reservasi_list = "SELECT r.*, s.nama as layanan_nama 
                       FROM reservations r 
                       LEFT JOIN services s ON r.layanan_id = s.id 
                       WHERE r.mekanik_id = $user_id 
                       AND r.status IN ('booked', 'in_progress')
                       ORDER BY r.tanggal ASC 
                       LIMIT 10";
$reservasi_list = fetchAll($sql_reservasi_list);

// Ambil parts yang sering dipakai (dari semua transaksi - untuk demo)
// TODO: Nanti bisa disesuaikan dengan relasi mekanik ke transaksi
$sql_my_parts = "SELECT p.nama, p.stok as total_used
                 FROM parts p
                 WHERE p.stok > 0
                 ORDER BY p.stok DESC
                 LIMIT 5";
$my_parts = fetchAll($sql_my_parts);
?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    
    <!-- Card 1 - Reservasi Aktif -->
    <div class="glass-panel p-6 rounded-3xl shadow-glass hover:-translate-y-1 transition duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center text-brand-blue">
                <i class="fas fa-wrench text-xl"></i>
            </div>
            <span class="bg-brand-blue/10 text-brand-blue py-1 px-3 rounded-full text-xs font-bold">Active</span>
        </div>
        <h3 class="text-3xl font-bold text-brand-dark"><?= $total_my_reservasi ?></h3>
        <p class="text-sm font-medium text-brand-gray mt-1">Pekerjaan Saya</p>
    </div>

    <!-- Card 2 - Selesai Hari Ini -->
    <div class="glass-panel p-6 rounded-3xl shadow-glass hover:-translate-y-1 transition duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-2xl bg-green-100 flex items-center justify-center text-green-600">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <span class="bg-green-100 text-green-600 py-1 px-3 rounded-full text-xs font-bold">Today</span>
        </div>
        <h3 class="text-3xl font-bold text-brand-dark"><?= $completed_today ?></h3>
        <p class="text-sm font-medium text-brand-gray mt-1">Selesai Hari Ini</p>
    </div>

    <!-- Card 3 - Quick Action -->
    <div class="glass-panel p-6 rounded-3xl shadow-glass hover:-translate-y-1 transition duration-300 cursor-pointer hover:shadow-xl">
        <div class="flex flex-col items-center justify-center h-full text-center">
            <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center text-brand-light-gray mb-3">
                <i class="fas fa-clipboard-list text-xl"></i>
            </div>
            <p class="text-sm font-bold text-brand-dark">Lihat Semua</p>
            <p class="text-xs text-brand-gray mt-1">Pekerjaan Saya</p>
        </div>
    </div>
</div>

<!-- Two Column Grid -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    
    <!-- Pekerjaan Saya -->
    <div class="glass-panel rounded-3xl shadow-glass overflow-hidden">
        <div class="px-6 py-4 border-b border-white/50">
            <h3 class="text-lg font-bold text-brand-dark">Pekerjaan Saya</h3>
            <p class="text-sm text-brand-gray">Reservasi yang ditugaskan</p>
        </div>
        <div class="p-6">
            <?php if (empty($reservasi_list)): ?>
            <div class="text-center py-8 text-brand-gray">
                <i class="fas fa-coffee text-4xl mb-3 text-gray-300"></i>
                <p>Tidak ada pekerjaan</p>
            </div>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($reservasi_list as $item): 
                    $waktu = date('H:i', strtotime($item['tanggal']));
                    $status_class = [
                        'booked' => 'bg-yellow-100 text-yellow-800',
                        'in_progress' => 'bg-blue-100 text-blue-800',
                    ][$item['status']] ?? 'bg-gray-100 text-gray-800';
                    $status_icon = [
                        'booked' => 'fa-clock',
                        'in_progress' => 'fa-tools',
                    ][$item['status']] ?? 'fa-question';
                ?>
                <div class="flex items-center justify-between p-3 bg-white/40 rounded-xl hover:bg-white/60 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-brand-blue/10 rounded-xl flex items-center justify-center">
                            <i class="fas <?= $status_icon ?> text-brand-blue"></i>
                        </div>
                        <div>
                            <p class="font-medium text-brand-dark text-sm"><?= htmlspecialchars($item['nama_pelanggan']) ?></p>
                            <p class="text-xs text-brand-gray"><?= htmlspecialchars($item['plat_kendaraan'] ?? 'N/A') ?> • <?= $waktu ?></p>
                            <p class="text-xs text-brand-gray mt-0.5"><?= htmlspecialchars($item['layanan_nama'] ?? '-') ?></p>
                        </div>
                    </div>
                    <span class="px-2 py-1 rounded-lg text-xs font-medium <?= $status_class ?>">
                        <?= $item['status'] == 'booked' ? 'Pending' : 'Progress' ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Parts yang Sering Dipakai -->
    <div class="glass-panel rounded-3xl shadow-glass overflow-hidden">
        <div class="px-6 py-4 border-b border-white/50">
            <h3 class="text-lg font-bold text-brand-dark">Parts Sering Dipakai</h3>
            <p class="text-sm text-brand-gray">7 hari terakhir</p>
        </div>
        <div class="p-6">
            <?php if (empty($my_parts)): ?>
            <div class="text-center py-8 text-brand-gray">
                <i class="fas fa-box text-4xl mb-3 text-gray-300"></i>
                <p>Belum ada data</p>
            </div>
            <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($my_parts as $item): ?>
                <div class="flex items-center justify-between p-3 bg-white/40 rounded-xl hover:bg-white/60 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-cog text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-brand-dark text-sm"><?= htmlspecialchars($item['nama']) ?></p>
                            <p class="text-xs text-brand-gray">Terpakai minggu ini</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-purple-100 text-purple-600 rounded-lg text-sm font-bold">
                        <?= $item['total_used'] ?> unit
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
