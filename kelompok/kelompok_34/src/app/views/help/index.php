<?php
?>

<div class="max-w-6xl mx-auto">
  <!-- Header -->
  <div class="glass-effect p-8 rounded-3xl mb-8 text-center">
    <?php if (isset($_SESSION['error'])): ?>
      <div class="bg-red-500/20 text-red-200 p-4 mb-6 rounded-xl border border-red-400/40 text-sm flex items-start gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
        <span><?= $_SESSION['error'] ?></span>
      </div>
    <?php unset($_SESSION['error']);
    endif; ?>

    <div class="inline-block p-4 bg-gradient-to-br from-blue-400 to-blue-600 rounded-3xl mb-4">
      <i data-lucide="help-circle" class="w-16 h-16"></i>
    </div>
    <h1 class="text-4xl font-bold mb-3">Pusat Bantuan <?= APP_NAME ?></h1>
    <p class="text-xl text-gray-200">Panduan lengkap untuk menggunakan aplikasi Point of Sale</p>
  </div>

  <!-- Quick Links -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

    <!-- Panduan Penjual -->
    <a href="<?= BASE_URL ?>/help/sellerGuide" class="glass-effect p-6 rounded-2xl hover:scale-105 transition-transform group">
      <div class="bg-gradient-to-br from-green-400/30 to-teal-500/30 p-4 rounded-xl mb-4 inline-block">
        <i data-lucide="shopping-cart" class="w-10 h-10"></i>
      </div>
      <h3 class="text-xl font-bold mb-2 group-hover:text-green-300 transition">Panduan Penjual</h3>
      <p class="text-gray-200 text-sm">Cara menggunakan kasir, transaksi, dan keranjang belanja</p>
      <div class="mt-4 flex items-center gap-2 text-green-300">
        <span class="text-sm font-semibold">Lihat Panduan</span>
        <i data-lucide="arrow-right" class="w-4 h-4"></i>
      </div>
    </a>

    <!-- Panduan Admin -->
    <a href="<?= BASE_URL ?>/help/adminGuide" class="glass-effect p-6 rounded-2xl hover:scale-105 transition-transform group">
      <div class="bg-gradient-to-br from-blue-400/30 to-blue-600/30 p-4 rounded-xl mb-4 inline-block">
        <i data-lucide="shield-check" class="w-10 h-10"></i>
      </div>
      <h3 class="text-xl font-bold mb-2 group-hover:text-blue-300 transition">Panduan Admin</h3>
      <p class="text-gray-200 text-sm">Kelola produk, lihat laporan, dan dashboard statistik</p>
      <div class="mt-4 flex items-center gap-2 text-blue-300">
        <span class="text-sm font-semibold">Lihat Panduan</span>
        <i data-lucide="arrow-right" class="w-4 h-4"></i>
      </div>
    </a>

    <!-- FAQ -->
    <a href="<?= BASE_URL ?>/help/faq" class="glass-effect p-6 rounded-2xl hover:scale-105 transition-transform group">
      <div class="bg-gradient-to-br from-yellow-400/30 to-orange-500/30 p-4 rounded-xl mb-4 inline-block">
        <i data-lucide="message-circle-question" class="w-10 h-10"></i>
      </div>
      <h3 class="text-xl font-bold mb-2 group-hover:text-yellow-300 transition">FAQ</h3>
      <p class="text-gray-200 text-sm">Pertanyaan yang sering ditanyakan dan solusinya</p>
      <div class="mt-4 flex items-center gap-2 text-yellow-300">
        <span class="text-sm font-semibold">Lihat FAQ</span>
        <i data-lucide="arrow-right" class="w-4 h-4"></i>
      </div>
    </a>

  </div>

  <!-- Video Tutorial -->
  <div class="glass-effect p-8 rounded-3xl mb-8">
    <div class="flex items-center gap-3 mb-6">
      <div class="bg-gradient-to-br from-red-400/30 to-pink-500/30 p-3 rounded-xl">
        <i data-lucide="video" class="w-8 h-8"></i>
      </div>
      <h2 class="text-2xl font-bold">Video Tutorial</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white/10 p-6 rounded-xl hover:bg-white/15 transition-all">
        <div class="bg-gradient-to-br from-blue-500/30 to-purple-500/30 rounded-xl aspect-video mb-4 flex items-center justify-center">
          <i data-lucide="play-circle" class="w-16 h-16 text-white/80"></i>
        </div>
        <h4 class="font-bold mb-2">Cara Memulai Transaksi</h4>
        <p class="text-sm text-gray-200 mb-3">Panduan lengkap untuk penjual pemula</p>
        <button class="text-blue-300 hover:text-blue-200 text-sm font-semibold flex items-center gap-2 transition">
          <i data-lucide="play" class="w-4 h-4"></i>
          Tonton Sekarang
        </button>
      </div>

      <div class="bg-white/10 p-6 rounded-xl hover:bg-white/15 transition-all">
        <div class="bg-gradient-to-br from-green-500/30 to-teal-500/30 rounded-xl aspect-video mb-4 flex items-center justify-center">
          <i data-lucide="play-circle" class="w-16 h-16 text-white/80"></i>
        </div>
        <h4 class="font-bold mb-2">Mengelola Produk (Admin)</h4>
        <p class="text-sm text-gray-200 mb-3">Tambah, edit, dan hapus produk dengan mudah</p>
        <button class="text-green-300 hover:text-green-200 text-sm font-semibold flex items-center gap-2 transition">
          <i data-lucide="play" class="w-4 h-4"></i>
          Tonton Sekarang
        </button>
      </div>
    </div>
  </div>

  <!-- Quick Tips -->
  <div class="glass-effect p-8 rounded-3xl">
    <div class="flex items-center gap-3 mb-6">
      <div class="bg-gradient-to-br from-blue-400/30 to-blue-600/30 p-3 rounded-xl">
        <i data-lucide="lightbulb" class="w-8 h-8"></i>
      </div>
      <h2 class="text-2xl font-bold">Tips & Trik</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="bg-white/10 p-5 rounded-xl flex items-start gap-4">
        <div class="bg-blue-500/30 p-2 rounded-lg flex-shrink-0">
          <i data-lucide="zap" class="w-5 h-5"></i>
        </div>
        <div>
          <h4 class="font-bold mb-1">Shortcut Keyboard</h4>
          <p class="text-sm text-gray-200">Gunakan Enter untuk cepat checkout</p>
        </div>
      </div>

      <div class="bg-white/10 p-5 rounded-xl flex items-start gap-4">
        <div class="bg-green-500/30 p-2 rounded-lg flex-shrink-0">
          <i data-lucide="check-circle" class="w-5 h-5"></i>
        </div>
        <div>
          <h4 class="font-bold mb-1">Cek Stok Berkala</h4>
          <p class="text-sm text-gray-200">Pastikan stok produk selalu terisi</p>
        </div>
      </div>

      <div class="bg-white/10 p-5 rounded-xl flex items-start gap-4">
        <div class="bg-yellow-500/30 p-2 rounded-lg flex-shrink-0">
          <i data-lucide="bar-chart" class="w-5 h-5"></i>
        </div>
        <div>
          <h4 class="font-bold mb-1">Pantau Laporan</h4>
          <p class="text-sm text-gray-200">Lihat dashboard untuk analisis penjualan</p>
        </div>
      </div>

      <div class="bg-white/10 p-5 rounded-xl flex items-start gap-4">
        <div class="bg-red-500/30 p-2 rounded-lg flex-shrink-0">
          <i data-lucide="shield-alert" class="w-5 h-5"></i>
        </div>
        <div>
          <h4 class="font-bold mb-1">Logout Setelah Selesai</h4>
          <p class="text-sm text-gray-200">Jaga keamanan akun Anda</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Contact Support -->
  <div class="glass-effect p-8 rounded-3xl mt-8 text-center">
    <div class="inline-block p-4 bg-gradient-to-br from-blue-400/30 to-blue-600/30 rounded-2xl mb-4">
      <i data-lucide="headphones" class="w-10 h-10"></i>
    </div>
    <h3 class="text-2xl font-bold mb-3">Masih Butuh Bantuan?</h3>
    <p class="text-gray-200 mb-6">Tim support kami siap membantu Anda</p>
    <div class="flex gap-4 justify-center flex-wrap">
      <button class="glass-effect px-6 py-3 rounded-xl hover:bg-white/20 transition flex items-center gap-2">
        <i data-lucide="mail" class="w-5 h-5"></i>
        Email Support
      </button>
      <button class="btn-primary px-6 py-3 rounded-xl flex items-center gap-2">
        <i data-lucide="message-circle" class="w-5 h-5"></i>
        Live Chat
      </button>
    </div>
  </div>

</div>