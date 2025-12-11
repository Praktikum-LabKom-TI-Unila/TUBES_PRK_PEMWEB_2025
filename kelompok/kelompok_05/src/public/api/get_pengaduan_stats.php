<?php

require '../../config/config.php';

header('Content-Type: application/json');

// Query total pengaduan dengan status pending atau proses (sedang diproses)
$query = "SELECT 
    COUNT(CASE WHEN status IN ('pending', 'proses') THEN 1 END) as processing,
    COUNT(*) as total
FROM pengaduan";

$result = mysqli_query($conn, $query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

$row = mysqli_fetch_assoc($result);
$processing = intval($row['processing']);
$total = intval($row['total']);

echo json_encode([
    'success' => true,
    'processing' => $processing,
    'total' => $total
]);
?>
