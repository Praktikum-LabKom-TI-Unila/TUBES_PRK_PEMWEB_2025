<?php
require_once __DIR__ . '/../../../helpers/response.php';
require_once __DIR__ . '/../../../helpers/database.php';
require_once __DIR__ . '/../../../helpers/admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$admin = require_admin(); // require_admin memastikan Authorization Bearer admin valid

$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
if ($limit < 1) {
    $limit = 10;
}
$limit = min($limit, 50);
$offset = ($page - 1) * $limit;

$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : null;
$allowedStatuses = ['tersedia', 'sibuk'];
if ($statusFilter !== null && $statusFilter !== '' && !in_array($statusFilter, $allowedStatuses, true)) {
    response_error(422, 'Status filter tidak valid.', [
        [
            'field' => 'status',
            'reason' => 'invalid_status',
        ],
    ]);
}

$whereClauses = [];
$params = [];
if ($statusFilter) {
    $whereClauses[] = 'o.officer_status = :status';
    $params[':status'] = $statusFilter;
}
$whereSql = $whereClauses ? ('WHERE ' . implode(' AND ', $whereClauses)) : '';

$pdo = get_pdo();

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM officers o $whereSql");
$countStmt->execute($params);
$totalData = (int) $countStmt->fetchColumn();
$totalPage = $totalData > 0 ? (int) ceil($totalData / $limit) : 0;

$dataSql = "SELECT 
        o.id,
        o.employee_id,
        o.department,
        o.specialization,
        o.officer_status,
        o.created_at,
        u.full_name,
        u.email,
        u.phone,
        COALESCE(SUM(CASE WHEN ot.finished_at IS NULL THEN 1 ELSE 0 END), 0) AS active_tasks,
        COALESCE(SUM(CASE WHEN ot.finished_at IS NOT NULL THEN 1 ELSE 0 END), 0) AS completed_tasks
    FROM officers o
    JOIN users u ON u.id = o.user_id
    LEFT JOIN officer_tasks ot ON ot.officer_id = o.id
    $whereSql
    GROUP BY o.id, o.employee_id, o.department, o.specialization, o.officer_status, o.created_at, u.full_name, u.email, u.phone
    ORDER BY o.created_at DESC
    LIMIT :limit OFFSET :offset";

$dataStmt = $pdo->prepare($dataSql);
foreach ($params as $key => $value) {
    $dataStmt->bindValue($key, $value);
}
$dataStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$dataStmt->execute();

$records = [];
foreach ($dataStmt->fetchAll() as $row) {
    $records[] = [
        'id' => (int) $row['id'],
        'employee_id' => $row['employee_id'],
        'name' => $row['full_name'],
        'email' => $row['email'],
        'phone' => $row['phone'],
        'department' => $row['department'],
        'specialization' => $row['specialization'],
        'status' => $row['officer_status'],
        'active_tasks' => (int) $row['active_tasks'],
        'completed_tasks' => (int) $row['completed_tasks'],
    ];
}

response_success(200, 'Daftar petugas berhasil diambil.', [
    'page' => $page,
    'limit' => $limit,
    'total_data' => $totalData,
    'total_page' => $totalPage,
    'filters' => [
        'status' => $statusFilter,
    ],
    'records' => $records,
]);

/*
Contoh Request:
GET /api/admin/officers?page=1&limit=10&status=tersedia
Authorization: Bearer <token_admin>

Contoh Response Sukses:
{
  "status": "success",
  "code": 200,
  "message": "Daftar petugas berhasil diambil.",
  "data": {
    "page": 1,
    "limit": 10,
    "total_data": 2,
    "total_page": 1,
    "filters": {
      "status": "tersedia"
    },
    "records": [
      {
        "id": 8,
        "employee_id": "OFF-0008",
        "name": "Budi Handoko",
        "email": "budi@pemkot.go.id",
        "phone": "0812121212",
        "department": "Dinas Perhubungan",
        "specialization": "Rambu_Lalu_Lintas",
        "status": "tersedia",
        "active_tasks": 1,
        "completed_tasks": 5
      }
    ]
  },
  "errors": []
}

Contoh Response Error (status salah):
{
  "status": "error",
  "code": 422,
  "message": "Status filter tidak valid.",
  "data": [],
  "errors": [
    {
      "field": "status",
      "reason": "invalid_status"
    }
  ]
}
*/
