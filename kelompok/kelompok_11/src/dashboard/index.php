<?php
require_once __DIR__ . '/../auth/cek_login.php';

require_login();

// Router dashboard berdasarkan role
$user = get_user_data();
$role = strtolower($user['role'] ?? '');

// Redirect ke dashboard sesuai role
switch ($role) {
    case 'admin':
        require_once __DIR__ . '/dashboard_admin.php';
        break;
    case 'kasir':
        require_once __DIR__ . '/dashboard_kasir.php';
        break;
    case 'mekanik':
        require_once __DIR__ . '/dashboard_mekanik.php';
        break;
    default:
        die('Role tidak dikenali');
}
