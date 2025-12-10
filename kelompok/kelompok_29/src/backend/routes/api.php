<?php
$routes = [
    [
        'method' => 'POST',
        'path' => 'auth/login',
        'controller' => __DIR__ . '/../controllers/auth/login.php',
    ],
    [
        'method' => 'POST',
        'path' => 'auth/logout',
        'controller' => __DIR__ . '/../controllers/auth/logout.php',
    ],
    [
        'method' => 'GET',
        'path' => 'pelapor/dashboard/stats',
        'controller' => __DIR__ . '/../controllers/pelapor/dashboard/stats.php',
    ],
    [
        'method' => 'GET',
        'path' => 'pelapor/complaints/recent',
        'controller' => __DIR__ . '/../controllers/pelapor/complaints/recent.php',
    ],
    [
        'method' => 'GET',
        'path' => 'pelapor/profile',
        'controller' => __DIR__ . '/../controllers/pelapor/profile/show.php',
    ],
    [
        'method' => 'PUT',
        'path' => 'pelapor/profile',
        'controller' => __DIR__ . '/../controllers/pelapor/profile/update.php',
    ],
    [
        'method' => 'GET',
        'path' => 'complaints/categories',
        'controller' => __DIR__ . '/../controllers/complaints/categories.php',
    ],
    [
        'method' => 'POST',
        'path' => 'complaints',
        'controller' => __DIR__ . '/../controllers/complaints/store.php',
    ],
    [
        'method' => 'GET',
        'path' => 'pelapor/complaints',
        'controller' => __DIR__ . '/../controllers/pelapor/complaints/index.php',
    ],
    [
        'method' => 'GET',
        'path' => 'pelapor/complaints/{id}',
        'controller' => __DIR__ . '/../controllers/pelapor/complaints/show.php',
    ],
    [
        'method' => 'GET',
        'path' => 'pelapor/complaints/{id}/timeline',
        'controller' => __DIR__ . '/../controllers/pelapor/complaints/timeline.php',
    ],
    [
        'method' => 'GET',
        'path' => 'admin/dashboard/stats',
        'controller' => __DIR__ . '/../controllers/admin/dashboard/stats.php',
    ],
    [
        'method' => 'GET',
        'path' => 'admin/dashboard/recent-activities',
        'controller' => __DIR__ . '/../controllers/admin/dashboard/recent_activities.php',
    ],
    [
        'method' => 'GET',
        'path' => 'admin/tickets',
        'controller' => __DIR__ . '/../controllers/admin/tickets/index.php',
    ],
    [
        'method' => 'GET',
        'path' => 'admin/tickets/search',
        'controller' => __DIR__ . '/../controllers/admin/tickets/search.php',
    ],
    [
        'method' => 'GET',
        'path' => 'health',
        'controller' => __DIR__ . '/../controllers/health.php',
    ],
];
