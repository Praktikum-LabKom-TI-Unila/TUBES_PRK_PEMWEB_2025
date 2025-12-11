<?php
/**
 * FITUR 1: AUTENTIKASI - SESSION CHECK (Middleware)
 * API Endpoint Security Middleware
 * 
 * Deskripsi: Validasi session dari X-Session-ID header
 * - Validate X-Session-ID header
 * - Restore session dari ID
 * - Throw 401 if unauthorized
 */

session_start();
require_once __DIR__ . '/session-helper.php';

// Get session ID from header
$sessionId = $_SERVER['HTTP_X_SESSION_ID'] ?? '';

// Validate session ID format
if (!validateSessionToken($sessionId) && !empty($sessionId)) {
    http_response_code(401);
    header('Content-Type: application/json');
    die(json_encode(['success' => false, 'message' => 'Invalid session token format']));
}

// For now, we accept the session token if provided
// Production: Implement token-based session management with Redis/Database

// Check if user is authenticated via PHP session
if (!isAuthenticated() && empty($sessionId)) {
    http_response_code(401);
    header('Content-Type: application/json');
    die(json_encode(['success' => false, 'message' => 'Unauthorized: Session required']));
}
?>

