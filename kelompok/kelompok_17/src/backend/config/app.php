<?php

if (!defined('BASE_PATH')) {
    http_response_code(403);
    exit('Direct access forbidden');
}

return [
    'name' => 'SIMORA',
    'version' => '1.0.0',
    'environment' => 'development',
    'base_url' => 'http://localhost/TUBES PPW/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_17/src/',
    'api_url' => 'http://localhost/TUBES PPW/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_17/src/backend/api/',
    'debug' => true,
    'timezone' => 'Asia/Jakarta',
    'language' => 'id',
];
