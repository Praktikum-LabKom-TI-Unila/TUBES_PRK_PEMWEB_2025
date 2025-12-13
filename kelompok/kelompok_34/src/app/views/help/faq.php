<?php
?>

<div class="max-w-4xl mx-auto">
  <!-- Header -->
  <div class="glass-effect p-6 rounded-2xl mb-6">
    <a href="<?= BASE_URL ?>/help" class="inline-flex items-center gap-2 text-blue-200 hover:text-white mb-4 transition">
      <i data-lucide="arrow-left" class="w-4 h-4"></i>
      Kembali ke Pusat Bantuan
    </a>
    <div class="flex items-center gap-4">
      <div class="bg-gradient-to-br from-yellow-400/30 to-orange-500/30 p-4 rounded-xl">
        <i data-lucide="message-circle-question" class="w-10 h-10"></i>
      </div>
      <div>
        <h1 class="text-3xl font-bold">FAQ - Pertanyaan Umum</h1>
        <p class="text-gray-200">Temukan jawaban atas pertanyaan yang sering diajukan</p>
      </div>
    </div>
  </div>

  <!-- Search FAQ (Optional Enhancement) -->
  <div class="glass-effect p-4 rounded-2xl mb-6">
    <div class="relative">
      <input type="text"
        id="searchFAQ"
        placeholder="🔍 Cari pertanyaan..."
        class="input-modern w-full px-4 py-3 rounded-xl text-white placeholder-gray-100 outline-none"
        oninput="searchFAQs(this.value)">
    </div>
  </div>

  <!-- FAQ Categories -->
  <div class="space-y-4" id="faqContainer">

    <!-- Umum -->
    <div class="glass-effect rounded-2xl overflow-hidden faq-item" data-category="umum">
      <button onclick="toggleFAQ(this)" class="w-full p-6 text-left flex items-center justify-between hover:bg-white/10 transition">
        <div class="flex items-center gap-3">
          <div class="bg-blue-500/30 p-2 rounded-lg">
            <i data-lucide="help-circle" class="w-5 h-5"></i>
          </div>
          <h3 class="font-bold text-lg">Apa itu LokaPOS?</h3>
        </div>
        <i data-lucide="chevron-down" class="w-5 h-5 transition-transform"></i>
      </button>
      <div class="faq-content hidden px-6 pb-6">
        <p class="text-gray-200 leading-relaxed">
          LokaPOS adalah aplikasi Point of Sale (Kasir) yang dirancang khusus untuk membantu UMKM dalam mengelola transaksi penjualan dengan lebih efisien. Aplikasi ini menyediakan fitur lengkap seperti manajemen produk, transaksi kasir, laporan penjualan, dan dashboard statistik.
        </p>
      </div>
    </div>

    <div class="glass-effect rounded-2xl overflow-hidden faq-item" data-category="umum">
      <button onclick="toggleFAQ(this)" class="w-full p-6 text-left flex items-center justify-between hover:bg-white/10 transition">
        <div class="flex items-center gap-3">
          <div class="bg-green-500/30 p-2 rounded-lg">
            <i data-lucide="users" class="w-5 h-5"></i>
          </div>
          <h3 class="font-bold text-lg">Siapa yang bisa menggunakan LokaPOS?</h3>
        </div>
        <i data-lucide="chevron-down" class="w-5 h-5 transition-transform"></i>
      </button>
      <div class="faq-content hidden px-6 pb-6">
        <p class="text-gray-200 leading-relaxed mb-3">
          LokaPOS dapat digunakan oleh:
        </p>
        <ul class="text-gray-200 space-y-2 ml-4">
          <li class="flex items-start gap-2">
            <i data-lucide="check" class="w-5 h-5 text-green-300 flex-shrink-0 mt-0.5"></i>
            <span><strong>Penjual/Kasir:</strong> Untuk melayani transaksi pelanggan</span>
          </li>
          <li class="flex items-start gap-2">
            <i data-lucide="check" class="w-5 h-5 text-blue-300 flex-shrink-0 mt-0.5"></i>
            <span><strong>Admin/Pemilik Usaha:</strong> Untuk mengelola produk dan melihat laporan</span>
          </li>
        </ul>
      </div>
    </div>

    <!-- Login & Akun -->
    <div class="glass-effect rounded-2xl overflow-hidden faq-item" data-category="akun">
      <button onclick="toggleFAQ(this)" class="w-full p-6 text-left flex items-center justify-between hover:bg-white/10 transition">
        <div class="flex items-center gap-3">
          <div class="bg-red-500/30 p-2 rounded-lg">
            <i data-lucide="key" class="w-5 h-5"></i>
          </div>
          <h3 class="font-bold text-lg">Lupa password, bagaimana cara reset?</h3>
        </div>
        <i data-lucide="chevron-down" class="w-5 h-5 transition-transform"></i>
      </button>
      <div class="faq-content hidden px-6 pb-6">
        <p class="text-gray-200 leading-relaxed mb-3">
          Untuk reset password, silakan hubungi administrator sistem atau pemilik usaha. Mereka dapat mereset password Anda melalui database atau memberikan password baru.
        </p>
        <div class="bg-yellow-500/20 border border-yellow-400/40 p-4 rounded-xl">
          <p class="text-sm text-gray-200">
            <strong>Tips Keamanan:</strong> Gunakan password yang kuat (minimal 8 karakter dengan kombinasi huruf dan angka) dan jangan share password Anda dengan siapapun.
          </p>
        </div>
      </div>
    </div>

    <div class="glass-effect rounded-2xl overflow-hidden faq-item" data-category="akun">
      <button onclick="toggleFAQ(this)" class="w-full p-6 text-left flex items-center justify-between hover:bg-white/10 transition">
        <div class="flex items-center gap-3">
          <div class="bg-blue-500/30 p-2 rounded-lg">
            <i data-lucide="user-x" class="w-5 h-5"></i>
          </div>
          <h3 class="font-bold text-lg">Akun saya terkunci, apa yang harus dilakukan?</h3>
        </div>
        <i data-lucide="chevron-down" class="w-5 h-5 transition-transform"></i>
      </button>
      <div class="faq-content hidden px-6 pb-6">
        <p class="text-gray-200 leading-relaxed">
          Akun mungkin terkunci karena beberapa kali gagal login. Hubungi administrator untuk membuka kembali akses Anda. Pastikan Anda memasukkan email dan password dengan benar saat login.
        </p>
      </div>
    </div>

    <!-- Transaksi -->
    <div class="glass-effect rounded-2xl overflow-hidden faq-item" data-category="transaksi">
      <button onclick="toggleFAQ(this)" class="w-full p-6 text-left flex items-center justify-between hover:bg-white/10 transition">
        <div class="flex items-center gap-3">
          <div class="bg-purple-500/30 p-2 rounded-lg">
            <i data-lucide="shopping-cart" class="w-5 h-5"></i>
          </div>
          <h3 class="font-bold text-lg">Bagaimana cara membatalkan transaksi yang salah?</h3>
        </div>
        <i data-lucide="chevron-down" class="w-5 h-5 transition-transform"></i>
      </button>
      <div class="faq-content hidden px-6 pb-6">
        <p class="text-gray-200 leading-relaxed mb-3">
          Sebelum checkout:
        </p>
        <ul class="text-gray-200 space-y-2 ml-4 mb-4">
          <li>• Klik tombol <strong>"-"</strong> di keranjang untuk mengurangi qty</li>
          <li>• Kurangi qty hingga 0 untuk menghapus item dari keranjang</li>
        </ul>
        <p class="text-gray-200 leading-relaxed mb-3">
          Setelah checkout (transaksi sudah tersimpan):
        </p>
        <div class="bg-red-500/20 border border-red-400/40 p-4 rounded-xl">
          <p class="text-sm text-gray-200">
            <strong>⚠️ Perhatian:</strong> Transaksi yang sudah selesai tidak bisa dibatalkan melalui sistem. Hubungi admin untuk penanganan lebih lanjut.
          </p>
        </div>
      </div>
    </div>

    <div class="glass-effect rounded-2xl overflow-hidden faq-item" data-category="transaksi">
      <button onclick="toggleFAQ(this)" class="w-full p-6 text-left flex items-center justify-between hover:bg-white/10 transition">
        <div class="flex items-center gap-3">
          <div class="bg-yellow-500/30 p-2 rounded-lg">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
          </div>
          <h3 class="font-bold text-lg">Stok produk habis, apa yang harus dilakukan?</h3>
        </div>
        <i data-lucide="chevron-down" class="w-5 h-5 transition-transform"></i>
      </button>
      <div class="faq-content hidden px-6 pb-6">
        <p class="text-gray-200 leading-relaxed mb-3">
          Jika stok produk habis (menampilkan "Stok: 0"), produk tidak bisa ditambahkan ke keranjang. Solusinya:
        </p>
        <ol class="text-gray-200 space-y-2 ml-4">
          <li>1. Hubungi admin untuk restok produk</li>
          <li>2. Admin akan update stok melalui menu <strong>"Kelola Produk"</strong></li>
          <li>3. Setelah stok diupdate, produk bisa dijual kembali</li>
        </ol>
      </div>
    </div>

    <div class="glass-effect rounded-2xl overflow-hidden faq-item" data-category="transaksi">
      <button onclick="toggleFAQ(this)" class="w-full p-6 text-left flex items-center justify-between hover:bg-white/10 transition">
        <div class="flex items-center gap-3">
          <div class="bg-green-500/30 p-2 rounded-lg">
            <i data-lucide="printer" class="w-5 h-5"></i>
          </div>
          <h3 class="font-bold text-lg">Apakah struk bisa dicetak ulang?</h3>
        </div>
        <i data-lucide="chevron-down" class="w-5 h-5 transition-transform"></i>
      </button>
      <div class="faq-content hidden px-6 pb-6">
        <p class="text-gray-200 leading-relaxed mb-3">
          Saat ini, struk hanya bisa dicetak langsung setelah checkout berhasil. Untuk cetak ulang, Anda bisa:
        </p>
        <ul class="text-gray-200 space-y-2 ml-4">
          <li>• Simpan struk dalam format PDF saat pertama kali checkout</li>
          <li>• Hubungi admin untuk melihat detail transaksi di laporan penjualan</li>
        </ul>
      </div>
    </div>

    <!-- Produk (Admin) -->
    <div class="glass-effect rounded-2xl overflow-hidden faq-item" data-category="produk">
      <button onclick="toggleFAQ(this)" class="w-full p-6 text-left flex items-center justify-between hover:bg-white/10 transition">
        <div class="flex items-center gap-3">
          <div class="bg-blue-500/30 p-2 rounded-lg">
            <i data-lucide="image" class="w-5 h-5"></i>
          </div>
          <h3 class="font-bold text-lg">Gambar produk gagal diupload, kenapa?</h3>
        </div>
        <i data-lucide="chevron-down" class="w-5 h-5 transition-transform"></i>
      </button>
      <div class="faq-content hidden px-6 pb-6">
        <p class="text-gray-200 leading-relaxed mb-3">
          Penyebab umum gambar gagal diupload:
        </p>
        <ul class="text-gray-200 space-y-2 ml-4 mb-4">
          <li class="flex items-start gap-2">
            <i data-lucide="x-circle" class="w-5 h-5 text-red-300 flex-shrink-0 mt-0.5"></i>
            <span><strong>Ukuran file terlalu besar:</strong> Maksimal 2MB</span>
          </li>
          <li class="flex items-start gap-2">
            <i data-lucide="x-circle" class="w-5 h-5 text-red-300 flex-shrink-0 mt-0.5"></i>
            <span><strong>Format tidak didukung:</strong> Gunakan JPG, PNG, GIF, atau WEBP</span>
          </li>
          <li class="flex items-start gap-2">
            <i data-lucide="x-circle" class="w-5 h-5 text-red-300 flex-shrink-0 mt-0.5"></i>
            <span><strong>Koneksi internet lambat:</strong> Coba lagi dengan koneksi yang lebih stabil</span>
          </li>
        </ul>
        <div class="bg-blue-500/20 border border-blue-400/40 p-4 rounded-xl">
          <p class="text-sm text-gray-200">
            <strong>Tips:</strong> Kompres gambar sebelum upload menggunakan tools online seperti TinyPNG atau Squoosh untuk mengurangi ukuran file.
          </p>
        </div>
      </div>
    </div>

    <div class="glass-effect rounded-2xl overflow-hidden faq-item" data-category="produk">
      <button onclick="toggleFAQ(this)" class="w-full p-6 text-left flex items-center justify-between hover:bg-white/10 transition">
        <div class="flex items-center gap-3">
          <div class="bg-red-500/30 p-2 rounded-lg">
            <i data-lucide="trash-2" class="w-5 h-5"></i>
          </div>
          <h3 class="font-bold text-lg">Produk yang sudah dihapus bisa dikembalikan?</h3>
        </div>
        <i data-lucide="chevron-down" class="w-5 h-5 transition-transform"></i>
      </button>
      <div class="faq-content hidden px-6 pb-6">
        <p class="text-gray-200 leading-relaxed mb-3">
          <strong>Tidak bisa.</strong> Penghapusan produk bersifat permanen (permanent delete). Data produk dan gambarnya akan terhapus dari database dan tidak bisa dikembalikan.
        </p>
        <div class="bg-red-500/20 border border-red-400/40 p-4 rounded-xl">
          <p class="text-sm text-gray-200">
            <strong>⚠️ Rekomendasi:</strong> Selalu periksa dua kali sebelum menghapus produk. Jika ragu, lebih baik set stok menjadi 0 daripada menghapus produk.
          </p>
        </div>
      </div>
    </div>

    <!-- Laporan -->
    <div class="glass-effect rounded-2xl overflow-hidden faq-item" data-category="laporan">
      <button onclick="toggleFAQ(this)" class="w-full p-6 text-left flex items-center justify-between hover:bg-white/10 transition">
        <div class="flex items-center gap-3">
          <div class="bg-green-500/30 p-2 rounded-lg">
            <i data-lucide="file-text" class="w-5 h-5"></i>
          </div>
          <h3 class="font-bold text-lg">Bagaimana cara melihat laporan penjualan bulanan?</h3>
        </div>
        <i data-lucide="chevron-down" class="w-5 h-5 transition-transform"></i>
      </button>
      <div class="faq-content hidden px-6 pb-6">
        <ol class="text-gray-200 space-y-3">
          <li class="flex items-start gap-3">
            <span class="bg-green-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">1</span>
            <span>Login sebagai <strong>Admin</strong></span>
          </li>
          <li class="flex items-start gap-3">
            <span class="bg-green-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">2</span>
            <span>Klik menu <strong>"Laporan"</strong></span>
          </li>
          <li class="flex items-start gap-3">
            <span class="bg-green-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">3</span>
            <span>Atur <strong>Tanggal Mulai</strong> ke tanggal 1 bulan tersebut</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="bg-green-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">4</span>
            <span>Atur <strong>Tanggal Akhir</strong> ke tanggal terakhir bulan tersebut</span>
          </li>
          <li class="flex items-start gap-3">
            <span class="bg-green-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">5</span>
            <span>Klik <strong>"Filter"</strong></span>
          </li>
        </ol>
      </div>
    </div>

    <!-- Teknis -->
    <div class="glass-effect rounded-2xl overflow-hidden faq-item" data-category="teknis">
      <button onclick="toggleFAQ(this)" class="w-full p-6 text-left flex items-center justify-between hover:bg-white/10 transition">
        <div class="flex items-center gap-3">
          <div class="bg-yellow-500/30 p-2 rounded-lg">
            <i data-lucide="wifi-off" class="w-5 h-5"></i>
          </div>
          <h3 class="font-bold text-lg">Aplikasi tidak bisa diakses, apa penyebabnya?</h3>
        </div>
        <i data-lucide="chevron-down" class="w-5 h-5 transition-transform"></i>
      </button>
      <div class="faq-content hidden px-6 pb-6">
        <p class="text-gray-200 leading-relaxed mb-3">
          Beberapa kemungkinan penyebab:
        </p>
        <ul class="text-gray-200 space-y-3 ml-4">
          <li class="flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5"></i>
            <div>
              <p class="font-semibold mb-1">Server tidak aktif</p>
              <p class="text-sm">Pastikan XAMPP/Laragon Apache dan MySQL sudah running</p>
            </div>
          </li>
          <li class="flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5"></i>
            <div>
              <p class="font-semibold mb-1">URL salah</p>
              <p class="text-sm">Cek kembali URL yang digunakan, pastikan sesuai dengan BASE_URL di config</p>
            </div>
          </li>
          <li class="flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5"></i>
            <div>
              <p class="font-semibold mb-1">Koneksi database gagal</p>
              <p class="text-sm">Periksa konfigurasi database di file <code class="bg-black/30 px-2 py-1 rounded">config/database.php</code></p>
            </div>
          </li>
        </ul>
      </div>
    </div>

  </div>

  <!-- Contact Support -->
  <div class="glass-effect p-8 rounded-2xl mt-8 text-center">
    <div class="inline-block p-4 bg-gradient-to-br from-blue-400/30 to-blue-600/30 rounded-2xl mb-4">
      <i data-lucide="headphones" class="w-10 h-10"></i>
    </div>
    <h3 class="text-2xl font-bold mb-3">Tidak Menemukan Jawaban?</h3>
    <p class="text-gray-200 mb-6">Hubungi tim support kami untuk bantuan lebih lanjut</p>
    <div class="flex gap-4 justify-center flex-wrap">
      <button class="glass-effect px-6 py-3 rounded-xl hover:bg-white/20 transition flex items-center gap-2">
        <i data-lucide="mail" class="w-5 h-5"></i>
        support@lokapos.com
      </button>
      <button class="btn-primary px-6 py-3 rounded-xl flex items-center gap-2 hover:shadow-xl transition">
        <i data-lucide="message-circle" class="w-5 h-5"></i>
        Live Chat
      </button>
    </div>
  </div>

