<?php

/**
 * Helper Functions
 */

/**
 * Redirect ke URL tertentu
 */
function redirect($path)
{
    $url = BASE_URL . $path;
    header("Location: $url");
    exit;
}

/**
 * Generate URL
 */
function url($path = '')
{
    return BASE_URL . $path;
}

/**
 * Generate Assets URL
 */
function asset($path)
{
    return ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * Generate Upload URL
 */
function upload_url($path)
{
    return UPLOAD_URL . '/' . ltrim($path, '/');
}

/**
 * Escape HTML
 */
function e($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF Token
 */
function csrf_token()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function csrf_verify($token)
{
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF Field
 */
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Set Flash Message
 */
function set_flash($key, $message)
{
    $_SESSION['flash'][$key] = $message;
}

/**
 * Get Flash Message
 */
function get_flash($key)
{
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

/**
 * Check if has flash message
 */
function has_flash($key)
{
    return isset($_SESSION['flash'][$key]);
}

/**
 * Get Old Input
 */
function old($key, $default = '')
{
    if (isset($_SESSION['old'][$key])) {
        $value = $_SESSION['old'][$key];
        return $value;
    }
    return $default;
}

/**
 * Set Old Input
 */
function set_old($data)
{
    $_SESSION['old'] = $data;
}

/**
 * Clear Old Input
 */
function clear_old()
{
    unset($_SESSION['old']);
}

/**
 * Dump and Die
 */
function dd(...$vars)
{
    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    die();
}

/**
 * Debug Print
 */
function debug($var)
{
    if (APP_DEBUG) {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }
}

/**
 * Format Tanggal Indonesia
 */
function format_date($date, $format = 'd/m/Y H:i')
{
    if (!$date) return '-';
    return date($format, strtotime($date));
}

/**
 * Format Angka
 */
function format_number($number, $decimals = 0)
{
    return number_format($number, $decimals, ',', '.');
}

/**
 * Format Currency IDR
 */
function format_currency($amount)
{
    return 'Rp ' . format_number($amount, 2);
}

/**
 * Sanitize String
 */
function sanitize($string)
{
    return trim(strip_tags($string));
}

/**
 * Check if request is POST
 */
function is_post()
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request is GET
 */
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Check if request is AJAX
 */
function is_ajax()
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get Current User
 */
function current_user()
{
    return $_SESSION['user'] ?? null;
}

/**
 * Check if user is logged in
 */
function is_logged_in()
{
    return isset($_SESSION['user']);
}

/**
 * Check if user has role
 */
function has_role($role)
{
    $user = current_user();
    if (!$user) return false;
    
    if (is_array($role)) {
        return in_array($user['role_code'], $role);
    }
    
    return $user['role_code'] === $role;
}

/**
 * Check if user has permission
 * @param string $permission Permission code to check
 * @return bool
 */
function has_permission($permission)
{
    $user = current_user();
    if (!$user) return false;
    
    // Admin has all permissions
    if (isset($user['role_code']) && $user['role_code'] === 'admin') {
        return true;
    }
    
    // Check if user has the permission in their session
    if (isset($user['permissions']) && is_array($user['permissions'])) {
        return in_array($permission, $user['permissions']);
    }
    
    return false;
}

/**
 * Check if user has any of the specified permissions
 * @param array $permissions Array of permission codes
 * @return bool
 */
function has_any_permission($permissions)
{
    if (!is_array($permissions)) {
        $permissions = [$permissions];
    }
    
    foreach ($permissions as $permission) {
        if (has_permission($permission)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Check if user has all specified permissions
 * @param array $permissions Array of permission codes
 * @return bool
 */
function has_all_permissions($permissions)
{
    if (!is_array($permissions)) {
        $permissions = [$permissions];
    }
    
    foreach ($permissions as $permission) {
        if (!has_permission($permission)) {
            return false;
        }
    }
    
    return true;
}

/**
 * Get user's permissions array
 * @return array
 */
function get_user_permissions()
{
    $user = current_user();
    if (!$user) return [];
    
    return $user['permissions'] ?? [];
}

/**
 * Generate random string
 */
function generate_token($length = 32)
{
    return bin2hex(random_bytes($length));
}

/**
 * Slugify string
 */
function slugify($text)
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    
    if (empty($text)) {
        return 'n-a';
    }
    
    return $text;
}
