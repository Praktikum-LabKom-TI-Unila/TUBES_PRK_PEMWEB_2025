<?php
require_once '../config.php';
require_once '../db.php';

header('Content-Type: application/json');

$pdo = connectDB();

function get($key, $default = null) {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

$start = get('start_date');
$end   = get('end_date');
$operator = get('operator_id', 'all');
$limit = intval(get('limit', 15));

if (!$start || !$end) {
    echo json_encode([
        "success" => false,
        "message" => "Missing start or end date."
    ]);
    exit;
}

// =======================
// LOAD OPERATORS
// =======================
$operators = $pdo->query("
    SELECT id, username AS label, username 
    FROM users
")->fetchAll();

// =======================
// LOAD METRICS
// =======================
$metrics = $pdo->prepare("
    SELECT 
        SUM(orders.total) AS total_revenue,
        COUNT(*) AS total_transactions,
        (
            SELECT items.name FROM order_items
            JOIN items ON order_items.item_id = items.id
            GROUP BY item_id
            ORDER BY SUM(order_items.quantity) DESC
            LIMIT 1
        ) AS best_seller_name,
        (
            SELECT SUM(order_items.quantity) FROM order_items
            GROUP BY item_id
            ORDER BY SUM(order_items.quantity) DESC
            LIMIT 1
        ) AS best_seller_units,
        0.25 AS profit_margin
    FROM orders
    WHERE DATE(datetime) BETWEEN ? AND ?
");
$metrics->execute([$start, $end]);
$metrics = $metrics->fetch();

// =======================
// DAILY SALES FOR CHART
// =======================
$chart = $pdo->prepare("
    SELECT DATE(datetime) AS date, SUM(total) AS value
    FROM orders
    WHERE DATE(datetime) BETWEEN ? AND ?
    GROUP BY DATE(datetime)
    ORDER BY DATE(datetime)
");
$chart->execute([$start, $end]);
$chartRows = $chart->fetchAll();

$daily_sales = [];
foreach ($chartRows as $r) {
    $daily_sales[$r['date']] = intval($r['value']);
}

// =======================
// TRANSACTION HISTORY
// =======================
$sql = "
    SELECT 
        o.id AS trx_id,
        o.datetime,
        o.total,
        o.status,
        u.username AS operator_username,
        (
            SELECT GROUP_CONCAT(CONCAT(oi.quantity, 'x ', it.name) SEPARATOR ', ')
            FROM order_items oi
            JOIN items it ON it.id = oi.item_id
            WHERE oi.order_id = o.id
        ) AS item_summary
    FROM orders o
    LEFT JOIN users u ON u.id = o.operator_id
    WHERE DATE(o.datetime) BETWEEN ? AND ?
";
$params = [$start, $end];

if ($operator !== 'all') {
    $sql .= " AND o.operator_id = ? ";
    $params[] = $operator;
}

$sql .= " ORDER BY o.datetime DESC LIMIT ?";
$params[] = $limit;

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$history = $stmt->fetchAll();

// =======================
// FINAL OUTPUT
// =======================
echo json_encode([
    "success" => true,
    "filters" => [
        "start_date" => $start,
        "end_date" => $end,
        "operators" => $operators
    ],
    "metrics" => $metrics,
    "daily_sales" => $daily_sales,
    "transaction_history" => $history
]);

