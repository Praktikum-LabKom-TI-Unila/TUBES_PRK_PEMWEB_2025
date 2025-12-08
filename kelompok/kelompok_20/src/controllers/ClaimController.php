<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Claim.php';
require_once __DIR__ . '/../models/Item.php';
require_once __DIR__ . '/../models/Notification.php';

/**
 * Claim Controller
 * Handles claim operations (store, verify, cancel)
 */
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

    /**
     * Store a new claim
     */
    public function store(): void
    {
        // Check if user is logged in
        if (!isLoggedIn()) {
            flash('message', 'Silakan login untuk mengajukan klaim.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $itemId = (int) ($_POST['item_id'] ?? 0);
        $answer = clean($_POST['answer'] ?? '');

        // Validate item ID
        if ($itemId <= 0) {
            flash('message', 'Item tidak valid.', 'error');
            redirect('index.php?page=items');
            return;
        }

        // Check if item exists
        $item = $this->itemModel->getById($itemId);
        if (!$item) {
            flash('message', 'Item tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        // Check: User cannot claim their own item
        if ((int) $item['user_id'] === $_SESSION['user_id']) {
            flash('message', 'Anda tidak dapat mengklaim barang milik sendiri.', 'error');
            redirect('index.php?page=items&action=show&id=' . $itemId);
            return;
        }

        // Check: Item must be open
        if ($item['status'] !== 'open') {
            flash('message', 'Item ini sudah tidak dapat diklaim.', 'error');
            redirect('index.php?page=items&action=show&id=' . $itemId);
            return;
        }

        // Check: User cannot claim twice
        if ($this->claimModel->hasClaimed($itemId, $_SESSION['user_id'])) {
            flash('message', 'Anda sudah mengajukan klaim untuk item ini.', 'error');
            redirect('index.php?page=items&action=show&id=' . $itemId);
            return;
        }

        // Safe Claim Logic: Validate answer if item has safe claim enabled
        if ($item['is_safe_claim'] && !empty($item['security_answer'])) {
            if (empty($answer)) {
                flash('message', 'Silakan jawab pertanyaan keamanan untuk mengklaim item ini.', 'error');
                redirect('index.php?page=items&action=show&id=' . $itemId);
                return;
            }

            // Compare answers (case-insensitive)
            if (strtolower(trim($answer)) !== strtolower(trim($item['security_answer']))) {
                flash('message', 'Jawaban keamanan tidak sesuai. Klaim tidak dapat diproses.', 'error');
                redirect('index.php?page=items&action=show&id=' . $itemId);
                return;
            }
        }

        // Create claim
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

        // Update item status to 'process' if this is the first claim
        $this->itemModel->updateStatus($itemId, 'process');

        // Send notification to Item Owner
        $this->notificationModel->create(
            (int) $item['user_id'],
            'Klaim Baru',
            $_SESSION['user']['name'] . ' mengajukan klaim untuk barang Anda: "' . $item['title'] . '"',
            'index.php?page=items&action=show&id=' . $itemId . '#claims'
        );

        flash('message', 'Klaim berhasil diajukan! Pemilik item akan menghubungi Anda.', 'success');
        redirect('index.php?page=items&action=show&id=' . $itemId);
    }

    /**
     * Verify a claim (only item owner can verify)
     */
    public function verify(): void
    {
        // Check if user is logged in
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

        // Get claim details
        $claim = $this->claimModel->getById($claimId);

        if (!$claim) {
            flash('message', 'Klaim tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        // Get item to verify ownership
        $item = $this->itemModel->getById($claim['item_id']);

        if (!$item) {
            flash('message', 'Item tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        // Only Item Owner can verify (or Admin)
        if ((int) $item['user_id'] !== $_SESSION['user_id'] && !isAdmin()) {
            flash('message', 'Anda tidak memiliki akses untuk memverifikasi klaim ini.', 'error');
            redirect('index.php?page=items&action=show&id=' . $item['id']);
            return;
        }

        // Check if claim is still pending
        if ($claim['status'] !== 'pending') {
            flash('message', 'Klaim ini sudah diproses sebelumnya.', 'error');
            redirect('index.php?page=items&action=show&id=' . $item['id']);
            return;
        }

        // Verify the claim (with transaction)
        $verified = $this->claimModel->verifyClaim($claimId, $item['id']);

        if (!$verified) {
            flash('message', 'Gagal memverifikasi klaim.', 'error');
            redirect('index.php?page=items&action=show&id=' . $item['id']);
            return;
        }

        // Send notification to successful claimer
        $this->notificationModel->create(
            (int) $claim['user_id'],
            'Klaim Diverifikasi! 🎉',
            'Klaim Anda untuk "' . $item['title'] . '" telah diverifikasi! Silakan hubungi pemilik untuk pengambilan.',
            'index.php?page=items&action=show&id=' . $item['id']
        );

        flash('message', 'Klaim berhasil diverifikasi! Item telah ditandai sebagai selesai.', 'success');
        redirect('index.php?page=items&action=show&id=' . $item['id']);
    }

    /**
     * Reject a claim (only item owner can reject)
     */
    public function reject(): void
    {
        // Check if user is logged in
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

        // Get claim details
        $claim = $this->claimModel->getById($claimId);

        if (!$claim) {
            flash('message', 'Klaim tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        // Get item to verify ownership
        $item = $this->itemModel->getById($claim['item_id']);

        if (!$item) {
            flash('message', 'Item tidak ditemukan.', 'error');
            redirect('index.php?page=items');
            return;
        }

        // Only Item Owner can reject (or Admin)
        if ((int) $item['user_id'] !== $_SESSION['user_id'] && !isAdmin()) {
            flash('message', 'Anda tidak memiliki akses untuk menolak klaim ini.', 'error');
            redirect('index.php?page=items&action=show&id=' . $item['id']);
            return;
        }

        // Check if claim is still pending
        if ($claim['status'] !== 'pending') {
            flash('message', 'Klaim ini sudah diproses sebelumnya.', 'error');
            redirect('index.php?page=items&action=show&id=' . $item['id']);
            return;
        }

        // Reject the claim
        $rejected = $this->claimModel->rejectClaim($claimId, !empty($notes) ? $notes : null);

        if (!$rejected) {
            flash('message', 'Gagal menolak klaim.', 'error');
            redirect('index.php?page=items&action=show&id=' . $item['id']);
            return;
        }

        // Send notification to rejected claimer
        $this->notificationModel->create(
            (int) $claim['user_id'],
            'Klaim Ditolak',
            'Klaim Anda untuk "' . $item['title'] . '" tidak diterima.' . (!empty($notes) ? ' Alasan: ' . $notes : ''),
            'index.php?page=items&action=show&id=' . $item['id']
        );

        flash('message', 'Klaim berhasil ditolak.', 'success');
        redirect('index.php?page=items&action=show&id=' . $item['id']);
    }

    /**
     * Cancel own pending claim
     */
    public function cancel(): void
    {
        // Check if user is logged in
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

        // Get claim details
        $claim = $this->claimModel->getById($claimId);

        if (!$claim) {
            flash('message', 'Klaim tidak ditemukan.', 'error');
            redirect('index.php?page=claims&action=my');
            return;
        }

        // Check ownership
        if ((int) $claim['user_id'] !== $_SESSION['user_id']) {
            flash('message', 'Anda tidak dapat membatalkan klaim milik orang lain.', 'error');
            redirect('index.php?page=claims&action=my');
            return;
        }

        // Check if claim is still pending
        if ($claim['status'] !== 'pending') {
            flash('message', 'Hanya klaim dengan status pending yang dapat dibatalkan.', 'error');
            redirect('index.php?page=claims&action=my');
            return;
        }

        // Cancel the claim
        $cancelled = $this->claimModel->cancel($claimId, $_SESSION['user_id']);

        if (!$cancelled) {
            flash('message', 'Gagal membatalkan klaim.', 'error');
            redirect('index.php?page=claims&action=my');
            return;
        }

        flash('message', 'Klaim berhasil dibatalkan.', 'success');
        redirect('index.php?page=claims&action=my');
    }

    /**
     * View user's claims
     */
    public function myClaims(): void
    {
        // Check if user is logged in
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
