<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

// Simple validation
if (!isset($_SESSION['user'])) {
    die(json_encode(['success' => false, 'message' => 'Not logged in']));
}

// Get database connection
require_once __DIR__ . '/../config/database.php';

$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user']['user_id'] ?? $_SESSION['user']['id'];

// CREATE SUBSCRIPTION
if ($action === 'create_subscription') {
    $plan = $_POST['plan'] ?? '';
    $price = intval($_POST['price'] ?? 0);
    
    $days = ['daily' => 1, 'weekly' => 7, 'monthly' => 30][$plan] ?? 0;
    
    if ($days === 0) {
        die(json_encode(['success' => false, 'message' => 'Invalid plan']));
    }
    
    date_default_timezone_set('Asia/Jakarta');
    
    // Cek apakah sudah ada subscription aktif
    $check = $conn->prepare("SELECT subscription_id, end_date FROM subscription WHERE user_id = ? AND status = 'active' ORDER BY end_date DESC LIMIT 1");
    $check->bind_param("i", $user_id);
    $check->execute();
    $existing = $check->get_result()->fetch_assoc();
    
    if ($existing) {
        // Sudah ada subscription aktif - EXTEND end_date
        $current_end = $existing['end_date'];
        $sub_id = $existing['subscription_id'];
        
        // Tambah durasi dari end_date yang ada (bukan dari hari ini)
        $new_end = date('Y-m-d', strtotime("$current_end +$days days"));
        
        $update = $conn->prepare("UPDATE subscription SET end_date = ?, plan = ? WHERE subscription_id = ?");
        $update->bind_param("ssi", $new_end, $plan, $sub_id);
        
        if (!$update->execute()) {
            die(json_encode(['success' => false, 'message' => 'Update error: ' . $update->error]));
        }
        
        $start = $current_end;
        $end = $new_end;
    } else {
        // Belum ada subscription - INSERT baru
        $start = date('Y-m-d');
        $end = date('Y-m-d', strtotime("+$days days"));
        
        $sql = "INSERT INTO subscription (user_id, plan, start_date, end_date, status, created_at) VALUES (?, ?, ?, ?, 'active', NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $user_id, $plan, $start, $end);
        
        if (!$stmt->execute()) {
            die(json_encode(['success' => false, 'message' => 'DB error: ' . $stmt->error]));
        }
        
        $sub_id = $stmt->insert_id;
    }
    
    // Insert payment with session_id=NULL (subscription payments tidak terkait dengan chat session)
    // NOTE: Jalankan database/fix_payment_fk.sql untuk make session_id nullable
    $pay_sql = "INSERT INTO payment (user_id, session_id, amount, status, created_at) VALUES (?, NULL, ?, 'pending', NOW())";
    $pay_stmt = $conn->prepare($pay_sql);
    $pay_stmt->bind_param("ii", $user_id, $price);
    
    if (!$pay_stmt->execute()) {
        die(json_encode(['success' => false, 'message' => 'Payment insert error. Run database/fix_payment_fk.sql first: ' . $pay_stmt->error]));
    }
    
    die(json_encode([
        'success' => true,
        'subscription_id' => $sub_id,
        'plan' => $plan,
        'start_date' => $start,
        'end_date' => $end,
        'message' => 'Paket berhasil dipilih'
    ]));
}

// UPLOAD PROOF  
if ($action === 'upload_proof') {
    $sub_id = intval($_POST['subscription_id'] ?? 0);
    
    if (!isset($_FILES['proof_image']) || $_FILES['proof_image']['error'] !== UPLOAD_ERR_OK) {
        die(json_encode(['success' => false, 'message' => 'File tidak valid']));
    }
    
    $file = $_FILES['proof_image'];
    
    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        die(json_encode(['success' => false, 'message' => 'File terlalu besar (max 5MB)']));
    }
    
    // Validate file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
        die(json_encode(['success' => false, 'message' => 'Format file harus JPEG, PNG, atau GIF']));
    }
    
    // Verify subscription belongs to user
    $check = $conn->prepare("SELECT subscription_id FROM subscription WHERE subscription_id=? AND user_id=?");
    $check->bind_param("ii", $sub_id, $user_id);
    $check->execute();
    
    if ($check->get_result()->num_rows === 0) {
        die(json_encode(['success' => false, 'message' => 'Subscription tidak ditemukan']));
    }
    
    // Save file
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = "proof_{$user_id}_{$sub_id}_" . time() . ".$ext";
    $dir = __DIR__ . '/../../uploads/payment_proofs/';
    
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) {
        die(json_encode(['success' => false, 'message' => 'Gagal upload file']));
    }
    
    // Update payment status to approved
    $update = $conn->prepare("UPDATE payment SET proof_image=?, status='approved' WHERE user_id=? AND status='pending' ORDER BY created_at DESC LIMIT 1");
    $update->bind_param("si", $filename, $user_id);
    
    if (!$update->execute()) {
        die(json_encode(['success' => false, 'message' => 'Gagal update payment']));
    }
    
    die(json_encode(['success' => true, 'message' => 'Bukti pembayaran berhasil diupload - paket aktif!']));
}

die(json_encode(['success' => false, 'message' => 'Unknown action']));
