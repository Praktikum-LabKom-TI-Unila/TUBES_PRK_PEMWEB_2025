<?php
/**
 * AUTO-POPULATE PROFILE PICTURES
 * Generates dummy images dan updates database
 */

require_once __DIR__ . '/config/database.php';

echo "=== GENERATING AND POPULATING PROFILE PICTURES ===\n\n";

// Fungsi untuk generate dummy image
function generateDummyImage($filepath, $name, $color) {
    $width = 200;
    $height = 200;
    $image = imagecreatetruecolor($width, $height);
    
    // Set background color
    $bgColor = imagecolorallocate($image, $color['r'], $color['g'], $color['b']);
    imagefill($image, 0, 0, $bgColor);
    
    // Add text
    $textColor = imagecolorallocate($image, 255, 255, 255);
    $fontSize = 5;
    $text = substr($name, 0, 20);
    $textX = 10;
    $textY = 90;
    imagestring($image, $fontSize, $textX, $textY, $text, $textColor);
    
    // Save
    imagejpeg($image, $filepath, 80);
    imagedestroy($image);
    return file_exists($filepath);
}

// Colors palette
$colors = [
    ['r' => 52, 'g' => 211, 'b' => 153],   // Teal
    ['r' => 96, 'g' => 165, 'b' => 250],   // Blue
    ['r' => 251, 'g' => 146, 'b' => 60],   // Orange
    ['r' => 239, 'g' => 68, 'b' => 68],    // Red
    ['r' => 168, 'g' => 85, 'b' => 247],   // Purple
    ['r' => 34, 'g' => 197, 'b' => 94],    // Green
    ['r' => 59, 'g' => 130, 'b' => 246],   // Indigo
    ['r' => 236, 'g' => 72, 'b' => 153],   // Pink
    ['r' => 107, 'g' => 114, 'b' => 128],  // Gray
];

$uploads_base = dirname(__DIR__) . '/uploads/images';

// ===== USERS =====
echo "Processing Users:\n";
$users = $conn->query("SELECT user_id, name FROM users ORDER BY user_id");
$user_idx = 0;

while ($row = $users->fetch_assoc()) {
    $user_id = $row['user_id'];
    $name = $row['name'];
    $color = $colors[$user_idx % count($colors)];
    $filename = "user_profile_{$user_id}_" . time() . ".jpg";
    $filepath = $uploads_base . "/user_profile_pictures/$filename";
    
    if (generateDummyImage($filepath, $name, $color)) {
        // Update database
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
        $stmt->bind_param("si", $filename, $user_id);
        if ($stmt->execute()) {
            echo "  ✓ User $user_id ($name): $filename\n";
        } else {
            echo "  ✗ User $user_id: Database update failed\n";
        }
        $stmt->close();
    } else {
        echo "  ✗ User $user_id: Image generation failed\n";
    }
    $user_idx++;
}

// ===== KONSELOR =====
echo "\nProcessing Konselor:\n";
$konselor = $conn->query("SELECT konselor_id, name FROM konselor ORDER BY konselor_id");
$konselor_idx = 0;

while ($row = $konselor->fetch_assoc()) {
    $konselor_id = $row['konselor_id'];
    $name = $row['name'];
    $color = $colors[$konselor_idx % count($colors)];
    $filename = "konselor_profile_{$konselor_id}_" . time() . ".jpg";
    $filepath = $uploads_base . "/konselor_profile_pictures/$filename";
    
    if (generateDummyImage($filepath, $name, $color)) {
        // Update database
        $stmt = $conn->prepare("UPDATE konselor SET profile_picture = ? WHERE konselor_id = ?");
        $stmt->bind_param("si", $filename, $konselor_id);
        if ($stmt->execute()) {
            echo "  ✓ Konselor $konselor_id ($name): $filename\n";
        } else {
            echo "  ✗ Konselor $konselor_id: Database update failed\n";
        }
        $stmt->close();
    } else {
        echo "  ✗ Konselor $konselor_id: Image generation failed\n";
    }
    $konselor_idx++;
}

echo "\n=== VERIFICATION ===\n";
echo "\nUser files:\n";
if (is_dir($uploads_base . '/user_profile_pictures')) {
    $files = scandir($uploads_base . '/user_profile_pictures');
    foreach ($files as $f) {
        if ($f != '.' && $f != '..') echo "  $f\n";
    }
}

echo "\nKonselor files:\n";
if (is_dir($uploads_base . '/konselor_profile_pictures')) {
    $files = scandir($uploads_base . '/konselor_profile_pictures');
    foreach ($files as $f) {
        if ($f != '.' && $f != '..') echo "  $f\n";
    }
}

echo "\nDatabase verification:\n";
$users_check = $conn->query("SELECT COUNT(*) as cnt, SUM(profile_picture IS NOT NULL) as with_pics FROM users");
$row = $users_check->fetch_assoc();
echo "  Users: {$row['cnt']} total, {$row['with_pics']} with pictures\n";

$konselor_check = $conn->query("SELECT COUNT(*) as cnt, SUM(profile_picture IS NOT NULL) as with_pics FROM konselor");
$row = $konselor_check->fetch_assoc();
echo "  Konselor: {$row['cnt']} total, {$row['with_pics']} with pictures\n";

echo "\n=== DONE ===\n";
?>
