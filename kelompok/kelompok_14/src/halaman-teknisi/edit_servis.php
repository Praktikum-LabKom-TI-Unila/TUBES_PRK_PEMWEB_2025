<?php
// ============================================
// EDIT SERVICE - edit_service.php
// ============================================
// File ini menangani pembaruan data service (customer name dan item name)

header('Content-Type: application/json');

// Pastikan ini POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
    exit;
}

// Ambil data dari request
$serviceId = isset($_POST['serviceId']) ? intval($_POST['serviceId']) : null;
$customerName = isset($_POST['customerName']) ? htmlspecialchars($_POST['customerName']) : null;
$itemName = isset($_POST['itemName']) ? htmlspecialchars($_POST['itemName']) : null;

// Validasi input
if ($serviceId === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Service ID tidak valid']);
    exit;
}

if (!$customerName || !$itemName) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nama pelanggan dan barang tidak boleh kosong']);
    exit;
}

// Untuk saat ini, kembalikan response sukses (placeholder)
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Data service berhasil diperbarui',
    'serviceId' => $serviceId,
    'customerName' => $customerName,
    'itemName' => $itemName
]);
?>
