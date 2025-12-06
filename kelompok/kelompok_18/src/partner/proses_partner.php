<?php
// partner/proses_partner.php
include '../config/koneksi.php';
session_start();

// 1. Cek Login
$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) {
    echo "<script>alert('Sesi habis. Silakan login ulang.'); window.location='../auth/login.php';</script>";
    exit;
}

$action = $_POST['action'] ?? '';

// ==========================================
// 1. KIRIM REQUEST / BUAT BUNDLE BARU
// ==========================================
if ($action == 'create_request') {
    $mitra_id    = mysqli_real_escape_string($koneksi, $_POST['mitra_id']);
    $nama_bundle = mysqli_real_escape_string($koneksi, $_POST['nama_bundle'] ?? 'Kolaborasi Baru');
    $pesan_awal  = mysqli_real_escape_string($koneksi, $_POST['pesan_awal']);

    // Cek request pending
    $cek = mysqli_query($koneksi, "SELECT id FROM bundles WHERE pembuat_id='$my_id' AND mitra_id='$mitra_id' AND status='pending'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Request kolaborasi masih menunggu konfirmasi (Pending).'); window.location='index.php';</script>";
        exit;
    }

    // Insert bundles (Produk masih NULL dulu)
    $q_bundle = "INSERT INTO bundles (pembuat_id, mitra_id, nama_bundle, status, created_at) 
                 VALUES ('$my_id', '$mitra_id', '$nama_bundle', 'pending', NOW())";
    
    if (mysqli_query($koneksi, $q_bundle)) {
        $new_bundle_id = mysqli_insert_id($koneksi);

        // Insert pesan pembuka
        $q_chat = "INSERT INTO chats (bundle_id, sender_id, message, created_at) 
                   VALUES ('$new_bundle_id', '$my_id', '$pesan_awal', NOW())";
        mysqli_query($koneksi, $q_chat);

        echo "<script>alert('Ajakan Kolaborasi Terkirim!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "'); window.location='index.php';</script>";
    }
}

// ==========================================
// 2. KIRIM PESAN CHAT (+ FILE UPLOAD)
// ==========================================
if ($action == 'send_message') {
    $bundle_id = mysqli_real_escape_string($koneksi, $_POST['bundle_id']);
    $message   = mysqli_real_escape_string($koneksi, $_POST['message']);
    
    $attachment = null;
    $attachment_type = null;

    // --- LOGIKA UPLOAD FILE ---
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $target_dir = "../assets/uploads/chat/";
        
        // Buat folder jika belum ada
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_name = time() . '_' . basename($_FILES["attachment"]["name"]);
        $target_file = $target_dir . $file_name;
        $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Tentukan Tipe File (Image atau File Dokumen)
        $img_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $doc_exts = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar'];

        if (in_array($file_ext, $img_exts)) {
            $attachment_type = 'image';
        } elseif (in_array($file_ext, $doc_exts)) {
            $attachment_type = 'file';
        } else {
            echo "<script>alert('Format file tidak didukung!'); window.history.back();</script>";
            exit;
        }

        // Proses Upload
        if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
            $attachment = $file_name;
        }
    }

    // --- INSERT KE DATABASE ---
    // Pastikan minimal ada pesan ATAU file (jangan kosong dua-duanya)
    if (!empty($bundle_id) && (!empty($message) || !empty($attachment))) {
        
        // Query Insert (Sekarang pakai attachment & attachment_type)
        $q_chat = "INSERT INTO chats (bundle_id, sender_id, message, attachment, attachment_type, created_at) 
                   VALUES ('$bundle_id', '$my_id', '$message', '$attachment', '$attachment_type', NOW())";
        
        if (mysqli_query($koneksi, $q_chat)) {
            header("Location: chat_room.php?bundle_id=$bundle_id");
            exit;
        } else {
            echo "Error Database: " . mysqli_error($koneksi);
        }
    } else {
        // Jika user tidak mengetik apa-apa dan tidak upload file
        header("Location: chat_room.php?bundle_id=$bundle_id");
    }
}

// ==========================================
// 3. TERIMA REQUEST (ACCEPT)
// ==========================================
if ($action == 'accept') {
    $bundle_id = $_POST['bundle_id'];
    
    // Update status jadi active
    $update = mysqli_query($koneksi, "UPDATE bundles SET status='active' WHERE id='$bundle_id' AND mitra_id='$my_id'");
    
    if ($update) {
        $sys_msg = "[SISTEM] Kolaborasi disetujui! Silakan diskusi produk.";
        mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$bundle_id', '$my_id', '$sys_msg')");
        
        echo "<script>alert('Kolaborasi Diterima!'); window.location='my_bundles.php';</script>";
    } else {
        echo "<script>alert('Gagal menerima request.'); window.location='request.php';</script>";
    }
}

