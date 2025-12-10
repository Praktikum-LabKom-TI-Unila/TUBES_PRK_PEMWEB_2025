# CleanSpot - Panduan Lengkap Implementasi

## 📁 Struktur Proyek Lengkap

```
kelompok_33/
├── db/
│   ├── schema.sql              ✅ SELESAI - Database schema lengkap
│   ├── config.php.example      ✅ SELESAI - Template konfigurasi
│   └── SETUP.md                ✅ SELESAI - Panduan setup
│
├── src/
│   ├── config.php              ✅ SELESAI - Konfigurasi database
│   ├── fungsi_helper.php       ✅ SELESAI - Fungsi helper
│   ├── helpers.php             ✅ SELESAI - Helper tambahan
│   ├── login.php               ⏳ PERLU DIBUAT
│   ├── register.php            ✅ SELESAI
│   ├── logout.php              ⏳ PERLU DIBUAT
│   │
│   ├── admin/                  Dashboard Admin
│   │   ├── beranda_admin.php         ⏳ PERLU DIBUAT - Dashboard & statistik
│   │   ├── laporan_admin.php         ⏳ PERLU DIBUAT - Daftar laporan
│   │   ├── detail_laporan_admin.php  ⏳ PERLU DIBUAT - Detail & assign
│   │   ├── kelola_pengguna.php       ⏳ PERLU DIBUAT - Manajemen user
│   │   └── log_aktivitas.php         ⏳ PERLU DIBUAT - Audit log
│   │
│   ├── petugas/                Dashboard Petugas
│   │   ├── beranda_petugas.php       ⏳ PERLU DIBUAT - Dashboard tugas
│   │   ├── tugas_saya.php            ⏳ PERLU DIBUAT - Daftar tugas
│   │   └── detail_tugas.php          ⏳ PERLU DIBUAT - Detail & update
│   │
│   ├── warga/                  Dashboard Warga
│   │   ├── buat_laporan.php          ⏳ PERLU DIBUAT - Form laporan
│   │   ├── laporan_saya.php          ⏳ PERLU DIBUAT - Daftar laporan
│   │   └── detail_laporan.php        ⏳ PERLU DIBUAT - Lihat detail
│   │
│   ├── api/                    API Endpoints
│   │   ├── map_data.php              ⏳ PERLU DIBUAT - Data untuk peta
│   │   ├── statistik_data.php        ⏳ PERLU DIBUAT - Data chart
│   │   │
│   │   ├── admin/
│   │   │   ├── ambil_laporan.php     ✅ SELESAI - List laporan
│   │   │   ├── detail_laporan.php    ✅ SELESAI - Detail laporan
│   │   │   ├── tugaskan_petugas.php  ⏳ PERLU DIBUAT - Assign petugas
│   │   │   ├── verifikasi_laporan.php ⏳ PERLU DIBUAT - Verifikasi selesai
│   │   │   ├── ambil_pengguna.php    ⏳ PERLU DIBUAT - List user
│   │   │   └── ambil_log.php         ⏳ PERLU DIBUAT - Audit log
│   │   │
│   │   ├── petugas/
│   │   │   ├── ambil_tugas.php       ⏳ PERLU DIBUAT - List tugas
│   │   │   ├── terima_tugas.php      ⏳ PERLU DIBUAT - Accept tugas
│   │   │   ├── mulai_tugas.php       ⏳ PERLU DIBUAT - Start tugas
│   │   │   └── selesaikan_tugas.php  ⏳ PERLU DIBUAT - Complete + upload
│   │   │
│   │   └── warga/
│   │       ├── buat_laporan.php      ⏳ PERLU DIBUAT - Submit laporan
│   │       └── ambil_laporan_saya.php ⏳ PERLU DIBUAT - List laporan user
│   │
│   ├── aset/
│   │   ├── styles.css                ✅ SELESAI - Styling global
│   │   └── js/
│   │       ├── admin_dashboard.js    ⏳ PERLU DIBUAT - Chart & map admin
│   │       ├── petugas_tugas.js      ⏳ PERLU DIBUAT - Interaksi petugas
│   │       └── warga_laporan.js      ⏳ PERLU DIBUAT - Form & map warga
│   │
│   └── screenshots/                  Screenshots untuk dokumentasi
│
└── uploads/                          File upload
    ├── laporan/                      Foto laporan warga
    └── bukti/                        Bukti penanganan petugas
```

