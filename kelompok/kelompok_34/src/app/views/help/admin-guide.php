<?php
?>

<div class="max-w-5xl mx-auto">
  <!-- Header -->
  <div class="glass-effect p-6 rounded-2xl mb-6">
    <a href="<?= BASE_URL ?>/help" class="inline-flex items-center gap-2 text-blue-200 hover:text-white mb-4 transition">
      <i data-lucide="arrow-left" class="w-4 h-4"></i>
      Kembali ke Pusat Bantuan
    </a>
    <div class="flex items-center gap-4">
      <div class="bg-gradient-to-br from-blue-400/30 to-blue-600/30 p-4 rounded-xl">
        <i data-lucide="shield-check" class="w-10 h-10"></i>
      </div>
      <div>
        <h1 class="text-3xl font-bold">Panduan Admin</h1>
        <p class="text-gray-200">Kelola produk, laporan, dan dashboard statistik</p>
      </div>
    </div>
  </div>

  <!-- Dashboard Admin -->
  <div class="glass-effect p-8 rounded-2xl mb-6">
    <div class="flex items-start gap-4">
      <div class="bg-blue-500/30 rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0 font-bold text-xl">
        1
      </div>
      <div class="flex-1">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <i data-lucide="layout-dashboard" class="w-6 h-6"></i>
          Dashboard Admin
        </h2>
        
        <div class="bg-white/10 p-6 rounded-xl mb-4">
          <p class="text-gray-200 mb-4">Dashboard Admin menampilkan statistik penjualan real-time. Akses melalui:</p>
          <div class="bg-black/30 p-3 rounded-lg mb-4">
            <code class="text-blue-300">Menu → Dashboard Admin</code>
          </div>

          <h4 class="font-semibold mb-3">Informasi yang Tersedia:</h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white/10 p-4 rounded-xl">
              <div class="flex items-center gap-2 mb-2">
                <i data-lucide="calendar-days" class="w-5 h-5 text-green-300"></i>
                <h5 class="font-semibold">Statistik Hari Ini</h5>
              </div>
              <ul class="text-sm text-gray-200 space-y-1">
                <li>• Jumlah transaksi hari ini</li>
                <li>• Total pendapatan hari ini</li>
              </ul>
            </div>

            <div class="bg-white/10 p-4 rounded-xl">
              <div class="flex items-center gap-2 mb-2">
                <i data-lucide="calendar" class="w-5 h-5 text-yellow-300"></i>
                <h5 class="font-semibold">Statistik Bulan Ini</h5>
              </div>
              <ul class="text-sm text-gray-200 space-y-1">
                <li>• Total transaksi bulan ini</li>
                <li>• Total pendapatan bulan ini</li>
              </ul>
            </div>

            <div class="bg-white/10 p-4 rounded-xl">
              <div class="flex items-center gap-2 mb-2">
                <i data-lucide="trophy" class="w-5 h-5 text-blue-300"></i>
                <h5 class="font-semibold">Top 5 Produk Terlaris</h5>
              </div>
              <ul class="text-sm text-gray-200 space-y-1">
                <li>• Produk paling banyak terjual</li>
                <li>• Total penjualan per produk</li>
              </ul>
            </div>

            <div class="bg-white/10 p-4 rounded-xl">
              <div class="flex items-center gap-2 mb-2">
                <i data-lucide="bar-chart" class="w-5 h-5 text-purple-300"></i>
                <h5 class="font-semibold">Grafik Penjualan</h5>
              </div>
              <ul class="text-sm text-gray-200 space-y-1">
                <li>• Grafik harian (7 hari terakhir)</li>
                <li>• Grafik bulanan (6 bulan terakhir)</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="bg-blue-500/20 border border-blue-400/40 p-4 rounded-xl flex items-start gap-3">
          <i data-lucide="info" class="w-5 h-5 text-blue-300 flex-shrink-0 mt-0.5"></i>
          <div class="text-sm text-gray-200">
            <p><strong>Tips:</strong> Gunakan dashboard untuk memantau performa penjualan secara real-time dan membuat keputusan bisnis yang lebih baik.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Kelola Produk -->
  <div class="glass-effect p-8 rounded-2xl mb-6">
    <div class="flex items-start gap-4">
      <div class="bg-blue-500/30 rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0 font-bold text-xl">
        2
      </div>
      <div class="flex-1">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <i data-lucide="package" class="w-6 h-6"></i>
          Mengelola Produk
        </h2>
        
        <!-- Lihat Daftar Produk -->
        <div class="bg-white/10 p-6 rounded-xl mb-4">
          <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
            <i data-lucide="list" class="w-5 h-5"></i>
            Melihat Daftar Produk
          </h3>
          <ol class="text-gray-200 space-y-2 mb-4">
            <li>1. Klik menu <strong>"Daftar Menu"</strong> di navbar</li>
            <li>2. Scroll ke bawah untuk melihat semua produk</li>
            <li>3. Di halaman ini Anda bisa melihat: ID, Nama, Harga, Stok, dan Gambar produk</li>
          </ol>
        </div>

        <!-- Tambah Produk -->
        <div class="bg-white/10 p-6 rounded-xl mb-4">
          <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
            <i data-lucide="plus-circle" class="w-5 h-5 text-green-300"></i>
            Menambah Produk Baru
          </h3>
          <ol class="text-gray-200 space-y-3">
            <li class="flex items-start gap-3">
              <span class="bg-green-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">1</span>
              <span>Klik tombol <strong>"Tambah Produk"</strong> di halaman Kelola Produk</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-green-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">2</span>
              <span>Isi form dengan data produk:
                <ul class="mt-2 ml-4 space-y-1 text-sm">
                  <li>• <strong>Nama Produk:</strong> Contoh "Roti Bakar Coklat"</li>
                  <li>• <strong>Harga:</strong> Contoh "15000" (tanpa titik/koma)</li>
                  <li>• <strong>Stok:</strong> Contoh "50"</li>
                  <li>• <strong>Gambar:</strong> Upload foto produk (opsional)</li>
                </ul>
              </span>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-green-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">3</span>
              <span>Klik <strong>"Simpan Produk"</strong></span>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-green-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">4</span>
              <span>Produk akan muncul di daftar dan kasir</span>
            </li>
          </ol>

          <div class="mt-4 bg-yellow-500/20 border border-yellow-400/40 p-3 rounded-lg">
            <p class="text-sm text-gray-200"><strong>Catatan:</strong> Format gambar yang didukung: JPG, PNG, GIF, WEBP (Max 2MB)</p>
          </div>
        </div>

        <!-- Edit Produk -->
        <div class="bg-white/10 p-6 rounded-xl mb-4">
          <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
            <i data-lucide="edit" class="w-5 h-5 text-blue-300"></i>
            Mengedit Produk
          </h3>
          <ol class="text-gray-200 space-y-3">
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">1</span>
              <span>Klik tombol <strong>"Edit"</strong> pada produk yang ingin diubah</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">2</span>
              <span>Ubah data yang diperlukan (nama, harga, stok, gambar)</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">3</span>
              <span>Klik <strong>"Update Produk"</strong></span>
            </li>
          </ol>

          <div class="mt-4 bg-blue-500/20 border border-blue-400/40 p-3 rounded-lg">
            <p class="text-sm text-gray-200"><strong>Tips:</strong> Jika tidak ingin mengganti gambar, biarkan field gambar kosong.</p>
          </div>
        </div>

        <!-- Hapus Produk -->
        <div class="bg-white/10 p-6 rounded-xl">
          <h3 class="font-bold text-lg mb-3 flex items-center gap-2">
            <i data-lucide="trash-2" class="w-5 h-5 text-red-300"></i>
            Menghapus Produk
          </h3>
          <ol class="text-gray-200 space-y-3">
            <li class="flex items-start gap-3">
              <span class="bg-red-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">1</span>
              <span>Klik tombol <strong>"Hapus"</strong> pada produk yang ingin dihapus</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-red-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">2</span>
              <span>Akan muncul konfirmasi, klik <strong>"OK"</strong> untuk melanjutkan</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-red-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">3</span>
              <span>Produk dan gambarnya akan terhapus permanen</span>
            </li>
          </ol>

          <div class="mt-4 bg-red-500/20 border border-red-400/40 p-3 rounded-lg">
            <p class="text-sm text-gray-200"><strong>⚠️ Peringatan:</strong> Penghapusan produk tidak bisa dibatalkan (permanent delete)!</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Laporan Penjualan -->
  <div class="glass-effect p-8 rounded-2xl mb-6">
    <div class="flex items-start gap-4">
      <div class="bg-blue-500/30 rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0 font-bold text-xl">
        3
      </div>
      <div class="flex-1">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <i data-lucide="file-text" class="w-6 h-6"></i>
          Laporan Penjualan
        </h2>
        
        <div class="bg-white/10 p-6 rounded-xl mb-4">
          <p class="text-gray-200 mb-4">Fitur laporan membantu Anda menganalisis penjualan berdasarkan periode waktu tertentu.</p>

          <h4 class="font-semibold mb-3">Cara Menggunakan Filter:</h4>
          <ol class="text-gray-200 space-y-3 mb-4">
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">1</span>
              <span>Klik menu <strong>"Laporan"</strong> di navbar</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">2</span>
              <span>Gunakan <strong>filter</strong> untuk menampilkan data:
                <ul class="mt-2 ml-4 space-y-1 text-sm">
                  <li>• <strong>Tanggal Mulai:</strong> Pilih tanggal awal periode</li>
                  <li>• <strong>Tanggal Akhir:</strong> Pilih tanggal akhir periode</li>
                  <li>• <strong>Produk:</strong> Filter berdasarkan produk tertentu (opsional)</li>
                </ul>
              </span>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">3</span>
              <span>Klik tombol <strong>"Filter"</strong></span>
            </li>
          </ol>

          <h4 class="font-semibold mb-3">Informasi yang Ditampilkan:</h4>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white/10 p-4 rounded-xl">
              <div class="flex items-center gap-2 mb-2">
                <i data-lucide="shopping-cart" class="w-5 h-5 text-green-300"></i>
                <h5 class="font-semibold">Total Transaksi</h5>
              </div>
              <p class="text-sm text-gray-200">Jumlah transaksi dalam periode</p>
            </div>

            <div class="bg-white/10 p-4 rounded-xl">
              <div class="flex items-center gap-2 mb-2">
                <i data-lucide="package" class="w-5 h-5 text-yellow-300"></i>
                <h5 class="font-semibold">Total Item Terjual</h5>
              </div>
              <p class="text-sm text-gray-200">Jumlah produk terjual</p>
            </div>

            <div class="bg-white/10 p-4 rounded-xl">
              <div class="flex items-center gap-2 mb-2">
                <i data-lucide="dollar-sign" class="w-5 h-5 text-blue-300"></i>
                <h5 class="font-semibold">Total Pendapatan</h5>
              </div>
              <p class="text-sm text-gray-200">Total uang yang masuk</p>
            </div>
          </div>
        </div>

        <!-- Print Laporan -->
        <div class="bg-white/10 p-6 rounded-xl">
          <h4 class="font-semibold mb-3 flex items-center gap-2">
            <i data-lucide="printer" class="w-5 h-5"></i>
            Mencetak Laporan
          </h4>
          <ol class="text-gray-200 space-y-2">
            <li>1. Setelah filter diterapkan, klik tombol <strong>"Print Laporan"</strong></li>
            <li>2. Halaman laporan akan terbuka di tab baru</li>
            <li>3. Tekan <kbd class="bg-black/30 px-2 py-1 rounded">Ctrl + P</kbd> atau klik tombol print di halaman</li>
            <li>4. Pilih printer atau <strong>"Save as PDF"</strong></li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Best Practices -->
  <div class="glass-effect p-8 rounded-2xl">
    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
      <i data-lucide="award" class="w-6 h-6"></i>
      Best Practices untuk Admin
    </h2>

    <div class="space-y-4">
      <div class="bg-white/10 p-5 rounded-xl flex items-start gap-4">
        <div class="bg-blue-500/30 p-2 rounded-lg flex-shrink-0">
          <i data-lucide="check-circle" class="w-6 h-6"></i>
        </div>
        <div>
          <h4 class="font-bold mb-2">Update Stok Secara Berkala</h4>
          <p class="text-sm text-gray-200">Cek dan update stok produk setiap hari untuk menghindari kekosongan stok saat transaksi.</p>
        </div>
      </div>

      <div class="bg-white/10 p-5 rounded-xl flex items-start gap-4">
        <div class="bg-green-500/30 p-2 rounded-lg flex-shrink-0">
          <i data-lucide="eye" class="w-6 h-6"></i>
        </div>
        <div>
          <h4 class="font-bold mb-2">Monitor Dashboard Setiap Hari</h4>
          <p class="text-sm text-gray-200">Pantau performa penjualan harian untuk mengidentifikasi trend dan membuat keputusan strategis.</p>
        </div>
      </div>

      <div class="bg-white/10 p-5 rounded-xl flex items-start gap-4">
        <div class="bg-yellow-500/30 p-2 rounded-lg flex-shrink-0">
          <i data-lucide="image" class="w-6 h-6"></i>
        </div>
        <div>
          <h4 class="font-bold mb-2">Upload Gambar Produk Berkualitas</h4>
          <p class="text-sm text-gray-200">Gambar yang jelas dan menarik meningkatkan pengalaman kasir dan pembeli.</p>
        </div>
      </div>

      <div class="bg-white/10 p-5 rounded-xl flex items-start gap-4">
        <div class="bg-red-500/30 p-2 rounded-lg flex-shrink-0">
          <i data-lucide="archive" class="w-6 h-6"></i>
        </div>
        <div>
          <h4 class="font-bold mb-2">Backup Laporan Secara Rutin</h4>
          <p class="text-sm text-gray-200">Download dan simpan laporan bulanan dalam format PDF untuk arsip.</p>
        </div>
      </div>

      <div class="bg-white/10 p-5 rounded-xl flex items-start gap-4">
        <div class="bg-purple-500/30 p-2 rounded-lg flex-shrink-0">
          <i data-lucide="shield" class="w-6 h-6"></i>
        </div>
        <div>
          <h4 class="font-bold mb-2">Jaga Keamanan Akun Admin</h4>
          <p class="text-sm text-gray-200">Jangan share password admin, dan selalu logout setelah selesai menggunakan sistem.</p>
        </div>
      </div>
    </div>
  </div>

</div>