<?php
/**
 * Authentication Helpers
 * SILATIUM
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 */
function checkLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit;
    }
}

/**
 * Check if user is admin
 */
function checkAdmin()
{
    checkLogin();
    if ($_SESSION['role'] !== 'admin') {
        header("Location: ../user/dashboard.php"); // Redirect non-admins to user dashboard
        exit;
    }
}

/**
 * Redirect based on role
 */
function redirectBasedOnRole($role)
{
    if ($role === 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/dashboard.php");
    }
    exit;
}

/**
 * Sanitize input
 */
function sanitize($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}
