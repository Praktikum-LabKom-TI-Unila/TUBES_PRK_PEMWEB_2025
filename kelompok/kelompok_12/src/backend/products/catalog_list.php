<?php
session_start();
require_once '../../koneksi/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, code, name, category, unit, current_stock, selling_price
                           FROM products WHERE is_active = 1 ORDER BY category, name");
    $stmt->execute();
    $products = $stmt->fetchAll();

    echo json_encode([
        'status' => 'success',
        'data' => $products
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengambil data produk',
        'detail' => $e->getMessage()
    ]);
}
