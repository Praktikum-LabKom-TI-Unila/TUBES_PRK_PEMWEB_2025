<?php

declare(strict_types=1);

function base_url(string $path = ''): string
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    $baseUrl = $protocol . '://' . $host . $scriptName;
    
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

function flash(string $name, string $message = '', string $type = 'success'): ?array
{
    if (!empty($message)) {
        $_SESSION['flash'][$name] = [
            'message' => $message,
            'type' => $type
        ];
        return null;
    }
    
    if (isset($_SESSION['flash'][$name])) {
        $flash = $_SESSION['flash'][$name];
        unset($_SESSION['flash'][$name]);
        return $flash;
    }
    
    return null;
}

function dd(...$data): void
{
    echo '<pre style="background: #1e293b; color: #e2e8f0; padding: 20px; border-radius: 8px; margin: 20px; font-family: monospace;">';
    foreach ($data as $item) {
        var_dump($item);
        echo "\n";
    }
    echo '</pre>';
    die;
}

function clean(string $data): string
{
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isAdmin(): bool
{
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

function formatDate(string $date): string
{
    $months = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = $months[(int)date('m', $timestamp)];
    $year = date('Y', $timestamp);
    
    return "{$day} {$month} {$year}";
}

function uploadImage(array $file, string $targetDir, string $category = 'IMG', int $userId = 0): string|false
{
    if ($file['error'] !== UPLOAD_ERR_OK || empty($file['tmp_name'])) {
        return false;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $maxSize = 2 * 1024 * 1024;

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
        return false;
    }

    if ($file['size'] > $maxSize) {
        return false;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mimeType, $allowedMimes, true)) {
        return false;
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $categoryClean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $category));
    $userIdFormatted = sprintf('U%03d', $userId);
    $timestamp = date('Ymd_His');
    $randomStr = strtoupper(substr(bin2hex(random_bytes(2)), 0, 4));
    
    $newFilename = sprintf('%s_%s_%s_%s.%s', $categoryClean, $userIdFormatted, $timestamp, $randomStr, $extension);
    $targetPath = rtrim($targetDir, '/') . '/' . $newFilename;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return false;
    }

    return $newFilename;
}

function deleteImage(string $filename, string $targetDir): bool
{
    if (empty($filename) || $filename === 'default.jpg') {
        return false;
    }

    $filePath = rtrim($targetDir, '/') . '/' . $filename;

    if (file_exists($filePath)) {
        return unlink($filePath);
    }

    return false;
}

function timeAgo(string $datetime): string
{
    $now = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);

    if ($diff->y > 0) {
        return $diff->y . ' tahun lalu';
    }
    if ($diff->m > 0) {
        return $diff->m . ' bulan lalu';
    }
    if ($diff->d > 0) {
        return $diff->d . ' hari lalu';
    }
    if ($diff->h > 0) {
        return $diff->h . ' jam lalu';
    }
    if ($diff->i > 0) {
        return $diff->i . ' menit lalu';
    }

    return 'Baru saja';
}

function truncate(string $text, int $length = 100): string
{
    if (strlen($text) <= $length) {
        return $text;
    }

    return substr($text, 0, $length) . '...';
}

function view(string $viewName, array $data = []): void
{
    extract($data); 
    
    require_once __DIR__ . '/../views/' . $viewName . '.php'; 
}