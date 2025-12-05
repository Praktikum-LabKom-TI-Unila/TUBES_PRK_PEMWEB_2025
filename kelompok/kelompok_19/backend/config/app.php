<?php
/**
 * Application Configuration
 * Konfigurasi umum aplikasi SiPEMAU
 */

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Base URL
define('BASE_URL', 'http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_19/backend/public');

// Upload Settings
define('UPLOAD_DIR', dirname(__DIR__) . '/assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);

// Session Settings
define('SESSION_LIFETIME', 3600); // 1 hour

// Password Settings
define('PASSWORD_MIN_LENGTH', 8);

// Pagination
define('ITEMS_PER_PAGE', 10);

// App Info
define('APP_NAME', 'SiPEMAU');
define('APP_VERSION', '1.0.0');
