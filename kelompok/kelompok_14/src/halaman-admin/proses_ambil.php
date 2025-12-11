<?php
/**
 * Proses Pengambilan Barang
 * Script PHP (API) untuk menangani verifikasi pembayaran saat barang diambil.
 */
session_start();
require_once '../config.php';
require_once '../log_helper.php';

// Cek Login & Role
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'superadmin')) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_servis = intval($_POST['id_servis']);
    $metode = $_POST['metode_pembayaran'];
    
    // Validasi Input
    if (empty($id_servis) || empty($metode)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap!']);
        exit();
    }

    // Handle File Upload
    $uploadDir = '../assets/uploads/payment_proofs/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $bukti_path = null;
    if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['bukti_pembayaran']['tmp_name'];
        $fileName = $_FILES['bukti_pembayaran']['name'];
        $fileSize = $_FILES['bukti_pembayaran']['size'];
        $fileType = $_FILES['bukti_pembayaran']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Generate unique name
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $bukti_path = 'assets/uploads/payment_proofs/' . $newFileName;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengupload gambar.']);
                exit();
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Format file tidak didukung. Gunakan JPG/PNG.']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Harap upload bukti pembayaran!']);
        exit();
    }

    // Update Database
    $stmt = $conn->prepare("UPDATE servis SET status='Diambil', metode_pembayaran=?, bukti_pembayaran=? WHERE id=?");
    $stmt->bind_param("ssi", $metode, $bukti_path, $id_servis);

    if ($stmt->execute()) {
        // Log Activity
        $admin_nama = $_SESSION['nama'] ?? 'Admin';
        logActivity($conn, $_SESSION['user_id'], $admin_nama, 'Update Status', "Barang diambil (Metode: $metode, ID: $id_servis)");
        
        echo json_encode(['status' => 'success', 'message' => 'Status berhasil diupdate!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
    }
}
?>
