<?php
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../routes/api.php';

function get_request_headers(): array
{
    if (function_exists('getallheaders')) {
        return getallheaders();
    }

    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (strpos($name, 'HTTP_') !== 0) {
            continue;
        }
        $headerName = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
        $headers[$headerName] = $value;
    }

    return $headers;
}

function apply_method_override(string $currentMethod, array $headers): string
{
    if ($currentMethod !== 'POST') {
        return $currentMethod;
    }

    $normalizedHeaders = [];
    foreach ($headers as $key => $value) {
        $normalizedHeaders[strtolower($key)] = $value;
    }

    $override = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']
        ?? ($normalizedHeaders['x-http-method-override'] ?? null)
        ?? ($_POST['_method'] ?? null);

    if (!$override) {
        return $currentMethod;
    }

    $override = strtoupper(trim($override));
    if (!in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
        return $currentMethod;
    }

    $_SERVER['REQUEST_METHOD'] = $override;
    return $override;
}

function hydrate_stream_multipart(string $effectiveMethod, string $originalMethod): void
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'multipart/form-data') === false) {
        return;
    }

    $manualMethods = ['PUT', 'PATCH', 'DELETE'];
    $shouldHydrateMethod = in_array($effectiveMethod, $manualMethods, true);
    $needsPostFallback = ($effectiveMethod === 'POST') && empty($_FILES);

    if (!$shouldHydrateMethod && !$needsPostFallback) {
        return;
    }

    if ($shouldHydrateMethod) {
        // Jika override dilakukan melalui POST, PHP sudah mengisi $_POST/$_FILES.
        if ($originalMethod !== $effectiveMethod) {
            return;
        }

        if (!empty($_POST) || !empty($_FILES)) {
            return;
        }
    } elseif (!$needsPostFallback) {
        return;
    }

    if (!preg_match('/boundary=(.*)$/', $contentType, $matches)) {
        return;
    }

    $boundary = trim($matches[1], "\"\" ");
    if ($boundary === '') {
        return;
    }

    $rawData = file_get_contents('php://input');
    if ($rawData === false || $rawData === '') {
        return;
    }

    $delimiter = '--' . $boundary;
    $parts = explode($delimiter, $rawData);

    foreach ($parts as $part) {
        if ($part === '' || $part === "--" || $part === "\r\n" || $part === "\n") {
            continue;
        }

        $part = ltrim($part, "\r\n");
        if ($part === '' || $part === '--') {
            continue;
        }

        $separatorPosition = strpos($part, "\r\n\r\n");
        if ($separatorPosition === false) {
            continue;
        }

        $rawHeaders = substr($part, 0, $separatorPosition);
        $body = substr($part, $separatorPosition + 4);

        if (substr($body, -2) === "\r\n") {
            $body = substr($body, 0, -2);
        }

        $headerLines = explode("\r\n", $rawHeaders);
        $headers = [];
        foreach ($headerLines as $line) {
            if (strpos($line, ':') === false) {
                continue;
            }
            [$headerName, $headerValue] = explode(':', $line, 2);
            $headers[strtolower(trim($headerName))] = trim($headerValue);
        }

        $contentDisposition = $headers['content-disposition'] ?? '';
        if (!preg_match('/name="([^"]+)"/', $contentDisposition, $nameMatch)) {
            continue;
        }

        $fieldName = $nameMatch[1];

        if (preg_match('/filename="([^"]*)"/', $contentDisposition, $filenameMatch)) {
            $filename = $filenameMatch[1];
            $tmpPath = tempnam(sys_get_temp_dir(), 'sipinda_put_');
            if ($tmpPath === false) {
                continue;
            }

            if (file_put_contents($tmpPath, $body) === false) {
                @unlink($tmpPath);
                continue;
            }

            $_FILES[$fieldName] = [
                'name' => $filename,
                'type' => $headers['content-type'] ?? 'application/octet-stream',
                'tmp_name' => $tmpPath,
                'size' => strlen($body),
                'error' => UPLOAD_ERR_OK,
                'is_stream_upload' => true,
            ];
        } else {
            if ($needsPostFallback && array_key_exists($fieldName, $_POST)) {
                continue;
            }
            $_POST[$fieldName] = $body;
        }
    }
}

function match_route_path(string $routePath, string $requestPath)
{
    if ($routePath === $requestPath) {
        return [];
    }

    if (strpos($routePath, '{') === false) {
        return false;
    }

    $paramNames = [];
    $pattern = preg_replace_callback('/\{(\w+)\}/', function ($matches) use (&$paramNames) {
        $paramNames[] = $matches[1];
        return '(?P<' . $matches[1] . '>[^/]+)';
    }, $routePath);

    $pattern = '#^' . $pattern . '$#';

    if (preg_match($pattern, $requestPath, $matches)) {
        $params = [];
        foreach ($paramNames as $name) {
            if (isset($matches[$name])) {
                $params[$name] = $matches[$name];
            }
        }

        return $params;
    }

    return false;
}

$originalMethod = $_SERVER['REQUEST_METHOD'];
$headers = get_request_headers();
$method = apply_method_override(strtoupper($originalMethod), $headers);
hydrate_stream_multipart($method, strtoupper($originalMethod));

if ($method === 'OPTIONS') {
    exit;
}

$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/backend/public';
$path = trim(str_replace($basePath, '', $requestUri), '/');

if ($path === 'api') {
    $path = '';
} elseif (strpos($path, 'api/') === 0) {
    $path = substr($path, 4);
}

foreach ($routes as $route) {
    if ($route['method'] !== $method) {
        continue;
    }

    $routePath = trim($route['path'], '/');
    $matched = match_route_path($routePath, $path);

    if ($matched === false) {
        if ($routePath !== $path) {
            continue;
        }
        $matched = [];
    }

    $_GET = array_merge($matched, $_GET);
    require_once $route['controller'];
    exit;
}

response_error(404, 'Endpoint tidak ditemukan.');
