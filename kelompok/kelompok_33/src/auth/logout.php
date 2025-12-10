<?php
// auth/logout.php - Logout handler
session_start();

// Hapus session
session_unset();
session_destroy();

// Redirect ke login
header('Location: ../login.html');
exit;
