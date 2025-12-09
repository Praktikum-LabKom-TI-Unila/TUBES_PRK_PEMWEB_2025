<?php
session_start();
require_once '../../src/config/database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$q_dokter = $conn->query("SELECT nama_dokter FROM dokter WHERE id_user = '$id_user'");
$data_dokter = $q_dokter->fetch_assoc();
$nama_dokter = $data_dokter['nama_dokter'] ?? 'Dokter';

function getCount($conn, $status = null) {
    $sql = "SELECT COUNT(*) as total FROM antrian WHERE DATE(waktu_daftar) = CURDATE()";
    
    if ($status) {
        $sql .= " AND status = '$status'";
    }
    
    $result = $conn->query($sql);
    return $result->fetch_assoc()['total'];
}

$stats = [
    'total' => getCount($conn),
    'menunggu' => getCount($conn, 'menunggu'),
    'diperiksa' => getCount($conn, 'diperiksa'),
    'selesai' => getCount($conn, 'selesai'),
];

$query_list = "SELECT a.*, p.nama_lengkap, po.nama_poli 
               FROM antrian a 
               JOIN pasien p ON a.id_pasien = p.id_pasien 
               JOIN jadwal_praktik j ON a.id_jadwal = j.id_jadwal
               JOIN dokter d ON j.id_dokter = d.id_dokter
               JOIN poli po ON d.id_poli = po.id_poli
               WHERE DATE(a.waktu_daftar) = CURDATE() 
               ORDER BY 
               CASE WHEN a.status = 'diperiksa' THEN 1 WHEN a.status = 'menunggu' THEN 2 ELSE 3 END, 
               a.nomor_antrian ASC";
$result_list = $conn->query($query_list);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dokter - Puskesmas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <div class="flex h-screen overflow-hidden">
        
        <?php require_once 'sidebar.php'; ?>

        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            
            <header class="bg-white border-b border-slate-100 sticky top-0 z-30 px-8 py-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-800">Dashboard Dokter</h2>
                        <p class="text-slate-500 text-sm mt-1">Selamat datang, <span class="text-slate-700 font-medium"><?= htmlspecialchars($nama_dokter) ?></span></p>
                    </div>
                    <div class="bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-600">
                        <?= date('d F Y') ?>
                    </div>
                </div>
            </header>

            <main class="w-full grow p-8">
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                    
                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col items-start">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-3xl font-bold text-slate-800 mb-1"><?= $stats['total'] ?></h3>
                        <p class="text-sm text-slate-500">Total Pasien Hari Ini</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col items-start">
                        <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-3xl font-bold text-slate-800 mb-1"><?= $stats['menunggu'] ?></h3>
                        <p class="text-sm text-slate-500">Menunggu</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col items-start">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-3xl font-bold text-slate-800 mb-1"><?= $stats['diperiksa'] ?></h3>
                        <p class="text-sm text-slate-500">Sedang Diperiksa</p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col items-start">
                        <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-3xl font-bold text-slate-800 mb-1"><?= $stats['selesai'] ?></h3>
                        <p class="text-sm text-slate-500">Selesai Hari Ini</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white">
                        <div>
                            <h3 class="font-bold text-slate-800 text-lg">Antrian Pasien Hari Ini</h3>
                            <p class="text-slate-400 text-xs">Daftar pasien berdasarkan status prioritas</p>
                        </div>
                        <a href="daftar_pasien.php" class="text-sm bg-emerald-50 text-emerald-600 px-4 py-2 rounded-lg hover:bg-emerald-100 font-bold transition">
                            Lihat Semua
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50">
                                <tr class="text-slate-500 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-4 font-bold border-b border-slate-200">No. Antrian</th>
                                    <th class="px-6 py-4 font-bold border-b border-slate-200">Nama Pasien</th>
                                    <th class="px-6 py-4 font-bold border-b border-slate-200">Poli</th>
                                    <th class="px-6 py-4 font-bold border-b border-slate-200">Status</th>
                                    <th class="px-6 py-4 font-bold border-b border-slate-200 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <?php if ($result_list->num_rows > 0): ?>
                                    <?php while ($row = $result_list->fetch_assoc()): ?>
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-6 py-5 font-bold text-emerald-600">
                                            A-<?= str_pad($row['nomor_antrian'], 3, '0', STR_PAD_LEFT) ?>
                                        </td>
                                        <td class="px-6 py-5 font-medium text-slate-700">
                                            <?= htmlspecialchars($row['nama_lengkap']) ?>
                                        </td>
                                        <td class="px-6 py-5 text-slate-500">
                                            <?= htmlspecialchars($row['nama_poli']) ?>
                                        </td>
                                        <td class="px-6 py-5">
                                            <?php 
                                            $badge_class = 'bg-slate-100 text-slate-600';
                                            if($row['status'] == 'menunggu') $badge_class = 'bg-yellow-100 text-yellow-800 border border-yellow-100';
                                            if($row['status'] == 'diperiksa') $badge_class = 'bg-emerald-100 text-emerald-800 border border-emerald-100';
                                            if($row['status'] == 'selesai') $badge_class = 'bg-green-100 text-green-800 border border-green-100';
                                            ?>
                                            <span class="px-3 py-1.5 rounded-md text-xs font-semibold <?= $badge_class ?>">
                                                <?= ucfirst($row['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <?php if($row['status'] == 'menunggu'): ?>
                                                <a href="pemeriksaan.php?id=<?= $row['id_antrian'] ?>" class="inline-flex items-center justify-center bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-2 rounded-lg text-xs font-bold transition shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                                                    Mulai Pemeriksaan
                                                </a>
                                            <?php elseif($row['status'] == 'diperiksa'): ?>
                                                <a href="pemeriksaan.php?id=<?= $row['id_antrian'] ?>" class="inline-flex items-center justify-center bg-slate-700 hover:bg-slate-800 text-white px-5 py-2 rounded-lg text-xs font-bold transition shadow-sm hover:shadow-md">
                                                    Lanjutkan
                                                </a>
                                            <?php else: ?>
                                                <span class="text-slate-400 text-xs italic flex justify-center items-center gap-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Selesai
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic bg-slate-50/50">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                                Belum ada antrian pasien hari ini.
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>
</body>
</html>