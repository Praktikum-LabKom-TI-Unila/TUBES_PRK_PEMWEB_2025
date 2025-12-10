# Inventory Manager – Kelompok 25

## Teknologi Utama
- PHP 8 (native, tanpa framework)
- Tailwind CSS (via CDN untuk pengembangan cepat)
- Router & Controller kustom
- PDO untuk akses database MySQL
- Struktur modular (views/layouts/partials) agar mudah di-scale

## Struktur Folder

```
kelompok_25/
├─ public/                      # Hanya direktori ini yang diakses browser
│  ├─ index.php                 # Front controller (semua request masuk sini)
│  ├─ .htaccess                 # Rewrite ke index.php (untuk Apache)
│  └─ assets/
│     ├─ css/app.css            # Style global
│     ├─ js/app.js             # Script global
│     ├─ js/modules/           # Script per fitur (auth/materials/stock/reports)
│     ├─ img/                  # Static assets
│     └─ uploads/materials/    # Foto bahan hasil upload
│
├─ src/
│  ├─ config/                  # Konfigurasi environment & koneksi DB
│  ├─ core/                    # Router, Base Controller, Auth helper, dll
│  ├─ routes/                  # `web.php` (view) & `api.php` (JSON)
│  ├─ models/                  # User, Role, Material, Supplier, Stock, dll
│  ├─ controllers/
│  │  ├─ web/                  # Controller yang merender view
│  │  └─ api/                  # Controller untuk request AJAX/JSON
│  ├─ views/                   # Layout, partial, dashboard, materials, dsb.
│  ├─ middleware/              # AuthMiddleware & RoleMiddleware
│  └─ helpers/                 # Utility (redirect, csrf, validator)
│
├─ tailwind.config.js
├─ package.json
└─ README.md
```

## Alur Singkat
1. Request masuk ke `public/index.php` lalu diteruskan ke Router.
2. Router mencocokkan path dengan `routes/web.php` (atau `routes/api.php`).
3. Middleware auth/role dijalankan jika dibutuhkan.
4. Controller mempersiapkan data, memanggil view (`views/...`) melalui `layouts/main.php` sehingga navbar dan sidebar otomatis ikut.
5. Asset CSS/JS di `public/assets` menangani tampilan dan interaksi ringan.

## Cara Menjalankan Aplikasi

### Prasyarat
- PHP 8.x terpasang di mesin lokal
- MySQL
  
### Langkah Development
1. Buka terminal PowerShell dan arahkan ke root repo.
2. Masuk ke direktori public:
	```powershell
	cd .\TUBES_PRK_PEMWEB_2025\kelompok\kelompok_25\src\public
	```
3. Jalankan server PHP built-in:
	```powershell
	php -S localhost:8000 index.php
	```
4. Buka `http://localhost:8000` di browser.

## Backend Features Completed

### ✅ Reports & Analytics (P1 - High Priority)
- **Models**: `ReportHelper.php` - Complete analytics engine
- **Controllers**: `ReportsApiController.php` - 9 API endpoints
- **Features**:
  - Inventory summary dashboard
  - Transaction summary with date filters
  - Low stock alerts
  - Material trend analysis (chart data)
  - Category distribution (pie chart)
  - Supplier performance ranking
  - Stock movement detail tracking
  - Top materials by value/quantity/usage
  - Stock value by category

### ✅ Activity Logs (P2 - Medium Priority)
- **Models**: `ActivityLog.php` - Complete logging system
- **Controllers**: `ActivityLogsApiController.php` - 6 API endpoints
- **Helpers**: `ActivityLogger.php` - Convenient logging methods
- **Features**:
  - Comprehensive activity tracking
  - User activity history
  - Action-based filtering
  - Entity-specific logs
  - Recent activities dashboard
  - Automatic cleanup for old logs
  - Security event logging

### 🔧 Integration Features
- **Validation**: `ReportValidation.php` - Input validation & sanitization
- **Testing**: `reports_api.http` - Complete API test suite
- **Documentation**: `API_DOCUMENTATION.md` - Full API reference
- **Routes**: Updated `api.php` with all new endpoints

### 📊 API Endpoints Summary
**Reports (9 endpoints):**
- `GET /api/reports/inventory` - Dashboard summary
- `GET /api/reports/transactions` - Transaction analytics
- `GET /api/reports/low-stock` - Stock alerts
- `GET /api/reports/material-trend/{id}` - Trend data
- `GET /api/reports/category-distribution` - Category stats
- `GET /api/reports/supplier-performance` - Supplier ranking
- `GET /api/reports/stock-movement/{id}` - Movement history
- `GET /api/reports/top-materials` - Top performers
- `GET /api/reports/stock-value-by-category` - Value analysis

**Activity Logs (6 endpoints):**
- `GET /api/activity-logs` - Paginated logs with filters
- `GET /api/activity-logs/user/{id}` - User activity
- `GET /api/activity-logs/action/{action}` - Action-based logs
- `GET /api/activity-logs/entity/{type}/{id}` - Entity logs
- `GET /api/activity-logs/recent` - Recent activities
- `POST /api/activity-logs/cleanup` - Admin cleanup

## Pengembangan Lanjutan
- Tambahkan halaman baru dengan membuat folder view (`views/<fitur>/index.php`) dan mapping route di `routes/web.php`.
- Integrasikan data nyata dengan membuat model & controller API, kemudian panggil via AJAX dari `public/assets/js/modules/<fitur>.js`.
- Gunakan Tailwind CDN saat prototyping; pindah ke build pipeline (`npm run build`) jika perlu optimisasi produksi.
- **Next**: Integrate frontend with new API endpoints for reports and activity logs

---
Kelompok 25 – Sistem Informasi Manajemen Stok Bahan Baku
