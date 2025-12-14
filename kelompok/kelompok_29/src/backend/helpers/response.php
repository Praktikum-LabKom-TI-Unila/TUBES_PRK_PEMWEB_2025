<?php
function send_headers(int $code = 200): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 86400');
}

function response_success(int $code, string $message, array $data = []): void
{
    send_headers($code);

    echo json_encode([
        'status'  => 'success',
        'code'    => $code,
        'message' => $message,
        'data'    => $data,
        'errors'  => [],
    ]);
    exit;
}

function response_error(int $code, string $message, array $errors = []): void
{
    send_headers($code);

    echo json_encode([
        'status'  => 'error',
        'code'    => $code,
        'message' => $message,
        'data'    => [],
        'errors'  => $errors,
    ]);
    exit;
}
