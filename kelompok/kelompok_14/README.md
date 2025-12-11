# RepairinBro - Electronic Service Management System

**RepairinBro** adalah sistem manajemen layanan servis elektronik berbasis web yang dirancang untuk mempermudah operasional bengkel reparasi. Sistem ini mendigitalisasi seluruh alur kerja mulai dari penerimaan barang, pengerjaan teknisi, hingga pengambilan barang oleh pelanggan.

Dibangun dengan **PHP Native** yang ringan dan antarmuka modern menggunakan **Tailwind CSS**, sistem ini menawarkan transparansi dan efisiensi bagi pengelola usaha servis.

---

## 🚀 Fitur Unggulan

### 1. Public Tracking (Pelacakan Resi)
Pelanggan tidak perlu login. Cukup masukkan **Nomor Resi** di halaman depan untuk melihat:
*   Status terkini (Barang Masuk, Pengerjaan, atau Selesai).
*   Estimasi waktu dan tanggal selesai.
*   Rincian biaya (jika sudah selseai).

### 2. Admin Dashboard (Front Office)
*   **Input Servis Baru**: Form penerimaan barang dengan auto-generate Nomor Resi.
*   **Cetak Resi**: Tanda terima fisik yang rapi siap cetak.
*   **Manajemen Antrian**: Melihat semua servis yang sedang berjalan.
*   **Verifikasi Pengambilan**: Upload bukti pembayaran (Transfer/QRIS) saat barang diambil.

### 3. Teknisi Dashboard (Back Office)
*   **Update Status Real-time**: Mengubah status dari *Pengecekan* -> *Menunggu Sparepart* -> *Pengerjaan* -> *Selesai*.
*   **Input Biaya & Sparepart**: 
    *   Sistem input dinamis untuk menambah banyak komponen sekaligus.
    *   **Auto-Format Harga**: Input "25000" otomatis tampil "25.000".
*   **Riwayat Pengerjaan**: Melihat log pekerjaan yang sudah diselesaikan.

### 4. Superadmin (Manajemen Pusat)
*   **Kelola Pengguna**: Tambah/Hapus akun Admin dan Teknisi.
*   **Log Aktivitas**: Memantau siapa yang login dan apa yang mereka lakukan (Audit Trail).
*   **Pengaturan Toko**: Mengubah Nama Toko, Alamat, dan Logo yang tampil di Resi dan Web secara instan.
*   **Backup Database**: Fitur satu klik untuk mengunduh backup SQL.

---

## 🛠️ Teknologi yang Digunakan

*   **Backend**: PHP 8.x (Native)
*   **Database**: MySQL / MariaDB
*   **Frontend**: HTML5, Tailwind CSS (CDN)
*   **JavaScript**: Vanilla JS, SweetAlert2 (untuk notifikasi interaktif)
*   **Server**: Apache (via XAMPP/Laragon)

---

## 📦 Struktur Folder

```
/tubes_pw
├── /database           # File dump SQL (database.sql)
├── /src                # Source code utama aplikasi
│   ├── /assets         # Logo, Foto Profil, Upload Bukti
│   ├── /halaman-admin  # Modul Admin (Dashboard, Input Servis)
│   ├── /halaman-resi   # Modul Tracking Public
│   ├── /halaman-teknisi# Modul Teknisi
│   ├── /super-admin    # Modul Superadmin
│   ├── config.php      # Koneksi Database
│   ├── index.php       # Landing Page
│   └── login.php       # Halaman Login
└── README.md           # Dokumentasi ini
```

---

## 💻 Cara Instalasi

1.  **Persiapan Environment**
    *   Pastikan **XAMPP** atau **Laragon** sudah terinstall (PHP 8+).
    *   Aktifkan Apache dan MySQL.

2.  **Setup Database**
    *   Buka `phpMyAdmin` (http://localhost/phpmyadmin).
    *   Buat database baru dengan nama: `fixtrack`.
    *   Import file `database/database.sql` ke dalam database `fixtrack`.

3.  **Konfigurasi Project**
    *   Salin folder project ke dalam `htdocs` (misal: `C:\xampp\htdocs\repairinbro`).
    *   Buka file `src/config.php` dan pastikan konfigurasi sesuai:
        ```php
        $servername = "localhost";
        $user = "root";
        $pass = ""; 
        $db   = "fixtrack";
        ```

4.  **Jalankan Aplikasi**
    *   Akses melalui browser: `http://localhost/repairinbro/src`
    *   **Akun Default** (Lihat di database tabel `users` atau gunakan akun demo yang dibuat):
        *   **Superadmin**: `superadmin` / `password123` (contoh)
        *   **Admin**: `admin1` / `password123`
        *   **Teknisi**: `teknisi1` / `password123`

---

## 👥 Tim Pengembang

**Kelompok 14 - Pemrograman Web**
Project ini dikembangkan sebagai Tugas Besar untuk mendemonstrasikan pemahaman tentang CRUD, Session Management, dan Database SQL.

---
*Dibuat dengan ❤️ dan Kopi.*
