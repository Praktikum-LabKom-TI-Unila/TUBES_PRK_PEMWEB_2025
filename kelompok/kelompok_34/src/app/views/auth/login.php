<div class="max-w-md mx-auto">
  <div class="glass-effect p-8 rounded-3xl shadow-2xl">
    <div class="text-center mb-8">
      <div class="inline-block p-3 bg-gradient-to-br from-blue-400 to-blue-500 rounded-2xl mb-4">
        <i data-lucide="shield-check" class="w-8 h-8"></i>
      </div>
      <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-300 to-blue-300 bg-clip-text text-transparent">
        Selamat Datang
      </h2>
      <p class="text-gray-100 text-sm mt-2">Masuk ke akun <?= APP_NAME ?> Anda</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="bg-red-500/20 text-red-200 p-4 mb-6 rounded-xl border border-red-400/40 text-sm flex items-start gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
        <span><?= $error ?></span>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
      <div class="bg-green-500/40 text-green-200 p-4 mb-6 rounded-xl border border-green-400/60 text-sm flex items-start gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
        <span><?= $_SESSION['success'] ?></span>
      </div>
    <?php unset($_SESSION['success']);
    endif; ?>

    <form method="POST" class="space-y-5">
      <div>
        <label class="block mb-2 font-semibold text-sm text-gray-200 flex items-center gap-2">
          <i data-lucide="mail" class="w-4 h-4"></i>
          Email
        </label>
        <input
          type="email"
          name="email"
          required
          placeholder="nama@email.com"
          class="input-modern w-full px-4 py-3 rounded-xl text-white placeholder-gray-200 outline-none">
      </div>

      <div>
        <label class="block mb-2 font-semibold text-sm text-gray-200 flex items-center gap-2">
          <i data-lucide="lock" class="w-4 h-4"></i>
          Password
        </label>
        <input
          type="password"
          name="password"
          required
          placeholder="Masukkan password"
          class="input-modern w-full px-4 py-3 rounded-xl text-white placeholder-gray-200 outline-none">
      </div>

      <button class="btn-primary w-full py-3 rounded-xl text-white font-semibold flex items-center justify-center gap-2 mt-6">
        <i data-lucide="log-in" class="w-5 h-5"></i>
        Masuk
      </button>

      <div class="relative my-6">
        <div class="relative flex justify-center text-sm">
          <span class="px-4 bg-transparent text-gray-100">atau</span>
        </div>
      </div>

      <p class="text-center text-sm text-gray-100">
        Belum punya akun?
        <a href="<?= BASE_URL ?>/auth/register" class="text-blue-100 hover:text-blue-200 font-semibold transition underline">
          Daftar sekarang
        </a>
      </p>
    </form>
  </div>
</div>