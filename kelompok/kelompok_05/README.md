## Kelompok 5

## Anggota Kelompok
| Nama | NPM |
|------|-----|
| Muhammad Rayhan Gumay | 2355061007 |
| Arfan Andhika Pramudya | 2315061019 |
| M. Sulthon Alfarizky | 2315061054 |
| Pangihutan Syahputra Purba | 2315061066 |

## Judul Project
**LampungSmart** - Platform Good Governance Provinsi Lampung

## Deskripsi
LampungSmart adalah platform web yang dikembangkan untuk mendukung tata kelola pemerintahan yang baik (Good Governance) di Provinsi Lampung. Platform ini menyediakan dua layanan inti:
1. **Pengaduan Infrastruktur Publik** - Pelaporan jalan rusak, lampu jalan mati, sampah menumpuk, dll.
2. **Perizinan UMKM** - Pendaftaran dan pengelolaan izin usaha mikro, kecil, dan menengah.

Dengan fokus pada transparansi, akuntabilitas, dan efisiensi, LampungSmart memungkinkan masyarakat berinteraksi langsung dengan pemerintah daerah melalui sistem digital yang aman dan mudah digunakan.

## Teknologi yang Digunakan
- Frontend: HTML5, CSS3, Bootstrap 5.3.2, JavaScript
- Backend: PHP Native
- Database: MySQL
- Web Server: Apache (Laragon/XAMPP)

## Cara Menjalankan Aplikasi

### Persyaratan
- PHP 7.4 atau lebih baru
- MySQL 5.7 atau lebih baru
- Web server (Apache/Nginx) atau Laragon/XAMPP

### Langkah Instalasi
1. Clone atau download repository ini
2. Pindahkan folder `kelompok_05` ke direktori web server (contoh: `htdocs` atau `www`)
3. Buat database baru dengan nama `lampungsmart`
4. Import file SQL dari `database/lampungsmart.sql` ke database
5. Sesuaikan konfigurasi database di `src/config/config.php` jika diperlukan:
   ```php
   $host = "localhost";
   $user = "root";
   $pass = "";
   $db   = "lampungsmart";
   ```
6. Akses aplikasi melalui browser: `http://localhost/kelompok_05/src/public/`

### Akun Default
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@lampungsmart.com | admin123 |

Atau daftar akun baru sebagai Warga melalui halaman registrasi.

## Fitur Aplikasi

### Fitur Warga
- Registrasi dan login akun
- Dashboard pribadi
- Membuat pengaduan infrastruktur dengan upload foto
- Melihat riwayat dan status pengaduan
- Mendaftar izin UMKM
- Melihat status pengajuan UMKM
- Mengelola profil dan foto profil
- Reset password

### Fitur Admin
- Dashboard dengan statistik pengaduan dan UMKM
- Validasi dan menanggapi pengaduan warga
- Menyetujui atau menolak pengajuan UMKM
- Mengelola data pengguna (ubah role, hapus user)

### Fitur Publik
- Landing page informatif
- Dashboard publik dengan peta lokasi pengaduan
- Halaman FAQ
- Halaman kontak

## Struktur Folder
```
kelompok_05/
├── README.md
├── database/
│   └── lampungsmart.sql
├── ERD/
│   └── erd.png
├── screenshot/
└── src/
    ├── admin/          # Halaman admin
    ├── assets/         # CSS, JS, images, uploads
    ├── auth/           # Login, register, logout, reset password
    ├── config/         # Konfigurasi database
    ├── dashboard/      # Dashboard warga
    ├── layouts/        # Header, sidebar, footer
    ├── pengaduan/      # Fitur pengaduan
    ├── profile/        # Profil pengguna
    ├── public/         # Halaman publik (landing, FAQ, kontak)
    └── umkm/           # Fitur UMKM
```

## Struktur Database
Database `lampungsmart` terdiri dari tabel-tabel berikut:
- **users** - Data pengguna (id, nama, email, password, role, profile_photo)
- **pengaduan** - Data pengaduan (id, user_id, judul, deskripsi, lokasi, foto, status, tanggal)
- **tanggapan** - Tanggapan admin terhadap pengaduan
- **umkm** - Data pendaftaran UMKM (id, user_id, nama_usaha, bidang, alamat, status)
- **password_resets** - Token reset password

ERD dapat dilihat di folder `ERD/erd.png`