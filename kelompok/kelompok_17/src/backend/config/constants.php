<?php

if (!defined('BASE_PATH')) {
    http_response_code(403);
    exit('Direct access forbidden');
}

define('UPLOAD_PATH', dirname(BASE_PATH, 2) . '/upload/');
define('PROFILE_UPLOAD_PATH', UPLOAD_PATH . 'profile/');
define('EVENT_UPLOAD_PATH', UPLOAD_PATH . 'event/');

define('MAX_FILE_SIZE', 2 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', [
    'image/jpeg',
    'image/png',
    'image/gif',
    'image/webp'
]);
define('ALLOWED_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

define('ROLE_ADMIN', 'admin');
define('ROLE_ANGGOTA', 'anggota');
define('ALLOWED_ROLES', [ROLE_ADMIN, ROLE_ANGGOTA]);

define('EVENT_STATUS_DRAFT', 'draft');
define('EVENT_STATUS_PUBLISHED', 'published');
define('EVENT_STATUS_CANCELLED', 'cancelled');
define('EVENT_STATUS_COMPLETED', 'completed');

define('ATTENDANCE_HADIR', 'hadir');
define('ATTENDANCE_IZIN', 'izin');
define('ATTENDANCE_ALPHA', 'alpha');

define('STATUS_ACTIVE', 'aktif');
define('STATUS_INACTIVE', 'tidak_aktif');

define('DEFAULT_PAGE', 1);
define('DEFAULT_LIMIT', 10);
define('MAX_LIMIT', 100);

define('SESSION_LIFETIME', 3600);
define('SESSION_NAME', 'SIMORA_SESSION');
