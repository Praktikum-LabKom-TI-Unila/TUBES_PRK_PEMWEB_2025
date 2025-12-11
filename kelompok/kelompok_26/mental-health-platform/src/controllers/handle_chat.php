<?php
// src/controllers/handle_chat.php
// Simple AJAX handler for chat: fetch messages and send new messages
header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 0);
error_reporting(0);

if (!isset($_SESSION)) {
    session_start();
}

require_once dirname(__DIR__) . '/config/database.php';

// Ensure $conn is available
global $conn;

if (!$conn) {
    echo json_encode(['success'=>false,'error'=>'Database connection failed']);
    exit;
}

$action = $_REQUEST['action'] ?? 'fetch';

// helper
function json_error($msg){ echo json_encode(['success'=>false,'error'=>$msg]); exit; }

if ($action === 'fetch') {
    $session_id = intval($_GET['session_id'] ?? 0);
    if (!$session_id) json_error('session_id required');

    $stmt = $conn->prepare("SELECT message_id, session_id, sender_type, sender_id, message, created_at FROM chat_message WHERE session_id = ? ORDER BY created_at ASC");
    if (!$stmt) json_error('DB prepare failed: ' . $conn->error);
    $stmt->bind_param('i',$session_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $messages = [];
    while ($r = $res->fetch_assoc()) $messages[] = $r;
    echo json_encode(['success'=>true,'messages'=>$messages]);
    exit;
}

if ($action === 'send') {
    // Expect POST
    $session_id = intval($_POST['session_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    if (!$session_id || $message === '') json_error('session_id and message required');

    // Determine sender - check both user and konselor sessions
    $sender_type = 'user';
    $sender_id = null;
    
    if (isset($_SESSION['konselor'])) {
        $sender_type = 'konselor';
        $sender_id = $_SESSION['konselor']['konselor_id'] ?? $_SESSION['konselor']['id'] ?? null;
    } elseif (isset($_SESSION['user'])) {
        $sender_type = 'user';
        $sender_id = $_SESSION['user']['user_id'] ?? $_SESSION['user']['id'] ?? null;
    }
    
    if (!$sender_id) json_error('not authenticated');

    $stmt = $conn->prepare("INSERT INTO chat_message (session_id, sender_type, sender_id, message) VALUES (?, ?, ?, ?)");
    if (!$stmt) json_error('DB prepare failed (insert): ' . $conn->error);
    $stmt->bind_param('isis', $session_id, $sender_type, $sender_id, $message);
    $ok = $stmt->execute();
    if (!$ok) json_error('DB insert failed: ' . $stmt->error);

    // fetch inserted row (created_at)
    $insert_id = $conn->insert_id;
    $stmt2 = $conn->prepare("SELECT message_id, session_id, sender_type, sender_id, message, created_at FROM chat_message WHERE message_id = ? LIMIT 1");
    if ($stmt2) {
        $stmt2->bind_param('i',$insert_id);
        $stmt2->execute();
        $row = $stmt2->get_result()->fetch_assoc();
        echo json_encode(['success'=>true,'message'=>$row]);
        exit;
    }

    echo json_encode(['success'=>true]);
    exit;
}

json_error('unknown action');
