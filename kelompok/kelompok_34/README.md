# LokaPOS: Sistem Point of Sale untuk Penjualan UMKM

## Daftar Anggota
| No | Nama                                   | NPM         |
|----|----------------------------------------|-------------|
| 1  | Muhammad Zachrie Kurniawan             | 2315061113  |
| 2  | Aleea Carisa Danielle Kalalo           | 2315061023  |
| 3  | Anggun Azqiyah Azzahra                 | 2355061005  |
| 4  | Lutfiya Fakhira                        | 2315061085  |
| 5  | Nur Aila Zahra                         | 2315061035  |

---

## Judul Proyek  
**LokaPOS: Sistem Point of Sale untuk Penjualan UMKM**

---

## Ringkasan Proyek  
LokaPOS adalah aplikasi Point of Sale yang berfokus pada kebutuhan penjual UMKM dalam mengelola produk dan transaksi penjualan secara lebih cepat, akurat, dan modern. Sistem ini juga memberikan pengalaman belanja digital sederhana bagi pembeli.
Fitur Utama:
- Login & Registrasi
- Kelola Produk (Tambah, Edit, Hapus)
- Transaksi Penjualan (Kasir)
- Keranjang Belanja & Checkout
- Riwayat Transaksi
- Pencarian Produk
- Dashboard Penjualan

---

## Cara Menjalankan Aplikasi

### Persyaratan Sistem
- PHP minimal versi 8.2.4 atau lebih tinggi
- MySQL/MariaDB
- Web Server (Apache/Nginx), disarankan menggunakan **XAMPP/Laragon**
- Browser Web

### Langkah Instalasi
1. Clone atau download repository
   ```bash
   git clone https://github.com/zachriek/TUBES_PRK_PEMWEB_2025.git
   ```

2. Pindahkan folder project ke dalam direktori web server:
   * Jika menggunakan XAMPP ➜ `C:\xampp\htdocs\TUBES_PRK_PEMWEB_2025\kelompok\kelompok_34\src`
   
3. Import database:
   * Buka **phpMyAdmin**
   * Buat database: `lokapos`
   * Import file `lokapos.sql` yang berada di folder `database/`

4. Konfigurasi koneksi database di file:
   ```
   src/config/database.php
   ```

   Sesuaikan:
   ```php
   $host = "localhost";
   $user = "root";
   $pass = "";
   $dbname = "lokapos";
   ```

5. Jalankan aplikasi melalui browser:
   ```
   http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_34/src/
   ```

---

## Akun Demo

| Role    | Email             | Password  |
| seller  | seller@gmail.com  | seller123 |

---