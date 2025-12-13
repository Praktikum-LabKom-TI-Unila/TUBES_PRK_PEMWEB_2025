<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Claim.php';
require_once __DIR__ . '/../models/Item.php';
require_once __DIR__ . '/../models/Notification.php';


final class ClaimController
{
    private Claim $claimModel;
    private Item $itemModel;
    private Notification $notificationModel;

    public function __construct()
    {
        $this->claimModel = new Claim();
        $this->itemModel = new Item();
        $this->notificationModel = new Notification();
    }

    
    public function store(): void
    {

        if (!isLoggedIn()) {
            flash('message', 'Silakan login untuk mengajukan klaim.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $itemId = (int) ($_POST['item_id'] ?? 0);
        $answer = clean($_POST['answer'] ?? '');

        if ($itemId <= 0) {
            flash('message', 'Item tidak valid.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $item = $this->itemModel->getById($itemId);
        if (!$item) {
            flash('message', 'Item tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        if ((int) $item['user_id'] === $_SESSION['user_id']) {
            flash('message', 'Anda tidak dapat mengklaim barang milik sendiri.', 'error');
            redirect('index.php?page=items&action=show&id=' . $itemId);
            return;
        }

        if ($item['status'] !== 'open') {
            flash('message', 'Item ini sudah tidak dapat diklaim.', 'error');
            redirect('index.php?page=items&action=show&id=' . $itemId);
            return;
        }

        if ($this->claimModel->hasClaimed($itemId, $_SESSION['user_id'])) {
            flash('message', 'Anda sudah mengajukan klaim untuk item ini.', 'error');
            redirect('index.php?page=items&action=show&id=' . $itemId);
            return;
        }

        if ($item['is_safe_claim'] && !empty($item['security_answer'])) {
            if (empty($answer)) {
                flash('message', 'Silakan jawab pertanyaan keamanan untuk mengklaim item ini.', 'error');
                redirect('index.php?page=items&action=show&id=' . $itemId);
                return;
            }

            if (strtolower(trim($answer)) !== strtolower(trim($item['security_answer']))) {
                flash('message', 'Jawaban keamanan tidak sesuai. Klaim tidak dapat diproses.', 'error');
                redirect('index.php?page=items&action=show&id=' . $itemId);
                return;
            }
        }

        $data = [
            'item_id'             => $itemId,
            'user_id'             => $_SESSION['user_id'],
            'verification_answer' => !empty($answer) ? $answer : null
        ];

        $claimId = $this->claimModel->create($data);

        if (!$claimId) {
            flash('message', 'Gagal mengajukan klaim.', 'error');
            redirect('index.php?page=items&action=show&id=' . $itemId);
            return;
        }

        $this->itemModel->updateStatus($itemId, 'process');

        $this->notificationModel->create(
            (int) $item['user_id'],
            'Klaim Baru',
            $_SESSION['user']['name'] . ' mengajukan klaim untuk barang Anda: "' . $item['title'] . '"',
            'index.php?page=items&action=show&id=' . $itemId . '#claims',
            'new_claim'
        );

        flash('message', 'Klaim berhasil diajukan! Pemilik item akan menghubungi Anda.', 'success');
        redirect('index.php?page=items&action=show&id=' . $itemId);
    }

    
    public function verify(): void
    {

        if (!isLoggedIn()) {
            flash('message', 'Silakan login terlebih dahulu.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $claimId = (int) ($_POST['claim_id'] ?? $_GET['id'] ?? 0);

        if ($claimId <= 0) {
            flash('message', 'Klaim tidak valid.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $claim = $this->claimModel->getById($claimId);

        if (!$claim) {
            flash('message', 'Klaim tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $item = $this->itemModel->getById($claim['item_id']);

        if (!$item) {
            flash('message', 'Item tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        if ((int) $item['user_id'] !== $_SESSION['user_id'] && !isAdmin()) {
            flash('message', 'Anda tidak memiliki akses untuk memverifikasi klaim ini.', 'error');
            redirect('index.php?page=items&action=show&id=' . $item['id']);
            return;
        }

        if ($claim['status'] !== 'pending') {
            flash('message', 'Klaim ini sudah diproses sebelumnya.', 'error');
            redirect('index.php?page=items&action=show&id=' . $item['id']);
            return;
        }

        $verified = $this->claimModel->verifyClaim($claimId, $item['id']);

        if (!$verified) {
            flash('message', 'Gagal memverifikasi klaim.', 'error');
            redirect('index.php?page=items&action=show&id=' . $item['id']);
            return;
        }

        $this->notificationModel->create(
            (int) $claim['user_id'],
            'Klaim Diverifikasi! 🎉',
            'Klaim Anda untuk "' . $item['title'] . '" telah diverifikasi! Silakan hubungi pemilik untuk pengambilan.',
            'index.php?page=items&action=show&id=' . $item['id'],
            'claim_accepted'
        );

        flash('message', 'Klaim berhasil diverifikasi! Item telah ditandai sebagai selesai.', 'success');
        redirect('index.php?page=items&action=show&id=' . $item['id']);
    }

    
    public function reject(): void
    {

        if (!isLoggedIn()) {
            flash('message', 'Silakan login terlebih dahulu.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $claimId = (int) ($_POST['claim_id'] ?? $_GET['id'] ?? 0);
        $notes = clean($_POST['notes'] ?? '');

        if ($claimId <= 0) {
            flash('message', 'Klaim tidak valid.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $claim = $this->claimModel->getById($claimId);

        if (!$claim) {
            flash('message', 'Klaim tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        $item = $this->itemModel->getById($claim['item_id']);

        if (!$item) {
            flash('message', 'Item tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        if ((int) $item['user_id'] !== $_SESSION['user_id'] && !isAdmin()) {
            flash('message', 'Anda tidak memiliki akses untuk menolak klaim ini.', 'error');
            redirect('index.php?page=items&action=show&id=' . $item['id']);
            return;
        }

        if ($claim['status'] !== 'pending') {
            flash('message', 'Klaim ini sudah diproses sebelumnya.', 'error');
            redirect('index.php?page=items&action=show&id=' . $item['id']);
            return;
        }

        $rejected = $this->claimModel->rejectClaim($claimId, !empty($notes) ? $notes : null);

        if (!$rejected) {
            flash('message', 'Gagal menolak klaim.', 'error');
            redirect('index.php?page=items&action=show&id=' . $item['id']);
            return;
        }

        $this->notificationModel->create(
            (int) $claim['user_id'],
            'Klaim Ditolak',
            'Klaim Anda untuk "' . $item['title'] . '" tidak diterima.' . (!empty($notes) ? ' Alasan: ' . $notes : ''),
            'index.php?page=items&action=show&id=' . $item['id'],
            'claim_rejected'
        );

        flash('message', 'Klaim berhasil ditolak.', 'success');
        redirect('index.php?page=items&action=show&id=' . $item['id']);
    }

    
    public function cancel(): void
    {

        if (!isLoggedIn()) {
            flash('message', 'Silakan login terlebih dahulu.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $claimId = (int) ($_POST['claim_id'] ?? $_GET['id'] ?? 0);

        if ($claimId <= 0) {
            flash('message', 'Klaim tidak valid.', 'error');
            redirect('index.php?page=claims&action=my');
            return;
        }

        $claim = $this->claimModel->getById($claimId);

        if (!$claim) {
            flash('message', 'Klaim tidak ditemukan.', 'error');
            redirect('index.php?page=claims&action=my');
            return;
        }

        if ((int) $claim['user_id'] !== $_SESSION['user_id']) {
            flash('message', 'Anda tidak dapat membatalkan klaim milik orang lain.', 'error');
            redirect('index.php?page=claims&action=my');
            return;
        }

        if ($claim['status'] !== 'pending') {
            flash('message', 'Hanya klaim dengan status pending yang dapat dibatalkan.', 'error');
            redirect('index.php?page=claims&action=my');
            return;
        }

        $cancelled = $this->claimModel->cancel($claimId, $_SESSION['user_id']);

        if (!$cancelled) {
            flash('message', 'Gagal membatalkan klaim.', 'error');
            redirect('index.php?page=claims&action=my');
            return;
        }

        flash('message', 'Klaim berhasil dibatalkan.', 'success');
        redirect('index.php?page=claims&action=my');
    }

    
    public function myClaims(): void
    {

        if (!isLoggedIn()) {
            flash('message', 'Silakan login terlebih dahulu.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $claims = $this->claimModel->getByUserId($_SESSION['user_id']);

        $pageTitle = 'Klaim Saya - myUnila Lost & Found';

        require_once __DIR__ . '/../views/claims/my_claims.php';
    }
}
