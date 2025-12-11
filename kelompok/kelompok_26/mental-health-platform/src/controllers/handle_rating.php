<?php
// src/controllers/handle_rating.php

// Ensure clean JSON output without PHP warnings/notices
if (function_exists('ob_get_level')) {
    while (ob_get_level()) { ob_end_clean(); }
}
ini_set('display_errors', '0');
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_OFF);

session_start();
header('Content-Type: application/json');
header('Cache-Control: no-store');
http_response_code(200);

if (!isset($_SESSION['user'])) {
    die(json_encode(['success' => false, 'message' => 'Not logged in']));
}

require_once __DIR__ . '/../config/database.php';

$user_id = $_SESSION['user']['user_id'] ?? $_SESSION['user']['id'];
$action = $_POST['action'] ?? '';

if ($action === 'submit_rating') {
    $konselor_id = intval($_POST['konselor_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $session_id = isset($_POST['session_id']) ? intval($_POST['session_id']) : null;

    if ($konselor_id <= 0 || $rating < 1 || $rating > 5) {
        die(json_encode(['success' => false, 'message' => 'Rating harus 1-5']));
    }

    // Pastikan konselor ada
    $check = $conn->prepare("SELECT konselor_id FROM konselor WHERE konselor_id = ?");
    if (!$check) { echo json_encode(['success' => false, 'message' => 'DB error (prepare konselor)']); exit; }
    $check->bind_param("i", $konselor_id);
    if (!$check->execute()) { echo json_encode(['success' => false, 'message' => 'DB error (exec konselor)']); exit; }
    $exists = $check->get_result()->fetch_assoc();
    if (!$exists) {
        die(json_encode(['success' => false, 'message' => 'Konselor tidak ditemukan']));
    }

    // Simpan rating per-user; update jika sudah pernah rating konselor yang sama
    // Requires table `ratings` with UNIQUE (user_id, konselor_id)
    // Ensure table exists (fallback auto-migration if missing)
    $tableExists = $conn->query("SHOW TABLES LIKE 'ratings'");
    if (!$tableExists || $tableExists->num_rows === 0) {
        $migrationFile = dirname(__DIR__, 2) . '/database/create_ratings.sql';
        if (file_exists($migrationFile)) {
            $sql = file_get_contents($migrationFile);
            if ($sql !== false) {
                $conn->multi_query($sql);
                // flush results
                do { if ($r = $conn->store_result()) { $r->free(); } } while ($conn->more_results() && $conn->next_result());
            }
        }
    }

    $sql = "INSERT INTO ratings (user_id, konselor_id, session_id, rating) VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE rating = VALUES(rating), session_id = VALUES(session_id), created_at = CURRENT_TIMESTAMP";
    $ins = $conn->prepare($sql);
    if (!$ins) { echo json_encode(['success' => false, 'message' => 'DB error (prepare ratings)']); exit; }
    $ins->bind_param("iiii", $user_id, $konselor_id, $session_id, $rating);
    if (!$ins->execute()) {
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan rating']);
        exit;
    }

    // Hitung rata-rata dan simpan ke konselor.rating
    $avgQ = $conn->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS cnt FROM ratings WHERE konselor_id = ?");
    if (!$avgQ) { echo json_encode(['success' => false, 'message' => 'DB error (prepare avg)']); exit; }
    $avgQ->bind_param("i", $konselor_id);
    if (!$avgQ->execute()) { echo json_encode(['success' => false, 'message' => 'DB error (exec avg)']); exit; }
    $avgRow = $avgQ->get_result()->fetch_assoc();
    $avg = $avgRow && $avgRow['avg_rating'] ? floatval($avgRow['avg_rating']) : $rating;

    // Optional: bulatkan ke 1 desimal
    $avgRounded = round($avg, 1);
    $upd = $conn->prepare("UPDATE konselor SET rating = ? WHERE konselor_id = ?");
    if (!$upd) { echo json_encode(['success' => false, 'message' => 'DB error (prepare update konselor)']); exit; }
    $upd->bind_param("di", $avgRounded, $konselor_id);
    if (!$upd->execute()) {
        echo json_encode(['success' => false, 'message' => 'Gagal update rata-rata rating']);
        exit;
    }

    echo json_encode(['success' => true, 'message' => 'Terima kasih! Rating tercatat dan dirata-ratakan.']);
    exit;
}

die(json_encode(['success' => false, 'message' => 'Unknown action']));

