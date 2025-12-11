<?php

require '../../config/config.php';

header('Content-Type: application/json');

// Query total UMKM dengan status approved
$query = "SELECT COUNT(*) as total FROM umkm WHERE status = 'approved'";
$result = mysqli_query($conn, $query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

$row = mysqli_fetch_assoc($result);
$total_umkm = intval($row['total']);

echo json_encode([
    'success' => true,
    'total_umkm' => $total_umkm
]);
?>
