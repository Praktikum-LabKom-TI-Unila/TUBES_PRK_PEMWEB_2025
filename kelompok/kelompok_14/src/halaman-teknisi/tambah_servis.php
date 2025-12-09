<?php
// ============================================
// TAMBAH SERVICE - tambah_service.php
// ============================================
// File ini menangani penambahan service baru

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
    exit;
}

// Ambil data dari request
$customerName = isset($_POST['customerName']) ? htmlspecialchars($_POST['customerName']) : null;
$itemName = isset($_POST['itemName']) ? htmlspecialchars($_POST['itemName']) : null;
$status = isset($_POST['status']) ? intval($_POST['status']) : null;

// Validasi input
if (!$customerName || !$itemName) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nama pelanggan dan barang tidak boleh kosong']);
    exit;
}

if ($status === null || $status < 1 || $status > 4) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit;
}

// Status mapping
$statusMap = [
    1 => 'Diterima admin',
    2 => 'Dikerjakan oleh teknisi',
    3 => 'Selesai dikerjakan',
    4 => 'Barang sudah dapat diambil'
];

// Untuk saat ini, kembalikan response sukses (placeholder)
http_response_code(201);
echo json_encode([
    'success' => true,
    'message' => 'Service baru berhasil ditambahkan',
    'serviceId' => time(), // Gunakan timestamp sebagai ID sementara
    'customerName' => $customerName,
    'itemName' => $itemName,
    'status' => $status,
    'statusName' => $statusMap[$status]
]);
?>
