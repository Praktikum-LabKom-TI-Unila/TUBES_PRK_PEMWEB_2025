<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donatur') {
    header("Location: ../login.php");
    exit();
}

require_once '../config/database.php';

$donatur_id  = $_SESSION['user_id'];
$judul       = trim($_POST['judul'] ?? '');
$category_id = (int)($_POST['category_id'] ?? 0);
$deskripsi   = trim($_POST['deskripsi'] ?? '');
$jumlah_awal = (int)($_POST['jumlah_awal'] ?? 0);
$lokasi      = trim($_POST['lokasi_pickup'] ?? '');
$raw_batas   = $_POST['batas_waktu'] ?? '';
$jenis       = $_POST['jenis_makanan'] ?? 'halal';

// Convert datetime-local (2025-12-07T10:30) ke DATETIME MySQL (2025-12-07 10:30:00)
$batas_waktu = '';
if ($raw_batas !== '') {
    $batas_waktu = str_replace('T', ' ', $raw_batas) . ':00';
}

// Validasi input dasar
if (
    $judul === '' || $category_id <= 0 || $jumlah_awal <= 0 ||
    $lokasi === '' || $batas_waktu === ''
) {
    header("Location: ../donatur/add_food.php?status=error");
    exit();
}

// Proses upload foto (wajib)
$foto_path = null;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $tmp  = $_FILES['foto']['tmp_name'];
    $name = basename($_FILES['foto']['name']);
    $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

    // (Opsional) batasi ekstensi
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $allowed)) {
        header("Location: ../donatur/add_food.php?status=error");
        exit();
    }

    $newName   = 'food_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
    $uploadDir = '../images/foods/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $dest = $uploadDir . $newName;

    if (move_uploaded_file($tmp, $dest)) {
        // Simpan path relatif (tanpa ../) ke DB
        $foto_path = 'images/foods/' . $newName;
    }
}

// Kalau gagal upload, kirim balik error
if ($foto_path === null) {
    header("Location: ../donatur/add_food.php?status=error");
    exit();
}

// Insert ke database
$sql = "
    INSERT INTO food_stocks
    (donatur_id, category_id, judul, deskripsi, foto_path,
     jumlah_awal, stok_tersedia, lokasi_pickup, batas_waktu, jenis_makanan, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'tersedia')
";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "iisssiisss",
    $donatur_id,
    $category_id,
    $judul,
    $deskripsi,
    $foto_path,
    $jumlah_awal,
    $jumlah_awal,
    $lokasi,
    $batas_waktu,
    $jenis
);

if ($stmt->execute()) {
    header("Location: ../donatur/manage_food.php?status=created");
} else {
    header("Location: ../donatur/add_food.php?status=error");
}
exit();
