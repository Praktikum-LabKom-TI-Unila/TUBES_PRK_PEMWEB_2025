# LokaPOS: Sistem Point of Sale untuk Penjualan UMKM

## Daftar Anggota
| No | Nama                                   | NPM         |
|----|----------------------------------------|-------------|
| 1  | Muhammad Zachrie Kurniawan             | 2315061113  |
| 2  | Aleea Carisa Danielle Kalalo           | 2315061023  |
| 3  | Anggun Azqiyah Azzahra                 | 2355061005  |
| 4  | Lutfiya Fakhira                        | 2315061085  |
| 5  | Nur Aila Zahra                         | 2315061035  |

---

## Judul Proyek  
**LokaPOS: Sistem Point of Sale untuk Penjualan UMKM**

---

## Ringkasan Proyek  
LokaPOS adalah aplikasi Point of Sale yang berfokus pada kebutuhan penjual UMKM dalam mengelola produk dan transaksi penjualan secara lebih cepat, akurat, dan modern. Sistem ini juga memberikan pengalaman belanja digital sederhana bagi pembeli.

### Fitur Utama:
- Login & Registrasi
- Kelola Produk (Tambah, Edit, Hapus)
- Transaksi Penjualan (Kasir)
- Keranjang Belanja & Checkout
- Riwayat Transaksi
- Dashboard Penjualan dengan Grafik
- Laporan Penjualan dengan Filter
- Sistem Bantuan Terintegrasi (FAQ, Panduan Seller, Panduan Admin)

---

## Petunjuk Penggunaan Aplikasi

### Akun Demo untuk Testing

Sistem telah menyediakan dua jenis akun demo yang dapat digunakan untuk mencoba fitur-fitur aplikasi:

| Role    | Email             | Password  | Akses                        |
|---------|-------------------|-----------|------------------------------|
| Seller  | seller@gmail.com  | seller123 | Kasir & Transaksi            |
| Admin   | admin@gmail.com   | admin123  | Full Access + Laporan        |

---

## Role & Hak Akses

| Role   | Akses Fitur |
|--------|-------------|
| **Seller** | - Daftar Menu (POS)<br>- Transaksi & Checkout<br>- Cetak Struk<br>- Bantuan |
| **Admin**  | - Semua akses Seller<br>- Kelola Produk (Tambah, Edit, Hapus)<br>- Dashboard Admin dengan Grafik<br>- Laporan Penjualan<br>- Print/Export Laporan<br>- Panduan Admin |

---

## Panduan Penggunaan untuk Seller/Kasir

### 1. Login ke Aplikasi

1. Buka aplikasi di browser
2. Klik menu **"Login"** di navigasi atas
3. Masukkan kredensial seller:
   - Email: `seller@gmail.com`
   - Password: `seller123`
4. Klik tombol **"Masuk"**
5. Sistem akan mengarahkan ke halaman Kasir (POS)

### 2. Melakukan Transaksi Penjualan

#### A. Menambah Produk ke Keranjang
1. Setelah login, halaman **Kasir** akan menampilkan daftar produk yang tersedia
2. Klik kartu produk yang ingin dijual
3. Produk akan otomatis masuk ke keranjang belanja di sebelah kanan
4. Klik produk yang sama untuk menambah jumlah (quantity)
5. Total belanja akan otomatis ter-update

#### B. Mengatur Jumlah Produk di Keranjang
- Klik tombol **"+"** untuk menambah jumlah item
- Klik tombol **"-"** untuk mengurangi jumlah item
- Jika jumlah dikurangi hingga 0, produk otomatis terhapus dari keranjang

#### C. Proses Checkout
1. Pastikan semua produk yang akan dibeli sudah ada di keranjang
2. Periksa total pembayaran di bagian bawah keranjang
3. Klik tombol **"Bayar Sekarang"**
4. Sistem akan menampilkan konfirmasi pembayaran
5. Klik **"OK"** untuk melanjutkan
6. Setelah berhasil, sistem akan:
   - Mengurangi stok produk otomatis
   - Menyimpan data transaksi ke database
   - Menampilkan halaman struk digital

#### D. Mencetak Struk
1. Setelah checkout berhasil, halaman struk akan muncul secara otomatis
2. Dialog print browser akan terbuka
3. Pilih salah satu opsi:
   - **Print**: Untuk mencetak langsung ke printer
   - **Save as PDF**: Untuk menyimpan struk dalam format PDF
4. Setelah selesai, klik **"Kembali ke Kasir"** untuk memulai transaksi baru

### 3. Mengakses Menu Bantuan
1. Klik menu **"Bantuan"** di navigasi
2. Pilih:
   - **Panduan Penjual**: Tutorial lengkap untuk seller
   - **FAQ**: Pertanyaan yang sering ditanyakan
3. Ikuti panduan sesuai kebutuhan

### 4. Logout
1. Klik menu **"Logout"** di navigasi
2. Sistem akan mengarahkan kembali ke halaman login

---

## Panduan Penggunaan untuk Admin

### 1. Login sebagai Admin

1. Akses halaman login
2. Masukkan kredensial admin:
   - Email: `admin@gmail.com`
   - Password: `admin123`
3. Klik **"Masuk"**
4. Admin akan diarahkan ke halaman Kasir dengan akses menu tambahan

### 2. Mengelola Produk

Admin memiliki akses penuh untuk mengelola data produk melalui menu **Kelola Produk**.

#### A. Melihat Daftar Produk
1. Klik menu **"Daftar Menu"** di navigasi
2. Halaman akan menampilkan daftar semua produk dalam bentuk tabel
3. Informasi yang ditampilkan: ID, Nama, Harga, Stok, Gambar, dan Aksi

