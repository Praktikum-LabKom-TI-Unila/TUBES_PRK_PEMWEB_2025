<?php
// Simple migration runner for ratings table
require_once __DIR__ . '/../config/database.php';
$sqlFile = __DIR__ . '/../../database/create_ratings.sql';

if (!file_exists($sqlFile)) {
    echo "Missing migration file: create_ratings.sql\n";
    exit(1);
}

$sql = file_get_contents($sqlFile);
if ($sql === false) {
    echo "Failed to read migration file\n";
    exit(1);
}

if (!$conn) {
    echo "DB connection not available\n";
    exit(1);
}

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) { $result->free(); }
    } while ($conn->more_results() && $conn->next_result());
    echo "Migration executed successfully.\n";
} else {
    echo "Migration failed: " . $conn->error . "\n";
    exit(1);
}
