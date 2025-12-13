# 🚀 Codevis - Sistem Pembelajaran Interactive Coding

**Codevis** adalah platform pembelajaran berbasis web yang dirancang untuk membantu mahasiswa memahami konsep pemrograman secara visual dan interaktif. Aplikasi ini menghubungkan dosen dan mahasiswa dalam lingkungan kelas virtual yang terintegrasi dengan simulasi algoritma.

---

## 👥 Kelompok 08

Aplikasi ini dikembangkan untuk memenuhi tugas mata kuliah Pemrograman Web oleh tim:

| No  | Nama                       | NPM        | Peran            |
| :-- | :------------------------- | :--------- | :--------------- |
| 1.  | **A M Rama**               | 2315061117 | _Ketua kelompok_ |
| 2.  | **M. Valerian Irwansyah**  | 2315061027 | _Anggota_        |
| 3.  | **Viola Putri Nurmadhani** | 2315061014 | _Anggota_        |
| 4.  | **Abrar Rafii Ibrahim**    | 2315061095 | _Anggota_        |

---

## 📖 Ringkasan Project

Codevis dibangun menggunakan **PHP Native** dan **MySQL** dengan antarmuka modern menggunakan **HTML, Tailwind CSS, dan Native JS**. Sistem ini memiliki dua peran utama (Dosen & Mahasiswa) serta fitur unggulan berupa visualisasi struktur data dinamis.

### Fitur Utama:

1.  **Modul Dosen:**

    - Membuat dan mengelola kelas.
    - Membuat kuis pilihan ganda.
    - Memantau daftar mahasiswa dan rekap nilai per kelas.

2.  **Modul Mahasiswa:**

    - Bergabung ke kelas menggunakan _Class Code_.
    - Mengerjakan kuis/ujian secara online.
    - Melihat riwayat nilai (Score).
    - Akses ke materi visualisasi.

3.  **Visualisasi Struktur Data (Interactive):**
    - **Stack**
    - **Queue**
    - **Linked List**
    - **Array**
    - **Searching**
    - **Sorting**

---

## 💻 Cara Menjalankan Aplikasi

Ikuti langkah-langkah berikut untuk menjalankan project ini di komputer lokal (Localhost).

### 1. Persiapan (Prerequisites)

Pastikan Anda sudah menginstal aplikasi berikut:

- **Laragon** (Pastikan Apache & MySQL aktif).
- **Git** (Untuk cloning repo).
- **Web Browser** (Chrome/Edge/Firefox).

### 2. Instalasi dan Menjalankan Program

1.  Clone repositori ini:
    ```bash
    git clone (https://github.com/inthemiddleof/TUBES_PRK_PEMWEB_2025)
    ```
2.  Masuk ke folder project:
    ```bash
    cd TUBES_PRK_PEMWEB_2025
    ```
3.  Jalankan Laragon yang sudah ada MySql dan apache server.

### 3. Konfigurasi Database

1.  Buka **phpMyAdmin** di browser: `http://localhost/phpmyadmin`
2.  Buat database baru dengan nama: **`coding_interactive`**
3.  Klik tab **Import**, pilih file **`coding_interactive.sql`** yang ada di dalam folder project ini.
4.  Klik **Go/Kirim** untuk membuat tabel.

### 4. Konfigurasi Koneksi (Opsional)

Jika Anda menggunakan password pada MySQL XAMPP Anda, edit file `config.php`:

```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "coding_interactive";
```
