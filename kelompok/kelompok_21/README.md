# 🚀 ScholarBridge
**Platform Penghubung Siswa & Tutor Mahasiswa Berprestasi**

> **Final Project Praktikum Pemrograman Web - Kelompok 21** > Tema: *Innovation in Education*

---

## 👥 Anggota Kelompok

| No | Nama | NPM |
| :--- | :--- | :--- | 
| 1. | **Dhito Aryo Trengginas** | **2315061015** |
| 2. | Saif Abdullah | 2315061109 |
| 3. | M. Aidil Fikri | 2315061130 | 
| 4. | Luthfi Muthathohirin | 2315061112 | 

---

## 📖 Deskripsi Proyek

**ScholarBridge** adalah platform marketplace jasa edukasi yang dirancang untuk menjembatani kesenjangan antara siswa sekolah yang membutuhkan bimbingan belajar dengan mahasiswa universitas berprestasi yang mencari pengalaman mengajar.

Aplikasi ini dibangun menggunakan arsitektur **MVC Sederhana** dengan pemisahan logika (Backend) dan tampilan (Frontend) untuk menjaga kerapian kode.

### Fitur Utama:
1.  **Admin:** Validasi pendaftaran tutor (Cek KTM) & manajemen user.
2.  **Public:** Pencarian tutor real-time (AJAX) berdasarkan mata pelajaran & harga.
3.  **Tutor (Mahasiswa):** Kelola iklan kelas, upload dokumen verifikasi, terima pesanan.
4.  **Learner (Siswa):** Booking jadwal tutor, riwayat belajar, rating & review.

---

## 🛠️ Teknologi yang Digunakan

* **Frontend:** HTML5, CSS3, JavaScript (Native)
* **Backend:** PHP (Native/Procedural)
* **Database:** MySQL
* **Version Control:** Git & GitHub

---

## 📂 Struktur Folder

Struktur repositori ini mengikuti aturan tugas akhir laboratorium:

```text
kelompok_21/
├── database/               # File SQL (scholarbridge.sql) & ERD
├── images/            # Bukti tampilan aplikasi
├── src/                    # Source code utama
│   ├── config/             # Koneksi database
│   ├── backend/            # Logika PHP (Auth, Admin, Tutor, Learner)
│   ├── frontend/           # Tampilan (Assets, Layouts, Pages)
│   └── index.php           # Entry point
└── README.md               # Dokumentasi ini
