<?php
session_start();
require_once '../../src/config/database.php';

$id_antrian = $_GET['id'] ?? null;
if(!$id_antrian) {
    echo "<script>window.location='dashboard.php';</script>";
    exit;
}

$query = "SELECT a.*, p.*, po.nama_poli 
          FROM antrian a 
          JOIN pasien p ON a.id_pasien = p.id_pasien 
          JOIN jadwal_praktik j ON a.id_jadwal = j.id_jadwal
          JOIN dokter d ON j.id_dokter = d.id_dokter
          JOIN poli po ON d.id_poli = po.id_poli
          WHERE a.id_antrian = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_antrian);
$stmt->execute();
$pasien = $stmt->get_result()->fetch_assoc();

if(!$pasien) {
    echo "<script>alert('Pasien tidak ditemukan!'); window.location='dashboard.php';</script>";
    exit;
}

$umur = 0;
if (!empty($pasien['tanggal_lahir'])) {
    $umur = date_diff(date_create($pasien['tanggal_lahir']), date_create('today'))->y;
}

$q_rm = $conn->query("SELECT * FROM rekam_medis WHERE id_antrian = '$id_antrian'");
$rm   = $q_rm->fetch_assoc(); 

$rujukan = null;
if ($rm) { 
    $id_rekam = $rm['id_rekam'];
    $q_rujukan = $conn->query("SELECT * FROM rujukan WHERE id_rekam = '$id_rekam'");
    $rujukan = $q_rujukan->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan Pasien</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

<div class="flex h-screen overflow-hidden">
    
    <?php require_once 'sidebar.php'; ?>

    <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden bg-slate-50">
        
        <header class="bg-white border-b border-slate-100 sticky top-0 z-30 px-8 py-4 flex justify-between items-center shadow-sm">
            <h2 class="text-xl font-bold text-slate-800">Pemeriksaan Pasien</h2>
            <a href="dashboard.php" class="text-sm text-slate-500 hover:text-emerald-600 flex items-center transition font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Dashboard
            </a>
        </header>

        <main class="w-full grow p-8 max-w-5xl mx-auto pb-32"> 
            
            <div class="bg-emerald-500 rounded-xl p-6 text-white shadow-lg mb-6 relative overflow-hidden">
                 <div class="relative z-10 flex flex-col md:flex-row justify-between items-start gap-4">
                    <div class="flex items-center gap-5">
                        <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm border border-white/20 shrink-0">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold mb-1"><?= htmlspecialchars($pasien['nama_lengkap']) ?></h3>
                            <div class="flex flex-wrap gap-x-8 gap-y-1 mt-2 text-emerald-50 text-sm">
                                <div><span class="opacity-70 text-[10px] uppercase font-bold">No. Antrian</span> <p class="font-bold text-lg">A-<?= str_pad($pasien['nomor_antrian'], 3, '0', STR_PAD_LEFT) ?></p></div>
                                <div><span class="opacity-70 text-[10px] uppercase font-bold">Poli</span> <p class="font-bold text-lg"><?= htmlspecialchars($pasien['nama_poli']) ?></p></div>
                                <div><span class="opacity-70 text-[10px] uppercase font-bold">Umur</span> <p class="font-bold text-lg"><?= $umur ?> Thn</p></div>
                                <div><span class="opacity-70 text-[10px] uppercase font-bold">JK</span> <p class="font-bold text-lg"><?= $pasien['jenis_kelamin'] == 'L' ? 'L' : 'P' ?></p></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/10 px-3 py-1 rounded-lg text-xs font-medium border border-white/10 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <?= date('d F Y') ?>
                    </div>
                </div>
            </div>

            <form action="proses_pemeriksaan.php" method="POST">
                <input type="hidden" name="id_antrian" value="<?= $id_antrian ?>">
                <input type="hidden" name="id_pasien" value="<?= $pasien['id_pasien'] ?>">

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-6">
                    <h4 class="font-bold text-slate-800 mb-4 text-sm border-b border-slate-100 pb-2">Tanda Vital</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Tekanan Darah</label>
                            <input type="text" name="tensi" value="<?= $rm['tensi'] ?? '' ?>" placeholder="120/80 mmHg" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Suhu Tubuh</label>
                            <input type="text" name="suhu" value="<?= $rm['suhu'] ?? '' ?>" placeholder="36.5°C" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Nadi</label>
                            <input type="text" name="nadi" value="<?= $rm['nadi'] ?? '' ?>" placeholder="80 x/menit" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase">Pernapasan</label>
                            <input type="text" name="rr" value="<?= $rm['rr'] ?? '' ?>" placeholder="20 x/menit" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-6 space-y-5">
                    <h4 class="font-bold text-slate-800 text-sm border-b border-slate-100 pb-2">Data Pemeriksaan</h4>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Keluhan Pasien <span class="text-red-500">*</span></label>
                        <textarea name="keluhan" rows="3" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" placeholder="Tuliskan keluhan pasien..."><?= $rm['keluhan'] ?? '' ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Diagnosa <span class="text-red-500">*</span></label>
                        <textarea name="diagnosa" rows="2" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" placeholder="Tuliskan hasil diagnosa..."><?= $rm['diagnosa'] ?? '' ?></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Resep Obat</label>
                        <textarea name="resep" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" placeholder="Nama obat, dosis, durasi..."><?= $rm['resep_obat'] ?? '' ?></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tindakan / Anjuran</label>
                        <textarea name="tindakan" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" placeholder="Tindakan medis atau anjuran..."><?= $rm['pemeriksaan'] ?? '' ?></textarea>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm mb-8">
                     <div class="flex items-center mb-4">
                        <div class="relative flex items-start">
                            <div class="flex items-center h-6">
                                <input id="checkRujukan" type="checkbox" class="w-5 h-5 text-emerald-600 border-slate-300 rounded focus:ring-emerald-500 cursor-pointer" onclick="toggleRujukan()" <?= $rujukan ? 'checked' : '' ?>>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="checkRujukan" class="font-bold text-slate-800 cursor-pointer select-none">Pasien Perlu Rujukan?</label>
                                <p class="text-slate-500 text-xs">Centang jika pasien perlu dirujuk ke Rumah Sakit lain.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div id="formRujukan" class="<?= $rujukan ? '' : 'hidden' ?> space-y-5 border-t border-slate-100 pt-5 animate-fade-in-down">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Rumah Sakit Tujuan <span class="text-red-500">*</span></label>
                            <input type="text" name="rs_tujuan" value="<?= $rujukan['faskes_tujuan'] ?? '' ?>" class="w-full bg-blue-50 border border-blue-200 rounded-lg px-3 py-2.5 text-sm text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Contoh: RSUD Hasan Sadikin Bandung">
                        </div>
                         <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Alasan Rujukan</label>
                            <textarea name="alasan_rujukan" rows="2" class="w-full bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="Alasan klinis..."><?= $rujukan['alasan_rujukan'] ?? '' ?></textarea>
                        </div>
                        <div class="bg-blue-50 text-blue-700 text-xs p-3 rounded-lg border border-blue-100">
                            <strong>Catatan:</strong> Permintaan rujukan akan dikirim ke Admin untuk diproses dan dicetak.
                        </div>
                    </div>
                </div>

                <div class="fixed bottom-0 right-0 w-full md:w-[calc(100%-16rem)] bg-white border-t border-slate-200 p-4 px-8 z-50">
                    <div class="flex gap-4 max-w-5xl mx-auto">
                        <button type="submit" name="action" value="simpan" class="flex-1 bg-[#4A6F9B] hover:bg-[#3B5D8F] text-white py-3.5 rounded-xl font-bold transition shadow-lg flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                            Simpan Pemeriksaan
                        </button>
                        
                        <button type="submit" name="action" value="selesai" class="flex-1 bg-emerald-500 hover:bg-emerald-600 text-white py-3.5 rounded-xl font-bold transition shadow-lg flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Tandai Selesai
                        </button>
                    </div>
                </div>

            </form>
        </main>
    </div>
</div>

<script>
function toggleRujukan() {
    const checkbox = document.getElementById('checkRujukan');
    const form = document.getElementById('formRujukan');
    if (checkbox.checked) {
        form.classList.remove('hidden');
    } else {
        form.classList.add('hidden');
    }
}
</script>
</body>
</html>