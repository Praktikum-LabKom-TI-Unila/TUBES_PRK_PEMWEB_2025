<?php
require_once __DIR__ . '/config/database.php';

echo "=== FINAL DATABASE CHECK ===\n\n";

// Check users
$result = $conn->query("SELECT user_id, name, profile_picture FROM users ORDER BY user_id");
echo "Users with profile pictures:\n";
while ($row = $result->fetch_assoc()) {
    if ($row['profile_picture']) {
        echo "  ✓ User {$row['user_id']}: {$row['name']} → {$row['profile_picture']}\n";
    }
}

echo "\nKonselor with profile pictures:\n";
$result = $conn->query("SELECT konselor_id, name, profile_picture FROM konselor ORDER BY konselor_id");
while ($row = $result->fetch_assoc()) {
    if ($row['profile_picture']) {
        echo "  ✓ Konselor {$row['konselor_id']}: {$row['name']} → {$row['profile_picture']}\n";
    }
}

echo "\n=== READY ===\n";
echo "Login to dashboard now and check if photos display!\n";
?>
