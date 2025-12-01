# 💻 Web-Based Point of Sales (POS) & Inventory Management

> **Tugas Besar Praktikum Pemrograman Web 2025**
>
> **Tema:** Digital Transformation for SMEs (No. 4)

Aplikasi ini adalah sistem kasir (Point of Sales) dan manajemen stok barang berbasis web yang dirancang untuk membantu UMKM (khususnya Toko Komputer) dalam mencatat transaksi penjualan dan mengelola inventaris secara digital, akurat, dan *real-time*.

---

## 👥 Anggota Kelompok

| No  | Nama Lengkap | NPM | Role |
| :--- | :--- | :--- | :--- |
| 1 | **M. Hibban Ramadhan** | **2315061094** | Fullstack / Project Lead |
| 2 | **Syahrul Ghufron Al Hamdan** | **2315061063** | Frontend |
| 3 | **M. Reza Rohman** | **2315061004** | Frontend |
| 4 | **Makka Muhammad Mustova** | **2315061100** | UI/Doc |

---

## 📖 Gambaran Proyek

### Latar Belakang
UMKM Toko Komputer seringkali mengalami kesulitan dalam pencatatan stok manual dan perhitungan transaksi yang rentan kesalahan. Proyek ini bertujuan mendigitalkan proses tersebut melalui sistem web yang ringan dan mudah digunakan.

### Fitur Utama
1.  **Multi-Role User Management:**
    * **Owner:** Akses penuh, melihat dashboard laporan penjualan & manajemen user.
    * **Admin Gudang:** Input stok masuk, manajemen kategori & data produk.
    * **Kasir:** Input transaksi penjualan (POS) dengan kalkulasi otomatis.
2.  **Transaksi Penjualan (POS):**
    * Keranjang belanja dinamis (menggunakan JavaScript Native/AJAX).
    * Potong stok otomatis saat *checkout*.
    * Cetak struk belanja.
3.  **Laporan Sederhana:** Grafik penjualan dan riwayat transaksi.

---

## 🛠️ Teknologi yang Digunakan

Sesuai ketentuan tugas besar, aplikasi ini dibangun tanpa Framework PHP/JS (Native):

* **Frontend:** HTML5, CSS3 (Bootstrap/Tailwind - *To be decided*), JavaScript Native (DOM & AJAX).
* **Backend:** PHP Native (Structured/Procedural dengan konsep MVC sederhana).
* **Database:** MySQL.
* **Tools:** Git, Visual Studio Code, XAMPP/Laragon.

---

## 🌳 Struktur Folder (Work Tree)

Tantangan PHP Native adalah kode yang berantakan (spaghetti code). Gunakan struktur ini untuk memisahkan Tampilan (Views) dan Logika (Logic)
```bash
/project-pos-sme
├── /config
│   └── database.php       # Koneksi ke database (mysqli)
├── /assets                # File statis (CSS/JS/Img)
│   ├── css/               # Pakai Bootstrap/Tailwind 
│   ├── js/                # JS Native untuk interaksi
│   └── images/            # Foto upload produk
├── /includes              # Potongan layout yang berulang
│   ├── header.php         # Navbar
│   ├── sidebar.php        # Menu samping
│   └── footer.php
├── /auth                  # Halaman Login/Logout
│   ├── login.php
│   └── process_login.php
├── /pages                 # Halaman utama (Views)
│   ├── dashboard.php      # Halaman awal
│   ├── pos.php            # Halaman Kasir (Transaksi)
│   ├── products.php       # Manajemen Data Barang
│   └── users.php          # Manajemen User (Khusus Owner)
├── /process               # Logika pemrosesan data (CRUD Action)
│   ├── product_add.php
│   ├── product_delete.php
│   └── transaction_save.php
└── index.php              # Redirect ke login atau dashboard
```
---

## 🔀 Alur Fitur & Hak Akses (Role)

Untuk memenuhi syarat User Management, kita bagi hak aksesnya:  
**1. Owner:**
    - Bisa akses semua halaman.
    - Fitur eksklusif: Melihat Laporan Penjualan (Grafik/Tabel total pendapatan) dan Manajemen User (Tambah/Hapus karyawan).

**2. Admin Gudang:**
    - Fokus pada halaman products.php.
    - Tugas: Tambah barang baru, edit harga, dan restock barang.

**3. Kasir:**
    - Fokus pada halaman pos.php.
    - Tugas: Input transaksi penjualan. Stok barang di database berkurang otomatis saat kasir menekan "Bayar".

--- 
## 🗂️ Rencana Struktur Database

Aplikasi ini akan menggunakan skema database relasional dengan tabel utama:
* `users`: Menyimpan data autentikasi dan hak akses.
* `categories`: Pengelompokan jenis barang.
* `products`: Data stok, harga, dan informasi barang.
* `transactions`: Header data penjualan (invoice, tanggal, user).
* `transaction_details`: Rincian barang yang dibeli dalam satu transaksi.

---

## 🚀 Cara Instalasi (Development)

1.  Clone repository ini (atau fork sesuai instruksi).
2.  Masuk ke folder project: `cd kelompok/kelompok_XX`.
3.  Buat database baru di MySQL dengan nama `db_pos_sme`.
4.  Import file `database.sql` (akan tersedia nanti di folder `sql/` atau `config/`).
5.  Sesuaikan konfigurasi database di `config/database.php`.
6.  Jalankan server lokal (Apache/Nginx) dan buka via browser.

---

> *Dibuat untuk memenuhi Tugas Besar Praktikum Pemrograman Web - Laboratorium Teknik Komputer Unila.*