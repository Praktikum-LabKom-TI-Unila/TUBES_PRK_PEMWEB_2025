# SiPEMAU Backend - PHP Native

Backend untuk Sistem Pengaduan Mahasiswa Universitas Lampung (SiPEMAU) menggunakan PHP Native dan MySQL.

## Struktur Folder

```
backend/
├── config/           # Konfigurasi aplikasi
│   ├── app.php      # Konfigurasi umum
│   └── database.php # Konfigurasi database
├── core/            # Core framework
│   ├── Controller.php
│   ├── Router.php
│   └── Helper.php
├── modules/         # Modul aplikasi
│   ├── auth/        # Authentication
│   ├── mahasiswa/   # Fitur mahasiswa
│   ├── petugas/     # Fitur petugas
│   ├── admin/       # Fitur admin
│   └── pages/       # Public pages
├── public/          # Public directory (document root)
│   ├── index.php    # Entry point
│   └── .htaccess    # Apache config
└── assets/          # Assets
    └── uploads/     # Upload directory
```

## Setup

### Requirements

- PHP 7.4 atau lebih tinggi
- MySQL 8.0 atau MariaDB 10.5
- Apache dengan mod_rewrite enabled
- Extension: PDO, PDO_MySQL

### Instalasi

1. **Import Database**

   ```bash
   mysql -u root -p < database/SIPEMAU.sql
   ```

2. **Konfigurasi Database**

   Edit `config/database.php`:

   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'sipemau_db');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

3. **Konfigurasi Base URL**

   Edit `config/app.php`:

   ```php
   define('BASE_URL', 'http://localhost/path/to/backend/public');
   ```

4. **Set Permissions**

   ```bash
   chmod 755 assets/uploads
   ```

5. **Apache Configuration**

   Set document root ke folder `public/`:

   ```apache
   <VirtualHost *:80>
       DocumentRoot "/path/to/backend/public"
       <Directory "/path/to/backend/public">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

## Fitur

### Mahasiswa

- ✅ Registrasi dan login
- ✅ Buat pengaduan (dengan upload bukti)
- ✅ Lihat daftar pengaduan
- ✅ Lihat detail pengaduan dan status

### Petugas

- ✅ Login (akun dibuat oleh admin)
- ✅ Dashboard laporan unit
- ✅ Update status pengaduan
- ✅ Tambah catatan tindak lanjut

### Admin

- ✅ Dashboard monitoring
- ✅ CRUD Unit
- ✅ CRUD Kategori
- ✅ CRUD Akun Petugas

## Routing

### Public Routes

- `GET /` - Landing page
- `GET /about` - About page
- `GET /login` - Login page
- `POST /login` - Process login
- `GET /register` - Register page
- `POST /register` - Process registration

### Mahasiswa Routes

- `GET /mahasiswa/dashboard` - Dashboard
- `GET /mahasiswa/complaints` - List complaints
- `GET /mahasiswa/complaints/create` - Create form
- `POST /mahasiswa/complaints/create` - Store complaint
- `GET /mahasiswa/complaints/:id` - Detail complaint

### Petugas Routes

- `GET /petugas/dashboard` - Dashboard
- `GET /petugas/complaints` - List complaints
- `GET /petugas/complaints/:id` - Detail complaint
- `POST /petugas/complaints/update-status` - Update status
- `POST /petugas/complaints/add-note` - Add note

### Admin Routes

- `GET /admin/dashboard` - Dashboard
- `GET /admin/units` - Manage units
- `GET /admin/categories` - Manage categories
- `GET /admin/petugas` - Manage petugas

## Security Features

- ✅ Password hashing dengan bcrypt
- ✅ Prepared statements (SQL injection prevention)
- ✅ Input sanitization
- ✅ CSRF token support
- ✅ Role-based access control
- ✅ File upload validation

## Default Accounts

Setelah setup, Anda perlu membuat akun admin secara manual:

```sql
-- Insert admin user
INSERT INTO users (name, email, password_hash, role)
VALUES ('Administrator', 'admin@sipemau.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN');

-- Get the user ID
SET @admin_id = LAST_INSERT_ID();

-- Insert admin profile
INSERT INTO admin (id, level) VALUES (@admin_id, 'superadmin');
```

Password default: `password` (segera ganti setelah login)

## API Response Format

Untuk AJAX requests, gunakan header `Accept: application/json`:

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {}
}
```

## Development

Untuk development mode, aktifkan error display di `public/index.php`:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

**⚠️ DISABLE untuk production!**

## License

Tugas Besar Praktikum Pemrograman Web 2025
