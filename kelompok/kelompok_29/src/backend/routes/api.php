<?php

$routes = [
    // Auth
    [
        'method' => 'POST',
        'path' => 'auth/login',
        'controller' => __DIR__ . '/../controllers/auth/login.php',
    ],
    [
        'method' => 'POST',
        'path' => 'auth/register',
        'controller' => __DIR__ . '/../controllers/auth/register.php',
    ],
    [
        'method' => 'POST',
        'path' => 'auth/logout',
        'controller' => __DIR__ . '/../controllers/auth/logout.php',
    ],

    // Admin dashboard & tickets
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
        'path' => 'admin/tickets/{id}',
        'controller' => __DIR__ . '/../controllers/admin/tickets/show.php',
    ],
    [
        'method' => 'POST',
        'path' => 'admin/tickets/{id}/verify',
        'controller' => __DIR__ . '/../controllers/admin/tickets/verify.php',
    ],
    [
        'method' => 'POST',
        'path' => 'admin/tickets/{id}/reject',
        'controller' => __DIR__ . '/../controllers/admin/tickets/reject.php',
    ],
    [
        'method' => 'GET',
        'path' => 'admin/officers/available',
        'controller' => __DIR__ . '/../controllers/admin/officers/available.php',
    ],
    [
        'method' => 'POST',
        'path' => 'admin/tickets/{id}/assign-officer',
        'controller' => __DIR__ . '/../controllers/admin/tickets/assign_officer.php',
    ],
    [
        'method' => 'GET',
        'path' => 'admin/tickets/{id}/proof',
        'controller' => __DIR__ . '/../controllers/admin/tickets/proof.php',
    ],
    [
        'method' => 'POST',
        'path' => 'admin/tickets/{id}/validate',
        'controller' => __DIR__ . '/../controllers/admin/tickets/validate.php',
    ],

    // Admin officer management
    [
        'method' => 'POST',
        'path' => 'admin/officers/create',
        'controller' => __DIR__ . '/../controllers/admin/officers/create.php',
    ],
    [
        'method' => 'GET',
        'path' => 'admin/officers',
        'controller' => __DIR__ . '/../controllers/admin/officers/index.php',
    ],
    [
        'method' => 'GET',
        'path' => 'admin/officers/{id}',
        'controller' => __DIR__ . '/../controllers/admin/officers/show.php',
    ],
    [
        'method' => 'GET',
        'path' => 'admin/officers/{id}/tasks',
        'controller' => __DIR__ . '/../controllers/admin/officers/tasks.php',
    ],

    // Admin profile
    [
        'method' => 'GET',
        'path' => 'admin/profile',
        'controller' => __DIR__ . '/../controllers/admin/profile/show.php',
    ],
    [
        'method' => 'PUT',
        'path' => 'admin/profile',
        'controller' => __DIR__ . '/../controllers/admin/profile/update.php',
    ],

    // Pelapor dashboard & profile
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

    // Pelapor complaints
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

    // Officer dashboard & profile
    [
        'method' => 'GET',
        'path' => 'officer/dashboard/stats',
        'controller' => __DIR__ . '/../controllers/officer/dashboard/stats.php',
    ],
    [
        'method' => 'GET',
        'path' => 'officer/profile',
        'controller' => __DIR__ . '/../controllers/officer/profile/show.php',
    ],
    [
        'method' => 'PUT',
        'path' => 'officer/profile',
        'controller' => __DIR__ . '/../controllers/officer/profile/update.php',
    ],

    // Officer tasks
    [
        'method' => 'GET',
        'path' => 'officer/tasks/active',
        'controller' => __DIR__ . '/../controllers/officer/tasks/active.php',
    ],
    [
        'method' => 'GET',
        'path' => 'officer/tasks/completed',
        'controller' => __DIR__ . '/../controllers/officer/tasks/completed.php',
    ],
    [
        'method' => 'GET',
        'path' => 'officer/tasks/{id}',
        'controller' => __DIR__ . '/../controllers/officer/tasks/show.php',
    ],
    [
        'method' => 'GET',
        'path' => 'officer/tasks/{id}/timeline',
        'controller' => __DIR__ . '/../controllers/officer/tasks/timeline.php',
    ],
    [
        'method' => 'POST',
        'path' => 'officer/tasks/{id}/start',
        'controller' => __DIR__ . '/../controllers/officer/tasks/start.php',
    ],
    [
        'method' => 'POST',
        'path' => 'officer/tasks/{id}/cancel',
        'controller' => __DIR__ . '/../controllers/officer/tasks/cancel.php',
    ],
    [
        'method' => 'POST',
        'path' => 'officer/tasks/{id}/complete',
        'controller' => __DIR__ . '/../controllers/officer/tasks/complete.php',
    ],
    [
        'method' => 'GET',
        'path' => 'officer/tasks/{id}/completion-proof',
        'controller' => __DIR__ . '/../controllers/officer/tasks/completion_proof/show.php',
    ],
    [
        'method' => 'PUT',
        'path' => 'officer/tasks/{id}/completion-proof',
        'controller' => __DIR__ . '/../controllers/officer/tasks/completion_proof/update.php',
    ],

    // Complaints submission and meta
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

    // Utility
    [
        'method' => 'GET',
        'path' => 'health',
        'controller' => __DIR__ . '/../controllers/health.php',
    ],
];
