<?php
/**
 * TEST API - MATERI DOSEN
 * API endpoint untuk web-based testing dashboard
 */

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../config/database.php';

$response = [
    'success' => false,
    'tests' => [],
    'message' => ''
];

// Test 1: Check PHP
$response['tests'][] = [
    'name' => 'PHP Execution',
    'status' => 'PASS',
    'detail' => 'PHP ' . phpversion()
];

// Test 2: Database
try {
    $pdo->query("SELECT 1");
    $response['tests'][] = [
        'name' => 'Database Connection',
        'status' => 'PASS',
        'detail' => 'Connected to kelasonline'
    ];
} catch (Exception $e) {
    $response['tests'][] = [
        'name' => 'Database Connection',
        'status' => 'FAIL',
        'detail' => $e->getMessage()
    ];
    echo json_encode($response);
    exit;
}

// Test 3: Check tables
$tables = ['users', 'kelas', 'materi'];
foreach ($tables as $table) {
    try {
        $pdo->query("SELECT 1 FROM $table LIMIT 1");
        $response['tests'][] = [
            'name' => "Table: $table",
            'status' => 'PASS',
            'detail' => 'Table exists'
        ];
    } catch (Exception $e) {
        $response['tests'][] = [
            'name' => "Table: $table",
            'status' => 'FAIL',
            'detail' => $e->getMessage()
        ];
    }
}

// Test 4: Check uploads directory
$upload_dir = __DIR__ . '/../../uploads/materi/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if (is_writable($upload_dir)) {
    $response['tests'][] = [
        'name' => 'Uploads Directory',
        'status' => 'PASS',
        'detail' => 'Writable'
    ];
} else {
    $response['tests'][] = [
        'name' => 'Uploads Directory',
        'status' => 'FAIL',
        'detail' => 'Not writable'
    ];
}

$response['success'] = true;
$response['message'] = 'System check complete';

echo json_encode($response, JSON_PRETTY_PRINT);
?>