#### B. Menambah Produk Baru
1. Di halaman Daftar Menu, klik tombol **"Tambah Produk"** di pojok kanan atas
2. Isi form dengan lengkap:
   - **Nama Produk**: Masukkan nama produk (contoh: "Roti Bakar Coklat")
   - **Harga**: Masukkan harga dalam Rupiah tanpa titik/koma (contoh: 15000)
   - **Stok**: Masukkan jumlah stok awal (contoh: 50)
   - **Gambar**: Upload foto produk (opsional)
     - Format yang didukung: JPG, PNG, GIF, WEBP
     - Ukuran maksimal: 5MB
     - Preview gambar akan muncul setelah file dipilih
3. Klik tombol **"Simpan Produk"**
4. Jika berhasil, akan muncul notifikasi sukses dan produk akan tampil di daftar

#### C. Mengedit Produk
1. Di halaman Daftar Menu, cari produk yang ingin diubah
2. Klik tombol **"Edit"** pada baris produk tersebut
3. Form edit akan muncul dengan data produk yang sudah terisi
4. Ubah data yang diperlukan:
   - Nama, harga, atau stok dapat diubah langsung
   - Untuk mengganti gambar, pilih file baru (kosongkan jika tidak ingin mengganti)
5. Klik tombol **"Update Produk"**
6. Sistem akan menyimpan perubahan

#### D. Menghapus Produk
1. Di halaman Daftar Menu, cari produk yang ingin dihapus
2. Klik tombol **"Hapus"** pada baris produk tersebut
3. Sistem akan meminta konfirmasi: "Yakin ingin menghapus produk [nama]?"
4. Klik **"OK"** untuk melanjutkan penghapusan
5. Produk dan gambarnya akan terhapus permanen dari database

**Catatan Penting**: Penghapusan produk bersifat permanent dan tidak dapat dibatalkan. Pastikan produk yang dihapus memang sudah tidak digunakan.

### 3. Dashboard Admin

Dashboard Admin menyediakan visualisasi statistik penjualan secara real-time dan komprehensif.

#### Cara Mengakses:
1. Klik menu **"Dashboard Admin"** di navigasi
2. Atau akses langsung: `http://localhost/.../src/report/dashboard`

#### Informasi yang Ditampilkan:

**A. Statistik Penjualan (Cards)**
- **Hari Ini**:
  - Jumlah transaksi hari ini
  - Total pendapatan hari ini
- **Bulan Ini**:
  - Jumlah transaksi bulan berjalan
  - Total pendapatan bulan berjalan
- **Tahun Ini**:
  - Jumlah transaksi tahun berjalan
  - Total pendapatan tahun berjalan

**B. Grafik Penjualan**
- **Grafik Harian (7 Hari Terakhir)**:
  - Tipe: Bar Chart
  - Menampilkan pendapatan per hari
  - Berguna untuk memantau trend penjualan mingguan
- **Grafik Bulanan (6 Bulan Terakhir)**:
  - Tipe: Line Chart
  - Menampilkan pendapatan per bulan
  - Berguna untuk analisis trend jangka panjang

**C. Top 5 Produk Terlaris**
- Ranking produk berdasarkan jumlah terjual
- Menampilkan total pendapatan per produk
- Membantu mengidentifikasi produk unggulan

**D. Transaksi Terbaru**
- Menampilkan 10 transaksi terakhir
- Informasi: ID transaksi, nama kasir, tanggal & waktu, total pembayaran
- Berguna untuk monitoring transaksi real-time

### 4. Laporan Penjualan

Fitur laporan memungkinkan admin untuk menganalisis penjualan dengan filter yang fleksibel.

#### A. Mengakses Halaman Laporan
1. Klik menu **"Laporan"** di navigasi
2. Atau akses: `http://localhost/.../src/report/index`

#### B. Filter Laporan
1. Pada bagian **Filter Laporan**, atur parameter:
   - **Tanggal Mulai**: Pilih tanggal awal periode
   - **Tanggal Akhir**: Pilih tanggal akhir periode
   - **Produk**: Pilih produk tertentu atau "Semua Produk"
2. Klik tombol **"Filter"**
3. Laporan akan otomatis ter-refresh sesuai filter

#### C. Ringkasan Laporan
Setelah filter diterapkan, sistem akan menampilkan ringkasan:
- **Total Transaksi**: Jumlah transaksi dalam periode
- **Total Item Terjual**: Jumlah produk yang terjual
- **Total Pendapatan**: Total uang masuk dalam periode

#### D. Tabel Detail Transaksi
Menampilkan daftar lengkap transaksi dengan informasi:
- ID Transaksi
- Tanggal dan Waktu
- Nama Kasir
- Produk yang dibeli (dengan quantity)
- Total Pembayaran

#### E. Print/Export Laporan
1. Setelah filter sesuai kebutuhan, klik tombol **"Print Laporan"**
2. Browser akan membuka tab baru dengan preview laporan
3. Laporan sudah diformat untuk cetak dengan header dan footer
4. Pilih salah satu:
   - **Print**: Cetak langsung ke printer (Ctrl+P)
   - **Save as PDF**: Simpan dalam format PDF
5. Format laporan mencakup:
   - Header dengan logo dan nama aplikasi
   - Periode laporan dan tanggal cetak
   - Summary cards
   - Tabel detail transaksi
   - Footer dengan copyright

### 5. Mengakses Panduan Admin
1. Klik menu **"Bantuan"** di navigasi
2. Pilih **"Panduan Admin"**
3. Panduan mencakup:
   - Cara menggunakan Dashboard Admin
   - Tutorial mengelola produk
   - Cara membuat dan mencetak laporan
   - Best practices untuk admin


---

**© 2025 LokaPOS - Kelompok 34 | Universitas Lampung**