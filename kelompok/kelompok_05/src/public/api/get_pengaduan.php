<?php

session_start();
require '../../config/config.php';

header('Content-Type: application/json');

// Query pengaduan dengan total upvotes
$query = "
    SELECT 
        p.id,
        p.judul,
        p.deskripsi,
        p.lokasi,
        p.status,
        p.created_at,
        u.nama as pelapor,
        COUNT(DISTINCT uv.id) as total_upvotes,
        CASE 
            WHEN EXISTS (
                SELECT 1 FROM pengaduan_upvotes 
                WHERE pengaduan_id = p.id 
                AND user_id = " . (isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0) . "
            ) THEN 1 
            ELSE 0 
        END as user_upvoted
    FROM pengaduan p
    LEFT JOIN users u ON p.user_id = u.id
    LEFT JOIN pengaduan_upvotes uv ON p.id = uv.pengaduan_id
    GROUP BY p.id
    ORDER BY total_upvotes DESC, p.created_at DESC
    LIMIT 10
";

$result = mysqli_query($conn, $query);

if (!$result) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

$pengaduan_list = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pengaduan_list[] = [
        'id' => intval($row['id']),
        'judul' => htmlspecialchars($row['judul']),
        'deskripsi' => htmlspecialchars($row['deskripsi']),
        'lokasi' => htmlspecialchars($row['lokasi']),
        'status' => $row['status'],
        'created_at' => $row['created_at'],
        'pelapor' => htmlspecialchars($row['pelapor']),
        'total_upvotes' => intval($row['total_upvotes']),
        'user_upvoted' => intval($row['user_upvoted']) === 1
    ];
}

echo json_encode([
    'success' => true,
    'is_logged_in' => isset($_SESSION['login']) && $_SESSION['login'] === true,
    'data' => $pengaduan_list
]);
?>
