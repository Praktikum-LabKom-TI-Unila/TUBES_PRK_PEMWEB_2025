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
