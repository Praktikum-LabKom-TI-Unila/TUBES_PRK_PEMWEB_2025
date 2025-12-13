<?php
session_start();

// PERBAIKAN: Gunakan __DIR__ agar path include selalu benar (Anti Tersesat)
// Ini memperbaiki masalah "Warning: include(config/koneksi.php): Failed to open stream"
include __DIR__ . '/../config/koneksi.php';

// Proteksi Admin: Cuma Admin yang boleh akses file ini
// Jika user biasa/tamu mencoba akses langsung, tendang ke homepage
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Ambil parameter aksi dari URL (?aksi=...)
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

// --- 1. FUNGSI HAPUS USER ---
// Dipanggil dari halaman users.php
if ($aksi == 'hapus_user') {
    // Pastikan ID ada dan valid
    if (isset($_GET['id'])) {
        $id_user = $_GET['id'];

        // Hapus user dari database
        // PENTING: Karena di database (SQL) kita sudah set "ON DELETE CASCADE",
        // Maka saat User dihapus, SEMUA data terkait (Produk, Bundle, Chat, Voucher)
        // milik user tersebut akan OTOMATIS terhapus bersih oleh MySQL.
        // Jadi kita tidak perlu repot menghapus satu per satu tabel lain.
        
        $query = "DELETE FROM users WHERE id='$id_user'";
        
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['success'] = "User beserta seluruh datanya berhasil dihapus permanen.";
        } else {
            $_SESSION['error'] = "Gagal menghapus user: " . mysqli_error($koneksi);
        }
    }
    
    // Redirect kembali ke halaman manajemen user
    header("Location: users.php");
    exit;
}

// --- 2. FUNGSI HAPUS VOUCHER ---
// Dipanggil dari halaman laporan.php
elseif ($aksi == 'hapus_voucher') {
    if (isset($_GET['id'])) {
        $id_voucher = $_GET['id'];

        // Hapus data voucher spesifik dari database
        // Ini berguna jika ada transaksi error atau voucher uji coba
        $query = "DELETE FROM vouchers WHERE id='$id_voucher'";
        
        if (mysqli_query($koneksi, $query)) {
            $_SESSION['success'] = "Data transaksi voucher berhasil dihapus.";
        } else {
            $_SESSION['error'] = "Gagal menghapus voucher: " . mysqli_error($koneksi);
        }
    }
    
    // Redirect kembali ke halaman laporan
    header("Location: laporan.php");
    exit;
}

// Jika aksi tidak dikenali, kembalikan ke dashboard admin
else {
    header("Location: index.php");
    exit;
}
?>