<?php ?><?php
// src/api/recipes.php

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'config.php';
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$pdo = connectDB();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        // Ambil semua resep, di-JOIN biar namanya muncul (bukan cuma ID)
        $sql = "
            SELECT 
                mr.recipe_id,
                mr.menu_id,
                m.name as menu_name,
                mr.ingredient_id,
                i.name as ingredient_name,
                mr.qty_used,
                i.unit
            FROM menu_recipes mr
            JOIN menu m ON mr.menu_id = m.menu_id
            JOIN ingredients i ON mr.ingredient_id = i.ingredient_id
            ORDER BY m.name ASC
        ";
        
        $stmt = $pdo->query($sql);
        $data = $stmt->fetchAll();
        
        echo json_encode(['success' => true, 'data' => $data]);
    }
    
    else if ($method === 'POST') {
        // Tambah bahan ke resep menu
        $data = json_decode(file_get_contents("php://input"), true);
        
        $sql = "INSERT INTO menu_recipes (menu_id, ingredient_id, qty_used) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['menu_id'],
            $data['ingredient_id'],
            $data['qty_used']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Recipe item added']);
    }

    else if ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        $stmt = $pdo->prepare("DELETE FROM menu_recipes WHERE recipe_id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Recipe item deleted']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>