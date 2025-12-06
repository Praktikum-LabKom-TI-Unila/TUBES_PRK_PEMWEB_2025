# Menjalankan `database.sql`

Panduan singkat untuk mengeksekusi seluruh skema & constraint pada berkas `database.sql`.

## 1. Pastikan Server MySQL Aktif

- Jalankan MySQL/MariaDB lokal Anda dan ingat kredensialnya.
- Buat database kosong bernama `npc` (atau ubah sesuai kebutuhan).
  ```sql
  CREATE DATABASE IF NOT EXISTS npc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  ```

## 2. Gunakan MySQL CLI

Eksekusi perintah berikut dari direktori `src/database`:

```bash
mysql -u root -p npc < database.sql
```

Penjelasan:

- `-u root -p` menyesuaikan user/password MySQL Anda.
- `npc` adalah nama database tujuan.
- Operator `<` meminta MySQL menjalankan seluruh isi `database.sql`.

## 3. Alternatif: MySQL Client GUI

1. Buka aplikasi MySQL Workbench.
2. Pilih schema `npc` sebagai database aktif.
3. Buka file `database.sql`, jalankan seluruh script (Ctrl/Cmd + Shift + Enter).

## 4. Verifikasi

Setelah impor, jalankan query cek foreign key:

```sql
SELECT table_name, column_name, constraint_name,
       referenced_table_name, referenced_column_name
FROM information_schema.KEY_COLUMN_USAGE
WHERE table_schema = 'npc'
  AND referenced_table_name IS NOT NULL
ORDER BY table_name, column_name;
```

Jika tidak ada error, seluruh tabel, index, dan relasi sudah siap dipakai aplikasi.
