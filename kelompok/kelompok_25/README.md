# Inventory Bahan Baku – Kelompok 25
# Kelompok 25
- M. Sidiq Firdaus
- Muhammad Taufik Saputra
- Rima Dwi Puspitasari
- Agnes Anggraini

Inventori stok dan bahan baku adalah sistem informasi manajemen stok bahan baku berbasis web yang dibangun menggunakan PHP native dengan arsitektur MVC (Model-View-Controller). Sistem ini dirancang untuk membantu perusahaan mengelola inventori bahan baku secara efisien dengan fitur monitoring stok real-time, pelacakan transaksi, dan sistem peringatan otomatis untuk bahan yang hampir habis.

## Fungsi Utama Sistem

### 1. 📦 Manajemen Data Master
- **Bahan Baku (Materials)**
  - CRUD data bahan dengan informasi lengkap (kode, nama, kategori, satuan, harga, stok minimum)
  - Upload foto bahan
  - Monitoring stok aktual vs stok minimum
  - Filter dan pencarian data

- **Kategori (Categories)**
  - Pengelompokan bahan berdasarkan jenis (Tepung, Gula, Minyak, dll)
  - CRUD kategori dengan deskripsi

- **Supplier**
  - Manajemen data pemasok (nama, kontak, alamat)
  - Status aktif/non-aktif supplier
  - Riwayat transaksi dengan supplier

### 2. 📊 Transaksi Stok
- **Stok Masuk (Stock In)**
  - Pencatatan penerimaan/pembelian bahan baru
  - Input jumlah, harga beli, supplier, dan tanggal transaksi
  - Generate nomor referensi otomatis (SI-YYYYMMDD-XXX)
  - Update stok otomatis setelah transaksi

- **Stok Keluar (Stock Out)**
  - Pencatatan penggunaan bahan dengan berbagai tujuan:
    - Produksi (production)
    - Penjualan (sale)
    - Limbah (waste)
    - Transfer (transfer)
    - Lainnya (other)
  - Validasi stok tersedia sebelum pengeluaran
  - Generate nomor referensi otomatis (SO-YYYYMMDD-XXX)

- **Penyesuaian Stok (Stock Adjustments)**
  - Koreksi stok fisik vs stok sistem (stock opname)
  - Alasan penyesuaian: koreksi perhitungan, barang rusak, kadaluarsa, kehilangan, kesalahan sistem
  - Audit trail lengkap untuk setiap penyesuaian

### 3. 🔔 Monitoring & Peringatan
- **Dashboard Real-time**
  - Total bahan baku aktif
  - Total nilai stok (Rp)
  - Jumlah bahan low stock dan out of stock
  - Recent activities (10 aktivitas terakhir)
  - Widget aksi cepat ke fitur utama

- **Low Stock Alert**
  - Deteksi otomatis bahan dengan stok di bawah minimum
  - Kategorisasi: Urgent (stok = 0), Warning (stok < minimum)
  - Saran reorder quantity
  - Filter berdasarkan kategori dan status
  - Export laporan

### 4. 📈 Pelaporan
- **Laporan Stok**
  - Snapshot kondisi stok semua bahan
  - Nilai stok per bahan dan total
  - Status stok (Aman/Low Stock/Habis)
  - Export ke Excel

- **Laporan Transaksi**
  - Riwayat transaksi (masuk, keluar, penyesuaian)
  - Filter berdasarkan periode dan jenis transaksi
  - Visualisasi tren transaksi dengan chart
  - Export Excel dan CSV

### 5. 👥 Manajemen User & Hak Akses
- **Role-Based Access Control (RBAC)**
  - Role: Admin, Manager, Staff (dapat dikustomisasi)
  - Permission granular per fitur (view, create, edit, delete, export)
  - Flexible assignment: 1 user dapat memiliki multiple roles

- **User Management**
  - CRUD user dengan informasi profil lengkap
  - Upload avatar
  - Assign roles ke user
  - Status aktif/non-aktif

- **Profile Management**
  - User dapat update profil sendiri
  - Change password
  - View recent activities

- **Activity Logs**
  - Audit trail semua aktivitas user
  - Tracking: who, what, when, IP address

### 6. 🔐 Keamanan
- Password hashing (bcrypt)
- CSRF token protection
- SQL injection prevention (prepared statements)
- XSS protection
- Session security
- Permission-based authorization

## Struktur Folder

