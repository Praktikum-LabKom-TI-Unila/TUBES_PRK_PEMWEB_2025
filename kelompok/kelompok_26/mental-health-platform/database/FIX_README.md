# FIX Payment Table untuk Subscription Payments

## Masalah
Payment table memiliki foreign key constraint `payment_ibfk_2` yang membutuhkan `session_id` harus exist di `chat_session` table. Ini menghalangi subscription payments yang tidak terkait dengan chat session.

## Solusi
Jalankan migration berikut untuk:
1. Drop foreign key constraint lama
2. Make `session_id` nullable
3. Recreate foreign key dengan `ON DELETE SET NULL`

## Cara Menjalankan

### Option 1: Via phpMyAdmin
1. Buka http://localhost/phpmyadmin
2. Pilih database `mental_health_platform`
3. Click tab "SQL"
4. Copy-paste isi file `fix_payment_fk.sql`
5. Click "Go"

### Option 2: Via Command Line (jika MySQL di PATH)
```bash
mysql -u root -p mental_health_platform < database/fix_payment_fk.sql
```

### Option 3: Via Laragon MySQL Console
1. Buka Laragon
2. Click "MySQL" → "MySQL Console"
3. Run:
```sql
USE mental_health_platform;
SOURCE X:/System/laragon/www/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_26/mental-health-platform/database/fix_payment_fk.sql
```

## Setelah Migration
Test payment flow:
1. Login ke aplikasi
2. Buka halaman Payment
3. Pilih paket (daily/weekly/monthly)
4. Should see "Paket berhasil dipilih"
5. Upload bukti pembayaran
6. Subscription langsung aktif!

## Catatan
- Migration ini SAFE - tidak menghapus data existing
- Subscription payments akan punya `session_id = NULL`
- Chat session payments tetap bisa pakai `session_id` yang valid
