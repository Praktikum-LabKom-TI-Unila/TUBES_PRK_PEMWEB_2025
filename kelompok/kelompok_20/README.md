# myUnila Lost & Found

## 👥 Anggota Kelompok 20

| No | Nama | NPM | Role |
|----|------|-----|------|
| 1  | Firman Farel Richardo | 2315061099 | PM & Backend (Ketua) |
| 2  | Muhammad Robbani Narsam | 2315061029 | -- |
| 3  | Ananda Fahmuzna Fauzi | 2315061009 | -- |
| 4  | Mutiara Khairunnisa Zulkifli | 2315061060 | -- |

---

## 📖 Deskripsi Project

**myUnila Lost & Found** adalah aplikasi berbasis web untuk mengelola sistem kehilangan dan penemuan barang di lingkungan Universitas Lampung. Aplikasi ini memfasilitasi mahasiswa dan civitas akademika untuk melaporkan barang hilang atau menemukan barang yang hilang, serta memudahkan proses pencocokan antara barang hilang dengan barang yang ditemukan.

### 🎯 Tema
Community & Organization Management

### ✨ Tujuan
- Memudahkan mahasiswa melaporkan barang hilang
- Menyediakan platform terpusat untuk barang temuan
- Meningkatkan peluang pemilik menemukan kembali barangnya
- Membangun komunitas yang saling membantu di kampus

---

## 🛠️ Persyaratan Teknis

### Frontend
- HTML5
- CSS3 (Bootstrap/Tailwind)
- JavaScript Native (tanpa framework)

### Backend
- PHP Native (tanpa framework)
- RESTful API design pattern

### Database
- MySQL
- Desain ERD yang terstruktur

### Version Control
- Git & GitHub

---

## 🎯 Fitur Utama

### 1. User Management
#### Registrasi & Autentikasi
- ✅ Registrasi akun baru dengan validasi email Unila
- ✅ Login/Logout system
- ✅ Manajemen profil pengguna
- ✅ Sistem role (Admin, User)

#### Hak Akses
- **Admin**: 
  - Kelola semua laporan
  - Verifikasi laporan
  - Kelola pengguna
  - Lihat statistik
- **User**: 
  - Buat laporan kehilangan
  - Buat laporan penemuan
  - Lihat daftar barang hilang/temuan
  - Update status laporan sendiri

### 2. Transaksi/Layanan Utama

#### Laporan Barang Hilang
- 📝 Buat laporan barang hilang
- 📸 Upload foto barang
- 📍 Tentukan lokasi kehilangan
- 📅 Tanggal dan waktu kehilangan
- ✏️ Edit/Update laporan
- ❌ Hapus laporan
- ✓ Tandai sebagai ditemukan

#### Laporan Barang Temuan
- 📝 Laporkan barang yang ditemukan
- 📸 Upload foto barang temuan
- 📍 Lokasi penemuan
- 📅 Tanggal dan waktu penemuan
- ✏️ Edit/Update laporan
- ❌ Hapus laporan
- ✓ Tandai sebagai sudah dikembalikan

#### Pencarian & Pencocokan
- 🔍 Pencarian berdasarkan kategori
- 🔍 Pencarian berdasarkan lokasi
- 🔍 Pencarian berdasarkan tanggal
- 🔍 Filter berdasarkan status
- 🤝 Sistem pencocokan otomatis

#### Komunikasi
- 💬 Sistem chat/komentar pada laporan
- 📧 Notifikasi email untuk pencocokan
- 🔔 Notifikasi status update

### 3. Fitur Tambahan
- 📊 Dashboard statistik (untuk Admin)
- 📱 Responsive design
- 🔐 Security (password hashing, SQL injection prevention)
- ✅ Validasi form
- 🖼️ Image upload & preview
- 📄 Export laporan (PDF/Excel)

---

## 📊 Desain Database (ERD)

### Tabel Utama

#### users
```sql
- id (PK)
- npm
- nama
- email
- password
- role (admin/user)
- no_telp
- foto_profil
- created_at
- updated_at
```

#### lost_items (Barang Hilang)
```sql
- id (PK)
- user_id (FK)
- nama_barang
- kategori
- deskripsi
- foto
- lokasi_hilang
- tanggal_hilang
- status (hilang/ditemukan)
- created_at
- updated_at
```

