<?php

/**
 * Front Controller - index.php
 * Semua request masuk melalui file ini
 */

// Start session
session_start();

// Define root path
define('ROOT_PATH', dirname(__DIR__));

// Load configuration
require_once ROOT_PATH . '/config/config.php';
require_once ROOT_PATH . '/config/database.php';

// Load helpers
require_once ROOT_PATH . '/helpers/functions.php';
require_once ROOT_PATH . '/helpers/validation.php';

// Load core classes
require_once ROOT_PATH . '/core/Router.php';
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/core/Response.php';
require_once ROOT_PATH . '/core/Upload.php';

// Load middleware
require_once ROOT_PATH . '/middleware/AuthMiddleware.php';
require_once ROOT_PATH . '/middleware/RoleMiddleware.php';

// Check remember me cookie
if (!Auth::check() && isset($_COOKIE['remember_token'])) {
    Auth::checkRememberMe();
}

// Initialize router
$router = new Router();

// Load routes - API routes first to prevent web routes from catching them
require_once ROOT_PATH . '/routes/api.php';
require_once ROOT_PATH . '/routes/web.php';

// Set 404 handler
$router->notFound(function() {
    http_response_code(404);
    echo '<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>404 - Halaman Tidak Ditemukan</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 flex items-center justify-center min-h-screen">
        <div class="text-center">
            <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
            <p class="text-xl text-gray-600 mb-8">Halaman tidak ditemukan</p>
            <a href="' . url('/') . '" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                Kembali ke Beranda
            </a>
        </div>
    </body>
    </html>';
});

// Dispatch request
try {
    $router->dispatch();
} catch (Exception $e) {
    if (APP_DEBUG) {
        echo '<pre>';
        echo '<strong>Error:</strong> ' . $e->getMessage() . '<br>';
        echo '<strong>File:</strong> ' . $e->getFile() . '<br>';
        echo '<strong>Line:</strong> ' . $e->getLine() . '<br><br>';
        echo '<strong>Trace:</strong><br>' . $e->getTraceAsString();
        echo '</pre>';
    } else {
        http_response_code(500);
        echo '<!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>500 - Server Error</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-100 flex items-center justify-center min-h-screen">
            <div class="text-center">
                <h1 class="text-6xl font-bold text-gray-800 mb-4">500</h1>
                <p class="text-xl text-gray-600 mb-8">Terjadi kesalahan pada server</p>
                <a href="' . url('/') . '" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                    Kembali ke Beranda
                </a>
            </div>
        </body>
        </html>';
    }
}
