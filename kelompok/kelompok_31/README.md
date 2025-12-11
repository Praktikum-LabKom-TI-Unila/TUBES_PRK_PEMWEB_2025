# EduPortal - Sistem Manajemen Pembelajaran Akademik

EduPortal adalah sistem manajemen pembelajaran akademik berbasis web yang dirancang untuk memudahkan proses pembelajaran antara dosen dan mahasiswa. Sistem ini menyediakan berbagai fitur untuk mengelola mata kuliah, materi pembelajaran, tugas, nilai, dan pengumuman.

## 📋 Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Entity Relationship Diagram (ERD)](#entity-relationship-diagram-erd)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Requirements](#requirements)
- [Instalasi](#instalasi)
- [Konfigurasi](#konfigurasi)
- [Struktur Folder](#struktur-folder)
- [Cara Penggunaan](#cara-penggunaan)
- [Default Credentials](#default-credentials)
- [API Documentation](#api-documentation)
- [Kontributor](#kontributor)

## ✨ Fitur Utama

### 👨‍💼 Admin
- **Dashboard** - Statistik lengkap sistem (mata kuliah, dosen, mahasiswa)
- **Kelola Mata Kuliah** - CRUD mata kuliah dengan assign dosen
- **Kelola Pengumuman** - Buat, edit, dan hapus pengumuman
- **User Management** - Tambah, lihat, dan hapus user (admin, dosen, mahasiswa)
- **Export Data** - Export data ke CSV (mata kuliah, users, pengumuman, nilai)
- **Pengaturan Sistem** - Konfigurasi sistem (nama, email, max file size, allowed extensions)

### 👨‍🏫 Dosen
- **Dashboard** - Statistik mata kuliah diampu, materi, tugas, dan nilai
- **Upload Materi** - Upload dan kelola bahan ajar digital
- **Buat Tugas** - Buat dan distribusikan tugas kepada mahasiswa
- **Input Nilai** - Input nilai dan feedback untuk tugas yang sudah dikumpulkan mahasiswa

### 👨‍🎓 Mahasiswa
- **Dashboard** - Statistik akademik (IPS, mata kuliah, tugas, nilai)
- **Mata Kuliah** - Lihat dan bergabung dengan mata kuliah yang tersedia
- **Materi** - Download materi pembelajaran yang diupload dosen
- **Tugas** - Lihat tugas dan submit jawaban
- **Nilai** - Lihat nilai dan feedback dari dosen


> **Catatan**: Ganti path screenshot di atas dengan path gambar yang sesuai. Simpan semua screenshot di folder `screenshots/` atau `docs/screenshots/`.

## 🗂 Entity Relationship Diagram (ERD)


*Entity Relationship Diagram (ERD) sistem EduPortal*

### Penjelasan ERD

ERD di atas menunjukkan relasi antar tabel dalam database EduPortal:

- **users** - Tabel utama untuk semua user (admin, dosen, mahasiswa)
- **mata_kuliah** - Tabel mata kuliah dengan relasi ke users (dosen_id)
- **enrollment** - Tabel relasi many-to-many antara mahasiswa dan mata kuliah
- **materi** - Tabel materi pembelajaran dengan relasi ke mata_kuliah dan users (uploaded_by)
- **tugas** - Tabel tugas dengan relasi ke mata_kuliah dan users (created_by)
- **submission** - Tabel submission tugas dengan relasi ke tugas dan users (mahasiswa_id, dinilai_oleh)
- **nilai** - Tabel nilai dengan relasi ke mata_kuliah, users (mahasiswa_id), dan users (created_by)
- **pengumuman** - Tabel pengumuman dengan relasi ke users (created_by)

> **Catatan**: Ganti path ERD di atas dengan path gambar ERD yang sesuai. Simpan file ERD di folder `docs/erd/` atau `database/erd/`.

## 🛠 Teknologi yang Digunakan

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: 
  - HTML5, CSS3
  - Bootstrap 5.3.0
  - JavaScript (Vanilla JS)
  - jQuery 3.6.0
  - Chart.js 4.4.0
  - SweetAlert2
  - Font Awesome 6.4.0

## 📦 Requirements

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web Server (Apache/Nginx) atau PHP Built-in Server
- Extension PHP:
  - PDO
  - PDO_MySQL
  - mbstring
  - fileinfo

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd kelompok_31
```

### 2. Setup Database

#### Opsi A: Menggunakan setup_database.sql (Recommended)
```bash
mysql -u root -p < database/setup_database.sql
```

#### Opsi B: Setup Manual
```bash
# 1. Buat database
mysql -u root -p < database/schema.sql

# 2. Insert data seed
mysql -u root -p < database/seed.sql

# 3. (Opsional) Jalankan migrations jika diperlukan
mysql -u root -p < database/add_enrollment_table.sql
mysql -u root -p < database/add_semester.sql
mysql -u root -p < database/migrations/004_add_nilai_pengumuman.sql
```

### 3. Konfigurasi Database

Edit file `config/database.php` dan sesuaikan kredensial database:

```php
private $host = 'localhost';
private $db_name = 'eduportal';
private $username = 'root';
private $password = '';
```

### 4. Setup Upload Directory

Pastikan folder uploads memiliki permission write:

```bash
mkdir -p uploads/materi uploads/tugas
chmod 777 uploads/materi uploads/tugas
```

### 5. Jalankan Aplikasi

#### Menggunakan PHP Built-in Server:
```bash
php -S localhost:8000
```

#### Menggunakan Apache/Nginx:
- Pastikan document root mengarah ke folder proyek
- Akses melalui browser: `http://localhost/kelompok_31`

## ⚙️ Konfigurasi

### Database Configuration
File: `config/database.php`

```php
private $host = 'localhost';        // Host database
private $db_name = 'eduportal';     // Nama database
private $username = 'root';          // Username database
private $password = '';              // Password database
```

### Upload Configuration
File: `admin/pengaturan.php` (dapat diubah melalui admin panel)

- Max File Size: Default 5MB (5242880 bytes)
- Allowed Extensions: pdf, doc, docx, txt, zip, rar

## 📁 Struktur Folder

```
kelompok_31/
├── admin/                 # Halaman admin
│   ├── mata_kuliah.php   # CRUD mata kuliah
│   ├── pengumuman.php    # CRUD pengumuman
│   ├── users.php         # User management
│   ├── export_data.php   # Export data ke CSV
│   └── pengaturan.php    # Pengaturan sistem
│
├── api/                  # API endpoints
│   ├── auth/
│   │   └── login.php    # API login
│   ├── buat_tugas.php    # API buat tugas
│   ├── enrollment.php    # API enrollment mahasiswa
│   ├── input_nilai.php   # API input nilai
│   ├── mata_kuliah_crud.php
│   ├── nilai.php         # API nilai mahasiswa
│   ├── pengumuman_crud.php
│   ├── submit_tugas.php  # API submit tugas
│   ├── upload_materi.php # API upload materi
│   └── users_crud.php    # API user management
│
├── assets/               # Assets (CSS, JS, Images)
│   ├── css/
│   │   └── custom.css   # Custom styles
│   ├── img/             # Images
│   └── js/
│       └── main.js      # Custom JavaScript
│
├── components/           # Reusable components
│   ├── header.php       # Header template
│   ├── navbar.php        # Navbar template
│   └── footer.php       # Footer template
│
├── config/               # Configuration files
│   └── database.php     # Database configuration
│
├── dashboard/            # Dashboard pages
│   ├── admin.php        # Admin dashboard
│   ├── dosen.php        # Dosen dashboard
│   └── mahasiswa.php    # Mahasiswa dashboard
│
├── database/             # Database files
│   ├── schema.sql       # Database schema
│   ├── seed.sql         # Seed data
│   ├── setup_database.sql # Complete setup
│   ├── add_enrollment_table.sql
│   ├── add_semester.sql
│   └── migrations/
│       └── 004_add_nilai_pengumuman.sql
│
├── dosen/                # Halaman dosen
│   ├── upload_materi.php
│   ├── buat_tugas.php
│   └── input_nilai.php
│
├── mahasiswa/            # Halaman mahasiswa
│   ├── matakuliah.php
│   ├── materi.php
│   ├── tugas.php
│   └── nilai.php
│
├── uploads/              # Uploaded files
│   ├── materi/          # Uploaded materi
│   └── tugas/           # Uploaded tugas
│
├── webservice/           # REST API
│   ├── api.php          # REST API endpoints
│   ├── consume.php      # API consumer example
│   └── README.md        # API documentation
│
├── index.php             # Landing page
├── login.php             # Login page
├── logout.php            # Logout handler
└── README.md            # This file
```

## 📖 Cara Penggunaan

### Login
1. Buka halaman login: `http://localhost/kelompok_31/login.php`
2. Masukkan username dan password sesuai role
3. Setelah login, akan diarahkan ke dashboard sesuai role

### Admin
- **Kelola Mata Kuliah**: Tambah, edit, hapus mata kuliah dan assign dosen
- **Kelola Pengumuman**: Buat pengumuman untuk semua user
- **User Management**: Tambah user baru (admin, dosen, mahasiswa)
- **Export Data**: Export data sistem ke CSV
- **Pengaturan**: Konfigurasi sistem

### Dosen
- **Upload Materi**: Upload file materi untuk mata kuliah yang diampu
- **Buat Tugas**: Buat tugas dengan deadline untuk mahasiswa
- **Input Nilai**: Berikan nilai dan feedback untuk tugas yang sudah dikumpulkan

### Mahasiswa
- **Mata Kuliah**: Lihat dan bergabung dengan mata kuliah yang tersedia
- **Materi**: Download materi pembelajaran
- **Tugas**: Lihat tugas dan submit jawaban sebelum deadline
- **Nilai**: Lihat nilai dan feedback dari dosen

## 🔐 Default Credentials

### Admin
- **Username**: `admin`
- **Password**: `password`

### Dosen
- **Username**: `dosen1`, `dosen2`, `dosen3`
- **Password**: `password`

### Mahasiswa
- **Username**: `mhs1`, `mhs2`, `mhs3`, `mhs4`, `mhs5`
- **Password**: `password`

> **⚠️ PENTING**: Ganti password default setelah instalasi untuk keamanan!

## 📡 API Documentation

### REST API
REST API tersedia di `webservice/api.php`. Dokumentasi lengkap ada di `webservice/README.md`.

**Endpoints:**
- `GET /webservice/api.php/mata-kuliah` - List semua mata kuliah
- `GET /webservice/api.php/mata-kuliah/{id}` - Detail mata kuliah
- `GET /webservice/api.php/materi` - List semua materi
- `GET /webservice/api.php/materi/{id}` - Detail materi
- `GET /webservice/api.php/tugas` - List semua tugas
- `GET /webservice/api.php/tugas/{id}` - Detail tugas
- `GET /webservice/api.php/pengumuman` - List semua pengumuman
- `GET /webservice/api.php/pengumuman/{id}` - Detail pengumuman

### Internal API
API internal untuk frontend tersedia di folder `api/`:
- `api/auth/login.php` - Authentication
- `api/upload_materi.php` - Upload materi
- `api/buat_tugas.php` - Buat tugas
- `api/submit_tugas.php` - Submit tugas
- `api/input_nilai.php` - Input nilai
- `api/nilai.php` - Get nilai mahasiswa
- `api/pengumuman_crud.php` - CRUD pengumuman
- `api/mata_kuliah_crud.php` - CRUD mata kuliah
- `api/users_crud.php` - User management
- `api/enrollment.php` - Enrollment mahasiswa

## 👥 Kontributor

Proyek ini dikerjakan oleh Kelompok 31:

1. **Sabilillah Irdo** (Anggota 1 - Ketua)
   - Login/Logout
   - Database Configuration
   - Authentication API

2. **Alfikri Deo Putra** (Anggota 2)
   - Components (Header, Navbar, Footer)
   - Dashboard (Admin, Dosen, Mahasiswa)
   - CRUD Mata Kuliah
   - Export Data & Pengaturan

3. **Muhammad Zaki Zain** (Anggota 3)
   - Upload Materi
   - Buat Tugas
   - Daftar Materi & Tugas (Mahasiswa)
   - Submit Tugas API

4. **Sony Kurniawan** (Anggota 4)
   - Input Nilai (Dosen)
   - Daftar Nilai (Mahasiswa)
   - CRUD Pengumuman
   - REST API

## 📝 License

Proyek ini dibuat untuk keperluan akademik (Tugas Besar Pemrograman Web 2025).

## 🐛 Troubleshooting

### Database Connection Error
- Pastikan MySQL service berjalan
- Cek kredensial di `config/database.php`
- Pastikan database `eduportal` sudah dibuat

### Upload File Error
- Pastikan folder `uploads/materi` dan `uploads/tugas` memiliki permission write (777)
- Cek max file size di `admin/pengaturan.php`
- Pastikan extension file sesuai dengan yang diizinkan

### Session Error
- Pastikan `session_start()` dipanggil sebelum menggunakan `$_SESSION`
- Cek `php.ini` untuk konfigurasi session

### API Error
- Cek error log PHP untuk detail error
- Pastikan semua required parameters dikirim
- Pastikan user sudah login dan memiliki permission yang sesuai

## 📞 Support

Untuk pertanyaan atau masalah, silakan hubungi tim pengembang atau buat issue di repository.

---

**EduPortal** - Sistem Manajemen Pembelajaran Akademik © 2025

