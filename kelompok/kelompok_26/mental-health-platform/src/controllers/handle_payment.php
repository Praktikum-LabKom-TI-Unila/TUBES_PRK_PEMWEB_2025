<?php
// src/controllers/handle_payment.php
// Handler untuk Payment & Subscription

// Cek session status sebelum start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $conn;
// Fallback: jika $conn tidak tersedia dari index.php
if (!isset($conn) || $conn === null) {
    require_once __DIR__ . '/../config/database.php';
}

// Verifikasi session user
if (!isset($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$user = $_SESSION['user'];
$user_id = $user['user_id'] ?? $user['id'] ?? null;

if (!$action || !$user_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// ===== CREATE SUBSCRIPTION ACTION =====
if ($action === 'create_subscription') {
    $plan = trim($_POST['plan'] ?? '');
    $price = intval($_POST['price'] ?? 0);

    // Validasi input
    if (empty($plan) || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Plan dan price harus valid']);
        exit;
    }

    // Validasi plan
    $valid_plans = ['daily', 'weekly', 'monthly'];
    if (!in_array($plan, $valid_plans)) {
        echo json_encode(['success' => false, 'message' => 'Plan tidak valid']);
        exit;
    }

    // Hitung duration berdasarkan plan
    $duration_days = [
        'daily' => 1,
        'weekly' => 7,
        'monthly' => 30
    ];

    $days = $duration_days[$plan] ?? 30;

    // Cek apakah sudah ada subscription aktif
    $check_stmt = $conn->prepare("
        SELECT subscription_id FROM subscription 
        WHERE user_id = ? AND status = 'active' AND end_date > NOW()
        LIMIT 1
    ");
    if ($check_stmt) {
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Ada subscription aktif, update akhir tanggal
            $existing = $check_result->fetch_assoc();
            $sub_id = $existing['subscription_id'];

            // Perpanjang subscription (tambah days ke end_date)
            $update_stmt = $conn->prepare("
                UPDATE subscription 
                SET end_date = DATE_ADD(end_date, INTERVAL ? DAY),
                    plan = ?
                WHERE subscription_id = ?
            ");
            if ($update_stmt) {
                $update_stmt->bind_param("isi", $days, $plan, $sub_id);
                $update_stmt->execute();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Subscription berhasil diperbarui',
                    'subscription_id' => $sub_id
                ]);
                exit;
            }
        }
    }

    // Tidak ada subscription aktif, buat baru
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime("+{$days} days"));
    $status = 'active';

    $insert_stmt = $conn->prepare("
        INSERT INTO subscription (user_id, plan, start_date, end_date, status, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");

    if (!$insert_stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit;
    }

    $insert_stmt->bind_param("issss", $user_id, $plan, $start_date, $end_date, $status);

    if (!$insert_stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Gagal membuat subscription']);
        exit;
    }

    // Ambil ID subscription yang baru dibuat
    $subscription_id = $insert_stmt->insert_id;

    // Buat record payment (coba dengan subscription_id; fallback tanpa jika kolom tidak ada)
    $payment_stmt = $conn->prepare("
        INSERT INTO payment (user_id, subscription_id, amount, status, created_at)
        VALUES (?, ?, ?, 'pending', NOW())
    ");

    if ($payment_stmt) {
        $payment_stmt->bind_param("iii", $user_id, $subscription_id, $price);
        $payment_stmt->execute();
    } else {
        // Fallback: tabel payment mungkin belum punya kolom subscription_id
        $fallback = $conn->prepare("
            INSERT INTO payment (user_id, amount, status, created_at)
            VALUES (?, ?, 'pending', NOW())
        ");
        if ($fallback) {
            $fallback->bind_param("ii", $user_id, $price);
            $fallback->execute();
        }
    }

    echo json_encode([
        'success' => true,
        'message' => 'Subscription berhasil dibuat',
        'subscription_id' => $subscription_id,
        'plan' => $plan,
        'start_date' => $start_date,
        'end_date' => $end_date
    ]);
    exit;
}

// ===== UPLOAD PAYMENT PROOF ACTION =====
elseif ($action === 'upload_proof') {
    $subscription_id = intval($_POST['subscription_id'] ?? 0);
    
    if (!$subscription_id || !isset($_FILES['proof_image']) || $_FILES['proof_image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'File tidak valid atau subscription_id tidak ditemukan']);
        exit;
    }
    
    // Validasi file
    $file = $_FILES['proof_image'];
    $file_size = $file['size'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    
    if ($file_size > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'File terlalu besar (maks 5MB)']);
        exit;
    }
    
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Tipe file tidak valid (JPEG, PNG, GIF saja)']);
        exit;
    }
    
    // Verifikasi subscription milik user
    $verify_stmt = $conn->prepare("SELECT subscription_id, plan FROM subscription WHERE subscription_id = ? AND user_id = ?");
    if (!$verify_stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit;
    }
    
    $verify_stmt->bind_param("ii", $subscription_id, $user_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    
    if ($verify_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Subscription tidak ditemukan']);
        exit;
    }
    
    $sub = $verify_result->fetch_assoc();
    
    // Upload file
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'payment_' . $user_id . '_' . $subscription_id . '_' . time() . '.' . $ext;
    $upload_dir = __DIR__ . '/../../uploads/payment_proofs/';
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    if (!move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
        echo json_encode(['success' => false, 'message' => 'Gagal upload file']);
        exit;
    }
    
    // Update payment record dengan proof image (fallback jika kolom subscription_id tidak ada)
    $payment_stmt = $conn->prepare("
        UPDATE payment 
        SET proof_image = ?, status = 'pending'
        WHERE user_id = ? AND subscription_id = ?
        LIMIT 1
    ");

    $payment_updated = false;
    if ($payment_stmt) {
        $payment_stmt->bind_param("sii", $filename, $user_id, $subscription_id);
        $payment_updated = $payment_stmt->execute();
    }

    if (!$payment_updated) {
        // Fallback: update payment tanpa subscription_id, ambil payment pending terbaru user
        $fallback = $conn->prepare("
            UPDATE payment
            SET proof_image = ?, status = 'pending'
            WHERE user_id = ? AND status = 'pending'
            ORDER BY created_at DESC
            LIMIT 1
        ");
        if ($fallback) {
            $fallback->bind_param("si", $filename, $user_id);
            $payment_updated = $fallback->execute();
        }
    }

    if (!$payment_updated) {
        unlink($upload_dir . $filename);
        echo json_encode(['success' => false, 'message' => 'Gagal menyimpan data pembayaran']);
        exit;
    }
    
    // LANGSUNG APPROVE: Update subscription status ke active dan set end_date
    $duration_days = ['daily' => 1, 'weekly' => 7, 'monthly' => 30];
    $days = $duration_days[$sub['plan']] ?? 30;
    $end_date = date('Y-m-d', strtotime("+{$days} days"));
    
    $update_sub_stmt = $conn->prepare("
        UPDATE subscription 
        SET status = 'active', end_date = ?
        WHERE subscription_id = ? AND user_id = ?
    ");
    
    if ($update_sub_stmt) {
        $update_sub_stmt->bind_param("sii", $end_date, $subscription_id, $user_id);
        $update_sub_stmt->execute();
    }
    
    // Update payment status ke approved (fallback jika kolom subscription_id tidak ada)
    $approve_payment = $conn->prepare("
        UPDATE payment 
        SET status = 'approved'
        WHERE subscription_id = ? AND user_id = ?
    ");
    
    $approved = false;
    if ($approve_payment) {
        $approve_payment->bind_param("ii", $subscription_id, $user_id);
        $approved = $approve_payment->execute();
    }

    if (!$approved) {
        $fallbackApprove = $conn->prepare("
            UPDATE payment
            SET status = 'approved'
            WHERE user_id = ? AND status = 'pending'
            ORDER BY created_at DESC
            LIMIT 1
        ");
        if ($fallbackApprove) {
            $fallbackApprove->bind_param("i", $user_id);
            $fallbackApprove->execute();
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Bukti pembayaran berhasil diunggah dan langganan diaktifkan',
        'subscription_id' => $subscription_id,
        'plan' => $sub['plan'],
        'end_date' => $end_date
    ]);
    exit;
}

// Action tidak valid
else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}
?>
