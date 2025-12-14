<div class="flex items-center justify-center min-h-[60vh]">
  <div class="glass-effect rounded-3xl p-8 max-w-md w-full text-center">
    <div class="bg-gradient-to-br from-green-400/20 to-blue-400/20 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
      <i data-lucide="check-circle" class="w-16 h-16 text-green-400"></i>
    </div>

    <h1 class="text-3xl font-bold mb-3">Pembayaran Selesai!</h1>
    <p class="text-gray-300 mb-8">Terima kasih telah melakukan pembayaran.</p>

    <div class="space-y-3">
      <a href="<?= BASE_URL ?>/pos"
        class="btn-primary w-full py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:shadow-lg transition">
        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
        Transaksi Baru
      </a>

      <a href="<?= BASE_URL ?>/home"
        class="block w-full py-3 rounded-xl font-medium border border-white/20 hover:bg-white/5 transition">
        Kembali ke Home
      </a>
    </div>
  </div>
</div>

<script>
  lucide.createIcons();
</script>