<?php
session_start();
require_once __DIR__ . '/../database/db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$role       = $_SESSION['role'] ?? 'guest';
$name       = $_SESSION['name'] ?? '';

// Ambil beberapa event terbaru
$stmt = $pdo->query("SELECT * FROM events ORDER BY tanggal ASC LIMIT 6");
$events = $stmt->fetchAll();

// Pesan setelah daftar event
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Temukan Event Menarik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-18px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .glass-white {
            background: rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(200, 200, 200, 0.3);
        }
        .card-hover { transition: all .3s ease; }
        .card-hover:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(0, 98, 255, 0.15);
        }
    </style>
</head>
<body class="bg-white font-sans">

<nav class="fixed w-full top-0 z-50 bg-white/80 backdrop-blur-lg border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-between items-center py-4">
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <div class="w-10 h-10 bg-[#111C3D] rounded-lg flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                EventHub
            </h1>

            <div class="flex gap-3">
                <?php if (!$isLoggedIn): ?>
                    <a href="login.php"
                       class="px-6 py-2 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition">
                        Login
                    </a>
                    <a href="register.php"
                       class="px-6 py-2 bg-[#111C3D] text-white font-semibold rounded-lg hover:bg-[#0d152f] transition">
                        Daftar
                    </a>
                <?php else: ?>
                    <span class="px-3 py-2 text-gray-700 text-sm">
                        Hai, <span class="font-semibold"><?= htmlspecialchars($name) ?></span>
                        (<?= htmlspecialchars($role) ?>)
                    </span>
                    <?php if ($role === 'admin'): ?>
                        <a href="../admin/dashboard.php"
                           class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm">
                            Admin Panel
                        </a>
                    <?php endif; ?>
                    <a href="logout.php"
                       class="px-4 py-2 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition text-sm">
                        Logout
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<section class="relative min-h-screen flex items-center justify-center px-4 pt-24 overflow-hidden bg-white">
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-20 left-20 w-72 h-72 bg-blue-200/30 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-20 w-96 h-96 bg-blue-100/40 rounded-full blur-3xl animate-float"
             style="animation-delay:1s"></div>
    </div>

    <div class="relative z-10 text-center max-w-4xl mx-auto">
        <h2 class="text-5xl md:text-7xl font-bold mt-6 mb-4 text-[#0D152F]">
            Temukan & Ikuti Event Terbaik
        </h2>
        <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed mb-8">
            EventHub membantu menemukan, mendaftar, dan mengelola event kampus, organisasi, maupun komunitas dengan lebih mudah.
        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="#events"
               class="px-8 py-4 bg-[#111C3D] text-white rounded-xl font-bold shadow-md hover:bg-[#0d152f] transition">
                Jelajahi Event
            </a>
            <a href="#features"
               class="px-8 py-4 bg-gray-100 border text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">
                Pelajari Lebih Lanjut
            </a>
        </div>

        <div class="mt-16 grid grid-cols-3 gap-8 max-w-3xl mx-auto">
            <div>
                <h3 class="text-4xl font-bold text-[#111C3D]">500+</h3>
                <p class="text-gray-600 mt-1">Event Tersedia</p>
            </div>
            <div>
                <h3 class="text-4xl font-bold text-[#111C3D]">10K+</h3>
                <p class="text-gray-600 mt-1">Peserta Aktif</p>
            </div>
            <div>
                <h3 class="text-4xl font-bold text-[#111C3D]">50+</h3>
                <p class="text-gray-600 mt-1">Organisasi Partner</p>
            </div>
        </div>
    </div>
</section>

<section id="features" class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
            <h3 class="text-4xl font-bold text-gray-900">Mengapa Memilih EventHub?</h3>
            <p class="text-gray-600 mt-2">Didesain untuk kebutuhan organisasi modern</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="glass-white rounded-2xl p-8 card-hover">
                <div class="w-14 h-14 bg-[#111C3D] rounded-xl flex items-center justify-center shadow-md mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h4 class="text-2xl font-bold text-gray-900 mb-2">Pendaftaran Cepat</h4>
                <p class="text-gray-600">Daftar event kini lebih mudah dan praktis.</p>
            </div>

            <div class="glass-white rounded-2xl p-8 card-hover">
                <div class="w-14 h-14 bg-[#111C3D] rounded-xl flex items-center justify-center shadow-md mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h4 class="text-2xl font-bold text-gray-900 mb-2">Event Terverifikasi</h4>
                <p class="text-gray-600">Setiap event telah melalui proses pengecekan.</p>
            </div>

            <div class="glass-white rounded-2xl p-8 card-hover">
                <div class="w-14 h-14 bg-[#111C3D] rounded-xl flex items-center justify-center shadow-md mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 00-4-5.659V5a2 2 0 00-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <h4 class="text-2xl font-bold text-gray-900 mb-2">Notifikasi Real-time</h4>
                <p class="text-gray-600">Dapatkan pembaruan langsung setiap saat.</p>
            </div>
        </div>
    </div>
</section>

