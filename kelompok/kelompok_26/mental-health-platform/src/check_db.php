<?php
require_once __DIR__ . '/config/database.php';

echo "=== KONSELOR PROFILE PICTURES IN DATABASE ===\n\n";

$result = $conn->query("SELECT konselor_id, name, profile_picture FROM konselor");
while ($row = $result->fetch_assoc()) {
    $pic = $row['profile_picture'] ?? 'NULL';
    $status = ($row['profile_picture'] && !empty($row['profile_picture'])) ? '✓ HAS' : '✗ NULL';
    echo "Konselor {$row['konselor_id']}: {$row['name']} → $pic [$status]\n";
}

echo "\n=== USER PROFILE PICTURES IN DATABASE ===\n\n";

$result = $conn->query("SELECT user_id, name, profile_picture FROM users");
while ($row = $result->fetch_assoc()) {
    $pic = $row['profile_picture'] ?? 'NULL';
    $status = ($row['profile_picture'] && !empty($row['profile_picture'])) ? '✓ HAS' : '✗ NULL';
    echo "User {$row['user_id']}: {$row['name']} → $pic [$status]\n";
}
?>
