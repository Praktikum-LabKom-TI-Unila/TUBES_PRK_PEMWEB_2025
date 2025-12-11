<?php

declare(strict_types=1);

session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Functions.php';
require_once __DIR__ . '/core/DatabaseSetup.php'; 
setupPasswordResetsTable();

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';
$isPostRequest = $_SERVER['REQUEST_METHOD'] === 'POST';

$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);
$action = preg_replace('/[^a-zA-Z0-9_-]/', '', $action);

ob_start();

try {
    switch ($page) {
        case 'home':
            require_once __DIR__ . '/controllers/HomeController.php';
            $controller = new HomeController();
            $controller->index();
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

        case 'comments':
            require_once __DIR__ . '/controllers/CommentController.php';
            $controller = new CommentController();

            if ($isPostRequest) {
                match ($action) {
                    'store'  => $controller->store(),
                    'delete' => $controller->delete(),
                    default  => redirect('index.php?page=items')
                };
            } else {
                match ($action) {
                    'delete' => $controller->delete(),
                    default  => redirect('index.php?page=items')
                };
            }
            break;

        case 'claims':
            require_once __DIR__ . '/controllers/ClaimController.php';
            $controller = new ClaimController();

            if ($isPostRequest) {
                match ($action) {
                    'store'  => $controller->store(),
                    'verify' => $controller->verify(),
                    'reject' => $controller->reject(),
                    'cancel' => $controller->cancel(),
                    default  => redirect('index.php?page=items')
                };
            } else {
                match ($action) {
                    'my'     => $controller->myClaims(),
                    'verify' => $controller->verify(),
                    'reject' => $controller->reject(),
                    'cancel' => $controller->cancel(),
                    default  => redirect('index.php?page=items')
                };
            }
            break;

        case 'notifications':
            require_once __DIR__ . '/controllers/NotificationController.php';
            $controller = new NotificationController();

            if ($isPostRequest) {
                match ($action) {
                    'mark-read'     => $controller->markRead(),
                    'mark-all-read' => $controller->markAllRead(),
                    default         => redirect('index.php?page=notifications')
                };
            } else {
                match ($action) {
                    'index', ''     => $controller->index(),
                    'mark-read'     => $controller->markRead(),
                    'mark-all-read' => $controller->markAllRead(),
                    'unread'        => $controller->getUnread(),
                    'count'         => $controller->getUnreadCount(),
                    default         => $controller->index()
                };
            }
            break;
            
        case 'auth':
            require_once __DIR__ . '/controllers/AuthController.php';
            $authController = new AuthController();
            
            if ($isPostRequest) {
                match ($action) {
                    'login'           => $authController->login(),
                    'register'        => $authController->register(),
                    'sendResetLink'   => $authController->sendResetLink(),
                    'resetPassword'   => $authController->resetPassword(),
                    default           => throw new Exception('Action not found for POST')
                };
            }

            if (!isLoggedIn()) {
                switch ($action) {
                    case 'login':
                        $pageTitle = 'Login - myUnila Lost & Found';
                        require_once __DIR__ . '/views/auth/login.php';
                        break;
                    case 'register':
                        $pageTitle = 'Daftar - myUnila Lost & Found';
                        require_once __DIR__ . '/views/auth/register.php';
                        break;
                    case 'forgotPasswordForm':
                        $pageTitle = 'Lupa Password - myUnila';
                        $authController->forgotPasswordForm(); 
                        break;
                    case 'resetPasswordForm':
                        $pageTitle = 'Reset Password - myUnila';
                        $authController->resetPasswordForm(); 
                        break;
                    case 'logout':
                        $authController->logout();
                        break;
                    default:
                        throw new Exception('Action not found');
                }
            } else {
                if ($action === 'logout') {
                    $authController->logout();
                } else {
                    redirect('index.php?page=home');
                }
            }
            break;
            
        case 'profile':
            require_once __DIR__ . '/controllers/ProfileController.php';
            require_once __DIR__ . '/models/User.php';
            require_once __DIR__ . '/models/Item.php';
            require_once __DIR__ . '/models/Claim.php';
            
            $controller = new ProfileController();

            if ($isPostRequest) {
                match ($action) {
                    'update' => $controller->update(),
                    default  => redirect('index.php?page=profile')
                };
            } else {
                match ($action) {
                    'index', '' => $controller->index(),
                    default     => $controller->index()
                };
            }
            break;
            
        case 'admin':
            if (!isAdmin()) {
                flash('message', 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.', 'error');
                redirect('index.php?page=home');
            }

            require_once __DIR__ . '/controllers/AdminController.php';
            $adminController = new AdminController();

            if ($isPostRequest) {
                match ($action) {
                    'delete_user'   => $adminController->deleteUser(),
                    'delete_item'   => $adminController->deleteItem(),
                    'toggle_active' => $adminController->toggleActive(),
                    default         => redirect('index.php?page=admin')
                };
            } else {
                match ($action) {
                    'users'        => $adminController->users(),
                    'items'        => $adminController->items(),
                    'dashboard', '' => $adminController->dashboard(),
                    default        => $adminController->dashboard()
                };
            }
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
    error_log('Stack trace: ' . $e->getTraceAsString());
    http_response_code(500);

    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Error</title></head><body style="font-family: sans-serif; padding: 40px; background: #1e293b; color: #e2e8f0;">';
    echo '<div style="max-width: 800px; margin: 0 auto;">';
    echo '<h1 style="color: #ef4444; font-size: 2rem; margin-bottom: 1rem;">⚠️ Application Error</h1>';
    echo '<div style="background: #0f172a; padding: 20px; border-radius: 8px; margin-bottom: 20px;">';
    echo '<p style="margin: 0;"><strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p style="margin: 10px 0 0 0;"><strong>File:</strong> ' . htmlspecialchars($e->getFile()) . ' (Line: ' . $e->getLine() . ')</p>';
    echo '</div>';
    echo '<pre style="background: #0f172a; padding: 20px; border-radius: 8px; overflow-x: auto; font-size: 12px;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '<a href="' . base_url('index.php') . '" style="display: inline-block; margin-top: 20px; padding: 12px 24px; background: #06b6d4; color: white; text-decoration: none; border-radius: 8px;">← Kembali ke Beranda</a>';
    echo '</div></body></html>';
    exit;
}

$content = ob_get_clean();

// If AJAX fragment requested (used by admin partial loading), return only content
if (isset($_GET['ajax']) && $_GET['ajax'] === '1') {
    echo $content;
    exit;
}

$layout = ($page === 'admin') ? 'admin' : 'main';
require_once __DIR__ . '/views/layouts/' . $layout . '.php';
