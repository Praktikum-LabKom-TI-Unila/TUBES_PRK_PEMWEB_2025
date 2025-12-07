<?php
// src/proses_klaim.php
session_start();

// --- 1. KONEKSI DATABASE ---
// Deteksi path config otomatis agar tidak error path
if (file_exists('../config/koneksi.php')) {
    include '../config/koneksi.php';
} elseif (file_exists('config/koneksi.php')) {
    include 'config/koneksi.php';
} else {
    echo json_encode(['status'=>'error', 'message'=>'Koneksi database tidak ditemukan.']);
    exit;
}

header('Content-Type: application/json');

$response = [
    'status' => 'error',
    'message' => 'Terjadi kesalahan.',
    'voucher_code' => ''
];

// --- 2. CEK ROLE USER (HANYA MASYARAKAT UMUM YG BOLEH) ---
// Jika user login, cek apakah dia Admin atau UMKM
if (isset($_SESSION['user_id'])) {
    $my_id = $_SESSION['user_id'];
    
    // Cek Role di Database untuk keamanan
    $q_cek = mysqli_query($koneksi, "SELECT role FROM users WHERE id='$my_id'");
    $d_user = mysqli_fetch_assoc($q_cek);

    // Jika user adalah admin atau umkm -> TOLAK KLAIM
    if ($d_user && ($d_user['role'] == 'admin' || $d_user['role'] == 'umkm')) {
        $response['message'] = 'Mode Mitra: Anda tidak perlu mengklaim voucher.';
        echo json_encode($response);
        exit;
    }
}

// --- 3. PROSES KLAIM ---
if (isset($_POST['voucher_id'])) {
    $voucher_id = intval($_POST['voucher_id']);

    // Cek Session Browser (Pengganti Login untuk Tamu)
    // Mencegah spam klaim berulang kali di browser yang sama
    if (isset($_SESSION['claimed_vouchers']) && in_array($voucher_id, $_SESSION['claimed_vouchers'])) {
        $response['message'] = 'Anda sudah mengklaim voucher ini sebelumnya!';
        echo json_encode($response);
        exit;
    }

    // Cek Ketersediaan Voucher di Database
    $query = mysqli_query($koneksi, "SELECT * FROM vouchers WHERE id = '$voucher_id'");
    $voucher = mysqli_fetch_assoc($query);

    if ($voucher) {
        // Cek Kuota & Tanggal Expired
        $today = date('Y-m-d');
        if ($voucher['kuota_maksimal'] > 0 && $voucher['expired_at'] >= $today) {
            
            // UPDATE: Kurangi Kuota -1
            $update = mysqli_query($koneksi, "UPDATE vouchers SET kuota_maksimal = kuota_maksimal - 1 WHERE id = '$voucher_id'");
            
            if ($update) {
                // Simpan ID ke session browser
                if (!isset($_SESSION['claimed_vouchers'])) {
                    $_SESSION['claimed_vouchers'] = [];
                }
                array_push($_SESSION['claimed_vouchers'], $voucher_id);

                // Sukses
                $response['status'] = 'success';
                $response['message'] = 'Klaim Berhasil!';
                $response['voucher_code'] = $voucher['kode_voucher'];
            } else {
                $response['message'] = 'Gagal mengupdate database.';
            }
        } else {
            $response['message'] = 'Yah, kuota voucher habis atau sudah kadaluwarsa.';
        }
    } else {
        $response['message'] = 'Voucher tidak ditemukan.';
    }
} else {
    $response['message'] = 'Data tidak lengkap.';
}

echo json_encode($response);
?>