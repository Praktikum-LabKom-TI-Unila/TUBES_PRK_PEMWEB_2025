# 🧪 Testing Guide - CleanSpot

## 📋 Checklist Pre-Testing

### 1. XAMPP Setup
- [x] Apache: Running
- [ ] MySQL: **START DULU!** (via XAMPP Control Panel)

### 2. Database Setup
```sql
-- Buka phpMyAdmin: http://localhost/phpmyadmin
-- Cek database 'cleanspot_db' sudah ada?
-- Jika belum:
CREATE DATABASE cleanspot_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Import file: db/schema.sql
```

### 3. Seed Admin
Akses: `http://localhost/TUBES_PRK_PEMWEB_2025-KELOMPOK-33/kelompok/kelompok_33/src/seed_admin.php`

Jika berhasil, muncul pesan sukses.

---

## 🚀 URL Testing

### Base URL
```
http://localhost/TUBES_PRK_PEMWEB_2025-KELOMPOK-33/kelompok/kelompok_33/src/
```

### 1. Login Page ✅
```
http://localhost/TUBES_PRK_PEMWEB_2025-KELOMPOK-33/kelompok/kelompok_33/src/login_page.html
```

**Login Admin:**
- Email: `admin@cleanspot.com`
- Password: `admin123`

**Expected:** Redirect ke `admin/beranda_admin.php`

---

## 🧪 Test Scenarios

### Scenario 1: Admin Login & Dashboard
1. Buka login_page.html
2. Login dengan admin credentials
3. ✅ Harusnya redirect ke admin dashboard
4. ✅ Lihat 4 card statistik (total laporan, petugas, pelapor, penugasan)
5. ✅ Lihat 2 chart (status & kategori)
6. ✅ Lihat chart trend 12 bulan
7. ✅ Lihat peta (jika ada data dengan lat/lng)

**Cek Console Browser (F12):**
- Tidak ada error merah
- API response sukses

### Scenario 2: Register Warga Baru
1. Buka register_page.html
2. Isi form:
   - Nama: Test Warga
   - Email: warga@test.com
   - Password: test123
   - Konfirmasi: test123
3. ✅ Muncul alert success
4. ✅ Redirect ke login
5. Login dengan kredensial baru
6. ✅ Redirect ke warga dashboard

### Scenario 3: Warga Buat Laporan
1. Login sebagai warga
2. Klik "Buat Laporan"
3. Isi form:
   - Judul: "Test Laporan Sampah"
   - Deskripsi: "Testing sistem"
   - Kategori: Organik
   - Alamat: "Jl. Test No. 123"
   - Klik map untuk pin lokasi (opsional)
   - Upload foto (opsional)
4. Klik "Kirim Laporan"
5. ✅ Redirect ke laporan_saya.php
6. ✅ Laporan muncul di list

### Scenario 4: Admin Assign Petugas
**Prasyarat:** Buat user petugas dulu via database:
```sql
INSERT INTO pengguna (nama, email, password_hash, role, created_at, updated_at)
VALUES ('Petugas Test', 'petugas@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petugas', NOW(), NOW());
-- Password: password
```

**Steps:**
1. Login sebagai admin
2. Ke menu "Laporan"
3. Cari laporan berstatus "baru"
4. Klik "Tugaskan"
5. Pilih petugas, prioritas "tinggi", catatan
6. Klik "Tugaskan"
7. ✅ Muncul alert success
8. ✅ Status laporan berubah "diproses"

### Scenario 5: Petugas Tangani Tugas
1. Login sebagai petugas (petugas@test.com / password)
2. ✅ Lihat dashboard dengan statistik tugas
3. ✅ Lihat list tugas
4. Klik detail pada tugas
5. Terima → Mulai → Selesaikan (upload foto bukti)

---

## 🔍 Common Issues & Solutions

### Issue 1: "Database connection failed"
**Solusi:**
1. Cek MySQL service running
2. Cek `src/config.php`:
   ```php
   $username = 'root';
   $password = ''; // Kosong untuk XAMPP default
   ```

### Issue 2: "Call to undefined function catat_log()"
**Solusi:**
Pastikan di setiap file PHP ada:
```php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../fungsi_helper.php';
```

### Issue 3: Map tidak muncul
**Solusi:**
- Cek koneksi internet (Leaflet butuh CDN)
- Buka Console (F12), lihat error

### Issue 4: Chart tidak muncul
**Solusi:**
- Cek API `statistik_data.php` return data
- Buka: `http://localhost/.../api/statistik_data.php`
- Harusnya return JSON

### Issue 5: Upload foto gagal
**Solusi:**
1. Buat folder `uploads/` di root src:
   ```powershell
   New-Item -ItemType Directory -Path "kelompok/kelompok_33/src/uploads"
   New-Item -ItemType Directory -Path "kelompok/kelompok_33/src/uploads/laporan"
   New-Item -ItemType Directory -Path "kelompok/kelompok_33/src/uploads/bukti"
   ```

2. Cek php.ini:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```

---

## 📊 Expected Data Flow

```
1. Warga register → Database: pengguna (role=warga)
2. Warga login → Session created
3. Warga buat laporan → Database: laporan + foto_laporan
4. Admin assign → Database: penugasan + log_aktivitas
5. Petugas terima → Update: penugasan.status_penugasan = 'diterima'
6. Petugas selesai → Database: bukti_penanganan + Update laporan.status
```

---

## ✅ Testing Checklist

### Authentication
- [ ] Login admin works
- [ ] Login petugas works
- [ ] Login warga works
- [ ] Register warga works
- [ ] Logout works
- [ ] Role-based redirect works

### Admin Features
- [ ] Dashboard loads with charts
- [ ] Map shows markers
- [ ] Laporan table dengan pagination
- [ ] Filter laporan (status, kategori, search)
- [ ] Assign petugas modal works
- [ ] API calls successful

### Petugas Features
- [ ] Dashboard shows statistics
- [ ] Tugas list loads
- [ ] Accept task works
- [ ] Start task works
- [ ] Complete task with photo upload works

### Warga Features
- [ ] Dashboard shows statistics
- [ ] Buat laporan form works
- [ ] Map picker works
- [ ] Multiple photo upload works
- [ ] Laporan saya loads

### API Tests
```bash
# Test via curl atau browser
http://localhost/.../api/statistik_data.php
http://localhost/.../api/map_data.php
http://localhost/.../api/admin/ambil_laporan.php
```

---

## 🎯 Quick Test Commands

### Create Test Data (SQL)
```sql
-- Buat petugas
INSERT INTO pengguna (nama, email, password_hash, role, created_at, updated_at)
VALUES ('Petugas Test', 'petugas@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petugas', NOW(), NOW());

-- Buat laporan dummy
INSERT INTO laporan (pengguna_id, judul, deskripsi, kategori, alamat, status, created_at)
VALUES (1, 'Sampah menumpuk di Jl. Sudirman', 'Banyak sampah organik', 'organik', 'Jl. Sudirman No. 45', 'baru', NOW());
```

---

## 📱 Browser DevTools

### Network Tab
- Cek API response 200 OK
- Cek JSON format benar

### Console Tab
- Tidak ada error JavaScript
- Chart.js loaded
- Leaflet loaded

### Application Tab
- Session storage ada
- Cookie ada (jika pakai cookie)

---

**Happy Testing! 🧪**

Jika ada error, lihat di Console Browser (F12) atau cek error_log PHP.
