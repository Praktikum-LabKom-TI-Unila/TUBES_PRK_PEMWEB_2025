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

## Cara Menjalankan Aplikasi

### Persyaratan Sistem
- PHP minimal versi 8.2.4 atau lebih tinggi
- MySQL/MariaDB
- Web Server (Apache/Nginx), disarankan menggunakan **XAMPP/Laragon**
- Browser Web modern (Chrome, Firefox, Edge)

### Langkah Instalasi

#### 1. Clone atau Download Repository
```bash
git clone https://github.com/zachriek/TUBES_PRK_PEMWEB_2025.git
```

Atau download file ZIP dan ekstrak ke folder lokal Anda.

#### 2. Pindahkan Project ke Direktori Web Server
- **Jika menggunakan XAMPP:**
  ```
  C:\xampp\htdocs\TUBES_PRK_PEMWEB_2025\kelompok\kelompok_34\src
  ```
- **Jika menggunakan Laragon:**
  ```
  C:\laragon\www\TUBES_PRK_PEMWEB_2025\kelompok\kelompok_34\src
  ```

#### 3. Buat Database
1. Buka **phpMyAdmin** melalui browser:
   ```
   http://localhost/phpmyadmin
   ```
2. Klik **"New"** untuk membuat database baru
3. Beri nama database: `lokapos`
4. Pilih collation: `utf8mb4_general_ci`
5. Klik **"Create"**

#### 4. Import Database
1. Setelah database dibuat, klik nama database `lokapos` di panel kiri
2. Klik tab **"Import"** di menu atas
3. Klik **"Choose File"** dan pilih file:
   ```
   TUBES_PRK_PEMWEB_2025/kelompok/kelompok_34/database/lokapos.sql
   ```
4. Scroll ke bawah dan klik **"Import"**
5. Tunggu hingga muncul pesan sukses

#### 5. Konfigurasi Koneksi Database
Buka file konfigurasi database di:
```
src/app/config/database.php
```

Pastikan pengaturan sesuai dengan konfigurasi MySQL Anda:
```php
$host = '127.0.0.1';      // Host database
$name = 'lokapos';         // Nama database
$user = 'root';            // Username MySQL (default: root)
$pass = '';                // Password MySQL (default: kosong)
```

Jika MySQL Anda menggunakan username atau password yang berbeda, sesuaikan nilai `$user` dan `$pass`.

#### 6. Jalankan Aplikasi
1. Pastikan Apache dan MySQL sudah berjalan di XAMPP/Laragon
2. Buka browser dan akses:
   ```
   http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_34/src/
   ```
3. Anda akan diarahkan ke halaman home

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

## Screenshot Aplikasi

### 1. Halaman Home
<p align="center">
  <img src="screenshots/start.png" alt="Halaman Awal" width="80%"/>
  <br>
  <em>Tampilan Awal aplikasi LokaPOS</em>
</p>

### 2. Login & Registrasi
<p align="center">
  <img src="screenshots/login.png" alt="Halaman Login" width="45%"/>
  <img src="screenshots/register.png" alt="Halaman Registrasi" width="45%"/>
  <br>
  <em>Halaman Login dan Registrasi</em>
</p>

### 3. Kasir (Point of Sale)
<p align="center">
  <img src="screenshots/pos-kasir.png" alt="Tampilan Kasir" width="80%"/>
  <br>
  <em>Interface kasir untuk melakukan transaksi penjualan</em>
</p>

### 4. Proses Checkout
<p align="center">
  <img src="screenshots/checkout.png" alt="Proses Checkout" width="80%"/>
  <br>
  <em>Keranjang belanja dan proses checkout</em>
</p>

### 5. Struk Pembayaran
<p align="center">
  <img src="screenshots/print-nota.png" alt="Struk Pembayaran" width="60%"/>
  <img src="screenshots/transaksi-selesai.png" alt="Transaksi Selesai" width="60%"/>
  <br>
  <em>Struk digital yang dapat dicetak</em>
</p>

### 6. Kelola Produk (Admin)
<p align="center">
  <img src="screenshots/daftar-produk.png" alt="Daftar Produk" width="80%"/>
  <br>
  <em>Halaman manajemen produk untuk admin</em>
</p>

### 7. Tambah/Edit Produk
<p align="center">
  <img src="screenshots/tambah-produk.png" alt="Tambah Produk" width="45%"/>
  <br>
  <em>Form tambah dan edit produk</em>
</p>

### 8. Dashboard Admin
<p align="center">
  <img src="screenshots/dashboard-admin.png" alt="Dashboard Admin" width="80%"/>
  <br>
  <em>Dashboard dengan statistik dan grafik penjualan</em>
