<div class="max-w-4xl mx-auto">
  <div class="glass-effect rounded-3xl p-12 shadow-2xl text-center relative overflow-hidden">

    <!-- Decorative Elements -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl -z-10"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl -z-10"></div>

    <div class="relative z-10">
      <div class="inline-block p-4 bg-gradient-to-br from-blue-400 to-blue-600 rounded-3xl mb-6 animate-pulse">
        <i data-lucide="shopping-bag" class="w-16 h-16"></i>
      </div>

      <h1 class="text-5xl font-bold mb-4">
        <span class="bg-gradient-to-r from-blue-200 via-blue-300 to-blue-200 bg-clip-text text-transparent">
          Selamat datang di <?= APP_NAME ?>
        </span>
      </h1>

      <p class="text-xl text-gray-200 mb-8 max-w-2xl mx-auto">
        Point of Sale terpercaya untuk produk lokal Indonesia 🇮🇩
      </p>

      <?php if (!empty($_SESSION['user'])): ?>
        <div class="glass-effect rounded-2xl p-8 mb-6 inline-block">
          <div class="flex items-center justify-center gap-4">
            <div class="bg-gradient-to-br from-green-400 to-blue-500 p-3 rounded-full">
              <i data-lucide="user-check" class="w-8 h-8"></i>
            </div>
            <div class="text-left">
              <p class="text-sm text-gray-300">Halo,</p>
              <p class="text-2xl font-bold text-transparent bg-gradient-to-r from-green-300 to-blue-300 bg-clip-text">
                <?= htmlspecialchars($_SESSION['user']['name']) ?>! 🎉
              </p>
            </div>
          </div>
        </div>

      <?php else: ?>
        <div class="space-y-4 mt-8">
          <p class="text-gray-300 text-lg">Mulai berbelanja produk lokal terbaik Indonesia</p>

          <div class="flex gap-4 justify-center flex-wrap">
            <a href="<?= BASE_URL ?>/auth/login" class="btn-primary px-8 py-3 rounded-xl font-semibold flex items-center gap-2 hover:shadow-lg">
              <i data-lucide="log-in" class="w-5 h-5"></i>
              Masuk
            </a>
            <a href="<?= BASE_URL ?>/auth/register" class="glass-effect px-8 py-3 rounded-xl font-semibold hover:bg-white/20 transition flex items-center gap-2">
              <i data-lucide="user-plus" class="w-5 h-5"></i>
              Daftar Gratis
            </a>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
          <div class="text-center">
            <div class="bg-gradient-to-br from-blue-400/20 to-blue-500/20 p-4 rounded-2xl inline-block mb-3">
              <i data-lucide="shield-check" class="w-8 h-8"></i>
            </div>
            <h3 class="font-semibold mb-2">Aman & Terpercaya</h3>
            <p class="text-sm text-gray-300">Transaksi dijamin aman</p>
          </div>

          <div class="text-center">
            <div class="bg-gradient-to-br from-green-400/20 to-teal-500/20 p-4 rounded-2xl inline-block mb-3">
              <i data-lucide="truck" class="w-8 h-8"></i>
            </div>
            <h3 class="font-semibold mb-2">Pengiriman Cepat</h3>
            <p class="text-sm text-gray-300">Ke seluruh Indonesia</p>
          </div>

          <div class="text-center">
            <div class="bg-gradient-to-br from-blue-400/20 to-blue-600/20 p-4 rounded-2xl inline-block mb-3">
              <i data-lucide="heart-handshake" class="w-8 h-8"></i>
            </div>
            <h3 class="font-semibold mb-2">Dukung UMKM</h3>
            <p class="text-sm text-gray-300">Produk lokal berkualitas</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>