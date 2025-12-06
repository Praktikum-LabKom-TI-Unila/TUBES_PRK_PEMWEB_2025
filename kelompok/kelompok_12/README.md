# Sistem Manajemen Operasional dan Keuangan Fotocopy

Proyek ini bertujuan membantu pengelola usaha fotokopi kecil-menengah dalam mengawasi operasional harian, pencatatan transaksi layanan, dan arus kas masuk/keluar secara terpadu sehingga keputusan bisnis bisa diambil berbasis data.

## Anggota Kelompok 12

| NPM        | Nama                          |
| ---------- | ----------------------------- |
| 2315061115 | M. Azmi Edfa Alhafizh         |
| 2315061051 | Arza Restu Arjuna             |
| 2315061116 | Muhamad Rakha Hadyan Pangestu |
| 2315061018 | Elthon Jhon Kevin             |

## Ringkasan Fitur

- **Manajemen Layanan:** pendataan paket fotokopi, print, jilid, dan layanan tambahan sesuai kebutuhan cabang.
- **Pencatatan Operasional:** input bahan/material, pengeluaran harian, jadwal shift operator, serta status mesin.
- **Keuangan & Pelaporan:** rekap pemasukan harian/bulanan, pengeluaran, margin, serta laporan yang siap diunduh.
- **Hak Akses Pengguna:** akun owner, operator, dan kasir dengan batasan akses berbeda.

## Struktur Direktori

```
kelompok_12/
├── src/
│   ├── backend/    # REST API PHP (router, controller, model, dokumentasi API)
│   ├── frontend/   # Aset/UI klien (akan diisi saat pengembangan antarmuka)
│   └── resources/  # Artefak pendukung (ERD, SQL, screenshot)
│       ├── database/
│       ├── erd/
│       └── screenshots/
└── README.md
```

## Prasyarat

- PHP 8.1+ dengan ekstensi PDO MySQL aktif.
- MySQL Server (disarankan 8.x).
- Web browser modern (Chrome/Firefox/Edge).

## Cara Menjalankan Secara Lokal

1. **Kloning repositori & masuk ke folder kelompok.**
   ```bash
   git clone <repo-anda>.git
   cd TUBES_PRK_PEMWEB_2025/kelompok/kelompok_12
   ```
2. **Import struktur database.**
   ```bash
   mysql -u root -p npc < src/resources/database/database.sql
   ```
3. **Atur koneksi database** (opsional bila tidak memakai kredensial bawaan).
   - Default berada pada `127.0.0.1:3306`, DB `npc`, user `root`, tanpa sandi.
   - Override dengan mengekspor variabel lingkungan sebelum menjalankan server:
     ```bash
     export DB_HOST=127.0.0.1
     export DB_PORT=3306
     export DB_DATABASE=npc
     export DB_USERNAME=root
     export DB_PASSWORD=your_password
     ```
4. **Seed akun default (owner/staff/member).**
   ```bash
   cd src/backend
   php seed_users.php
   ```
5. **Jalankan server PHP built-in (pada folder backend).**
   ```bash
   cd src/backend
   php -S localhost:8000 -t .
   ```
6. **Akses API** lewat `http://localhost:8000` dan login menggunakan akun default (mis. `owner_default` / `owner123`) atau akun lain yang Anda buat.

## Catatan Pengembangan

- ERD, screenshot, dan skrip SQL tersedia dalam `src/resources/`.
- Dokumentasi API backend (termasuk login & registrasi) disimpan terpisah di `src/backend/api-docs/openapi.yaml`.
- Gunakan branch/commit terpisah untuk fitur baru supaya integrasi ke CI laboratorium berjalan lancar.

---

> Laboratorium Teknik Komputer — Final Project Praktikum Pemrograman Web 2025