</p>

### 9. Grafik Penjualan
<p align="center">
  <img src="screenshots/grafik.png" alt="Grafik Harian" width="45%"/>
  <br>
  <em>Grafik penjualan harian dan bulanan</em>
</p>

### 10. Laporan Penjualan
<p align="center">
  <img src="screenshots/laporan-penjualan.png" alt="Laporan Penjualan" width="80%"/>
  <br>
  <em>Halaman laporan dengan filter periode dan produk</em>
</p>

### 11. Print Laporan
<p align="center">
  <img src="screenshots/print-laporan.png" alt="Print Laporan" width="70%"/>
  <br>
  <em>Preview laporan siap cetak/export PDF</em>
</p>

### 12. Sistem Bantuan
<p align="center">
  <img src="screenshots/bantuan.png" alt="bantuan" width="45%"/>
  <img src="screenshots/panduan-seller.png" alt="Panduan Seller" width="45%"/>
  <img src="screenshots/FAQ.png" alt="FAQ" width="45%"/>
  <br>
  <em>FAQ dan Panduan untuk Seller</em>
</p>

<p align="center">
  <img src="screenshots/panduan-admin.png" alt="Panduan Admin" width="80%"/>
  <br>
  <em>Panduan lengkap untuk Admin</em>
</p>

---

## Troubleshooting

### Masalah Login

**Problem**: Tidak bisa login / Email atau Password salah

**Solusi**:
1. Pastikan database sudah di-import dengan benar
2. Gunakan akun demo yang disediakan:
   - Seller: `seller@gmail.com` / `seller123`
   - Admin: `admin@gmail.com` / `admin123`
3. Periksa tidak ada spasi di awal atau akhir email/password
4. Cek koneksi database di `src/app/config/database.php`

---

### Masalah Database

**Problem**: Database connection failed

**Solusi**:
1. Pastikan MySQL sudah running di XAMPP/Laragon
2. Verifikasi kredensial database di `src/app/config/database.php`:
   - Host: `127.0.0.1`
   - Database: `lokapos`
   - Username: `root` (default)
   - Password: kosong (default)
3. Cek apakah database `lokapos` sudah dibuat
4. Pastikan port MySQL adalah 3306

---

### Masalah Gambar Produk

**Problem**: Gambar produk tidak muncul / Error 404 saat mengakses gambar

**Solusi**:
1. Pastikan folder `src/public/uploads/products/` ada
2. Cek permission folder (chmod 777 di Linux/Mac)
3. Verifikasi BASE_URL di `src/app/config/config.php` sudah benar
4. Cek apakah file gambar benar-benar ter-upload di folder tersebut
5. Refresh browser dengan Ctrl+F5

**Problem**: Error saat upload gambar

**Solusi**:
1. Cek ukuran file (maksimal 5MB)
2. Pastikan format file didukung (JPG, PNG, GIF, WEBP)
3. Cek permission folder `uploads/products/` harus writable
4. Periksa setting PHP:
   - `php.ini`: `upload_max_filesize = 5M`
   - `php.ini`: `post_max_size = 5M`
5. Restart Apache setelah mengubah php.ini

---

### Masalah Transaksi

**Problem**: Stok tidak berkurang setelah checkout

**Solusi**:
1. Cek koneksi database
2. Buka Console Browser (F12) dan periksa error
3. Verifikasi fungsi `reduceStock()` di `app/models/Product.php` berjalan
4. Cek tabel `products` di database apakah stok ter-update

**Problem**: Struk tidak muncul setelah checkout

**Solusi**:
1. Pastikan pop-up blocker browser tidak aktif
2. Allow pop-up untuk localhost di browser settings
3. Coba browser lain (Chrome/Firefox recommended)
4. Cek Console Browser untuk error JavaScript

**Problem**: Produk dengan stok 0 masih bisa ditambah ke keranjang

**Solusi**:
1. Refresh halaman kasir (F5)
2. Cek fungsi JavaScript `addToCart()` di `app/views/pos/index.php`
3. Pastikan validasi stok berjalan dengan benar

---

### Masalah Dashboard & Laporan

**Problem**: Dashboard tidak menampilkan data / Grafik kosong

**Solusi**:
1. Pastikan sudah ada transaksi di database
2. Cek tabel `transactions` dan `transaction_details` di phpMyAdmin
3. Refresh halaman dengan Ctrl+F5
4. Buka Console Browser (F12) untuk cek error
5. Pastikan Chart.js ter-load (cek tab Network di Console)

**Problem**: Grafik tidak muncul

