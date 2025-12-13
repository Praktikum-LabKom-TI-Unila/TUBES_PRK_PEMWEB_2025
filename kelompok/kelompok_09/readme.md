# 🍱 ZeroWaste - Food Rescue Platform

**ZeroWaste** adalah aplikasi marketplace sosial berbasis web yang bertujuan mendistribusikan makanan berlebih (sisa event, katering, atau pribadi) dari donatur kepada mahasiswa.

Proyek ini dikembangkan sebagai **Final Project Pemrograman Web** dengan fokus pada tema **No. 6: Smart City & Environment**, sebagai solusi digital untuk mengurangi limbah pangan (*food waste*) di lingkungan kampus melalui sistem klaim yang adil dan terukur.

[**🚀 Lihat Live Demo**](https://zerowaste.exyluminate.my.id)

-----

## 👥 Anggota Kelompok 09

| NIM | Nama Anggota | Role & Tanggung Jawab |
| :--- | :--- | :--- |
| **2315061071** | **Alexandrio Ariel Rafidan** | **Lead & Frontend** (UI/UX, Tailwind, Integration) |
| **2315061007** | **Naisyah Nopriani** | **Backend Auth** (Login, Register, Session) |
| **2315061024** | **Nadjwa Tasya Safira** | **Donatur Features** (CRUD Makanan, Stok Management) |
| **2315061050** | **Andhika Junion L** | **Logic & Transaction** (Klaim, Limit Kuota, Tiketing) |

-----

## ✨ Fitur Unggulan

Aplikasi ini dirancang dengan alur kerja *marketplace* namun tanpa transaksi uang (gratis), dengan fitur keamanan logika yang ketat:

  * **♻️ Food Rescue Catalog** — Katalog makanan *real-time* dengan fitur pencarian AJAX dan filter kategori/jenis (Halal/Non-Halal).
  * **⏳ Smart Expiration Logic** — Sistem otomatis mendeteksi makanan basi. Status makanan akan berubah otomatis dan tidak muncul di katalog jika lewat batas waktu.
  * **⚖️ Fair Claim System** — Mahasiswa dibatasi maksimal **2 klaim per hari** untuk pemerataan distribusi.
  * **🛡️ Auto-Ban Mechanism** — Jika tiket tidak diambil dalam **30 menit**, tiket hangus, stok kembali ke donatur, dan akun mahasiswa otomatis dinonaktifkan (Simulasi kedisiplinan).
  * **🎟️ Ticketing System** — Generate kode unik untuk setiap pengambilan yang harus diverifikasi oleh donatur.
  * **📊 Interactive Dashboard** — Dashboard khusus untuk Admin, Donatur, dan Mahasiswa dengan statistik visual.

-----

## 💻 Cara Penggunaan (Live Demo)

Aplikasi sudah di-hosting dan dapat diakses secara publik. Berikut adalah akses untuk pengujian fitur:

**URL Website:** [https://zerowaste.exyluminate.my.id](https://zerowaste.exyluminate.my.id)

### 🔑 Akun Testing

Silakan gunakan kredensial berikut untuk mencoba berbagai *role*:

| Role | Username | Password | Deskripsi Akses |
| :--- | :--- | :--- | :--- |
| **Mahasiswa** | `mahasiswa` | `112233` | Bisa klaim makanan, lihat tiket, batalkan pesanan. |
| **Donatur** | `donatur` | `123123` | Bisa upload makanan, edit stok, verifikasi tiket. |
| **Admin** | `admin` | `123321` | Bisa kelola user, unbanned user, hapus data. |

> **Catatan:** Jika akun mahasiswa terkena *Auto-Ban* saat testing, silakan login sebagai **Admin** untuk mengaktifkannya kembali.

-----

## 🛠️ Teknologi yang Digunakan

Dibangun menggunakan *Native PHP* dengan struktur kode yang bersih dan terorganisir:

 
 
   Server-side logic, manajemen session, dan koneksi database. 
   Penyimpanan data relasional (User, Food Stocks, Claims). 
   Styling antarmuka modern dan responsif. 
   Filter katalog AJAX, Countdown timer, dan interaksi modal. 
   Menjalankan skrip otomatis per menit untuk cek tiket kadaluarsa. 

-----

## 📂 Alur Sistem (Dokumentasi Singkat)

1.  **Donasi:** Donatur mengupload foto dan detail makanan serta menentukan batas waktu (expired).
2.  **Klaim:** Mahasiswa memilih makanan di katalog. Jika kuota harian cukup, tiket diterbitkan.
3.  **Timer Berjalan:** Mahasiswa memiliki waktu **30 Menit** untuk datang ke lokasi donatur.
4.  **Verifikasi:**
      * **Berhasil:** Donatur memasukkan kode tiket -\> Transaksi Selesai -\> Stok berkurang permanen.
      * **Gagal/Telat:** Jika \> 30 menit belum diambil -\> Tiket Hangus -\> Stok kembali -\> User Banned.

-----

## ⚙️ Instalasi Lokal (Opsional)

Jika ingin menjalankan di `localhost`:

1.  Clone repository ini atau download ZIP.
2.  Import file `db_zerowaste.sql` ke phpMyAdmin.
3.  Sesuaikan file `config/database.php`:
    ```php
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "db_zerowaste";
    ```
4.  Jalankan di browser (misal: `http://localhost/zerowaste`).

-----

Copyright © 2025 Kelompok 09 - ZeroWaste Platform.