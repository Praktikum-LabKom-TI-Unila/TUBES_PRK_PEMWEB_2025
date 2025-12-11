<?php
require_once '../config/database.php';
require_once '../config/auth.php';

checkAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $laporan_id = $_POST['laporan_id'];
    $status = $_POST['status'];

    if (!empty($laporan_id) && !empty($status)) {
        // Validation: Verify status value to prevent injection of invalid ENUMs (though DB handles it, good practice)
        $allowed_statuses = ['received', 'processing', 'completed'];
        if (in_array($status, $allowed_statuses)) {
            $stmt = $pdo->prepare("UPDATE laporan SET status = ? WHERE id = ?");
            if ($stmt->execute([$status, $laporan_id])) {
                // Success
            }
        }
    }
}

// Redirect back to list
header("Location: laporan.php");
exit;
