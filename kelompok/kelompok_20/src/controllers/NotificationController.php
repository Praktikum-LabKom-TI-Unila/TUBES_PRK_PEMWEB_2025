<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Notification.php';


final class NotificationController
{
    private Notification $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new Notification();
    }

    
    public function index(): void
    {

        if (!isLoggedIn()) {
            flash('message', 'Silakan login terlebih dahulu.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $notifications = $this->notificationModel->getAllByUserId($_SESSION['user_id'], 50);
        $unreadCount = $this->notificationModel->countUnread($_SESSION['user_id']);

        $pageTitle = 'Notifikasi - myUnila Lost & Found';

        require_once __DIR__ . '/../views/notifications/index.php';
    }

    
    public function markRead(): void
    {

        if (!isLoggedIn()) {
            if ($this->isAjaxRequest()) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            flash('message', 'Silakan login terlebih dahulu.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $notificationId = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);

        if ($notificationId <= 0) {
            if ($this->isAjaxRequest()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
                exit;
            }
            flash('message', 'Notifikasi tidak valid.', 'error');
            redirect('index.php?page=notifications');
            return;
        }

        $marked = $this->notificationModel->markAsRead($notificationId, $_SESSION['user_id']);

        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $marked,
                'message' => $marked ? 'Notification marked as read' : 'Failed to mark notification'
            ]);
            exit;
        }

        if ($marked) {
            flash('message', 'Notifikasi telah dibaca.', 'success');
        }
        redirect('index.php?page=notifications');
    }

    
    public function markAllRead(): void
    {

        if (!isLoggedIn()) {
            if ($this->isAjaxRequest()) {
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit;
            }
            flash('message', 'Silakan login terlebih dahulu.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $marked = $this->notificationModel->markAllAsRead($_SESSION['user_id']);

        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $marked,
                'message' => $marked ? 'All notifications marked as read' : 'Failed to mark notifications'
            ]);
            exit;
        }

        if ($marked) {
            flash('message', 'Semua notifikasi telah dibaca.', 'success');
        }
        redirect('index.php?page=notifications');
    }

    
    public function getUnreadCount(): void
    {
        header('Content-Type: application/json');

        if (!isLoggedIn()) {
            echo json_encode(['count' => 0]);
            exit;
        }

        $count = $this->notificationModel->countUnread($_SESSION['user_id']);
        echo json_encode(['count' => $count]);
        exit;
    }

    
    public function getUnread(): void
    {
        header('Content-Type: application/json');

        if (!isLoggedIn()) {
            echo json_encode(['notifications' => [], 'count' => 0]);
            exit;
        }

        $limit = (int) ($_GET['limit'] ?? 5);
        $notifications = $this->notificationModel->getUnread($_SESSION['user_id'], $limit);
        $count = $this->notificationModel->countUnread($_SESSION['user_id']);

        echo json_encode([
            'notifications' => $notifications,
            'count' => $count
        ]);
        exit;
    }

    
    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
