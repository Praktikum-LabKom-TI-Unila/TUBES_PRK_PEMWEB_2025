<?php
session_start();
header('Content-Type: application/json');
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) { 
    http_response_code(403);
    echo json_encode(['status'=>'error', 'message'=>'Unauthorized']); exit; 
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role']; 

if (isset($_POST['action']) && $_POST['action'] == 'delete' && $role === 'dosen') {
    $id = $_POST['id'];
    try {
        $stmt = $pdo->prepare("SELECT file_path FROM materi WHERE id = ? AND uploaded_by = ?"); 
        $stmt->execute([$id, $user_id]); 
        $d = $stmt->fetch();
        
        if($d) {
            if($d['file_path']) unlink("../uploads/materi/".$d['file_path']);
            $pdo->prepare("DELETE FROM materi WHERE id = ?")->execute([$id]);
            echo json_encode(['status'=>'success', 'message'=>'Materi dihapus.']);
        } else {
            echo json_encode(['status'=>'error', 'message'=>'Akses ditolak.']);
        }
    } catch (Exception $e) { echo json_encode(['status'=>'error']); } exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT m.*, mk.nama as nama_mk, u.nama as nama_dosen FROM materi m JOIN mata_kuliah mk ON m.mata_kuliah_id = mk.id JOIN users u ON m.uploaded_by = u.id ORDER BY m.uploaded_at DESC");
    echo json_encode(['status'=>'success', 'data'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'dosen') {
    $judul = $_POST['judul']; $mk = $_POST['mata_kuliah_id']; $desc = $_POST['deskripsi'];
    $file = "materi_".uniqid().".".pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
    
    if (!file_exists('../uploads/materi/')) mkdir('../uploads/materi/', 0777, true);
    
    if(move_uploaded_file($_FILES['file']['tmp_name'], "../uploads/materi/".$file)) {
        $pdo->prepare("INSERT INTO materi (judul, mata_kuliah_id, deskripsi, file_path, uploaded_by) VALUES (?,?,?,?,?)")->execute([$judul, $mk, $desc, $file, $user_id]);
        echo json_encode(['status'=>'success']);
    } else {
        echo json_encode(['status'=>'error', 'message'=>'Gagal upload file.']);
    }
    exit;
}
?>