</div>

<footer class="w-full mt-16 relative z-10">
  <div class="container mx-auto px-4">
    <div class="glass-effect rounded-t-3xl py-8 px-8">

      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">

        <!-- Brand -->
        <div>
          <div class="flex items-center gap-2 mb-4">
            <div class="bg-gradient-to-br from-blue-400 to-blue-600 p-2 rounded-xl">
              <i data-lucide="shopping-bag" class="w-5 h-5"></i>
            </div>
            <span class="font-bold text-lg bg-gradient-to-r from-blue-100 to-blue-200 bg-clip-text text-transparent">
              <?= APP_NAME ?>
            </span>
          </div>
          <p class="text-sm text-gray-100">
            Point of Sale terpercaya untuk produk lokal Indonesia
          </p>
        </div>

        <!-- Links -->
        <div>
          <h3 class="font-semibold mb-4 text-blue-200">Produk</h3>
          <ul class="space-y-2 text-sm text-gray-100">
            <li><a href="#" class="hover:text-white transition">Kategori</a></li>
            <li><a href="#" class="hover:text-white transition">Promo</a></li>
            <li><a href="#" class="hover:text-white transition">Terlaris</a></li>
            <li><a href="#" class="hover:text-white transition">Terbaru</a></li>
          </ul>
        </div>

        <div>
          <h3 class="font-semibold mb-4 text-blue-200">Perusahaan</h3>
          <ul class="space-y-2 text-sm text-gray-100">
            <li><a href="#" class="hover:text-white transition">Tentang Kami</a></li>
            <li><a href="#" class="hover:text-white transition">Karir</a></li>
            <li><a href="#" class="hover:text-white transition">Blog</a></li>
            <li><a href="#" class="hover:text-white transition">Kontak</a></li>
          </ul>
        </div>

        <div>
          <h3 class="font-semibold mb-4 text-blue-200">Bantuan</h3>
          <ul class="space-y-2 text-sm text-gray-100">
            <li><a href="#" class="hover:text-white transition">FAQ</a></li>
            <li><a href="#" class="hover:text-white transition">Syarat & Ketentuan</a></li>
            <li><a href="#" class="hover:text-white transition">Kebijakan Privasi</a></li>
            <li><a href="#" class="hover:text-white transition">Pusat Bantuan</a></li>
          </ul>
        </div>

      </div>

      <!-- Social Media -->
      <div class="flex justify-center gap-4 mb-6 pt-6 border-t border-white/20">
        <a href="#" class="glass-effect p-3 rounded-xl hover:bg-white/20 transition">
          <i data-lucide="facebook" class="w-5 h-5"></i>
        </a>
        <a href="#" class="glass-effect p-3 rounded-xl hover:bg-white/20 transition">
          <i data-lucide="twitter" class="w-5 h-5"></i>
        </a>
        <a href="#" class="glass-effect p-3 rounded-xl hover:bg-white/20 transition">
          <i data-lucide="instagram" class="w-5 h-5"></i>
        </a>
        <a href="#" class="glass-effect p-3 rounded-xl hover:bg-white/20 transition">
          <i data-lucide="youtube" class="w-5 h-5"></i>
        </a>
      </div>

      <!-- Copyright -->
      <div class="text-center text-sm text-gray-100">
        <p>&copy; <?= date("Y") ?> <?= APP_NAME ?>. Semua hak dilindungi.</p>
        <p class="mt-1">Dibuat dengan <i data-lucide="heart" class="w-4 h-4 inline text-red-400"></i> di Indonesia</p>
      </div>

    </div>
  </div>
</footer>

<script src="<?= BASE_URL ?>/public/assets/js/main.js"></script>
</body>

</html>