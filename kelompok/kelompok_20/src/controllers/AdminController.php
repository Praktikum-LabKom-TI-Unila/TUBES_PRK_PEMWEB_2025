<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Item.php';
require_once __DIR__ . '/../models/Claim.php';


final class AdminController
{
    private User $userModel;
    private Item $itemModel;
    private Claim $claimModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->itemModel = new Item();
        $this->claimModel = new Claim();
    }

    
    public function dashboard(): void
    {

        if (!isAdmin()) {
            flash('message', 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.', 'error');
            redirect('index.php?page=home');
            return;
        }

        $totalUsers = $this->userModel->countAll();
        $itemStats = $this->itemModel->getStats();
        $totalVerifiedClaims = $this->claimModel->countVerified();

        $stats = [
            'total_users' => $totalUsers,
            'total_lost' => (int) ($itemStats['total_lost'] ?? 0),
            'total_found' => (int) ($itemStats['total_found'] ?? 0),
            'total_verified_claims' => $totalVerifiedClaims,
            'total_items' => (int) ($itemStats['total_items'] ?? 0),
            'total_open' => (int) ($itemStats['total_open'] ?? 0),
            'total_closed' => (int) ($itemStats['total_closed'] ?? 0)
        ];

        $pageTitle = 'Dashboard Admin - myUnila Lost & Found';
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    
    public function users(): void
    {

        if (!isAdmin()) {
            flash('message', 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.', 'error');
            redirect('index.php?page=home');
            return;
        }

        $page = isset($_GET['p']) ? max(1, (int) $_GET['p']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $users = $this->userModel->getAll($limit, $offset);
        $totalUsers = $this->userModel->countAll();
        $totalPages = (int) ceil($totalUsers / $limit);

        $pageTitle = 'Kelola Pengguna - Admin';
        require_once __DIR__ . '/../views/admin/users.php';
    }

    
    public function items(): void
    {

        if (!isAdmin()) {
            flash('message', 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.', 'error');
            redirect('index.php?page=home');
            return;
        }

        $page = isset($_GET['p']) ? max(1, (int) $_GET['p']) : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $filters = [
            'limit' => $limit,
            'offset' => $offset,
            'sort' => 'newest'
        ];
        
        $items = $this->itemModel->getAll($filters);
        $totalItems = $this->itemModel->countAllFiltered([]);
        $totalPages = (int) ceil($totalItems / $limit);

        $pageTitle = 'Kelola Barang - Admin';
        require_once __DIR__ . '/../views/admin/items.php';
    }

    
    public function deleteUser(): void
    {

        if (!isAdmin()) {
            if ($this->isAjaxRequest()) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
                exit;
            }
            flash('message', 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.', 'error');
            redirect('index.php?page=home');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&action=users');
            return;
        }

        $userId = (int) ($_POST['id'] ?? 0);

        if ($userId <= 0) {
            if ($this->isAjaxRequest()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID pengguna tidak valid']);
                exit;
            }
            flash('message', 'ID pengguna tidak valid.', 'error');
            redirect('index.php?page=admin&action=users');
            return;
        }

        if ($userId === $_SESSION['user_id']) {
            if ($this->isAjaxRequest()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Tidak dapat menonaktifkan akun sendiri']);
                exit;
            }
            flash('message', 'Tidak dapat menonaktifkan akun sendiri.', 'error');
            redirect('index.php?page=admin&action=users');
            return;
        }

        $deleted = $this->userModel->delete($userId);

        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $deleted,
                'message' => $deleted ? 'User berhasil dinonaktifkan' : 'Gagal menonaktifkan user'
            ]);
            exit;
        }

        if ($deleted) {
            flash('message', 'User berhasil dinonaktifkan.', 'success');
        } else {
            flash('message', 'Gagal menonaktifkan user.', 'error');
        }

        redirect('index.php?page=admin&action=users');
    }

    
    public function deleteItem(): void
    {

        if (!isAdmin()) {
            if ($this->isAjaxRequest()) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
                exit;
            }
            flash('message', 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.', 'error');
            redirect('index.php?page=home');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=admin&action=items');
            return;
        }

        $itemId = (int) ($_POST['id'] ?? 0);

        if ($itemId <= 0) {
            if ($this->isAjaxRequest()) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'ID item tidak valid']);
                exit;
            }
            flash('message', 'ID item tidak valid.', 'error');
            redirect('index.php?page=admin&action=items');
            return;
        }

        $deleted = $this->itemModel->delete($itemId);

        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $deleted,
                'message' => $deleted ? 'Item berhasil dihapus oleh Admin' : 'Gagal menghapus item'
            ]);
            exit;
        }

        if ($deleted) {
            flash('message', 'Item berhasil dihapus oleh Admin.', 'success');
        } else {
            flash('message', 'Gagal menghapus item.', 'error');
        }

        redirect('index.php?page=admin&action=items');
    }

    
    private function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}
