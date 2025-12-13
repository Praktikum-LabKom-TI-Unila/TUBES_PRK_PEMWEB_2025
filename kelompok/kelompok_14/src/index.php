<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RepairinBro - Jasa Service Elektronik Profesional</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-white text-slate-800">
<?php
/**
 * Landing Page Public
 * Halaman utama yang diakses oleh user umum/pelanggan.
 * Menampilkan fitur tracking resi dan informasi layanan.
 */
session_start();
require_once 'config.php';
// Ambil pengaturan aplikasi dari database (Logo, Nama App, dll)
$settings = $conn->query("SELECT * FROM app_settings WHERE id=1 LIMIT 1")->fetch_assoc();
$company_name = $settings['company_name'] ?? 'RepairinBro';
$address = $settings['address'] ?? 'Jl. Elektronik Maju No. 123, Jakarta Selatan';
$phone = $settings['phone'] ?? '0812-3456-7890';
$email = $settings['email'] ?? 'support@repairinbro.com';
?>

    <!-- Navbar Simple -->
    <nav class="border-b border-slate-100 sticky top-0 bg-white z-50">
        <div class="container mx-auto px-6 h-20 flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center gap-3">
                <img src="assets/photos/logo.png" alt="RepairinBro" class="h-12 w-12 object-contain">
                <div>
                    <span class="block text-xl font-bold tracking-tight text-slate-900 leading-none">RepairinBro</span>
                    <span class="text-xs text-slate-500 font-medium">Electronic Service Center</span>
                </div>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-8">
                <a href="#layanan" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Layanan</a>
                <a href="#keunggulan" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Kenapa Kami</a>
                <a href="#lokasi" class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Lokasi</a>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3">
                <a href="halaman-resi/tracking_resi.php" class="hidden sm:flex items-center gap-2 text-slate-700 hover:text-blue-600 font-semibold text-sm px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                    <i class="fas fa-search"></i> Cek Resi
                </a>
                <a href="login.php" class="bg-slate-900 hover:bg-slate-800 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition">
                    Masuk
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section: Fokus pada Kepercayaan & Masalah User -->
    <header class="relative bg-slate-50 overflow-hidden">
        <div class="container mx-auto px-6 py-20 md:py-32 relative z-10">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 mb-6 leading-tight">
                    Gadget Rusak?<br> 
                    <span class="text-blue-600">Kami Perbaiki Sekarang.</span>
                </h1>
                <p class="text-lg text-slate-600 mb-8 max-w-xl leading-relaxed">
                    Jangan biarkan perangkat rusak menghambat produktivitasmu. Kami ahli memperbaiki Laptop, HP, dan PC dengan garansi resmi dan pengerjaan transparan.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="halaman-resi/tracking_resi.php" class="inline-flex justify-center items-center gap-3 bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-bold transition shadow-lg shadow-blue-200">
                        <i class="fas fa-search"></i> Cek Status Servis
                    </a>
                    <a href="https://wa.me/6281234567890" class="inline-flex justify-center items-center gap-3 bg-white text-slate-700 hover:text-green-600 border border-slate-200 hover:border-green-200 px-8 py-4 rounded-xl font-bold transition">
                        <i class="fab fa-whatsapp text-xl"></i> Konsultasi Gratis
                    </a>
                </div>

                <div class="mt-10 flex items-center gap-6 text-sm text-slate-500 font-medium">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i> Part Original
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i> Bergaransi
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i> Teknisi Ahli
                    </div>
                </div>
            </div>
        </div>

        <!-- Decorative Image -->
        <div class="hidden lg:block absolute top-0 right-0 w-1/2 h-full">
            <img src="assets/photos/foto_komponen.jpeg" alt="Workbench" class="w-full h-full object-cover opacity-90" style="clip-path: polygon(20% 0, 100% 0, 100% 100%, 0% 100%);">
            <div class="absolute inset-0 bg-gradient-to-r from-slate-50 via-slate-50/20 to-transparent"></div>
        </div>
    </header>

    <!-- Layanan Section: Lebih Visual & Langsung -->
    <section id="layanan" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-4">Apa yang Bisa Kami Bantu?</h2>
                    <p class="text-slate-500 max-w-lg">Kami menangani berbagai jenis kerusakan perangkat elektronik, mulai dari hardware hingga software.</p>
                </div>
                <!-- <a href="#" class="text-blue-600 font-semibold hover:underline mt-4 md:mt-0">Lihat Semua Layanan &rarr;</a> -->
            </div>

            <!-- Single Box Design -->
            <div class="max-w-5xl mx-auto bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden relative">
                <!-- Header Box -->
                <div class="bg-slate-900 text-white p-10 md:p-12 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600 rounded-full blur-3xl opacity-20 -mr-16 -mt-16"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div>
                            <h3 class="text-3xl font-bold mb-2">Daftar Layanan & Estimasi</h3>
                            <p class="text-slate-400">Transparansi harga dan kualitas adalah prioritas kami.</p>
                        </div>
                        <a href="https://wa.me/6281234567890" class="flex items-center gap-3 bg-white/10 hover:bg-white/20 px-6 py-3 rounded-xl backdrop-blur-md transition border border-white/10">
                            <i class="fab fa-whatsapp text-green-400 text-xl"></i>
                            <span class="font-semibold">Konsultasi Dulu</span>
                        </a>
                    </div>
                </div>

                <!-- Service List -->
                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-100">
                    
                    <!-- Left Column: Heavy Devices -->
                    <div class="p-8 md:p-12 space-y-10">
                        <div class="flex gap-6 group">
                            <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-600 shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-colors duration-300">
                                <i class="fas fa-desktop text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-bold text-slate-900 mb-1">Laptop & PC Workstation</h4>
                                <p class="text-sm text-slate-500 mb-4 leading-relaxed">Penanganan hardware berat, perbaikan motherboard, dan penggantian layar.</p>
                                <ul class="space-y-3">
                                    <li class="flex items-center justify-between text-sm group/item hover:bg-slate-50 p-2 rounded-lg -mx-2 transition">
                                        <span class="text-slate-600">Ganti LCD / Layar</span>
                                        <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-xs">Mulai 350rb</span>
                                    </li>
                                    <li class="flex items-center justify-between text-sm group/item hover:bg-slate-50 p-2 rounded-lg -mx-2 transition">
                                        <span class="text-slate-600">Service Motherboard</span>
                                        <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-xs">Check Dulu</span>
                                    </li>
                                    <li class="flex items-center justify-between text-sm group/item hover:bg-slate-50 p-2 rounded-lg -mx-2 transition">
                                        <span class="text-slate-600">Install OS + Driver</span>
                                        <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-xs">50rb</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="flex gap-6 group">
                            <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-600 shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-colors duration-300">
                                <i class="fas fa-microchip text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-bold text-slate-900 mb-1">Upgrade & Maintenance</h4>
                                <p class="text-sm text-slate-500 mb-2 leading-relaxed">Optimasi performa perangkat lama agar kembali ngebut.</p>
                                <div class="flex gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700">
                                        <i class="fas fa-check-circle"></i> Bersih Debu
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700">
                                        <i class="fas fa-check-circle"></i> Ganti Pasta
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Mobile & Others -->
                    <div class="p-8 md:p-12 space-y-10">
                        <div class="flex gap-6 group">
                            <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-600 shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-colors duration-300">
                                <i class="fas fa-mobile-screen-button text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-bold text-slate-900 mb-1">Smartphone Repair</h4>
                                <p class="text-sm text-slate-500 mb-4 leading-relaxed">Spesialis Android & iPhone segala kondisi kerusakan.</p>
                                <ul class="space-y-3">
                                    <li class="flex items-center justify-between text-sm group/item hover:bg-slate-50 p-2 rounded-lg -mx-2 transition">
                                        <span class="text-slate-600">Ganti Baterai Original</span>
                                        <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-xs">Mulai 100rb</span>
                                    </li>
                                    <li class="flex items-center justify-between text-sm group/item hover:bg-slate-50 p-2 rounded-lg -mx-2 transition">
                                        <span class="text-slate-600">LCD / Touchscreen</span>
                                        <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-xs">Mulai 150rb</span>
                                    </li>
                                    <li class="flex items-center justify-between text-sm group/item hover:bg-slate-50 p-2 rounded-lg -mx-2 transition">
                                        <span class="text-slate-600">Bootloop / Software</span>
                                        <span class="font-bold text-slate-900 bg-slate-100 px-2 py-0.5 rounded text-xs">75rb</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="flex gap-6 group">
                            <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-600 shrink-0 group-hover:bg-slate-900 group-hover:text-white transition-colors duration-300">
                                <i class="fas fa-screwdriver-wrench text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-bold text-slate-900 mb-1">Elektronik Umum</h4>
                                <p class="text-sm text-slate-500 leading-relaxed">Service Printer (Macet/Tinta), Speaker Bluetooth, Konsol Game, dan perangkat IT lainnya.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistik Sederhana (Kenapa Kami) -->
    <section id="keunggulan" class="py-16 bg-slate-900 text-white">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-x divide-slate-800">
                <div>
                    <div class="text-4xl font-bold mb-2 text-blue-400">5+</div>
                    <div class="text-slate-400 text-sm">Tahun Pengalaman</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2 text-blue-400">1000+</div>
                    <div class="text-slate-400 text-sm">Perangkat Diperbaiki</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2 text-blue-400">30</div>
                    <div class="text-slate-400 text-sm">Hari Garansi</div>
                </div>
                <div>
                    <div class="text-4xl font-bold mb-2 text-blue-400">4.9/5</div>
                    <div class="text-slate-400 text-sm">Rating Pelanggan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Simple & Direct -->
    <footer id="lokasi" class="bg-white border-t border-slate-200 pt-16 pb-8">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="assets/photos/logo.png" alt="RepairinBro" class="h-10 w-10 object-contain">
                        <span class="text-xl font-bold text-slate-900"><?php echo htmlspecialchars($company_name); ?></span>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed mb-6">
                        Jasa service elektronik terpercaya dengan mengutamakan kualitas sparepart dan kejujuran diagnosa.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-black hover:text-white transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-blue-600 hover:text-white transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-green-600 hover:text-white transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <!-- Kontak -->
                <div class="col-span-1 md:col-span-1">
                    <h3 class="font-bold text-slate-900 mb-6">Hubungi Kami</h3>
                    <ul class="space-y-4 text-sm text-slate-500">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1 text-slate-400"></i>
                            <span><?php echo nl2br(htmlspecialchars($address)); ?></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone text-slate-400"></i>
                            <span><?php echo htmlspecialchars($phone); ?></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-slate-400"></i>
                            <span><?php echo htmlspecialchars($email); ?></span>
                        </li>
                    </ul>
                </div>

                <!-- Jam Buka -->
                <div class="col-span-1 md:col-span-1">
                    <h3 class="font-bold text-slate-900 mb-6">Jam Operasional</h3>
                    <ul class="space-y-3 text-sm text-slate-500">
                        <li class="flex justify-between">
                            <span>Senin - Jumat</span>
                            <span class="font-semibold text-slate-900">09:00 - 20:00</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Sabtu</span>
                            <span class="font-semibold text-slate-900">09:00 - 17:00</span>
                        </li>
                        <li class="flex justify-between">
                            <span>Minggu</span>
                            <span class="text-red-500 font-semibold">Tutup</span>
                        </li>
                    </ul>
                </div>

                <!-- Maps -->
                <div class="col-span-1 md:col-span-1">
                    <div class="bg-slate-100 rounded-xl overflow-hidden h-40 flex items-center justify-center text-slate-400 text-sm border border-slate-200">
                        <?php if (!empty($settings['location_map'])): ?>
                            <div class="w-full h-full [&>iframe]:w-full [&>iframe]:h-full [&>iframe]:border-0">
                                <?php echo $settings['location_map']; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                <i class="fas fa-map-marked-alt text-3xl mb-2"></i>
                                <p>Peta Belum Diatur</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($settings['location_map'])): ?>
                        <p class="text-slate-500 text-xs mt-3 flex items-center gap-1">
                            <i class="fas fa-info-circle"></i> Lokasi Workshop Kami
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-8 text-center text-sm text-slate-500">
                <p>&copy; 2025 RepairinBro Service Center. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
