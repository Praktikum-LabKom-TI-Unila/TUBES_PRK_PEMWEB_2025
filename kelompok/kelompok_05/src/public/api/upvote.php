<?php

session_start();
require '../../config/config.php';

header('Content-Type: application/json');

// Validasi method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validasi login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    http_response_code(401);
    echo json_encode([
        'success' => false, 
        'message' => 'Anda harus login terlebih dahulu',
        'require_login' => true
    ]);
    exit;
}

// Ambil data dari request
$pengaduan_id = isset($_POST['pengaduan_id']) ? intval($_POST['pengaduan_id']) : 0;
$user_id = $_SESSION['user_id'];

// Validasi pengaduan_id
if ($pengaduan_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID pengaduan tidak valid']);
    exit;
}

// Cek apakah pengaduan exists
$check_pengaduan = mysqli_query($conn, "SELECT id FROM pengaduan WHERE id = $pengaduan_id");
if (mysqli_num_rows($check_pengaduan) === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Pengaduan tidak ditemukan']);
    exit;
}

// Cek apakah user sudah upvote
$check_vote = mysqli_query($conn, 
    "SELECT id FROM pengaduan_upvotes WHERE pengaduan_id = $pengaduan_id AND user_id = $user_id"
);

if (mysqli_num_rows($check_vote) > 0) {
    // User sudah upvote, hapus upvote (toggle)
    $delete = mysqli_query($conn, 
        "DELETE FROM pengaduan_upvotes WHERE pengaduan_id = $pengaduan_id AND user_id = $user_id"
    );
    
    if ($delete) {
        // Hitung total upvotes
        $count_result = mysqli_query($conn, 
            "SELECT COUNT(*) as total FROM pengaduan_upvotes WHERE pengaduan_id = $pengaduan_id"
        );
        $count = mysqli_fetch_assoc($count_result)['total'];
        
        echo json_encode([
            'success' => true, 
            'message' => 'UpVote dibatalkan',
            'action' => 'removed',
            'total_upvotes' => intval($count)
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal membatalkan upvote']);
    }
} else {
    // User belum upvote, tambahkan
    $insert = mysqli_query($conn, 
        "INSERT INTO pengaduan_upvotes (pengaduan_id, user_id) VALUES ($pengaduan_id, $user_id)"
    );
    
    if ($insert) {
        // Hitung total upvotes
        $count_result = mysqli_query($conn, 
            "SELECT COUNT(*) as total FROM pengaduan_upvotes WHERE pengaduan_id = $pengaduan_id"
        );
        $count = mysqli_fetch_assoc($count_result)['total'];
        
        echo json_encode([
            'success' => true, 
            'message' => 'UpVote berhasil ditambahkan',
            'action' => 'added',
            'total_upvotes' => intval($count)
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan upvote']);
    }
}
?>
