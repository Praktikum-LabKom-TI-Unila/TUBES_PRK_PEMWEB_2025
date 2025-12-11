<?php
// src/controllers/handle_payment.php
// Handler untuk Payment & Subscription

session_start();
global $conn;

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

    // Buat record payment
    $payment_stmt = $conn->prepare("
        INSERT INTO payment (user_id, subscription_id, amount, status, created_at)
        VALUES (?, ?, ?, 'pending', NOW())
    ");

    if ($payment_stmt) {
        $payment_stmt->bind_param("iii", $user_id, $subscription_id, $price);
        $payment_stmt->execute();
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

// Action tidak valid
else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit;
}
?>
