<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/auth.php';
require_once __DIR__ . '/../../../helpers/complaints.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$pelapor = require_pelapor();
$pdo = get_pdo();

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$limit = max(1, min($limit, 50));
$statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? trim($_GET['status']) : null;

if ($statusFilter !== null && !in_array($statusFilter, complaint_statuses(), true)) {
    response_error(422, 'Status tidak dikenal.', [
        'field' => 'status',
        'reason' => 'invalid_status',
    ]);
}

$conditions = ['reporter_id = :reporter_id'];
$params = [':reporter_id' => $pelapor['id']];

if ($statusFilter !== null) {
    $conditions[] = 'status = :status';
    $params[':status'] = $statusFilter;
}

$whereClause = implode(' AND ', $conditions);

$countStmt = $pdo->prepare("SELECT COUNT(*) AS total FROM complaints WHERE {$whereClause}");
$countStmt->execute($params);
$totalData = (int) $countStmt->fetchColumn();

$totalPage = (int) ceil($totalData / $limit ?: 1);
$offset = ($page - 1) * $limit;

$listStmt = $pdo->prepare(
    "SELECT id, title, status, category, address, created_at
     FROM complaints
     WHERE {$whereClause}
     ORDER BY created_at DESC
     LIMIT :limit OFFSET :offset"
);
foreach ($params as $key => $value) {
    $paramType = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
    $listStmt->bindValue($key, $value, $paramType);
}
$listStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$listStmt->execute();

$records = array_map(function ($row) {
    return [
        'id' => (int) $row['id'],
        'title' => $row['title'],
        'category' => $row['category'],
        'status' => $row['status'],
        'address' => $row['address'],
        'created_at' => $row['created_at'],
    ];
}, $listStmt->fetchAll());

response_success(200, 'Daftar pengaduan.', [
    'page' => $page,
    'limit' => $limit,
    'total_data' => $totalData,
    'total_page' => $totalPage,
    'records' => $records,
]);