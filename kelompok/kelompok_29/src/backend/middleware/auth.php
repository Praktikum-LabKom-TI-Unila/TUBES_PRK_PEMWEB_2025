<?php
require_once __DIR__ . '/../helpers/response.php';

function require_auth(): void
{
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';

    if ($token !== 'Bearer your-secret-token') {
        response_error(401, 'Unauthorized.');
    }
}
