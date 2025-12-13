<?php
require_once 'src/config.php';

// Check if column exists
$check = $conn->query("SHOW COLUMNS FROM app_settings LIKE 'location_map'");

if ($check->num_rows == 0) {
    // Add column
    $sql = "ALTER TABLE app_settings ADD COLUMN location_map TEXT AFTER email";
    if ($conn->query($sql) === TRUE) {
        echo "Column 'location_map' added successfully.";
    } else {
        echo "Error adding column: " . $conn->error;
    }
} else {
    echo "Column 'location_map' already exists.";
}
?>