---

## 🎯 Prioritas Pengerjaan

### Sprint 1: Infrastruktur (SELESAI ✅)
- [x] Database schema
- [x] Config & helper functions
- [x] Folder structure

### Sprint 2: Authentication & API Core
- [ ] `src/login.php` - Halaman login
- [ ] `src/logout.php` - Logout handler
- [ ] `src/api/admin/tugaskan_petugas.php`
- [ ] `src/api/petugas/ambil_tugas.php`
- [ ] `src/api/warga/buat_laporan.php`

### Sprint 3: Dashboard Admin
- [ ] `src/admin/beranda_admin.php` - Statistik & peta
- [ ] `src/admin/laporan_admin.php` - Tabel laporan
- [ ] `src/admin/detail_laporan_admin.php` - Detail + assign
- [ ] `src/aset/js/admin_dashboard.js` - Chart.js & Leaflet

### Sprint 4: Dashboard Petugas
- [ ] `src/petugas/beranda_petugas.php`
- [ ] `src/petugas/tugas_saya.php`
- [ ] `src/petugas/detail_tugas.php`
- [ ] `src/api/petugas/*` - Semua endpoint petugas

### Sprint 5: Dashboard Warga
- [ ] `src/warga/buat_laporan.php`
- [ ] `src/warga/laporan_saya.php`
- [ ] `src/aset/js/warga_laporan.js` - Leaflet untuk lokasi

---

## 📝 Template & Contoh Kode

### Template Halaman Dashboard

```php
<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../fungsi_helper.php';

// Cek role
cek_role('admin'); // atau 'petugas' / 'warga'

$user = get_user_info();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - CleanSpot</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php tampilkan_pesan_flash(); ?>
    
    <nav class="bg-green-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">CleanSpot</h1>
            <div>
                <span><?= htmlspecialchars($user['nama']) ?></span>
                <a href="/src/logout.php" class="ml-4 text-white">Logout</a>
            </div>
        </div>
    </nav>
    
    <main class="container mx-auto p-6">
        <!-- Konten halaman di sini -->
    </main>
    
    <script src="/src/aset/js/[nama-file].js"></script>
</body>
</html>
```

### Template API Endpoint

```php
<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../fungsi_helper.php';

// Cek role
cek_role('admin'); // sesuaikan

header('Content-Type: application/json');

try {
    // Validasi input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Proses data
    // ... kode logika ...
    
    // Catat log aktivitas
    catat_log($pdo, $_SESSION['user_id'], 'AKSI_DILAKUKAN', 'laporan', $id);
    
    // Response sukses
    json_response([
        'success' => true,
        'message' => 'Berhasil',
        'data' => $result
    ]);
    
} catch (Exception $e) {
    json_response([
        'success' => false,
        'message' => $e->getMessage()
    ], 500);
}
```

### Template JavaScript untuk Chart

```javascript
// admin_dashboard.js
document.addEventListener('DOMContentLoaded', function() {
    // Load statistik
    fetch('/src/api/statistik_data.php')
        .then(res => res.json())
        .then(data => {
            // Render chart dengan Chart.js
            const ctx = document.getElementById('chartStatus').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Baru', 'Diproses', 'Selesai'],
                    datasets: [{
                        data: [data.baru, data.diproses, data.selesai],
                        backgroundColor: ['#EF4444', '#F59E0B', '#10B981']
                    }]
                }
            });
        });
    
    // Load data peta
    fetch('/src/api/map_data.php')
        .then(res => res.json())
        .then(data => {
            // Render peta dengan Leaflet
            const map = L.map('map').setView([-5.45, 105.26], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            
            data.forEach(laporan => {
                L.marker([laporan.lat, laporan.lng])
                    .bindPopup(laporan.judul)
                    .addTo(map);
            });
        });
});
```

