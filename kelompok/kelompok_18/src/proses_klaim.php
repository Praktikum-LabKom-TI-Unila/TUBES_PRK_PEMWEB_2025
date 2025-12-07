<?php
// src/proses_klaim.php
include 'config/koneksi.php'; // Sesuaikan path koneksi
session_start();

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Terjadi kesalahan sistem.',
    'voucher_code' => ''
];

if (isset($_POST['voucher_id'])) {
    $voucher_id = intval($_POST['voucher_id']);
    
    // 1. Cek apakah user (browser ini) sudah pernah klaim voucher ini?
    if (isset($_SESSION['claimed_vouchers']) && in_array($voucher_id, $_SESSION['claimed_vouchers'])) {
        $response['message'] = 'Anda sudah mengklaim voucher ini!';
        echo json_encode($response);
        exit;
    }

    // 2. Cek Data Voucher di Database
    $query = mysqli_query($koneksi, "SELECT * FROM vouchers WHERE id = '$voucher_id'");
    $voucher = mysqli_fetch_assoc($query);

    if ($voucher) {
        // Cek Kuota
        if ($voucher['kuota_maksimal'] > 0) {
            
            // Cek Expired
            $expired = $voucher['expired_at']; // Asumsi format YYYY-MM-DD
            if (date('Y-m-d') > $expired) {
                $response['message'] = 'Maaf, voucher sudah kedaluwarsa.';
            } else {
                // 3. PROSES KLAIM (Kurangi Kuota)
                $update = mysqli_query($koneksi, "UPDATE vouchers SET kuota_maksimal = kuota_maksimal - 1 WHERE id = '$voucher_id'");
                
                if ($update) {
                    // Simpan history klaim di SESSION user
                    if (!isset($_SESSION['claimed_vouchers'])) {
                        $_SESSION['claimed_vouchers'] = [];
                    }
                    array_push($_SESSION['claimed_vouchers'], $voucher_id);

                    // Berhasil
                    $response['status'] = 'success';
                    $response['message'] = 'Voucher berhasil diklaim!';
                    $response['voucher_code'] = $voucher['kode_voucher'];
                } else {
                    $response['message'] = 'Gagal mengupdate database.';
                }
            }
        } else {
            $response['message'] = 'Yah, kuota voucher sudah habis!';
        }
    } else {
        $response['message'] = 'Voucher tidak ditemukan.';
    }
}

echo json_encode($response);
?>