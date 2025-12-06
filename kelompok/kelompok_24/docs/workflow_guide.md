# ⚔️ PROTOKOL PENGEMBANGAN WARKOPS (KELOMPOK 24)

Dokumen ini adalah panduan wajib bagi seluruh operator (anggota tim) dalam berkontribusi kode ke repository WarkOps.

## 🚫 ATURAN EMAS (THE GOLDEN RULES)

1. **JANGAN PERNAH PUSH KE BRANCH `master` ATAU `setup` LANGSUNG!**
   * Selalu kerja di branch fitur sendiri. Branch `setup` adalah "Branch Suci" yang terhubung ke Asisten Lab.

2. **PULL SEBELUM KERJA.**
   * Kodingan di branch `setup` bisa berubah setiap jam. Update dulu sebelum mulai ngetik.

3. **SATU FITUR = SATU BRANCH.**
   * Jangan campur aduk (misal: fitur login digabung sama fitur stok barang). Pisahkan biar gampang dicek.

---

## 🛠️ PERSIAPAN AWAL (Hanya Sekali)

Jika belum clone repository ketua, lakukan ini:

1. Buka Terminal / Git Bash.

2. Jalankan command ini (Pastikan clone punya **hino89**, BUKAN punya Lab):
```bash
   git clone https://github.com/hino89/TUBES_PRK_PEMWEB_2025.git
   ```

3. Masuk folder : 
```bash
   cd TUBES_PRK_PEMWEB_2025
   ```

4. Pindah ke branch utama kita (setup):
```bash
   git checkout setup
   ```

---

## 🔄 SIKLUS KERJA HARIAN (DAILY ROUTINE)

Setiap kali mau ngoding, WAJIB ikuti urutan langkah ini:

1. Update Kodingan (Sync)
```bash
   git checkout setup
   git pull origin setup
   ```

2. Jalankan command ini (Pastikan clone punya **hino89**, BUKAN punya Lab):
```bash
   # Pastikan sedang di setup
   git checkout setup

   # Buat branch fitur baru
   git checkout -b fitur/nama-fitur-kamu
   ```

3. Ngoding (Coding Phase)
Silakan bekerja sesuai Role masing-masing di folder src/.
   1. Backend: Fokus di src/api/. Test output JSON di browser.
   2. Frontend: Fokus di src/views/ dan src/js/. Gunakan warna dari theme.js.
   3. Database: Update database.sql jika ada tabel baru.

4. Simpan Perubahan (Save & Commit)
```bash
   git add .
   git commit -m "Pesan commit yang jelas (misal: Menyelesaikan fitur login backend)"
   ```

5. Upload ke GitHub (Push)
Kirim branch fiturmu ke repo fork ketua.
```bash
   git push -u origin fitur/nama-fitur-kamu
   ```

6. Lapor ke Ketua (Pull Request)
    1. Buka GitHub repo ketua: https://github.com/hino89/TUBES_PRK_PEMWEB_2025
    2. Klik tombol hijau "Compare & pull request".
    3. PENTING (Perhatikan Targetnya):
        - Base: setup (punya hino89) ⬅️ Jangan pilih main/master!
        - Compare: fitur/nama-fitur-kamu
    4. Klik Create Pull Request.