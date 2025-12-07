<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

// Log activity sebelum logout
if (isset($_SESSION['user_id'])) {
    logActivity($conn, $_SESSION['user_id'], 'LOGOUT', 'User logout dari sistem');
}

// Destroy session
session_unset();
session_destroy();

// Redirect ke landing page
header('Location: ../index.php');
exit();
?>