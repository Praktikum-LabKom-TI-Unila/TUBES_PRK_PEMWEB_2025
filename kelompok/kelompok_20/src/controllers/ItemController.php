<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Item.php';
require_once __DIR__ . '/../models/Notification.php';

final class ItemController
{
    private Item $itemModel;
    private Notification $notificationModel;
    private string $uploadDir;
    private int $itemsPerPage = 12;

    public function __construct()
    {
        $this->itemModel = new Item();
        $this->notificationModel = new Notification();
        $this->uploadDir = __DIR__ . '/../assets/uploads/items/';
    }

    public function index(): void
    {

        $filters = [
            'type'        => clean($_GET['type'] ?? ''),
            'category_id' => !empty($_GET['category']) ? (int) $_GET['category'] : null,
            'location_id' => !empty($_GET['location']) ? (int) $_GET['location'] : null,
            'keyword'     => clean($_GET['q'] ?? ''),
            'status'      => clean($_GET['status'] ?? '')
        ];

        if (!empty($_GET['start_date'])) {
            $filters['start_date'] = clean($_GET['start_date']);
        }
        if (!empty($_GET['end_date'])) {
            $filters['end_date'] = clean($_GET['end_date']);
        }

        $sort = clean($_GET['sort'] ?? 'newest');
        if (!in_array($sort, ['newest', 'oldest'], true)) {
            $sort = 'newest';
        }
        $filters['sort'] = $sort;

        $filters = array_filter($filters, fn($v) => $v !== null && $v !== '');

        $totalItems = $this->itemModel->countAllFiltered($filters);
        $totalPages = (int) ceil($totalItems / $this->itemsPerPage);

        $currentPage = (int) ($_GET['page'] ?? 1);
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        if ($totalPages > 0 && $currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $offset = ($currentPage - 1) * $this->itemsPerPage;

        $filters['limit'] = $this->itemsPerPage;
        $filters['offset'] = $offset;

        $items = $this->itemModel->getAll($filters);
        $categories = $this->itemModel->getCategories();
        $locations = $this->itemModel->getLocations();

        $pagination = [
            'current_page' => $currentPage,
            'total_pages'  => $totalPages,
            'total_items'  => $totalItems,
            'per_page'     => $this->itemsPerPage,
            'has_prev'     => $currentPage > 1,
            'has_next'     => $currentPage < $totalPages
        ];

        $pageTitle = 'Daftar Barang - myUnila Lost & Found';

        require_once __DIR__ . '/../views/items/index.php';
    }

    
    public function matches(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            flash('message', 'Item tidak valid.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $item = $this->itemModel->getById($id);

        if (!$item) {
            flash('message', 'Item tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $limit = (int) ($_GET['limit'] ?? 5);
        if ($limit < 1) $limit = 5;
        if ($limit > 20) $limit = 20;

        $matches = $this->itemModel->findMatches($item, $limit);

        $matchData = [
            'target_item'   => $item,
            'matches'       => $matches,
            'matches_count' => count($matches),
            'opposite_type' => ($item['type'] === 'lost') ? 'found' : 'lost'
        ];

        $pageTitle = 'Kecocokan untuk: ' . $item['title'] . ' - myUnila Lost & Found';

        require_once __DIR__ . '/../views/items/matches.php';
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            flash('message', 'Item tidak valid.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $item = $this->itemModel->getById($id);

        if (!$item) {
            flash('message', 'Item tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $isLoggedIn = isLoggedIn();
        $isOwner = $isLoggedIn && (int) $_SESSION['user']['id'] === (int) $item['user_id'];

        require_once __DIR__ . '/../models/Comment.php';
        $commentModel = new Comment();
        $comments = $commentModel->getByItemId($id);

        $pageTitle = $item['title'] . ' - myUnila Lost & Found';

        require_once __DIR__ . '/../views/items/show.php';
    }

    public function create(): void
    {
        if (!isLoggedIn()) {
            flash('message', 'Silakan login untuk membuat laporan.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $categories = $this->itemModel->getCategories();
        $locations = $this->itemModel->getLocations();

        $pageTitle = 'Buat Laporan - myUnila Lost & Found';

        require_once __DIR__ . '/../views/items/create.php';
    }

    public function store(): void
    {
        if (!isLoggedIn()) {
            flash('message', 'Silakan login untuk membuat laporan.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $title         = clean($_POST['title'] ?? '');
        $description   = clean($_POST['description'] ?? '');
        $type          = clean($_POST['type'] ?? '');
        $categoryId    = (int) ($_POST['category_id'] ?? 0);
        $locationId    = (int) ($_POST['location_id'] ?? 0);
        $incidentDate  = clean($_POST['incident_date'] ?? '');
        $isSafeClaim   = isset($_POST['is_safe_claim']) ? 1 : 0;
        $securityQ     = clean($_POST['security_question'] ?? '');
        $securityA     = clean($_POST['security_answer'] ?? '');

        $errors = $this->validateItem($title, $description, $type, $categoryId, $locationId, $incidentDate);

        if ($isSafeClaim && (empty($securityQ) || empty($securityA))) {
            $errors[] = 'Pertanyaan dan jawaban keamanan wajib diisi jika Safe Claim diaktifkan.';
        }

        if (!empty($errors)) {
            flash('message', implode('<br>', $errors), 'error');
            redirect('index.php?page=items&action=create');
            return;
        }

        $imagePath = null;
        if (!empty($_FILES['image']['name'])) {
            $categories = $this->itemModel->getCategories();
            $categoryName = 'ITEM';
            foreach ($categories as $cat) {
                if ((int)$cat['id'] === $categoryId) {
                    $categoryName = $cat['name'];
                    break;
                }
            }
            
            $uploadResult = uploadImage($_FILES['image'], $this->uploadDir, $categoryName, $_SESSION['user_id']);
            if ($uploadResult === false) {
                flash('message', 'Gagal upload gambar. Pastikan format JPG/PNG/WEBP dan ukuran maksimal 2MB.', 'error');
                redirect('index.php?page=items&action=create');
                return;
            }
            $imagePath = $uploadResult;
        }

        $data = [
            'user_id'           => $_SESSION['user_id'],
            'category_id'       => $categoryId,
            'location_id'       => $locationId,
            'title'             => $title,
            'description'       => $description,
            'type'              => $type,
            'incident_date'     => $incidentDate,
            'image_path'        => $imagePath,
            'status'            => 'open',
            'is_safe_claim'     => $isSafeClaim,
            'security_question' => $isSafeClaim ? $securityQ : null,
            'security_answer'   => $isSafeClaim ? strtolower($securityA) : null
        ];

        $itemId = $this->itemModel->create($data);

        if (!$itemId) {
            flash('message', 'Gagal membuat laporan. Silakan coba lagi.', 'error');
            redirect('index.php?page=items&action=create');
            return;
        }

        // Notifikasi 1: Konfirmasi laporan berhasil dibuat
        $typeLabel = $type === 'lost' ? 'kehilangan' : 'penemuan';
        $this->notificationModel->create(
            $_SESSION['user_id'],
            '✅ Laporan Berhasil Dipublikasikan',
            "Laporan {$typeLabel} \"{$title}\" telah berhasil dipublikasikan dan dapat dilihat oleh pengguna lain.",
            "index.php?page=items&action=show&id={$itemId}",
            'item_created'
        );

        // Notifikasi 2: Cek item matching dan beri notifikasi ke pemilik item yang cocok
        $this->notifyMatchingItems($itemId, $type, $title, $categoryId, $locationId);

        flash('message', 'Laporan berhasil dibuat!', 'success');
        redirect('index.php?page=items&action=show&id=' . $itemId);
    }

    public function edit(): void
    {
        if (!isLoggedIn()) {
            flash('message', 'Silakan login terlebih dahulu.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            flash('message', 'Item tidak valid.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $item = $this->itemModel->getById($id);

        if (!$item) {
            flash('message', 'Item tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        if (!$this->isOwnerOrAdmin($item)) {
            flash('message', 'Anda tidak memiliki akses untuk mengedit item ini.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $categories = $this->itemModel->getCategories();
        $locations = $this->itemModel->getLocations();

        $pageTitle = 'Edit Laporan - myUnila Lost & Found';

        require_once __DIR__ . '/../views/items/edit.php';
    }

    public function update(): void
    {
        if (!isLoggedIn()) {
            flash('message', 'Silakan login terlebih dahulu.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            flash('message', 'Item tidak valid.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $item = $this->itemModel->getById($id);

        if (!$item) {
            flash('message', 'Item tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        if (!$this->isOwnerOrAdmin($item)) {
            flash('message', 'Anda tidak memiliki akses untuk mengedit item ini.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $title         = clean($_POST['title'] ?? '');
        $description   = clean($_POST['description'] ?? '');
        $type          = clean($_POST['type'] ?? '');
        $categoryId    = (int) ($_POST['category_id'] ?? 0);
        $locationId    = (int) ($_POST['location_id'] ?? 0);
        $incidentDate  = clean($_POST['incident_date'] ?? '');
        $isSafeClaim   = isset($_POST['is_safe_claim']) ? 1 : 0;
        $securityQ     = clean($_POST['security_question'] ?? '');
        $securityA     = clean($_POST['security_answer'] ?? '');

        $errors = $this->validateItem($title, $description, $type, $categoryId, $locationId, $incidentDate);

        if ($isSafeClaim && (empty($securityQ) || empty($securityA))) {
            $errors[] = 'Pertanyaan dan jawaban keamanan wajib diisi jika Safe Claim diaktifkan.';
        }

        if (!empty($errors)) {
            flash('message', implode('<br>', $errors), 'error');
            redirect('index.php?page=items&action=edit&id=' . $id);
            return;
        }

        $imagePath = $item['image_path'];

        if (!empty($_FILES['image']['name'])) {
            $categories = $this->itemModel->getCategories();
            $categoryName = 'ITEM';
            foreach ($categories as $cat) {
                if ((int)$cat['id'] === $categoryId) {
                    $categoryName = $cat['name'];
                    break;
                }
            }
            
            $uploadResult = uploadImage($_FILES['image'], $this->uploadDir, $categoryName, $_SESSION['user_id']);
            if ($uploadResult === false) {
                flash('message', 'Gagal upload gambar baru.', 'error');
                redirect('index.php?page=items&action=edit&id=' . $id);
                return;
            }

            if (!empty($item['image_path'])) {
                deleteImage($item['image_path'], $this->uploadDir);
            }

            $imagePath = $uploadResult;
        }

        $data = [
            'category_id'       => $categoryId,
            'location_id'       => $locationId,
            'title'             => $title,
            'description'       => $description,
            'type'              => $type,
            'incident_date'     => $incidentDate,
            'image_path'        => $imagePath,
            'is_safe_claim'     => $isSafeClaim,
            'security_question' => $isSafeClaim ? $securityQ : null,
            'security_answer'   => $isSafeClaim ? strtolower($securityA) : null
        ];

        $updated = $this->itemModel->update($id, $data);

        if (!$updated) {
            flash('message', 'Gagal mengupdate laporan.', 'error');
            redirect('index.php?page=items&action=edit&id=' . $id);
            return;
        }

        flash('message', 'Laporan berhasil diupdate!', 'success');
        redirect('index.php?page=items&action=show&id=' . $id);
    }

    public function delete(): void
    {
        if (!isLoggedIn()) {
            flash('message', 'Silakan login terlebih dahulu.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

        if ($id <= 0) {
            flash('message', 'Item tidak valid.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $item = $this->itemModel->getById($id);

        if (!$item) {
            flash('message', 'Item tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        if (!$this->isOwnerOrAdmin($item)) {
            flash('message', 'Anda tidak memiliki akses untuk menghapus item ini.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $deleted = $this->itemModel->delete($id);

        if (!$deleted) {
            flash('message', 'Gagal menghapus laporan.', 'error');
            redirect('index.php?page=items&action=my');
            return;
        }

        flash('message', 'Laporan berhasil dihapus.', 'success');
        redirect('index.php?page=items&action=my');
    }

    public function myItems(): void
    {
        if (!isLoggedIn()) {
            flash('message', 'Silakan login terlebih dahulu.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $items = $this->itemModel->getByUserId($_SESSION['user_id']);

        $pageTitle = 'Laporan Saya - myUnila Lost & Found';

        require_once __DIR__ . '/../views/items/my_items.php';
    }

    private function validateItem(
        string $title,
        string $description,
        string $type,
        int $categoryId,
        int $locationId,
        string $incidentDate
    ): array {
        $errors = [];

        if (empty($title) || strlen($title) < 5) {
            $errors[] = 'Judul minimal 5 karakter.';
        }

        if (empty($description) || strlen($description) < 20) {
            $errors[] = 'Deskripsi minimal 20 karakter.';
        }

        if (!in_array($type, ['lost', 'found'], true)) {
            $errors[] = 'Tipe laporan tidak valid.';
        }

        if ($categoryId <= 0) {
            $errors[] = 'Pilih kategori yang valid.';
        }

        if ($locationId <= 0) {
            $errors[] = 'Pilih lokasi yang valid.';
        }

        if (empty($incidentDate)) {
            $errors[] = 'Tanggal kejadian wajib diisi.';
        } elseif (strtotime($incidentDate) > time()) {
            $errors[] = 'Tanggal kejadian tidak boleh di masa depan.';
        }

        return $errors;
    }

    private function isOwnerOrAdmin(array $item): bool
    {
        $currentUserId = $_SESSION['user_id'] ?? 0;
        $isOwner = (int) $item['user_id'] === $currentUserId;

        return $isOwner || isAdmin();
    }

    /**
     * Mencari item yang cocok dan memberi notifikasi ke pemilik item tersebut
     * Ketika ada laporan baru, cek apakah ada item dengan tipe berlawanan (lost vs found)
     * yang cocok berdasarkan kategori dan lokasi
     */
    private function notifyMatchingItems(int $newItemId, string $type, string $title, int $categoryId, int $locationId): void
    {
        // Ambil data item yang baru dibuat
        $newItem = $this->itemModel->getById($newItemId);
        if (!$newItem) {
            return;
        }
        
        // Cari item yang cocok menggunakan findMatches
        $matches = $this->itemModel->findMatches($newItem, 5);
        
        if (empty($matches)) {
            return;
        }

        $typeLabel = $type === 'lost' ? 'kehilangan' : 'penemuan';
        $oppositeLabel = $oppositeType === 'lost' ? 'kehilangan' : 'penemuan';
        
        // Notifikasi ke pemilik item yang cocok (tanpa duplikasi)
        $notifiedUsers = [];
        foreach ($matches as $match) {
            $matchOwnerId = (int) $match['user_id'];
            
            // Skip jika user yang sama atau sudah dinotifikasi
            if ($matchOwnerId === $_SESSION['user_id'] || in_array($matchOwnerId, $notifiedUsers, true)) {
                continue;
            }
            
            $this->notificationModel->create(
                $matchOwnerId,
                '🔍 Ada Barang yang Cocok!',
                "Laporan {$typeLabel} baru \"{$title}\" mungkin cocok dengan laporan {$oppositeLabel} Anda. Cek sekarang!",
                "index.php?page=items&action=show&id={$newItemId}",
                'item_match'
            );
            
            $notifiedUsers[] = $matchOwnerId;
        }
    }
}
