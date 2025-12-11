<?php
/**
 * COMPREHENSIVE PROJECT AUDIT
 * Memvalidasi:
 * 1. Folder struktur
 * 2. Database data
 * 3. Path calculations untuk setiap file type
 * 4. Actual files vs database records
 */

echo "========================================\n";
echo "    PROJECT STRUCTURE AUDIT\n";
echo "========================================\n\n";

$root = dirname(__DIR__);
$src = __DIR__;

echo "ROOT: $root\n";
echo "SRC: $src\n\n";

// Check folder structure
echo "--- FOLDER STRUCTURE ---\n";
$folders = [
    'root_uploads' => $root . '/uploads',
    'src_uploads' => $src . '/uploads',
    'user_profile_pics' => $root . '/uploads/images/user_profile_pictures',
    'konselor_profile_pics' => $root . '/uploads/images/konselor_profile_pictures',
];

foreach ($folders as $name => $path) {
    $exists = is_dir($path) ? '✓ EXISTS' : '✗ MISSING';
    echo "$name: $path\n  $exists\n";
}

echo "\n--- DATABASE CONNECTIONS ---\n";
require_once $src . '/config/database.php';

// Check users
$users = $conn->query("SELECT user_id, name, profile_picture FROM users ORDER BY user_id");
echo "\nUsers in database:\n";
$user_count = 0;
while ($row = $users->fetch_assoc()) {
    $user_count++;
    $pic_status = $row['profile_picture'] ? "HAS: {$row['profile_picture']}" : "NULL";
    echo "  User {$row['user_id']}: {$row['name']} - $pic_status\n";
}

// Check konselor
$konselor = $conn->query("SELECT konselor_id, name, profile_picture FROM konselor ORDER BY konselor_id");
echo "\nKonselor in database:\n";
$konselor_count = 0;
while ($row = $konselor->fetch_assoc()) {
    $konselor_count++;
    $pic_status = $row['profile_picture'] ? "HAS: {$row['profile_picture']}" : "NULL";
    echo "  Konselor {$row['konselor_id']}: {$row['name']} - $pic_status\n";
}

echo "\n--- ACTUAL FILES ON DISK ---\n";

if (is_dir($folders['user_profile_pics'])) {
    $user_files = scandir($folders['user_profile_pics']);
    echo "\nUser profile pictures:\n";
    $user_file_count = 0;
    foreach ($user_files as $f) {
        if ($f != '.' && $f != '..') {
            $user_file_count++;
            echo "  $f\n";
        }
    }
    echo "  Total: $user_file_count files\n";
} else {
    echo "✗ User profile pictures folder MISSING\n";
}

if (is_dir($folders['konselor_profile_pics'])) {
    $konselor_files = scandir($folders['konselor_profile_pics']);
    echo "\nKonselor profile pictures:\n";
    $konselor_file_count = 0;
    foreach ($konselor_files as $f) {
        if ($f != '.' && $f != '..') {
            $konselor_file_count++;
            echo "  $f\n";
        }
    }
    echo "  Total: $konselor_file_count files\n";
} else {
    echo "✗ Konselor profile pictures folder MISSING\n";
}

echo "\n--- PATH CALCULATIONS ---\n";

// Simulating path from different file locations
echo "\nFrom /src/index.php (web root entry):\n";
echo "  ./uploads/ = " . $root . '/uploads/ ' . (is_dir($root . '/uploads') ? '✓' : '✗') . "\n";

echo "\nFrom /src/views/dashboard/user_dashboard.php:\n";
$dashboard_path = $src . '/views/dashboard/user_dashboard.php';
$dashboard_dir = dirname($dashboard_path);
$rel_path_3up = realpath($dashboard_dir . '/../../../uploads/images/user_profile_pictures');
echo "  ../../../uploads/images/user_profile_pictures = \n  $rel_path_3up\n  " . (is_dir($rel_path_3up) ? '✓' : '✗') . "\n";

echo "\nFrom /src/views/chat/chat_room.php:\n";
$chat_path = $src . '/views/chat/chat_room.php';
$chat_dir = dirname($chat_path);
$rel_path_2up = realpath($chat_dir . '/../../uploads/images/user_profile_pictures');
echo "  ../../uploads/images/user_profile_pictures = \n  $rel_path_2up\n  " . (is_dir($rel_path_2up) ? '✓' : '✗') . "\n";

echo "\nFrom /src/controllers/UserController.php:\n";
$controller_dir = $src . '/controllers';
$abs_path = realpath($controller_dir . '/../../uploads/images/user_profile_pictures');
echo "  __DIR__ . '/../../uploads/images/user_profile_pictures' = \n  $abs_path\n  " . (is_dir($abs_path) ? '✓' : '✗') . "\n";

echo "\n========================================\n";
echo "END AUDIT\n";
echo "========================================\n";
?>
