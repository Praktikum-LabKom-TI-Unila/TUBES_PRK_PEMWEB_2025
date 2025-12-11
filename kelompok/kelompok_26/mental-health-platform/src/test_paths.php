<?php
// Cek struktur project dan paths

echo "=== PROJECT STRUCTURE ===\n";
echo "Current script: " . __FILE__ . "\n";
echo "_DIR_: " . __DIR__ . "\n";
echo "Web root: " . dirname(__DIR__) . "\n\n";

echo "=== FOLDER LOCATIONS ===\n";
$uploads_root = dirname(__DIR__) . '/uploads';
$uploads_src = __DIR__ . '/uploads';
$uploads_parent = dirname(__DIR__) . '/../../../uploads';

echo "uploads at root (/uploads): " . (is_dir($uploads_root) ? "EXISTS ✓" : "NOT FOUND ✗") . "\n";
echo "uploads in src (/src/uploads): " . (is_dir($uploads_src) ? "EXISTS ✓" : "NOT FOUND ✗") . "\n";
echo "uploads parent path check: " . (is_dir($uploads_parent) ? "EXISTS ✓" : "NOT FOUND ✗") . "\n\n";

echo "=== SUBDIRECTORIES ===\n";
if (is_dir($uploads_root)) {
    echo "Contents of /uploads:\n";
    $items = scandir($uploads_root);
    foreach ($items as $item) {
        if ($item != '.' && $item != '..') {
            $path = $uploads_root . '/' . $item;
            $type = is_dir($path) ? 'DIR' : 'FILE';
            echo "  - $item ($type)\n";
            
            if (is_dir($path) && $item === 'images') {
                $images = scandir($path);
                foreach ($images as $img) {
                    if ($img != '.' && $img != '..') {
                        echo "    - $img\n";
                    }
                }
            }
        }
    }
}

echo "\n=== PATH TESTS FROM /src/views/dashboard/ ===\n";
$dashboard_file = __DIR__ . '/views/dashboard/konselor_dashboard.php';
$relative_path = dirname($dashboard_file) . '/../../../uploads/images/user_profile_pictures/';
echo "From konselor_dashboard.php: " . $relative_path . "\n";
echo "Resolved: " . (is_dir($relative_path) ? "EXISTS ✓" : "NOT FOUND ✗") . "\n";

echo "\n=== DATABASE INFO ===\n";
require_once __DIR__ . '/config/database.php';
$result = $conn->query("SELECT user_id, name, profile_picture FROM users LIMIT 3");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "User {$row['user_id']}: {$row['name']} - profile: {$row['profile_picture']}\n";
    }
}

$konselor_result = $conn->query("SELECT konselor_id, name, profile_picture FROM konselor LIMIT 3");
if ($konselor_result) {
    echo "\nKonselor:\n";
    while ($row = $konselor_result->fetch_assoc()) {
        echo "Konselor {$row['konselor_id']}: {$row['name']} - profile: {$row['profile_picture']}\n";
    }
}
?>
