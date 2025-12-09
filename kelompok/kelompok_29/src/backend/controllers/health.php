<?php
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/database.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$pdo = get_pdo();

try {
    $statement = $pdo->query('SELECT 1 AS connected');
    $result = $statement->fetch();

    response_success(200, 'Database OK', [
        'connected' => (bool) ($result['connected'] ?? false),
        'timestamp' => date('c'),
    ]);
} catch (PDOException $e) {
    response_error(500, 'Query health check gagal.', [
        'reason' => 'query_failed',
        'detail' => $e->getMessage(),
    ]);
}