<section id="events" class="py-20 px-4 bg-white">
    <div class="max-w-7xl mx-auto">

        <div class="text-center mb-6">
            <h3 class="text-4xl font-bold text-gray-900">Event Terbaru</h3>
            <p class="text-gray-600">Ikuti berbagai event menarik dari komunitas dan organisasi</p>
        </div>

        <?php if ($msg === 'daftar_sukses'): ?>
            <div class="max-w-3xl mx-auto mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-lg">
                Pendaftaran event berhasil, silakan menunggu konfirmasi dari admin.
            </div>
        <?php elseif ($msg === 'sudah_daftar'): ?>
            <div class="max-w-3xl mx-auto mb-6 bg-yellow-50 border border-yellow-200 text-yellow-700 text-sm px-4 py-3 rounded-lg">
                Anda sudah terdaftar pada event ini.
            </div>
        <?php endif; ?>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (!$events): ?>
                <p class="text-gray-500 col-span-3 text-center">Belum ada event yang tersedia.</p>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-lg card-hover">
                        <div class="h-40 relative overflow-hidden">

                            <?php if (!empty($event['thumbnail'])): ?>
                                <img src="../uploads/event_images/<?= htmlspecialchars($event['thumbnail']) ?>"
                                    class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-[#111C3D]"></div>
                            <?php endif; ?>

                            <div class="absolute bottom-4 left-4">
                                <span class="px-3 py-1 bg-white/90 text-[#111C3D] rounded-full text-xs font-bold">
                                    <?= htmlspecialchars($event['kategori'] ?: 'Event') ?>
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <h4 class="text-2xl font-bold text-gray-900 mb-2">
                                <a href="detail_event.php?id=<?= $event['id'] ?>" 
                                class="hover:text-blue-700 transition">
                                <?= htmlspecialchars($event['title']) ?>
                                </a>
                            </h4>
                            <p class="text-gray-600 mb-4">
                                <?= htmlspecialchars(mb_strimwidth($event['description'] ?? '', 0, 100, '...')) ?>
                            </p>

                            <div class="flex items-center gap-2 text-gray-500 text-sm mb-4">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <?= date('d M Y', strtotime($event['tanggal'])) ?> • <?= substr($event['waktu'],0,5) ?>
                            </div>

                            <?php if (!$isLoggedIn): ?>
                                <button onclick="window.location.href='login.php'"
                                        class="w-full bg-[#111C3D] text-white py-3 rounded-xl font-semibold shadow-md hover:bg-[#0d152f] transition">
                                    Login untuk Mendaftar
                                </button>
                            <?php elseif ($role === 'anggota'): ?>
                                <form action="daftar_event.php" method="POST">
                                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                    <button type="submit"
                                            class="w-full bg-[#111C3D] text-white py-3 rounded-xl font-semibold shadow-md hover:bg-[#0d152f] transition">
                                        Daftar Sekarang
                                    </button>
                                </form>
                            <?php else: ?>
                                <button disabled
                                        class="w-full bg-gray-300 text-gray-600 py-3 rounded-xl font-semibold">
                                    Admin tidak dapat mendaftar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-20">
    <div class="max-w-5xl mx-auto">
        <div class="glass-white p-12 rounded-3xl text-center shadow-lg">
            <h3 class="text-4xl font-bold text-gray-900 mb-4">Siap Memulai Perjalananmu?</h3>
            <p class="text-gray-600 mb-8">Temukan berbagai event dan daftar dalam hitungan detik.</p>

            <a href="<?= $isLoggedIn ? '#events' : 'register.php' ?>"
               class="px-10 py-4 inline-block bg-[#111C3D] text-white font-bold rounded-xl shadow-md hover:bg-[#0d152f] hover:scale-105 transition">
                <?= $isLoggedIn ? 'Lihat Event' : 'Daftar Sekarang' ?>
            </a>
        </div>
    </div>
</section>

<footer class="py-12 border-t border-gray-200 bg-white">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
            <div>
                <h4 class="text-gray-900 font-bold text-lg mb-3 flex items-center gap-2">
                    <div class="w-8 h-8 bg-[#111C3D] rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    EventHub
                </h4>
                <p class="text-gray-600 text-sm">Platform event modern untuk organisasi, komunitas, dan kampus.</p>
            </div>
            <div>
                <h5 class="text-gray-900 font-semibold mb-3">Fitur</h5>
                <ul class="text-gray-600 text-sm space-y-2">
                    <li><a class="hover:text-[#111C3D]" href="#events">Jelajahi Event</a></li>
                    <li><a class="hover:text-[#111C3D]" href="#">Buat Event</a></li>
                    <li><a class="hover:text-[#111C3D]" href="#">Manajemen Peserta</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-gray-900 font-semibold mb-3">Organisasi</h5>
                <ul class="text-gray-600 text-sm space-y-2">
                    <li><a class="hover:text-[#111C3D]" href="#">Tentang Organisasi</a></li>
                    <li><a class="hover:text-[#111C3D]" href="#">Struktur Kepengurusan</a></li>
                    <li><a class="hover:text-[#111C3D]" href="#">Dokumentasi Kegiatan</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-gray-900 font-semibold mb-3">Bantuan</h5>
                <ul class="text-gray-600 text-sm space-y-2">
                    <li><a class="hover:text-[#111C3D]" href="#">FAQ</a></li>
                    <li><a class="hover:text-[#111C3D]" href="#">Kontak</a></li>
                    <li><a class="hover:text-[#111C3D]" href="#">Syarat & Ketentuan</a></li>
                </ul>
            </div>
        </div>
        <div class="text-center text-gray-500 text-sm pt-6 border-t border-gray-200">
            © 2025 EventHub. Semua Hak Dilindungi.
        </div>
    </div>
</footer>

</body>
</html>
