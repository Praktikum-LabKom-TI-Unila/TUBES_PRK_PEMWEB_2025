<?php
require_once __DIR__ . '/config/database.php';

echo "=== IMAGE FETCH TEST ===\n\n";

$root = dirname(__DIR__);

// Test 1: Check database
echo "1. DATABASE CHECK:\n";
$result = $conn->query("SELECT user_id, name, profile_picture FROM users LIMIT 5");
while ($row = $result->fetch_assoc()) {
    if ($row['profile_picture']) {
        $pic = $row['profile_picture'];
        $file_path = $root . '/uploads/images/user_profile_pictures/' . $pic;
        $exists = file_exists($file_path) ? '✓ EXISTS' : '✗ MISSING';
        echo "   User {$row['user_id']}: $pic → $exists\n";
    }
}

// Test 2: Verify paths resolve from views
echo "\n2. PATH RESOLUTION:\n";
$dashboard_dir = __DIR__ . '/views/dashboard';
$path = $dashboard_dir . '/../../../uploads/images/user_profile_pictures';
$real = realpath($path);
echo "   From /views/dashboard/: ../../../uploads/images/user_profile_pictures\n";
echo "   Resolves to: $real\n";
echo "   " . (is_dir($real) ? "✓ VALID" : "✗ INVALID") . "\n";

// Test 3: List actual files
echo "\n3. FILES ON DISK:\n";
$dir = $root . '/uploads/images/user_profile_pictures';
if (is_dir($dir)) {
    $files = scandir($dir);
    $count = 0;
    foreach ($files as $f) {
        if ($f != '.' && $f != '..') {
            $count++;
            echo "   ✓ $f\n";
        }
    }
    echo "   Total: $count files\n";
} else {
    echo "   ✗ DIRECTORY MISSING!\n";
}

echo "\n=== READY TO TEST ===\n";
echo "Login as Konselor and check Dashboard!\n";
?>