---

## 🔧 Yang Sudah Dibuat

### 1. Database Schema (`db/schema.sql`)
- ✅ Tabel pengguna
- ✅ Tabel laporan
- ✅ Tabel foto_laporan
- ✅ Tabel penugasan (dengan status_penugasan)
- ✅ Tabel bukti_penanganan
- ✅ Tabel log_aktivitas
- ✅ Tabel komentar
- ✅ Semua foreign keys & indexes

### 2. Helper Functions (`src/fungsi_helper.php`)
- ✅ `catat_log()` - Logging aktivitas
- ✅ `cek_login()` - Cek autentikasi
- ✅ `cek_role()` - Cek authorization
- ✅ `upload_file()` - Upload dengan validasi
- ✅ `format_tanggal()` - Format tanggal Indonesia
- ✅ `json_response()` - Response API
- ✅ `redirect_dengan_pesan()` - Flash message
- ✅ `tampilkan_pesan_flash()` - Display flash

### 3. API Admin (Partial)
- ✅ `ambil_laporan.php` - Pagination & filter
- ✅ `detail_laporan.php` - Detail lengkap

---

## 🚀 Cara Lanjutkan Pengerjaan

### Untuk Dimas (Dashboard Admin):
1. Buat `src/admin/beranda_admin.php` - gunakan template di atas
2. Buat `src/api/statistik_data.php` - query COUNT laporan per status/kategori
3. Buat `src/api/map_data.php` - query semua laporan dengan lat/lng
4. Buat `src/aset/js/admin_dashboard.js` - integrate Chart.js & Leaflet
5. Buat halaman laporan & detail

### Untuk Alda (User Management):
1. Update `src/login.php` & `register.php` 
2. Buat `src/admin/kelola_pengguna.php`
3. Buat `src/api/admin/ambil_pengguna.php`
4. Tambah fitur ubah role user

### Untuk Nabila (Laporan):
1. Sudah ada `LaporanSampah.php` - bisa digabungkan
2. Buat `src/warga/buat_laporan.php` dengan upload foto
3. Buat `src/api/warga/buat_laporan.php`
4. Integrate Leaflet untuk pilih lokasi

### Untuk Alyaa (Penanganan):
1. Buat semua file di `src/petugas/`
2. Buat semua API di `src/api/petugas/`
3. Fitur upload bukti penanganan
4. Update status penugasan

---

## 📚 Library yang Digunakan

### CSS Framework
```html
<script src="https://cdn.tailwindcss.com"></script>
```

### Chart.js untuk Grafik
```html
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
```

### Leaflet untuk Peta
```html
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
```

---

## ✅ Checklist Fitur

### Admin
- [ ] Login/logout
- [ ] Dashboard statistik (chart)
- [ ] Peta semua laporan
- [ ] Daftar laporan (tabel + filter)
- [ ] Detail laporan
- [ ] Assign petugas
- [ ] Verifikasi selesai
- [ ] Kelola pengguna
- [ ] Audit log

### Petugas
- [ ] Login/logout
- [ ] Dashboard tugas
- [ ] List tugas ditugaskan
- [ ] Terima tugas
- [ ] Mulai tugas
- [ ] Upload bukti
- [ ] Selesaikan tugas
- [ ] Riwayat tugas

### Warga
- [ ] Register
- [ ] Login/logout
- [ ] Buat laporan + foto
- [ ] Pilih lokasi di peta
- [ ] List laporan saya
- [ ] Lihat status laporan
- [ ] Komentar laporan

---

## 🎨 Design Tips

- Gunakan warna hijau (#10B981) untuk tema CleanSpot
- Merah untuk status "baru"
- Kuning untuk "diproses"
- Hijau untuk "selesai"
- Card-based layout untuk dashboard
- Responsive design dengan Tailwind

---

**Status Proyek:** 30% selesai (Database + Helper + Struktur folder)  
**Next Step:** Buat API endpoints & halaman dashboard

Silakan pilih mana yang ingin dikerjakan terlebih dahulu, saya akan bantu generate kode lengkapnya!
