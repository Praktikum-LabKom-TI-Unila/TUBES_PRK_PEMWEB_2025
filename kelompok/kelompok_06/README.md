# 🚌 SILATIUM

**Sistem Informasi Layanan Transportasi Umum Berbasis Web**

SILATIUM adalah aplikasi web yang menyediakan informasi transportasi
umum serta layanan pengaduan masyarakat secara online. Sistem ini
dikembangkan menggunakan **PHP Native**, **MySQL**, dan **HTML/CSS/JS**
untuk mendukung konsep smart city di bidang transportasi.

------------------------------------------------------------------------

## 👥 Anggota Kelompok 06

  No   Nama                       NPM
  ---- -------------------------- ------------
  1    Makhasin Muhammad          2315061084
  2    Nabila Salwa Alghaida      2315061034
  3    Nabilla Chairunisa         2315061022
  4    Risdam Ananda Rholanjiba   2315061052

------------------------------------------------------------------------

## 📌 Ringkasan Sistem

SILATIUM memiliki dua fitur utama:

### 1️⃣ **Informasi Transportasi**

-   Admin dapat mengelola data transportasi (CRUD)
-   Pengguna dapat melihat jadwal, rute, dan informasi umum transportasi

### 2️⃣ **Pengaduan Masyarakat**

-   User dapat mengirim laporan/pengaduan
-   Admin dapat melihat seluruh laporan
-   Admin dapat memperbarui status laporan

Sistem dilengkapi autentikasi login, role management (User/Admin), dan
tampilan dashboard untuk mempermudah navigasi.

------------------------------------------------------------------------

## 🚀 Fitur Utama

### 👤 **Modul Autentikasi**

-   Login\
-   Register\
-   Logout\
-   Role-based access control (User/Admin)

### 🚌 **Modul Transportasi**

-   Admin: Tambah, Edit, Hapus data transportasi\
-   User: Melihat daftar transportasi

### 📝 **Modul Laporan**

-   User: Tambah laporan & melihat laporan pribadi\
-   Admin: Melihat semua laporan & memperbarui status

### 📊 **Dashboard**

-   Dashboard Admin\
-   Dashboard User

### ⚙️ **Konfigurasi Sistem**

-   Koneksi database (`config/database.php`)
-   Sistem autentikasi (`config/auth.php`)

------------------------------------------------------------------------

## 📁 Struktur Folder Project

    src/
    │
    ├── admin/
    │   ├── dashboard.php
    │   ├── laporan.php
    │   ├── transportasi.php
    │   └── update_status.php
    │
    ├── assets/
    │   ├── css/style.css
    │   ├── js/script.js
    │   └── icons/*.svg
    │
    ├── auth/
    │   ├── login.php
    │   ├── logout.php
    │   └── register.php
    │
    ├── config/
    │   ├── database.php
    │   └── auth.php
    │
    ├── laporan/
    │   └── proses_laporan.php
    │
    ├── transportasi/
    │   └── data_transportasi.php
    │
    └── user/
        ├── dashboard.php
        ├── laporan_saya.php
        ├── tambah_laporan.php
        └── transportasi.php

    database.sql
    README.md

------------------------------------------------------------------------

## 🔧 Kebutuhan Sistem

-   PHP 7.4+ atau PHP 8.x\
-   MySQL / MariaDB\
-   Apache (XAMPP / LAMPP / Laragon / WAMP)\
-   Browser modern (Chrome, Firefox, Edge)

------------------------------------------------------------------------

## 🛠️ Cara Instalasi & Menjalankan Aplikasi

### **1️⃣ Clone Repository**

    git clone https://github.com/mascynnn/TUBES_PRK_PEMWEB_2025.git

Masuk ke folder project:

    cd TUBES_PRK_PEMWEB_2025

------------------------------------------------------------------------

### **2️⃣ Import Database**

1.  Buka phpMyAdmin\
2.  Buat database baru, misalnya: `silatium`\
3.  Import file `database.sql`

------------------------------------------------------------------------

### **3️⃣ Atur Koneksi Database**

Buka file:

`src/config/database.php`

Lalu sesuaikan:

``` php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "silatium";
```

------------------------------------------------------------------------

### **4️⃣ Jalankan di Localhost**

Jika menggunakan XAMPP:

1.  Pindahkan folder project ke:

```{=html}
<!-- -->
```
    htdocs/KELOMPOK_06/

2.  Jalankan Apache & MySQL\
3.  Akses website:

```{=html}
<!-- -->
```
    http://localhost/KELOMPOK_06/src/auth/login.php

------------------------------------------------------------------------

## 🔐 Akun Login

### Admin:

    email: admin@example.com
    password: admin123

### User:

    email: user@example.com
    password: user123

*(Sesuaikan dengan data pada `database.sql`.)*