**Solusi**:
1. Pastikan koneksi internet aktif (Chart.js dari CDN)
2. Cek Console Browser untuk error loading Chart.js
3. Verifikasi data dari `app/models/Report.php` ter-return dengan benar
4. Inspect element pada canvas grafik

**Problem**: Print laporan tidak berfungsi

**Solusi**:
1. Pastikan pop-up tidak diblokir
2. Coba tekan Ctrl+P manual di halaman preview
3. Gunakan fitur "Save as PDF" sebagai alternatif
4. Cek file `app/views/reports/print.php` ter-load dengan benar

---

### Masalah Routing & URL

**Problem**: Error 404 Not Found pada semua halaman

**Solusi**:
1. Pastikan file `.htaccess` ada di folder `src/`
2. Enable mod_rewrite di Apache:
   - XAMPP: Edit `httpd.conf`, uncomment `LoadModule rewrite_module`
   - Restart Apache
3. Verifikasi BASE_URL di `src/app/config/config.php` sesuai struktur folder
4. Cek file `src/app/core/App.php` untuk routing logic

**Problem**: CSS/JavaScript tidak ter-load

**Solusi**:
1. Cek koneksi internet (Tailwind dan Lucide dari CDN)
2. Verifikasi URL CDN di `src/app/views/layouts/header.php`
3. Buka Console Browser tab Network untuk cek failed requests
4. Clear browser cache (Ctrl+Shift+Del)

---

### Masalah Upload & File

**Problem**: Permission denied saat upload gambar

**Solusi Linux/Mac**:
```bash
chmod -R 777 src/public/uploads/
```

**Solusi Windows**:
1. Klik kanan folder `uploads`
2. Properties > Security
3. Edit > Add > Everyone > Full Control

---

## Tips Penggunaan

### Untuk Seller:
- Selalu cek stok produk sebelum melakukan transaksi
- Pastikan jumlah item di keranjang sudah benar sebelum checkout
- Cetak struk sebagai bukti pembayaran untuk pelanggan
- Logout setelah selesai shift untuk keamanan
- Hubungi admin jika stok produk habis

### Untuk Admin:
- Rutin update stok produk agar tidak kehabisan
- Pantau Dashboard Admin setiap hari untuk monitoring penjualan
- Backup database secara berkala (export dari phpMyAdmin)
- Export laporan penjualan bulanan untuk dokumentasi
- Upload gambar produk berkualitas tinggi untuk tampilan menarik
- Hapus produk yang sudah tidak dijual untuk menjaga data tetap relevan
- Gunakan filter laporan untuk analisis penjualan per produk

---

## Struktur Folder Project
```
kelompok_34/
│
├── database/
│   └── lokapos.sql           # File SQL untuk import
│
├── screenshots/              # Folder untuk screenshot aplikasi
│   ├── bantuan.png
│   ├── checkout.png
│   ├── daftar-produk.png
│   ├── dashboard-admin.png
│   ├── faq.png
│   ├── grafik.png
│   ├── laporan-penjualan.png
│   ├── login.png
│   ├── panduan-admin.png
│   ├── panduan-kasir.png
│   ├── pos-kasir.png
│   ├── print-laporan.png
│   ├── print-nota.png
│   ├── register.png
│   ├── start.png
│   ├── tambah-produk.png
│   └── transaksi-selesai.png
│
└── src/
    ├── .htaccess             # URL rewriting
    ├── index.php             # Entry point
    │
    ├── app/
    │   ├── config/
    │   │   ├── config.php         # BASE_URL & APP_NAME
    │   │   └── database.php       # Database connection
    │   │
    │   ├── controllers/
    │   │   ├── AuthController.php
    │   │   ├── HelpController.php
    │   │   ├── HomeController.php
    │   │   ├── PosController.php
    │   │   ├── ProductController.php
    │   │   └── ReportController.php
    │   │
    │   ├── core/
    │   │   ├── App.php         # Routing
    │   │   ├── Controller.php  # Base controller
    │   │   └── Model.php       # Base model
    │   │
    │   ├── helpers/
    │   │   ├── auth_helper.php
    │   │   └── product_helper.php
    │   │
    │   ├── models/
    │   │   ├── Product.php
    │   │   ├── Report.php
    │   │   ├── Transaction.php
    │   │   └── User.php
    │   │
    │   └── views/
    │       ├── layouts/
    │       ├── assets/
    │       ├── auth/
    │       ├── home/
    │       ├── pos/
    │       ├── products/
    │       ├── reports/
    │       ├── help/
    │       └── errors/
    │
    └── public/
        ├── .htaccess
        └── uploads/
            └── products/
```
---

**© 2025 LokaPOS - Kelompok 34 | Universitas Lampung**