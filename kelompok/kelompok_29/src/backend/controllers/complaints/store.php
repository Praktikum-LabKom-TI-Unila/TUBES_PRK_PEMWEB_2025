<?php
require_once __DIR__ . '/../../helpers/response.php';
require_once __DIR__ . '/../../helpers/validation.php';
require_once __DIR__ . '/../../helpers/database.php';
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../helpers/upload.php';
require_once __DIR__ . '/../../helpers/complaints.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_error(405, 'Method tidak diperbolehkan.');
}

$pelapor = require_pelapor();
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
$isMultipart = stripos($contentType, 'multipart/form-data') !== false;

if ($isMultipart) {
    $payload = $_POST ?? [];
} else {
    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true) ?? [];
}

unset($payload['_method']);

$payload = array_map(function ($value) {
    return is_string($value) ? trim($value) : $value;
}, $payload);

if (empty($payload['photo_base64']) && !empty($payload['photo']) && strpos($payload['photo'], 'data:') === 0) {
    $payload['photo_base64'] = $payload['photo'];
}

unset($payload['photo']);

$required = ['category', 'title', 'description', 'address'];
$errors = require_fields($payload, $required);

$allowedCategories = array_column(complaint_categories(), 'id');
if (!empty($payload['category']) && !in_array($payload['category'], $allowedCategories, true)) {
    $errors[] = ['field' => 'category', 'message' => 'Kategori tidak dikenal.'];
}

if (isset($payload['latitude']) && $payload['latitude'] !== '' && !is_numeric($payload['latitude'])) {
    $errors[] = ['field' => 'latitude', 'message' => 'Latitude harus numerik.'];
}

if (isset($payload['longitude']) && $payload['longitude'] !== '' && !is_numeric($payload['longitude'])) {
    $errors[] = ['field' => 'longitude', 'message' => 'Longitude harus numerik.'];
}

if ($errors) {
    response_error(422, 'Validasi gagal.', $errors);
}

$photoPath = null;
$uploadFields = ['photo', 'photo_before'];
$uploadedPhoto = find_uploaded_file($_FILES ?? [], $uploadFields);

if ($uploadedPhoto) {
    $photoPath = save_uploaded_file($uploadedPhoto, 'complaints');
} elseif (!empty($payload['photo_base64'])) {
    $photoPath = save_base64_image($payload['photo_base64'], 'complaints');
}

$latitude = isset($payload['latitude']) && $payload['latitude'] !== '' ? (float) $payload['latitude'] : null;
$longitude = isset($payload['longitude']) && $payload['longitude'] !== '' ? (float) $payload['longitude'] : null;

$pdo = get_pdo();
$pdo->beginTransaction();

try {
    $insertStmt = $pdo->prepare(
        'INSERT INTO complaints (reporter_id, category, title, description, photo_before, latitude, longitude, address, status, created_at, updated_at)
         VALUES (:reporter_id, :category, :title, :description, :photo_before, :latitude, :longitude, :address, :status, NOW(), NOW())'
    );
    $insertStmt->execute([
        ':reporter_id' => $pelapor['id'],
        ':category' => $payload['category'],
        ':title' => $payload['title'],
        ':description' => $payload['description'],
        ':photo_before' => $photoPath,
    ':latitude' => $latitude,
    ':longitude' => $longitude,
        ':address' => $payload['address'],
        ':status' => 'diajukan',
    ]);

    $complaintId = (int) $pdo->lastInsertId();

    $progressStmt = $pdo->prepare(
        'INSERT INTO complaint_progress (complaint_id, status, note, created_by, created_at)
         VALUES (:complaint_id, :status, :note, :created_by, NOW())'
    );
    $progressStmt->execute([
        ':complaint_id' => $complaintId,
        ':status' => 'diajukan',
        ':note' => 'Pelapor mengajukan laporan melalui aplikasi.',
        ':created_by' => $pelapor['id'],
    ]);

    $pdo->commit();

    response_success(201, 'Pengaduan berhasil dibuat.', [
        'id' => $complaintId,
        'title' => $payload['title'],
        'status' => 'diajukan',
        'photo_before' => $photoPath,
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    response_error(500, 'Terjadi kesalahan pada server.', [
        'reason' => 'database_error',
        'detail' => $e->getMessage(),
    ]);
}