// ==========================================
// 4. TOLAK REQUEST (REJECT)
// ==========================================
if ($action == 'reject') {
    $bundle_id = $_POST['bundle_id'];
    mysqli_query($koneksi, "UPDATE bundles SET status='rejected' WHERE id='$bundle_id' AND mitra_id='$my_id'");
    echo "<script>alert('Kolaborasi Ditolak.'); window.location='request.php';</script>";
}

// ==========================================
// 5. DEAL PRODUK (BARU & PENTING!) 
// ==========================================
if ($action == 'deal_bundle') {
    $bundle_id      = mysqli_real_escape_string($koneksi, $_POST['bundle_id']);
    $prod_pembuat   = mysqli_real_escape_string($koneksi, $_POST['produk_pembuat']);
    $prod_mitra     = mysqli_real_escape_string($koneksi, $_POST['produk_mitra']);
    $harga_bundle   = mysqli_real_escape_string($koneksi, $_POST['harga_bundle']);
    $nama_bundle    = mysqli_real_escape_string($koneksi, $_POST['nama_bundle']);

    // Update tabel bundles dengan produk yang dipilih
    $query = "UPDATE bundles SET 
              produk_pembuat_id = '$prod_pembuat',
              produk_mitra_id = '$prod_mitra',
              harga_bundle = '$harga_bundle',
              nama_bundle = '$nama_bundle'
              WHERE id = '$bundle_id'";

    if (mysqli_query($koneksi, $query)) {
        // Kirim Notifikasi Sistem
        $msg = "[SISTEM]  KESEPAKATAN TERCAPAI!\nProduk telah dipilih untuk bundle '$nama_bundle' dengan harga Rp " . number_format($harga_bundle);
        mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$bundle_id', '$my_id', '$msg')");

        echo "<script>alert('Deal Berhasil! Bundle telah diperbarui.'); window.location='chat_room.php?bundle_id=$bundle_id';</script>";
    } else {
        echo "<script>alert('Gagal update deal: " . mysqli_error($koneksi) . "'); window.history.back();</script>";
    }
}

// ==========================================
// 6. BATALKAN KOLABORASI (CANCEL)
// ==========================================
if ($action == 'cancel_bundle') {
    $bundle_id = mysqli_real_escape_string($koneksi, $_POST['bundle_id']);
    
    $update = mysqli_query($koneksi, "UPDATE bundles SET status='cancelled' WHERE id='$bundle_id' AND (pembuat_id='$my_id' OR mitra_id='$my_id')");
    
    if ($update) {
        $sys_msg = "[SISTEM]  Kolaborasi dibatalkan.";
        mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$bundle_id', '$my_id', '$sys_msg')");
        echo "<script>alert('Kolaborasi dibatalkan.'); window.location='history.php';</script>";
    } else {
        echo "<script>alert('Gagal membatalkan.'); window.location='my_bundles.php';</script>";
    }
}

// ==========================================
// 7. BUAT VOUCHER (Opsional Member 4)
// ==========================================
if ($action == 'create_voucher') {
    $bundle_id      = mysqli_real_escape_string($koneksi, $_POST['bundle_id']);
    $kode_voucher   = strtoupper(mysqli_real_escape_string($koneksi, $_POST['kode_voucher']));
    $potongan_harga = mysqli_real_escape_string($koneksi, $_POST['potongan_harga']);
    $kuota          = mysqli_real_escape_string($koneksi, $_POST['kuota_maksimal']);
    $expired        = mysqli_real_escape_string($koneksi, $_POST['expired_at']);

    $cek = mysqli_query($koneksi, "SELECT id FROM vouchers WHERE kode_voucher='$kode_voucher'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Kode Voucher sudah ada!'); window.history.back();</script>";
        exit;
    }

    $q = "INSERT INTO vouchers (bundle_id, kode_voucher, potongan_harga, kuota_maksimal, expired_at) 
          VALUES ('$bundle_id', '$kode_voucher', '$potongan_harga', '$kuota', '$expired')";

    if (mysqli_query($koneksi, $q)) {
        $msg = "[SISTEM]  Voucher Dibuat: $kode_voucher (Disc: $potongan_harga)";
        mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$bundle_id', '$my_id', '$msg')");
        echo "<script>alert('Voucher berhasil dibuat!'); window.location='chat_room.php?bundle_id=$bundle_id';</script>";
    }
}
?>