# KELOMPOK 19

## Anggota Kelompok

- Rakha Ukta Pamungkas (2255061012)
- Dimas Eka Putra Santoso (2315061114)
- Khintan Rachelia Radina Putri (2355061009)
- Muhammad Hanif Saputra (2315061125)

---

## "SiPEMAU" Sistem Pengaduan Mahasiswa Universitas Lampung Berbasis Web untuk Meningkatkan Transparansi dan Efisiensi Layanan Kampus

Sistem Pengaduan Mahasiswa Universitas Lampung adalah aplikasi berbasis web yang dirancang untuk memfasilitasi mahasiswa dalam menyampaikan keluhan, laporan, atau aspirasi terkait pelayanan akademik maupun non-akademik di lingkungan kampus. Aplikasi ini bertujuan untuk meningkatkan kualitas pelayanan, memperkuat akuntabilitas, serta mempercepat proses penanganan aduan oleh unit terkait.

Dalam sistem ini, mahasiswa dapat membuat akun, mengirimkan pengaduan lengkap dengan kategori masalah dan bukti pendukung, serta memantau perkembangan status aduan secara real-time. Pengaduan yang masuk akan diteruskan kepada petugas atau unit terkait berdasarkan kategori, sehingga proses tindak lanjut dapat dilakukan dengan lebih cepat dan terkoordinasi.

Petugas/unit terkait memiliki akses untuk memverifikasi laporan, memberikan respon awal, memperbarui status penanganan, hingga menyelesaikan laporan. Sementara itu, admin bertugas mengelola kategori pengaduan, data petugas, dan memastikan kelancaran operasional sistem.

Dengan penerapan teknologi web menggunakan HTML, CSS, JavaScript (Native), PHP Native, dan MySQL, aplikasi ini diharapkan dapat menjadi solusi digital yang membantu universitas menciptakan layanan yang lebih transparan, responsif, dan efisien, sekaligus menghadirkan pengalaman pelaporan yang mudah dan terstruktur bagi mahasiswa.

---

## Menjalankan Aplikasi

### Prerequisites

- PHP 7.4 atau lebih baru
- MySQL/MariaDB 10.5 atau lebih baru
- Web server (PHP built-in server, Apache, atau Nginx)

### 1. Setup Database

**Import Schema:**

```bash
mysql -u root -p < database/SIPEMAU.sql
```

**Seed Data (Opsional - untuk data testing):**

```bash
cd database
php seed_data.php
```

**Akun Testing:**

- Admin: `admin@sipemau.ac.id` / `password`
- Petugas: `budi@sipemau.ac.id` / `password`
- Mahasiswa: `john@student.unila.ac.id` / `password`

### 2. Konfigurasi Backend

Copy file `.env.example` menjadi `.env` di folder `backend/`:

```bash
cd backend
cp .env.example .env
```

Edit `.env` sesuai konfigurasi database Anda:

```env
DB_HOST=localhost
DB_NAME=sipemau_db
DB_USER=root
DB_PASS=your_password
```

### 3. Menjalankan Server

#### Opsi A: PHP Built-in Server (Development)

```bash
cd backend
php -S localhost:8000 -t public
```

Akses: `http://localhost:8000`

#### Opsi B: Laragon (Windows)

1. Install [Laragon](https://laragon.org/download/)
2. Copy folder `backend` ke `C:\laragon\www\sipemau`
3. Copy folder `database` ke `C:\laragon\www\sipemau`
4. Start Laragon (Apache & MySQL)
5. Import database melalui phpMyAdmin atau command line
6. Akses: `http://sipemau.test` atau `http://localhost/sipemau/backend/public`

#### Opsi C: XAMPP

1. Install [XAMPP](https://www.apachefriends.org/)
2. Copy folder `backend` ke `C:\xampp\htdocs\sipemau`
3. Copy folder `database` ke `C:\xampp\htdocs\sipemau`
4. Start Apache & MySQL dari XAMPP Control Panel
5. Import database melalui phpMyAdmin (`http://localhost/phpmyadmin`)
6. Edit `backend/.env` sesuai konfigurasi MySQL XAMPP
7. Akses: `http://localhost/sipemau/backend/public`

#### Opsi D: MAMP (macOS)

1. Install [MAMP](https://www.mamp.info/)
2. Copy folder project ke `/Applications/MAMP/htdocs/sipemau`
3. Start MAMP servers
4. Import database melalui phpMyAdmin
5. Akses: `http://localhost:8888/sipemau/backend/public`

### 4. Testing API

Import file `backend/postman_collection.json` ke Postman untuk testing semua endpoint.

**Base URL:** `http://localhost:8000` (atau sesuai konfigurasi server Anda)

### 5. Frontend Development

Frontend akan berada di folder `frontend/` dengan struktur:

- HTML Native
- CSS Native
- JavaScript Native (Fetch API untuk consume REST API)

Untuk development, frontend dapat diakses langsung atau menggunakan Live Server di VS Code.

---

## API Endpoints

### Authentication

- `POST /login` - Login user
- `POST /register` - Register mahasiswa baru
- `GET /logout` - Logout user

### Mahasiswa

- `GET /mahasiswa/dashboard` - Dashboard mahasiswa
- `GET /mahasiswa/complaints` - List pengaduan
- `POST /mahasiswa/complaints` - Buat pengaduan baru
- `GET /mahasiswa/complaints/:id` - Detail pengaduan

### Petugas

- `GET /petugas/dashboard` - Dashboard petugas
- `GET /petugas/complaints` - List pengaduan per unit
- `GET /petugas/complaints/:id` - Detail pengaduan
- `PATCH /petugas/complaints/:id/status` - Update status
- `POST /petugas/complaints/:id/notes` - Tambah catatan

### Admin

- `GET /admin/dashboard` - Dashboard admin
- **Units**: `GET`, `POST`, `PUT /:id`, `DELETE /:id`
- **Categories**: `GET`, `POST`, `PUT /:id`, `DELETE /:id`
- **Petugas**: `GET`, `POST`, `PUT /:id`, `DELETE /:id`

---

**Upload file tidak berfungsi:**

- Pastikan folder `backend/assets/uploads/evidence/` memiliki permission write
- Cek `php.ini` untuk `upload_max_filesize` dan `post_max_size`

---

## Preview Aplikasi

![Landing Page](src/frontend/preview/251211_23h33m39s_screenshot.png)
