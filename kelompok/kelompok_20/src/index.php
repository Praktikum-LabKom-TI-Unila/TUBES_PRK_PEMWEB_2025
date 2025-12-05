<?php

declare(strict_types=1);

session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Functions.php';

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';
$isPostRequest = $_SERVER['REQUEST_METHOD'] === 'POST';

$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);
$action = preg_replace('/[^a-zA-Z0-9_-]/', '', $action);

ob_start();

try {
    switch ($page) {
        case 'home':
            $pageTitle = 'Beranda - myUnila Lost & Found';
            require_once __DIR__ . '/views/home/index.php';
            break;
            
        case 'items':
            require_once __DIR__ . '/controllers/ItemController.php';
            $controller = new ItemController();

            if ($isPostRequest) {
                match ($action) {
                    'store'  => $controller->store(),
                    'update' => $controller->update(),
                    'delete' => $controller->delete(),
                    default  => redirect('index.php?page=items')
                };
            } else {
                match ($action) {
                    'index', ''  => $controller->index(),
                    'show'       => $controller->show(),
                    'create'     => $controller->create(),
                    'edit'       => $controller->edit(),
                    'delete'     => $controller->delete(),
                    'my'         => $controller->myItems(),
                    'matches'    => $controller->matches(),
                    default      => $controller->index()
                };
            }
            break;
            
        case 'auth':
            if ($isPostRequest) {
                require_once __DIR__ . '/controllers/AuthController.php';
                $authController = new AuthController();

                if ($action === 'login') {
                    $authController->login();
                } elseif ($action === 'register') {
                    $authController->register();
                }
                exit;
            }

            if ($action === 'login') {
                if (isLoggedIn()) {
                    redirect('index.php?page=home');
                }
                $pageTitle = 'Login - myUnila Lost & Found';
                require_once __DIR__ . '/views/auth/login.php';
            } elseif ($action === 'register') {
                if (isLoggedIn()) {
                    redirect('index.php?page=home');
                }
                $pageTitle = 'Daftar - myUnila Lost & Found';
                require_once __DIR__ . '/views/auth/register.php';
            } elseif ($action === 'logout') {
                require_once __DIR__ . '/controllers/AuthController.php';
                $authController = new AuthController();
                $authController->logout();
            } else {
                throw new Exception('Action not found');
            }
            break;
            
        case 'profile':
            if (!isLoggedIn()) {
                flash('message', 'Silakan login terlebih dahulu', 'error');
                redirect('index.php?page=auth&action=login');
            }
            $pageTitle = 'Profil - myUnila Lost & Found';
            require_once __DIR__ . '/views/profile/index.php';
            break;
            
        case 'admin':
            if (!isAdmin()) {
                flash('message', 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.', 'error');
                redirect('index.php?page=home');
            }
            $pageTitle = 'Dashboard Admin - myUnila Lost & Found';
            require_once __DIR__ . '/views/admin/dashboard.php';
            break;
            
        default:
            http_response_code(404);
            echo '<div class="container mx-auto px-4 py-20 text-center">';
            echo '<h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>';
            echo '<p class="text-xl text-gray-600 mb-8">Halaman tidak ditemukan</p>';
            echo '<a href="' . base_url('index.php') . '" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-600 transition inline-block">Kembali ke Beranda</a>';
            echo '</div>';
            break;
    }
} catch (Exception $e) {
    error_log('Router Error: ' . $e->getMessage());
    http_response_code(500);
    echo '<div class="container mx-auto px-4 py-20 text-center">';
    echo '<h1 class="text-6xl font-bold text-red-600 mb-4">500</h1>';
    echo '<p class="text-xl text-gray-600 mb-8">Terjadi kesalahan pada server</p>';
    echo '<a href="' . base_url('index.php') . '" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-blue-600 transition inline-block">Kembali ke Beranda</a>';
    echo '</div>';
}

$content = ob_get_clean();

require_once __DIR__ . '/views/layouts/main.php';
