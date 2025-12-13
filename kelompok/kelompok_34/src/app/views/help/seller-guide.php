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
      <div class="bg-gradient-to-br from-green-400/30 to-teal-500/30 p-4 rounded-xl">
        <i data-lucide="shopping-cart" class="w-10 h-10"></i>
      </div>
      <div>
        <h1 class="text-3xl font-bold">Panduan Penjual/Kasir</h1>
        <p class="text-gray-200">Cara menggunakan sistem kasir dengan mudah</p>
      </div>
    </div>
  </div>

  <!-- Step 1: Login -->
  <div class="glass-effect p-8 rounded-2xl mb-6">
    <div class="flex items-start gap-4">
      <div class="bg-blue-500/30 rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0 font-bold text-xl">
        1
      </div>
      <div class="flex-1">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <i data-lucide="log-in" class="w-6 h-6"></i>
          Login ke Sistem
        </h2>
        <div class="bg-white/10 p-6 rounded-xl mb-4">
          <h4 class="font-semibold mb-3 flex items-center gap-2">
            <i data-lucide="user" class="w-5 h-5"></i>
            Langkah-langkah:
          </h4>
          <ol class="space-y-3 text-gray-200">
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">1</span>
              <span>Buka halaman login di browser: <code class="bg-black/30 px-2 py-1 rounded"><?= BASE_URL ?>/auth/login</code></span>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">2</span>
              <span>Masukkan <strong>Email</strong> dan <strong>Password</strong> Anda</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">3</span>
              <span>Klik tombol <strong>"Masuk"</strong></span>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">4</span>
              <span>Anda akan diarahkan ke halaman kasir</span>
            </li>
          </ol>
        </div>

        <div class="bg-yellow-500/20 border border-yellow-400/40 p-4 rounded-xl flex items-start gap-3">
          <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-300 flex-shrink-0 mt-0.5"></i>
          <div>
            <p class="font-semibold text-yellow-200 mb-1">Akun Demo untuk Testing:</p>
            <p class="text-sm text-gray-200">Email: <code class="bg-black/30 px-2 py-1 rounded text-yellow-200">seller@gmail.com</code></p>
            <p class="text-sm text-gray-200">Password: <code class="bg-black/30 px-2 py-1 rounded text-yellow-200">seller123</code></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Step 2: Pilih Produk -->
  <div class="glass-effect p-8 rounded-2xl mb-6">
    <div class="flex items-start gap-4">
      <div class="bg-blue-500/30 rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0 font-bold text-xl">
        2
      </div>
      <div class="flex-1">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <i data-lucide="package" class="w-6 h-6"></i>
          Memilih Produk
        </h2>
        
        <div class="bg-white/10 p-6 rounded-xl mb-4">
          <p class="text-gray-200 mb-4">Di halaman kasir, Anda akan melihat daftar produk yang tersedia. Berikut cara menambahkan produk ke keranjang:</p>
          
          <ol class="space-y-3 text-gray-200">
            <li class="flex items-start gap-3">
              <span class="bg-green-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">✓</span>
              <span>Klik pada <strong>kartu produk</strong> yang ingin dibeli</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-green-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">✓</span>
              <span>Produk akan otomatis masuk ke <strong>keranjang belanja</strong> di sebelah kanan</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-green-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">✓</span>
              <span>Klik lagi untuk menambah <strong>jumlah (qty)</strong></span>
            </li>
          </ol>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="bg-white/10 p-4 rounded-xl">
            <div class="flex items-center gap-2 mb-2">
              <i data-lucide="eye" class="w-5 h-5 text-blue-300"></i>
              <h4 class="font-semibold">Informasi Produk</h4>
            </div>
            <ul class="text-sm text-gray-200 space-y-1">
              <li>• Nama produk</li>
              <li>• Harga satuan</li>
              <li>• Stok tersedia</li>
              <li>• Gambar produk</li>
            </ul>
          </div>

          <div class="bg-white/10 p-4 rounded-xl">
            <div class="flex items-center gap-2 mb-2">
              <i data-lucide="info" class="w-5 h-5 text-yellow-300"></i>
              <h4 class="font-semibold">Catatan Penting</h4>
            </div>
            <ul class="text-sm text-gray-200 space-y-1">
              <li>• Stok 0 tidak bisa dibeli</li>
              <li>• Periksa harga sebelum checkout</li>
              <li>• Gambar hanya ilustrasi</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Step 3: Kelola Keranjang -->
  <div class="glass-effect p-8 rounded-2xl mb-6">
    <div class="flex items-start gap-4">
      <div class="bg-blue-500/30 rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0 font-bold text-xl">
        3
      </div>
      <div class="flex-1">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <i data-lucide="shopping-bag" class="w-6 h-6"></i>
          Mengelola Keranjang
        </h2>
        
        <div class="bg-white/10 p-6 rounded-xl mb-4">
          <h4 class="font-semibold mb-3">Di dalam keranjang, Anda bisa:</h4>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white/10 p-4 rounded-xl text-center">
              <div class="bg-green-500/30 p-3 rounded-xl inline-block mb-3">
                <i data-lucide="plus-circle" class="w-8 h-8"></i>
              </div>
              <h5 class="font-semibold mb-2">Tambah Qty</h5>
              <p class="text-sm text-gray-200">Klik tombol <strong>+</strong> untuk menambah jumlah</p>
            </div>

            <div class="bg-white/10 p-4 rounded-xl text-center">
              <div class="bg-red-500/30 p-3 rounded-xl inline-block mb-3">
                <i data-lucide="minus-circle" class="w-8 h-8"></i>
              </div>
              <h5 class="font-semibold mb-2">Kurangi Qty</h5>
              <p class="text-sm text-gray-200">Klik tombol <strong>-</strong> untuk mengurangi jumlah</p>
            </div>

            <div class="bg-white/10 p-4 rounded-xl text-center">
              <div class="bg-yellow-500/30 p-3 rounded-xl inline-block mb-3">
                <i data-lucide="trash-2" class="w-8 h-8"></i>
              </div>
              <h5 class="font-semibold mb-2">Hapus Item</h5>
              <p class="text-sm text-gray-200">Kurangi qty hingga <strong>0</strong> untuk menghapus</p>
            </div>
          </div>
        </div>

        <div class="bg-blue-500/20 border border-blue-400/40 p-4 rounded-xl flex items-start gap-3">
          <i data-lucide="info" class="w-5 h-5 text-blue-300 flex-shrink-0 mt-0.5"></i>
          <div class="text-sm text-gray-200">
            <p class="mb-2"><strong>Total Belanja</strong> akan otomatis dihitung di bagian bawah keranjang.</p>
            <p>Format: <code class="bg-black/30 px-2 py-1 rounded">Rp 50.000</code></p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Step 4: Checkout -->
  <div class="glass-effect p-8 rounded-2xl mb-6">
    <div class="flex items-start gap-4">
      <div class="bg-blue-500/30 rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0 font-bold text-xl">
        4
      </div>
      <div class="flex-1">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <i data-lucide="credit-card" class="w-6 h-6"></i>
          Proses Pembayaran (Checkout)
        </h2>
        
        <div class="bg-white/10 p-6 rounded-xl mb-4">
          <ol class="space-y-4 text-gray-200">
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">1</span>
              <div>
                <p class="font-semibold mb-1">Periksa Keranjang</p>
                <p class="text-sm">Pastikan semua produk dan jumlahnya sudah benar</p>
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">2</span>
              <div>
                <p class="font-semibold mb-1">Cek Total Pembayaran</p>
                <p class="text-sm">Lihat total di bagian bawah keranjang</p>
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">3</span>
              <div>
                <p class="font-semibold mb-1">Klik "Bayar Sekarang"</p>
                <p class="text-sm">Tombol biru besar di bawah keranjang</p>
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">4</span>
              <div>
                <p class="font-semibold mb-1">Konfirmasi Pembayaran</p>
                <p class="text-sm">Akan muncul popup konfirmasi, klik <strong>"OK"</strong></p>
              </div>
            </li>
            <li class="flex items-start gap-3">
              <span class="bg-blue-500/50 rounded-full w-6 h-6 flex items-center justify-center text-sm flex-shrink-0">5</span>
              <div>
                <p class="font-semibold mb-1">Transaksi Berhasil!</p>
                <p class="text-sm">Sistem akan otomatis mengarahkan ke halaman struk</p>
              </div>
            </li>
          </ol>
        </div>

        <div class="bg-green-500/20 border border-green-400/40 p-4 rounded-xl flex items-start gap-3">
          <i data-lucide="check-circle" class="w-5 h-5 text-green-300 flex-shrink-0 mt-0.5"></i>
          <div class="text-sm text-gray-200">
            <p class="font-semibold mb-1">Setelah checkout berhasil:</p>
            <ul class="space-y-1">
              <li>✓ Keranjang akan otomatis kosong</li>
              <li>✓ Stok produk berkurang otomatis</li>
              <li>✓ Data transaksi tersimpan di database</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Step 5: Cetak Struk -->
  <div class="glass-effect p-8 rounded-2xl mb-6">
    <div class="flex items-start gap-4">
      <div class="bg-blue-500/30 rounded-full w-12 h-12 flex items-center justify-center flex-shrink-0 font-bold text-xl">
        5
      </div>
      <div class="flex-1">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <i data-lucide="printer" class="w-6 h-6"></i>
          Mencetak Struk Belanja
        </h2>
        
        <div class="bg-white/10 p-6 rounded-xl mb-4">
          <p class="text-gray-200 mb-4">Setelah checkout berhasil, sistem otomatis membuka halaman struk yang berisi:</p>
          
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="bg-white/10 p-4 rounded-xl">
              <h5 class="font-semibold mb-2 flex items-center gap-2">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                Informasi Struk
              </h5>
              <ul class="text-sm text-gray-200 space-y-1">
                <li>• Nomor Struk/ID Transaksi</li>
                <li>• Nama Kasir</li>
                <li>• Tanggal & Waktu</li>
                <li>• Alamat Toko</li>
              </ul>
            </div>

            <div class="bg-white/10 p-4 rounded-xl">
              <h5 class="font-semibold mb-2 flex items-center gap-2">
                <i data-lucide="list" class="w-4 h-4"></i>
                Detail Pembelian
              </h5>
              <ul class="text-sm text-gray-200 space-y-1">
                <li>• Daftar produk dibeli</li>
                <li>• Jumlah per item</li>
                <li>• Harga per item</li>
                <li>• <strong>Total Pembayaran</strong></li>
              </ul>
            </div>
          </div>

          <div class="bg-blue-500/20 border border-blue-400/40 p-4 rounded-xl">
            <h5 class="font-semibold mb-2">Cara Mencetak:</h5>
            <ol class="text-sm text-gray-200 space-y-2">
              <li>1. Struk akan otomatis tampil di tab baru</li>
              <li>2. Tekan <kbd class="bg-black/30 px-2 py-1 rounded">Ctrl + P</kbd> (Windows) atau <kbd class="bg-black/30 px-2 py-1 rounded">Cmd + P</kbd> (Mac)</li>
              <li>3. Pilih printer atau <strong>Save as PDF</strong></li>
              <li>4. Klik <strong>"Print"</strong></li>
            </ol>
          </div>
        </div>

        <a href="<?= BASE_URL ?>" class="btn-primary px-6 py-3 rounded-xl inline-flex items-center gap-2 hover:shadow-xl transition">
          <i data-lucide="arrow-left" class="w-5 h-5"></i>
          Kembali ke Kasir
        </a>
      </div>
    </div>
  </div>

  <!-- Troubleshooting -->
  <div class="glass-effect p-8 rounded-2xl">
    <h2 class="text-2xl font-bold mb-6 flex items-center gap-2">
      <i data-lucide="alert-circle" class="w-6 h-6"></i>
      Troubleshooting (Mengatasi Masalah)
    </h2>

    <div class="space-y-4">
      <div class="bg-white/10 p-5 rounded-xl">
        <h4 class="font-semibold mb-2 flex items-center gap-2">
          <i data-lucide="x-circle" class="w-5 h-5 text-red-300"></i>
          Gagal Login
        </h4>
        <p class="text-sm text-gray-200 mb-2"><strong>Penyebab:</strong> Email/Password salah</p>
        <p class="text-sm text-gray-200"><strong>Solusi:</strong> Cek kembali email & password, atau hubungi admin untuk reset password</p>
      </div>

      <div class="bg-white/10 p-5 rounded-xl">
        <h4 class="font-semibold mb-2 flex items-center gap-2">
          <i data-lucide="package-x" class="w-5 h-5 text-yellow-300"></i>
          Produk Tidak Bisa Ditambah ke Keranjang
        </h4>
        <p class="text-sm text-gray-200 mb-2"><strong>Penyebab:</strong> Stok habis (0)</p>
        <p class="text-sm text-gray-200"><strong>Solusi:</strong> Hubungi admin untuk restok produk</p>
      </div>

      <div class="bg-white/10 p-5 rounded-xl">
        <h4 class="font-semibold mb-2 flex items-center gap-2">
          <i data-lucide="wifi-off" class="w-5 h-5 text-red-300"></i>
          Checkout Gagal
        </h4>
        <p class="text-sm text-gray-200 mb-2"><strong>Penyebab:</strong> Koneksi internet terputus</p>
        <p class="text-sm text-gray-200"><strong>Solusi:</strong> Cek koneksi internet, refresh halaman, dan coba lagi</p>
      </div>
    </div>
  </div>

</div>