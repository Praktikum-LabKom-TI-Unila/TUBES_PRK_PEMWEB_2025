<?php
// File ini dipanggil via AJAX (JS)
include '../config/koneksi.php';

header('Content-Type: application/json'); // Wajib biar JS bisa baca

$kode = isset($_POST['kode']) ? $_POST['kode'] : '';

// 1. Cek Kode Ada Gak?
$q = mysqli_query($koneksi, "SELECT * FROM vouchers WHERE kode_voucher='$kode'");

if (mysqli_num_rows($q) > 0) {
    $data = mysqli_fetch_assoc($q);
    $hari_ini = date('Y-m-d');

    // 2. Cek Expired
    if ($data['expired_at'] < $hari_ini) {
        echo json_encode(["result" => "expired", "msg" => "Voucher sudah kadaluarsa!"]);
        exit;
    }

    // 3. Cek Kuota
    if ($data['kuota_terpakai'] >= $data['kuota_maksimal']) {
        echo json_encode(["result" => "habis", "msg" => "Kuota voucher sudah habis!"]);
        exit;
    }

    // 4. Jika Lolos Semua -> Update Pemakaian (+1)
    // Di dunia nyata, update ini dilakukan setelah tombol "Konfirmasi Bayar".
    // Tapi untuk simulasi tugas, kita update saat validasi sukses saja.
    $id_voc = $data['id'];
    mysqli_query($koneksi, "UPDATE vouchers SET kuota_terpakai = kuota_terpakai + 1 WHERE id='$id_voc'");

    echo json_encode([
        "result" => "valid", 
        "msg" => "Voucher Valid! Diskon: Rp " . number_format($data['potongan_harga'])
    ]);

} else {
    echo json_encode(["result" => "notfound", "msg" => "Kode tidak ditemukan."]);
}
?>