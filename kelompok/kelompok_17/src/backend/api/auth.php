<?php

require_once __DIR__ . '/../init.php';

// Atur header CORS dan handle preflight request
Response::setCorsHeaders();
Response::handlePreflight();

require_once CONTROLLERS_PATH . '/AuthController.php';

$controller = new AuthController();
$action = Request::query('action', '');
$data = Request::all();

try {
    switch ($action) {
        case 'login':
            if (!Request::isPost()) {
                Response::methodNotAllowed('Gunakan method POST');
            }
            $controller->login($data);
            break;

        case 'register':
            if (!Request::isPost()) {
                Response::methodNotAllowed('Gunakan method POST');
            }
            $controller->register($data);
            break;

        case 'logout':
            if (!Request::isPost()) {
                Response::methodNotAllowed('Gunakan method POST');
            }
            $controller->logout();
            break;

        // --- Tambahan: Action untuk Mengambil Daftar Anggota Pending (GET) ---
        case 'pending_members':
            if (!Request::isGet()) {
                Response::methodNotAllowed('Gunakan method GET');
            }
            $controller->getPendingMembers();
            break;

        // --- Tambahan: Action untuk Persetujuan Anggota (Approve/Reject) (POST) ---
        case 'approve_member':
            if (!Request::isPost()) {
                Response::methodNotAllowed('Gunakan method POST');
            }
            $controller->approveMember($data);
            break;
        // -----------------------------------------------------------------------

        case 'me':
            if (!Request::isGet()) {
                Response::methodNotAllowed('Gunakan method GET');
            }
            $controller->me();
            break;

        case 'change-password':
            if (!Request::isPost()) {
                Response::methodNotAllowed('Gunakan method POST');
            }
            $controller->changePassword($data);
            break;

        case 'check':
            if (!Request::isGet()) {
                Response::methodNotAllowed('Gunakan method GET');
            }
            $controller->checkSession();
            break;

        default:
            Response::error('Action tidak ditemukan. Gunakan: login, register, logout, me, change-password, check, pending_members, approve_member', 404);
    }
} catch (Exception $e) {
    error_log("Auth API Error: " . $e->getMessage());
    Response::serverError('Terjadi kesalahan server');
}