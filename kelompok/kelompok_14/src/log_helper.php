<?php
/**
 * Helper: Activity Log
 * Fungsi bantuan untuk mencatat aktivitas user ke database.
 */
if(!function_exists('logActivity')) {
    function logActivity($conn, $user_id, $user_name, $action, $desc) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, user_name, action, description, ip_address) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("issss", $user_id, $user_name, $action, $desc, $ip);
            $stmt->execute();
            $stmt->close();
        }
    }
}
?>
