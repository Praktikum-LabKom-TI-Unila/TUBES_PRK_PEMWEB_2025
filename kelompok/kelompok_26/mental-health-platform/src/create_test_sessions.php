<?php
require_once __DIR__ . '/config/database.php';

echo "=== CREATING TEST CHAT SESSIONS ===\n\n";

// Create a few chat sessions between konselor 1 and various users
$konselor_id = 1;
$users = [1, 2, 4, 6];

foreach ($users as $user_id) {
    // Create chat session
    $stmt = $conn->prepare("
        INSERT INTO chat_session (user_id, konselor_id, status, started_at)
        VALUES (?, ?, 'active', NOW())
    ");
    $stmt->bind_param("ii", $user_id, $konselor_id);
    if ($stmt->execute()) {
        $session_id = $stmt->insert_id;
        echo "✓ Created session: User $user_id ↔ Konselor $konselor_id (session_id: $session_id)\n";
        
        // Add a test message
        $msg = "Halo, bagaimana kabarmu hari ini?";
        $stmt2 = $conn->prepare("
            INSERT INTO chat_message (session_id, sender_type, sender_id, message, created_at)
            VALUES (?, 'konselor', ?, ?, NOW())
        ");
        $stmt2->bind_param("iis", $session_id, $konselor_id, $msg);
        if ($stmt2->execute()) {
            echo "  └─ Added test message\n";
        }
    } else {
        echo "✗ Failed to create session for user $user_id\n";
        echo "  Error: " . $stmt->error . "\n";
    }
}

echo "\n=== VERIFYING DATA ===\n\n";

// Verify clients are now visible
$stmt = $conn->prepare("
    SELECT DISTINCT u.user_id, u.name, u.profile_picture, 
           COUNT(cs.session_id) as session_count
    FROM chat_session cs
    JOIN users u ON u.user_id = cs.user_id
    WHERE cs.konselor_id = ?
    GROUP BY u.user_id
");
$stmt->bind_param("i", $konselor_id);
$stmt->execute();
$res = $stmt->get_result();

echo "Clients visible to Konselor $konselor_id:\n";
$count = 0;
while ($row = $res->fetch_assoc()) {
    $count++;
    $pic = $row['profile_picture'] ? "✓ {$row['profile_picture']}" : "✗ NO PICTURE";
    echo "  $count. {$row['name']} (User {$row['user_id']}) - $pic\n";
}

if ($count == 0) {
    echo "  No clients found!\n";
} else {
    echo "\n✓ SUCCESS! Now go to Konselor Dashboard to see the clients and their profile pictures!\n";
}
?>
