# Sistem Manajemen Operasional & Keuangan NPC

## 👥 Daftar Anggota Kelompok 04

| NPM        | Nama                          |
| ---------- | ----------------------------- |
| 2315061115 | M. Azmi Edfa Alhafizh         |
| 2315061051 | Arza Restu Arjuna             |
| 2315061116 | Muhamad Rakha Hadyan Pangestu |
| 2315061018 | Elthon Jhon Kevin             |

## 📘 Judul & Ringkasan Proyek

**Judul:** Sistem Manajemen Operasional & Keuangan Nagoya Print & Copy

Portal berbasis PHP untuk mengelola order cetak, layanan kasir POS, stok ATK, serta pelaporan keuangan pada outlet fotokopi. Aplikasi menyediakan dashboard role-based (customer, staff, admin, owner), modul katalog/keranjang, manajemen produk & layanan, input pengeluaran, hingga laporan omset dengan grafik.

## 🚀 Cara Menjalankan di Windows (XAMPP)

1. **Salin proyek ke htdocs**
   - `git clone <url-repo>` atau ekstrak ZIP ke `C:\xampp\htdocs\Nagoya-Print-Copy`.
2. **Jalankan Apache & MySQL** dari XAMPP Control Panel.
3. **Import database**
   - Buka `http://localhost/phpmyadmin`.
   - Buat database misal `npc_printing_db`.
   - Import `sql/database.sql`.
4. **Atur koneksi** di `src\koneksi\database.php` (host `localhost`, user `root`, password kosong).
5. **Akses aplikasi** via `http://localhost/Nagoya-Print-Copy/index.php`.
6. **Login contoh**: `admin/admin123`, `staff/staff123`, `owner/owner123`, `edfa/123`.
