<div class="min-h-[70vh] flex items-center justify-center">
  <div class="text-center">
    <div class="glass-effect rounded-3xl p-12 inline-block shadow-2xl">

      <div class="relative mb-8">
        <h1 class="text-9xl font-bold bg-gradient-to-r from-blue-100 via-blue-400 to-blue-600 bg-clip-text text-transparent animate-pulse">
          404
        </h1>
        <div class="absolute inset-0 bg-gradient-to-r from-blue-100/20 via-blue-400/20 to-blue-600/20 blur-3xl -z-10"></div>
      </div>

      <div class="bg-gradient-to-br from-blue-100/20 to-blue-500/20 p-4 rounded-2xl inline-block mb-6">
        <i data-lucide="search-x" class="w-16 h-16"></i>
      </div>

      <h2 class="text-2xl font-bold mb-3">Halaman Tidak Ditemukan</h2>
      <p class="text-gray-300 text-lg mb-8 max-w-md mx-auto">
        Maaf, halaman yang Anda cari tidak dapat ditemukan atau mungkin telah dipindahkan.
      </p>

      <div class="flex gap-4 justify-center flex-wrap">
        <a href="<?= BASE_URL ?>/home" class="btn-primary px-6 py-3 rounded-xl font-semibold inline-flex items-center gap-2">
          <i data-lucide="home" class="w-5 h-5"></i>
          Kembali ke Beranda
        </a>

        <button onclick="history.back()" class="glass-effect px-6 py-3 rounded-xl font-semibold hover:bg-white/20 transition inline-flex items-center gap-2">
          <i data-lucide="arrow-left" class="w-5 h-5"></i>
          Halaman Sebelumnya
        </button>
      </div>

      <div class="mt-8 pt-8 border-t border-white/20">
        <p class="text-sm text-gray-100 mb-4">Mungkin Anda mencari:</p>
        <div class="flex gap-3 justify-center flex-wrap text-sm">
          <a href="<?= BASE_URL ?>" class="text-blue-300 hover:text-blue-200 underline">Home</a>
          <span class="text-gray-500">•</span>
          <a href="<?= BASE_URL ?>/auth/login" class="text-blue-300 hover:text-blue-200 underline">Login</a>
          <span class="text-gray-500">•</span>
          <a href="<?= BASE_URL ?>/auth/register" class="text-blue-300 hover:text-blue-200 underline">Register</a>
        </div>
      </div>

    </div>
  </div>
</div>