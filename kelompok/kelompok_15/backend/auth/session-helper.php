<?php
/**
 * SESSION HELPER FUNCTIONS
 * Core authentication and session management
 */

/**
 * Get user ID from session
 */
function getUserId() {
    return $_SESSION['id_user'] ?? null;
}

/**
 * Get user role from session
 */
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    return isset($_SESSION['id_user']) && !empty($_SESSION['id_user']);
}

/**
 * Require dosen role or throw 403
 */
function requireDosen() {
    if (!isAuthenticated() || getUserRole() !== 'dosen') {
        http_response_code(403);
        die(json_encode(['success' => false, 'message' => 'Only dosen can access this']));
    }
}

/**
 * Require mahasiswa role or throw 403
 */
function requireMahasiswa() {
    if (!isAuthenticated() || getUserRole() !== 'mahasiswa') {
        http_response_code(403);
        die(json_encode(['success' => false, 'message' => 'Only mahasiswa can access this']));
    }
}

/**
 * Require specific role or throw 403
 */
function requireRole($role) {
    if (!isAuthenticated() || getUserRole() !== $role) {
        http_response_code(403);
        die(json_encode(['success' => false, 'message' => "Only $role can access this"]));
    }
}

/**
 * Require authentication or throw 401
 */
function requireAuth() {
    if (!isAuthenticated()) {
        http_response_code(401);
        die(json_encode(['success' => false, 'message' => 'Unauthorized: Please login first']));
    }
}

/**
 * Create session token (for header validation)
 */
function createSessionToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Validate session token format
 */
function validateSessionToken($token) {
    return !empty($token) && preg_match('/^[a-f0-9]{64}$/', $token);
}
?>
