<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../models/Item.php';
require_once __DIR__ . '/../models/Notification.php';


final class CommentController
{
    private Comment $commentModel;
    private Item $itemModel;
    private Notification $notificationModel;

    public function __construct()
    {
        $this->commentModel = new Comment();
        $this->itemModel = new Item();
        $this->notificationModel = new Notification();
    }

    
    public function store(): void
    {

        if (!isLoggedIn()) {
            flash('message', 'Silakan login untuk berkomentar.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $itemId = (int) ($_POST['item_id'] ?? 0);
        $body = clean($_POST['body'] ?? '');

        if ($itemId <= 0) {
            flash('message', 'Item tidak valid.', 'error');
            redirect('index.php?page=items');
            return;
        }

        if (empty($body)) {
            flash('message', 'Komentar tidak boleh kosong.', 'error');
            redirect('index.php?page=items&action=show&id=' . $itemId);
            return;
        }

        if (strlen($body) < 3) {
            flash('message', 'Komentar minimal 3 karakter.', 'error');
            redirect('index.php?page=items&action=show&id=' . $itemId);
            return;
        }

        if (strlen($body) > 1000) {
            flash('message', 'Komentar maksimal 1000 karakter.', 'error');
            redirect('index.php?page=items&action=show&id=' . $itemId);
            return;
        }

        $item = $this->itemModel->getById($itemId);
        if (!$item) {
            flash('message', 'Item tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $data = [
            'item_id' => $itemId,
            'user_id' => $_SESSION['user_id'],
            'body'    => $body
        ];

        $commentId = $this->commentModel->create($data);

        if (!$commentId) {
            flash('message', 'Gagal menambahkan komentar.', 'error');
            redirect('index.php?page=items&action=show&id=' . $itemId);
            return;
        }

        if ((int) $item['user_id'] !== $_SESSION['user_id']) {
            $this->notificationModel->create(
                (int) $item['user_id'],
                'Komentar Baru',
                $_SESSION['user']['name'] . ' mengomentari laporan Anda: "' . $item['title'] . '"',
                'index.php?page=items&action=show&id=' . $itemId . '#comment-' . $commentId
            );
        }

        flash('message', 'Komentar berhasil ditambahkan.', 'success');
        redirect('index.php?page=items&action=show&id=' . $itemId . '#comments');
    }

    
    public function delete(): void
    {

        if (!isLoggedIn()) {
            flash('message', 'Silakan login terlebih dahulu.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $commentId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

        if ($commentId <= 0) {
            flash('message', 'Komentar tidak valid.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $comment = $this->commentModel->getById($commentId);

        if (!$comment) {
            flash('message', 'Komentar tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $item = $this->itemModel->getById($comment['item_id']);

        $isCommentOwner = (int) $comment['user_id'] === $_SESSION['user_id'];
        $isItemOwner = $item && (int) $item['user_id'] === $_SESSION['user_id'];
        $isAdminUser = isAdmin();

        if (!$isCommentOwner && !$isItemOwner && !$isAdminUser) {
            flash('message', 'Anda tidak memiliki akses untuk menghapus komentar ini.', 'error');
            redirect('index.php?page=items&action=show&id=' . $comment['item_id']);
            return;
        }

        $deleted = $this->commentModel->delete($commentId);

        if (!$deleted) {
            flash('message', 'Gagal menghapus komentar.', 'error');
            redirect('index.php?page=items&action=show&id=' . $comment['item_id']);
            return;
        }

        flash('message', 'Komentar berhasil dihapus.', 'success');
        redirect('index.php?page=items&action=show&id=' . $comment['item_id'] . '#comments');
    }
}