#### found_items (Barang Temuan)
```sql
- id (PK)
- user_id (FK)
- nama_barang
- kategori
- deskripsi
- foto
- lokasi_temukan
- tanggal_temukan
- status (tersedia/dikembalikan)
- created_at
- updated_at
```

#### matches (Pencocokan)
```sql
- id (PK)
- lost_item_id (FK)
- found_item_id (FK)
- status (pending/confirmed/rejected)
- created_at
```

#### comments
```sql
- id (PK)
- user_id (FK)
- item_id (FK)
- item_type (lost/found)
- comment
- created_at
```

#### categories
```sql
- id (PK)
- nama_kategori
- icon
```

---

## 🗂️ Struktur Folder

```
myunila-lost-found/
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   └── responsive.css
│   ├── js/
│   │   ├── main.js
│   │   ├── validation.js
│   │   └── search.js
│   └── images/
│       ├── logo.png
│       └── default-avatar.png
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── navbar.php
├── modules/
│   ├── auth/
│   │   ├── login.php
│   │   ├── register.php
│   │   └── logout.php
│   ├── lost-items/
│   │   ├── create.php
│   │   ├── list.php
│   │   ├── detail.php
│   │   └── update.php
│   ├── found-items/
│   │   ├── create.php
│   │   ├── list.php
│   │   ├── detail.php
│   │   └── update.php
│   ├── profile/
│   │   └── index.php
│   └── admin/
│       ├── dashboard.php
│       └── users.php
├── uploads/
│   ├── lost-items/
│   ├── found-items/
│   └── profiles/
├── database/
│   └── myunila_lostfound.sql
├── index.php
└── README.md
```

---

## 🚀 Instalasi & Setup

### Prerequisites
- XAMPP/Laragon (PHP 7.4+, MySQL)
- Web Browser modern
- Git

### Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/firmanfarelrichardo/TUBES_PRK_PEMWEB_2025.git
   cd TUBES_PRK_PEMWEB_2025/kelompok/kelompok_20
   ```

2. **Setup Database**
   - Buka phpMyAdmin
   - Buat database baru: `myunila_lostfound`
   - Import file `database/myunila_lostfound.sql`

3. **Konfigurasi Database**
   - Buka file `config/database.php`
   - Sesuaikan kredensial database:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'myunila_lostfound');
   ```

4. **Jalankan Aplikasi**
   - Akses via browser: `http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_20`

5. **Login Default**
   - Admin: `admin@unila.ac.id` / `admin123`
   - User: Registrasi akun baru

---

## 📸 Screenshot

> *Screenshot akan ditambahkan setelah development*

---

## 🔐 Security Features

- Password hashing menggunakan `password_hash()`
- Prepared statements untuk mencegah SQL Injection
- CSRF token protection
- Input validation & sanitization
- Session management
- XSS prevention

---

## 📝 Cara Penggunaan

### Untuk User Biasa

1. **Melaporkan Barang Hilang**
   - Login ke akun
   - Klik "Laporkan Barang Hilang"
   - Isi form (nama barang, kategori, deskripsi, foto, lokasi, tanggal)
   - Submit laporan

2. **Melaporkan Barang Temuan**
   - Login ke akun
   - Klik "Laporkan Barang Temuan"
   - Isi form detail barang
   - Submit laporan

3. **Mencari Barang**
   - Gunakan fitur pencarian
   - Filter berdasarkan kategori/lokasi/tanggal
   - Lihat detail barang
   - Hubungi pelapor melalui komentar

### Untuk Admin

1. **Kelola Laporan**
   - Verifikasi laporan baru
   - Edit/hapus laporan yang tidak sesuai
   - Pantau status pencocokan

2. **Kelola Pengguna**
   - Lihat daftar pengguna
   - Ubah role pengguna
   - Nonaktifkan akun jika diperlukan

---

## 🧪 Testing

- Unit testing untuk fungsi-fungsi kritis
- User acceptance testing
- Cross-browser testing
- Responsive design testing

---

## 📚 Referensi & Resources

- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)
- [JavaScript MDN](https://developer.mozilla.org/en-US/docs/Web/JavaScript)

---

## 📄 License

Project ini dibuat untuk memenuhi tugas Praktikum Pemrograman Web 2025.

---

## 📞 Kontak

Untuk pertanyaan atau saran, hubungi salah satu anggota kelompok di atas.

---

**Universitas Lampung - Praktikum Pemrograman Web 2025**
