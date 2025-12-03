<?php

header('Content-Type: application/json');

$routes = require 'routes.php';

$method = $_SERVER['REQUEST_METHOD'];

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

if (isset($routes[$method][$uri])) {
    list($controllerName, $methodName) = $routes[$method][$uri];

    require_once "controllers/$controllerName.php";
    $controller = new $controllerName;
    $controller->$methodName();
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Not found']);