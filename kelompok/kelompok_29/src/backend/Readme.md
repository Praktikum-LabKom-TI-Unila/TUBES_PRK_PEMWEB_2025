# SIPINDA Backend

## Direktori Upload

- File unggahan pelapor disimpan di `uploads/profile_photos` dan `uploads/complaints` (sudah disertakan dengan berkas `.gitkeep`).
- Pastikan kedua folder tersebut **writable** oleh user yang menjalankan PHP/web server (contoh: `chmod 775 uploads uploads/profile_photos uploads/complaints`).
- Jika service dijalankan oleh user lain (mis. `www-data` di Debian/Ubuntu), jalankan `sudo chown -R www-data:www-data uploads` agar PHP bisa melakukan `rename()` dan `copy()`.
- Jika server memakai konfigurasi selain `backend/public` sebagai document root, sesuaikan path statis agar URL `uploads/...` dapat diakses publik.

Contoh jika document root server Anda adalah `/var/www/backend/public` tetapi repositori disimpan di tempat lain:

```bash
sudo ln -s /home/rmyd/projects/sipinda/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_29/src/backend/uploads /var/www/backend/uploads
sudo chown -R http:http /var/www/backend/uploads
sudo chmod -R 775 /var/www/backend/uploads
```

### Mengakses file upload via URL

- File disimpan relatif terhadap root proyek, contoh path `uploads/profile_photos/upload_xxx.png`.
- Jika `backend/public` dijadikan document root web server, cukup akses `https://domain-anda/uploads/profile_photos/upload_xxx.png`.
- Jika document root berada di lokasi lain, buat symlink atau konfigurasi alias web server yang mengarah ke folder `uploads` agar path tersebut dapat diakses publik.
