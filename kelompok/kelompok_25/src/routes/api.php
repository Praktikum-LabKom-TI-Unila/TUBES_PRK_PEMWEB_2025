<?php

/**
 * API Routes
 */

// Auth API routes
$router->post('/api/auth/login', 'api/AuthApiController@login');
$router->post('/api/auth/register', 'api/AuthApiController@register');
$router->post('/api/auth/logout', 'api/AuthApiController@logout');
$router->get('/api/auth/me', 'api/AuthApiController@me');
$router->get('/api/auth/check', 'api/AuthApiController@check');

// Materials API routes
$router->get('/api/materials', function() {
    AuthMiddleware::check();
    Response::success('Materials API endpoint', []);
});

$router->get('/api/materials/{id}', function($id) {
    AuthMiddleware::check();
    Response::success('Material detail', ['id' => $id]);
});

$router->post('/api/materials', function() {
    AuthMiddleware::check();
    RoleMiddleware::staff();
    Response::success('Create material endpoint', []);
});

$router->post('/api/materials/{id}', function($id) {
    AuthMiddleware::check();
    RoleMiddleware::staff();
    Response::success('Update material', ['id' => $id]);
});

$router->post('/api/materials/{id}/delete', function($id) {
    AuthMiddleware::check();
    RoleMiddleware::manager();
    Response::success('Delete material', ['id' => $id]);
});

// Stock API routes
$router->get('/api/stock', function() {
    AuthMiddleware::check();
    Response::success('Stock API endpoint', []);
});

$router->post('/api/stock/in', function() {
    AuthMiddleware::check();
    RoleMiddleware::staff();
    Response::success('Stock in endpoint', []);
});

$router->post('/api/stock/out', function() {
    AuthMiddleware::check();
    RoleMiddleware::staff();
    Response::success('Stock out endpoint', []);
});

// Reports API routes
$router->get('/api/reports/inventory', function() {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ReportsApiController.php';
    $controller = new ReportsApiController();
    $controller->inventory();
});

$router->get('/api/reports/transactions', function() {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ReportsApiController.php';
    $controller = new ReportsApiController();
    $controller->transactions();
});

$router->get('/api/reports/low-stock', function() {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ReportsApiController.php';
    $controller = new ReportsApiController();
    $controller->lowStock();
});

$router->get('/api/reports/material-trend/{id}', function($id) {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ReportsApiController.php';
    $controller = new ReportsApiController();
    $controller->materialTrend($id);
});

$router->get('/api/reports/category-distribution', function() {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ReportsApiController.php';
    $controller = new ReportsApiController();
    $controller->categoryDistribution();
});

$router->get('/api/reports/supplier-performance', function() {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ReportsApiController.php';
    $controller = new ReportsApiController();
    $controller->supplierPerformance();
});

$router->get('/api/reports/stock-movement/{id}', function($id) {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ReportsApiController.php';
    $controller = new ReportsApiController();
    $controller->stockMovement($id);
});

$router->get('/api/reports/top-materials', function() {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ReportsApiController.php';
    $controller = new ReportsApiController();
    $controller->topMaterials();
});

$router->get('/api/reports/stock-value-by-category', function() {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ReportsApiController.php';
    $controller = new ReportsApiController();
    $controller->stockValueByCategory();
});

// Activity Logs API routes
$router->get('/api/activity-logs', function() {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ActivityLogsApiController.php';
    $controller = new ActivityLogsApiController();
    $controller->index();
});

$router->get('/api/activity-logs/user/{id}', function($id) {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ActivityLogsApiController.php';
    $controller = new ActivityLogsApiController();
    $controller->byUser($id);
});

$router->get('/api/activity-logs/action/{action}', function($action) {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ActivityLogsApiController.php';
    $controller = new ActivityLogsApiController();
    $controller->byAction($action);
});

$router->get('/api/activity-logs/entity/{type}/{id}', function($type, $id) {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ActivityLogsApiController.php';
    $controller = new ActivityLogsApiController();
    $controller->byEntity($type, $id);
});

$router->get('/api/activity-logs/recent', function() {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ActivityLogsApiController.php';
    $controller = new ActivityLogsApiController();
    $controller->recent();
});

$router->post('/api/activity-logs/cleanup', function() {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/controllers/api/ActivityLogsApiController.php';
    $controller = new ActivityLogsApiController();
    $controller->cleanup();
});



// Legacy route for compatibility
$router->get('/api/transactions/trend', function() {
    AuthMiddleware::check();
    require_once ROOT_PATH . '/models/Transaction.php';
    require_once ROOT_PATH . '/controllers/web/TransactionController.php';
    $controller = new TransactionController();
    $controller->getTrendData();
});
