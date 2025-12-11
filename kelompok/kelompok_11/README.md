# 🔧 Sistem Informasi Manajemen & POS Bengkel UMKM

## 👥 Anggota Kelompok 11

Proyek ini dikembangkan oleh tim mahasiswa Teknik Komputer Universitas Lampung:

| NIM | Nama Anggota | Peran & Tanggung Jawab |
| :--- | :--- | :--- |
| **2315061067** | **Muhammad Favian Rizki (Ketua)** | Fullstack (Inventory, Dashboard), UI/UX Design, Dokumentasi |
| 2315061039 | Ary Nanda Pratama | (User Management, Auth, Role Access) |
| 2315061082 | Daffa Raihan Permana | (Modul Reservasi Service & Check-in) |
| 2315061110 | Tomy Arya Fiosa | (Point of Sale, Transaction Logic, QRIS) |

---

## 📝 Tentang Proyek

**Sistem POS Bengkel UMKM** adalah solusi transformasi digital yang dirancang khusus untuk membantu pemilik bengkel kecil dan menengah dalam mengelola operasional bisnis mereka secara efisien.

Aplikasi ini menggantikan pencatatan manual dengan sistem terintegrasi yang mencakup manajemen stok sparepart, kasir (POS), reservasi pelanggan, hingga laporan kinerja mekanik secara *real-time*.

### 🌟 Fitur Unggulan

#### 1. 🖥️ Dashboard Interaktif
Monitoring bisnis dalam satu layar dengan statistik *real-time* menggunakan AJAX.
- Grafik omzet harian.
- Notifikasi stok menipis (Low Stock Alert).
- Pelacakan performa mekanik teraktif.

#### 2. 🛒 Point of Sales (Kasir) Modern
Antarmuka kasir yang cepat dan mudah digunakan.
- **Support Layanan & Sparepart:** Bisa input jasa servis dan barang sekaligus.
- **QRIS Payment Simulation:** Simulasi pembayaran digital dengan QR Code dinamis.
- **Cetak Struk:** Fitur cetak bukti pembayaran langsung dari browser.

#### 3. 📅 Manajemen Reservasi
Pelanggan tidak perlu antre lama.
- Pencatatan booking servis.
- Status tracking (Booked -> In Progress -> Completed).
- Integrasi langsung ke Kasir saat check-in.

#### 4. 📦 Inventory Management
- Manajemen data Supplier.
- CRUD Sparepart dengan foto produk.
- Riwayat stok masuk dan keluar.

#### 5. 🔐 Multi-Role Access
Sistem keamanan berbasis peran untuk membatasi akses:
- **Admin:** Akses penuh (Inventory, User, Laporan).
- **Kasir:** Fokus pada transaksi POS.
- **Mekanik:** Melihat jadwal pekerjaan servis.

---

## 📸 Tangkapan Layar (Screenshots)

| Dashboard Admin | Point of Sale (POS) |
| :---: | :---: |
| ![Dashboard](Screenshot%20Aplikasi/Screenshot%202025-12-11%20214059.png) | ![POS](Screenshot%20Aplikasi/Screenshot%202025-12-11%20213554.png) |
| **Reservasi** | **Pembayaran QRIS** |
| ![Reservasi](Screenshot%20Aplikasi/Screenshot%202025-12-11%20213338.png) | ![QRIS](Screenshot%20Aplikasi/Screenshot%202025-12-11%20213938.png) |

---

## 🛠️ Teknologi yang Digunakan

* **Bahasa Pemrograman:** PHP Native (v7.4 / v8.0+)
* **Database:** MySQL / MariaDB
* **Frontend:** HTML5, Tailwind CSS (via CDN)
* **Scripting:** Vanilla JavaScript (Fetch API, DOM Manipulation)
* **Server:** Apache (XAMPP / Laragon)

---

## 🚀 Cara Instalasi & Menjalankan

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di komputer lokal Anda:

### Prasyarat
Pastikan Anda telah menginstal **XAMPP** atau **Laragon**.

### 1. Persiapan Database
1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Buat database baru dengan nama: `pos_bengkel`.
3. Import file SQL yang berada di:
   `src/database/pos_bengkel.sql`
4. (Opsional) Jalankan file `seed_data.php` jika ingin mereset data dummy.

### 2. Konfigurasi Database
Buka file `src/config/database.php` dan sesuaikan kredensial jika perlu:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Sesuaikan dengan password database Anda
define('DB_NAME', 'pos_bengkel');
```
### 3. Akses Aplikasi
Tempatkan folder proyek di dalam folder htdocs (XAMPP) atau www (Laragon), lalu buka browser:

http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_11/src/

---

## 🔑 Akun Demo (Login)

Gunakan akun berikut untuk mencoba berbagai role:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Administrator** | `admin@bengkel.com` | `password` |
| **Kasir** | `kasir1@bengkel.com` | `password` |
| **Mekanik** | `joko@bengkel.com` | `password` |

---

### 📂 Struktur Direktori Utama
```
kelompok_11/src/
├── auth/           # Logika Login, Logout, Register
├── config/         # Koneksi Database
├── dashboard/      # Halaman Dashboard & API Statistik
├── database/       # File SQL & Seeder
├── inventory/      # CRUD Sparepart & Supplier
├── js/             # Script JS (AJAX, DOM)
├── layout/         # Header, Sidebar, Footer (Modular)
├── pos/            # Sistem Kasir & Transaksi
├── reservasi/      # Manajemen Booking Servis
└── index.php       # Routing Awal```