```
kelompok_25/
├─ public/                      # Hanya direktori ini yang diakses browser
│  ├─ index.php                 # Front controller (semua request masuk sini)
│  ├─ .htaccess                 # Rewrite ke index.php (untuk Apache)
│  └─ assets/
│     ├─ css/app.css            # Style global
│     ├─ js/app.js             # Script global
│     ├─ js/modules/           # Script per fitur (auth/materials/stock/reports)
│     ├─ img/                  # Static assets
│     └─ uploads/materials/    # Foto bahan hasil upload
│
├─ src/
│  ├─ config/                  # Konfigurasi environment & koneksi DB
│  ├─ core/                    # Router, Base Controller, Auth helper, dll
│  ├─ routes/                  # `web.php` (view) & `api.php` (JSON)
│  ├─ models/                  # User, Role, Material, Supplier, Stock, dll
│  ├─ controllers/
│  │  ├─ web/                  # Controller yang merender view
│  │  └─ api/                  # Controller untuk request AJAX/JSON
│  ├─ views/                   # Layout, partial, dashboard, materials, dsb.
│  ├─ middleware/              # AuthMiddleware & RoleMiddleware
│  └─ helpers/                 # Utility (redirect, csrf, validator)
│
├─ tailwind.config.js
├─ package.json
└─ README.md
```

## Alur Singkat
1. Request masuk ke `public/index.php` lalu diteruskan ke Router.
2. Router mencocokkan path dengan `routes/web.php` (atau `routes/api.php`).
3. Middleware auth/role dijalankan jika dibutuhkan.
4. Controller mempersiapkan data, memanggil view (`views/...`) melalui `layouts/main.php` sehingga navbar dan sidebar otomatis ikut.
5. Asset CSS/JS di `public/assets` menangani tampilan dan interaksi ringan.

## Setup Awal (Clone dari GitHub)

### Prasyarat
- PHP 8.x terpasang di mesin lokal
- MySQL/MariaDB server aktif
- Composer (opsional, untuk dependencies jika ada)

### Langkah Setup dari Awal

#### 1. Clone Repository
```bash
git clone <repository-url>
cd kelompok_25
```

#### 2. Konfigurasi Database
Buat file `src/config/config.php` dengan mengcopy dari template (jika ada) atau buat baru:
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'inventory_manager');
define('DB_USER', 'root');
define('DB_PASS', '');
define('ROOT_PATH', dirname(__DIR__));
```

#### 3. Import Database
1. Buat database baru di MySQL:
   ```sql
   CREATE DATABASE inventory_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Import schema dan data awal:
   ```bash
   mysql -u root -p inventory_manager < database.sql
   ```

   File `database.sql` sudah include:
   - ✅ Struktur tabel lengkap
   - ✅ User admin default (`admin@inventory.com` / `admin123`)
   - ✅ Roles & Permissions
   - ✅ Data sample (kategori & supplier)

3. **[OPSIONAL]** Jika ada kolom yang perlu ditambahkan setelah import:
   ```bash
   mysql -u root -p inventory_manager < add_destination_column.sql
   ```

#### 4. Verifikasi Permissions (OPSIONAL)
Script `setup_permissions.php` dan `create_admin.php` **TIDAK PERLU** dijalankan karena `database.sql` sudah include semuanya. 

**Hanya jalankan jika:**
- Lupa password admin atau login pertama gagal → jalankan `php create_admin.php` untuk reset atau ketika login awal gagal
- Permissions tidak lengkap → jalankan `php setup_permissions.php`

#### 5. Jalankan Server Development
```powershell
# Windows PowerShell
cd src
php -S localhost:8000 -t public

# Linux/Mac
cd src
php -S localhost:8000 -t public
```

#### 6. Akses Aplikasi
- URL: `http://localhost:8000`
- Login: `admin@inventory.com` / `admin123`
- Ganti password setelah login pertama!

### Troubleshooting

**Error koneksi database:**
- Cek kredensial di `src/config/config.php`
- Pastikan MySQL service aktif
- Pastikan database `inventory_manager` sudah dibuat

**Permissions tidak bekerja:**
- Jalankan `php setup_permissions.php` untuk rebuild permissions
- Cek tabel `role_permissions` apakah terisi

**Lupa password admin:**
- Jalankan `php create_admin.php` untuk reset ke default (`admin123`)

---

## Cara Menjalankan Aplikasi (Development)

### Langkah Development
1. Buka terminal PowerShell dan arahkan ke direktori src:
	```powershell
	cd D:\BelajarPemrograman\TUBES_PRK_PEMWEB_2025\kelompok\kelompok_25\src
	```
2. Jalankan server PHP built-in:
	```powershell
	php -S localhost:8000 -t public
	```
3. Buka `http://localhost:8000` di browser.

> Catatan: Untuk lingkungan Apache/Nginx, arahkan document root ke folder `public/` dan pastikan rewrite rule mengarahkan semua request ke `index.php`.

## Pengembangan Lanjutan
- Tambahkan halaman baru dengan membuat folder view (`views/<fitur>/index.php`) dan mapping route di `routes/web.php`.
- Integrasikan data nyata dengan membuat model & controller API, kemudian panggil via AJAX dari `public/assets/js/modules/<fitur>.js`.
- Gunakan Tailwind CDN saat prototyping; pindah ke build pipeline (`npm run build`) jika perlu optimisasi produksi.

---
Kelompok 25 – Sistem Informasi Inventori Stok Bahan Baku
