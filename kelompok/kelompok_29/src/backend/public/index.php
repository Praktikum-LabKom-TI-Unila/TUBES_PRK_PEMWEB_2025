<?php
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../routes/api.php';

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/backend/public';
$path = trim(str_replace($basePath, '', $requestUri), '/');

if ($method === 'OPTIONS') {
    send_headers(204);
    exit;
}

foreach ($routes as $route) {
    if ($route['method'] === $method && $route['path'] === $path) {
        require_once $route['controller'];
        exit;
    }
}

response_error(404, 'Endpoint tidak ditemukan.');
