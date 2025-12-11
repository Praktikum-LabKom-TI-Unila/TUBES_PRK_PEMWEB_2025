<?php
/**
 * FITUR 1: AUTENTIKASI - LOGIN
 * API Endpoint untuk login user
 * 
 * Deskripsi: Handle login user dengan password verification
 * - Validasi credentials (email + password)
 * - Gunakan password_verify()
 * - Buat session dengan user data
 * - Return JSON dengan session ID dan redirect URL
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/session-helper.php';

$response = ['success' => false, 'message' => '', 'data' => null];

try {
    // Validasi request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    // Validasi input
    if (empty($input['email']) || empty($input['password'])) {
        throw new Exception('Email dan password harus diisi');
    }

    $email = trim($input['email']);
    $password = $input['password'];

    // Validasi email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Email format tidak valid');
    }

    // Query user by email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify user exists
    if (!$user) {
        throw new Exception('Email atau password salah');
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        throw new Exception('Email atau password salah');
    }

    // Create session token
    $sessionToken = createSessionToken();

    // Store session data
    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['nama_user'] = $user['nama_user'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['session_token'] = $sessionToken;
    $_SESSION['login_time'] = time();

    // Prepare response
    $response['success'] = true;
    $response['message'] = 'Login berhasil';
    $response['data'] = [
        'id_user' => $user['id_user'],
        'nama_user' => $user['nama_user'],
        'email' => $user['email'],
        'role' => $user['role'],
        'session_id' => $sessionToken,
        'redirect_url' => $user['role'] === 'dosen' ? 'dashboard-dosen.php' : 'dashboard-mahasiswa.php'
    ];

    http_response_code(200);
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    http_response_code(401);
}

echo json_encode($response);
?>
