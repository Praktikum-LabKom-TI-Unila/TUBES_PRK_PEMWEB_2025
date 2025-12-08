<?php

declare(strict_types=1);

final class HomeController
{
    private Item $itemModel;

    public function __construct()
    {
        require_once __DIR__ . '/../config/database.php';
        require_once __DIR__ . '/../models/Item.php';
        $this->itemModel = new Item();
    }

    public function index(): void
    {
        $pageTitle = 'Beranda - myUnila Lost & Found';

        // Fetch Statistics
        $stats = [
            'total_lost' => $this->itemModel->countByType('lost'),
            'total_found' => $this->itemModel->countByType('found'),
            'total_returned' => $this->itemModel->countByStatus('closed')
        ];

        // Fetch Recent Items (Latest 6)
        $recentItems = $this->itemModel->getRecent(6);

        // Load view directly (buffer is handled by index.php)
        require_once __DIR__ . '/../views/home/index.php';
    }
}
