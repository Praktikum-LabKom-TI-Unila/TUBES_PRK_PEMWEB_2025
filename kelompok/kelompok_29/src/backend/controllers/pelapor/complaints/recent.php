<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$pelapor = require_pelapor();
$pdo = get_pdo();

$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
$limit = max(1, min($limit, 20));

$stmt = $pdo->prepare(
    'SELECT id, title, status, category, address, created_at
     FROM complaints
     WHERE reporter_id = :reporter_id
     ORDER BY created_at DESC
     LIMIT :limit'
);
$stmt->bindValue(':reporter_id', $pelapor['id'], PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();

$complaints = $stmt->fetchAll();

response_success(200, 'Daftar pengaduan terbaru.', [
    'limit' => $limit,
    'records' => array_map(function ($row) {
        return [
            'id' => (int) $row['id'],
            'title' => $row['title'],
            'category' => $row['category'],
            'status' => $row['status'],
            'address' => $row['address'],
            'created_at' => $row['created_at'],
        ];
    }, $complaints),
]);