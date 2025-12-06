<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database.php';

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'Core\\' => __DIR__ . '/core/',
        'Controllers\\' => __DIR__ . '/controllers/',
        'Models\\' => __DIR__ . '/models/',
        'Middleware\\' => __DIR__ . '/middleware/',
        'Bootstrap\\' => __DIR__ . '/bootstrap/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        if (str_starts_with($class, $prefix)) {
            $relative = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
            if (is_file($file)) {
                require_once $file;
            }
        }
    }
});

use Bootstrap\UserSeeder;

UserSeeder::seedDefaults();

fwrite(STDOUT, "Default users seeded (owner, staff, member)\n");