</div>

<script>
  function toggleFAQ(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('[data-lucide="chevron-down"]');

    // Close all other FAQs
    document.querySelectorAll('.faq-content').forEach(item => {
      if (item !== content && !item.classList.contains('hidden')) {
        item.classList.add('hidden');
        const otherIcon = item.previousElementSibling.querySelector('[data-lucide="chevron-down"]');
        if (otherIcon) otherIcon.style.transform = 'rotate(0deg)';
      }
    });

    // Toggle current FAQ
    content.classList.toggle('hidden');

    // Rotate icon with smooth transition
    if (content.classList.contains('hidden')) {
      icon.style.transform = 'rotate(0deg)';
    } else {
      icon.style.transform = 'rotate(180deg)';
    }

    // Reinitialize Lucide icons
    lucide.createIcons();
  }

  function searchFAQs(keyword) {
    const items = document.querySelectorAll('.faq-item');
    const lowerKeyword = keyword.toLowerCase();
    let visibleCount = 0;

    items.forEach(item => {
      const title = item.querySelector('h3').textContent.toLowerCase();
      const content = item.querySelector('.faq-content').textContent.toLowerCase();

      if (title.includes(lowerKeyword) || content.includes(lowerKeyword)) {
        item.style.display = 'block';
        visibleCount++;
      } else {
        item.style.display = 'none';
      }
    });

    // If no keyword, show all
    if (keyword === '') {
      items.forEach(item => {
        item.style.display = 'block';
      });
    }
  }
</script>