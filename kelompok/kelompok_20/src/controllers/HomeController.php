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

        $stats = [
            'total_lost' => $this->itemModel->countByType('lost'),
            'total_found' => $this->itemModel->countByType('found'),
            'total_returned' => $this->itemModel->countByStatus('closed')
        ];

        $recentItems = $this->itemModel->getRecent(6);

        require_once __DIR__ . '/../views/home/index.php';
    }
}
