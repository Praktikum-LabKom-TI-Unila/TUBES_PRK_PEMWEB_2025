<?php
// register.php
require __DIR__ . '/helpers.php';
global $config;
$cfg = $config;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) send_json(['error' => 'Invalid JSON body'], 400);

$required = ['nama','email','password'];
foreach ($required as $r) {
    if (empty($input[$r])) send_json(['error' => "$r required"], 400);
}

$nama = trim($input['nama']);
$email = trim($input['email']);
$password = $input['password'];
$telepon = isset($input['telepon']) ? trim($input['telepon']) : null;
$alamat = isset($input['alamat']) ? trim($input['alamat']) : null;
$role = isset($input['role']) ? $input['role'] : 'warga';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) send_json(['error' => 'Invalid email'], 400);
if (strlen($password) < 6) send_json(['error' => 'Password minimal 6 karakter'], 400);

if ($role === 'admin') {
    if (empty($input['admin_code']) || $input['admin_code'] !== $cfg['admin_code']) {
        send_json(['error' => 'Admin code invalid'], 403);
    }
}

$pdo = db();
$stmt = $pdo->prepare("SELECT id FROM pengguna WHERE email = :email");
$stmt->execute(['email' => $email]);
if ($stmt->fetch()) send_json(['error' => 'Email sudah terdaftar'], 409);

$hash = password_hash($password, PASSWORD_DEFAULT);
$ins = $pdo->prepare("INSERT INTO pengguna (nama,email,password_hash,role,telepon,alamat,created_at,updated_at) VALUES (:nama,:email,:password_hash,:role,:telepon,:alamat, NOW(), NOW())");
$ins->execute([
    'nama' => $nama,
    'email' => $email,
    'password_hash' => $hash,
    'role' => $role,
    'telepon' => $telepon,
    'alamat' => $alamat
]);

$id = $pdo->lastInsertId();
send_json(['message' => 'Registrasi berhasil', 'user_id' => (int)$id], 201);
