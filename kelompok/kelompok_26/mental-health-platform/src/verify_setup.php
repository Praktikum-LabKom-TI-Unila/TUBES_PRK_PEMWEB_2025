<?php
/**
 * DETAILED VERIFICATION SCRIPT
 * Checks:
 * 1. Database records have profile_picture filenames
 * 2. Files exist on disk
 * 3. Paths resolve correctly from each file location
 * 4. Image URLs would work
 */

require_once __DIR__ . '/config/database.php';

echo "========================================\n";
echo "       COMPREHENSIVE VERIFICATION\n";
echo "========================================\n\n";

$root = dirname(__DIR__);

// 1. Check database
echo "=== DATABASE CHECK ===\n\n";

echo "Users:\n";
$users = $conn->query("SELECT user_id, name, profile_picture FROM users ORDER BY user_id");
while ($row = $users->fetch_assoc()) {
    $pic = $row['profile_picture'];
    if ($pic) {
        $file_path = $root . '/uploads/images/user_profile_pictures/' . $pic;
        $exists = file_exists($file_path) ? '✓ FILE EXISTS' : '✗ FILE MISSING';
        echo "  User {$row['user_id']}: {$row['name']}\n";
        echo "    pic_name: $pic\n";
        echo "    path: $file_path\n";
        echo "    $exists\n";
    }
}

echo "\nKonselor:\n";
$konselor = $conn->query("SELECT konselor_id, name, profile_picture FROM konselor ORDER BY konselor_id");
while ($row = $konselor->fetch_assoc()) {
    $pic = $row['profile_picture'];
    if ($pic) {
        $file_path = $root . '/uploads/images/konselor_profile_pictures/' . $pic;
        $exists = file_exists($file_path) ? '✓ FILE EXISTS' : '✗ FILE MISSING';
        echo "  Konselor {$row['konselor_id']}: {$row['name']}\n";
        echo "    pic_name: $pic\n";
        echo "    path: $file_path\n";
        echo "    $exists\n";
    }
}

// 2. Check file paths from different view files
echo "\n=== PATH RESOLUTION CHECK ===\n\n";

// From dashboard files (3 levels up)
echo "From /src/views/dashboard/*.php (need 3 ups):\n";
$dashboard_path = __DIR__ . '/views/dashboard/user_dashboard.php';
$dashboard_dir = dirname($dashboard_path);
$resolved = realpath($dashboard_dir . '/../../../uploads/images/user_profile_pictures');
echo "  ../../../uploads/images/user_profile_pictures\n";
echo "  Resolves to: $resolved\n";
echo "  " . (is_dir($resolved) ? "✓ EXISTS" : "✗ MISSING") . "\n";

// From chat files (2 levels up)
echo "\nFrom /src/views/chat/*.php (need 2 ups):\n";
$chat_path = __DIR__ . '/views/chat/chat_room.php';
$chat_dir = dirname($chat_path);
$resolved = realpath($chat_dir . '/../../uploads/images/konselor_profile_pictures');
echo "  ../../uploads/images/konselor_profile_pictures\n";
echo "  Resolves to: $resolved\n";
echo "  " . (is_dir($resolved) ? "✓ EXISTS" : "✗ MISSING") . "\n";

// From profile files (3 levels up)
echo "\nFrom /src/views/profile/*.php (need 3 ups):\n";
$profile_path = __DIR__ . '/views/profile/user_profile.php';
$profile_dir = dirname($profile_path);
$resolved = realpath($profile_dir . '/../../../uploads/images/user_profile_pictures');
echo "  ../../../uploads/images/user_profile_pictures\n";
echo "  Resolves to: $resolved\n";
echo "  " . (is_dir($resolved) ? "✓ EXISTS" : "✗ MISSING") . "\n";

// 3. Simulate image URLs as browser would request them
echo "\n=== IMAGE URL SIMULATION ===\n\n";

echo "From index.php routing (entry point at /src/index.php):\n";
echo "  Relative path from web root: ./uploads/images/\n";

echo "\nExample URL for User 1:\n";
$user1 = $conn->query("SELECT profile_picture FROM users WHERE user_id = 1")->fetch_assoc();
if ($user1 && $user1['profile_picture']) {
    $url = "./uploads/images/user_profile_pictures/" . $user1['profile_picture'];
    $actual_file = $root . '/uploads/images/user_profile_pictures/' . $user1['profile_picture'];
    echo "  URL: $url\n";
    echo "  File: $actual_file\n";
    echo "  " . (file_exists($actual_file) ? "✓ WOULD DISPLAY" : "✗ FILE MISSING") . "\n";
}

echo "\nExample URL for Konselor 1:\n";
$kon1 = $conn->query("SELECT profile_picture FROM konselor WHERE konselor_id = 1")->fetch_assoc();
if ($kon1 && $kon1['profile_picture']) {
    $url = "./uploads/images/konselor_profile_pictures/" . $kon1['profile_picture'];
    $actual_file = $root . '/uploads/images/konselor_profile_pictures/' . $kon1['profile_picture'];
    echo "  URL: $url\n";
    echo "  File: $actual_file\n";
    echo "  " . (file_exists($actual_file) ? "✓ WOULD DISPLAY" : "✗ FILE MISSING") . "\n";
}

// 4. Check all profile pictures are populated
echo "\n=== POPULATION STATUS ===\n\n";

$users_total = $conn->query("SELECT COUNT(*) as cnt FROM users")->fetch_assoc()['cnt'];
$users_with_pics = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE profile_picture IS NOT NULL")->fetch_assoc()['cnt'];
echo "Users: $users_with_pics / $users_total have profile pictures\n";

$konselor_total = $conn->query("SELECT COUNT(*) as cnt FROM konselor")->fetch_assoc()['cnt'];
$konselor_with_pics = $conn->query("SELECT COUNT(*) as cnt FROM konselor WHERE profile_picture IS NOT NULL")->fetch_assoc()['cnt'];
echo "Konselor: $konselor_with_pics / $konselor_total have profile pictures\n";

echo "\n========================================\n";
echo "VERIFICATION COMPLETE\n";
echo "========================================\n";
?>
