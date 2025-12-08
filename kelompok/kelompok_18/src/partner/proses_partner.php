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
// 1. KIRIM REQUEST / AJAK KOLABORASI
// ==========================================
if ($action == 'create_request') {
    $mitra_id    = mysqli_real_escape_string($koneksi, $_POST['mitra_id']);
    $nama_bundle = mysqli_real_escape_string($koneksi, $_POST['nama_bundle'] ?? 'Kolaborasi Baru');
    $pesan_awal  = mysqli_real_escape_string($koneksi, $_POST['pesan_awal']);

    // Cek apakah sudah ada request pending?
    $cek = mysqli_query($koneksi, "SELECT id FROM bundles WHERE pembuat_id='$my_id' AND mitra_id='$mitra_id' AND status='pending'");
    
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Request masih pending. Tunggu respon mitra.'); window.location='index.php';</script>";
        exit;
    }

    // Insert ke tabel bundles
    $q_bundle = "INSERT INTO bundles (pembuat_id, mitra_id, nama_bundle, status, created_at) 
                 VALUES ('$my_id', '$mitra_id', '$nama_bundle', 'pending', NOW())";
    
    if (mysqli_query($koneksi, $q_bundle)) {
        $new_bundle_id = mysqli_insert_id($koneksi);

        // Insert pesan pembuka
        $q_chat = "INSERT INTO chats (bundle_id, sender_id, message, created_at) 
                   VALUES ('$new_bundle_id', '$my_id', '$pesan_awal', NOW())";
        mysqli_query($koneksi, $q_chat);

        echo "<script>alert('Ajakan Terkirim!'); window.location='chat_room.php?bundle_id=$new_bundle_id';</script>";
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

        // Tentukan Tipe File
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
    if (!empty($bundle_id) && (!empty($message) || !empty($attachment))) {
        
        $q_chat = "INSERT INTO chats (bundle_id, sender_id, message, attachment, attachment_type, created_at) 
                   VALUES ('$bundle_id', '$my_id', '$message', '$attachment', '$attachment_type', NOW())";
        
        if (mysqli_query($koneksi, $q_chat)) {
            header("Location: chat_room.php?bundle_id=$bundle_id");
            exit;
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    } else {
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
        $sys_msg = "[SISTEM] Kolaborasi disetujui! Silakan mulai diskusi.";
        mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$bundle_id', '$my_id', '$sys_msg')");
        
        echo "<script>alert('Kolaborasi Diterima!'); window.location='chat_room.php?bundle_id=$bundle_id';</script>";
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
// 5. AJUKAN KESEPAKATAN / DEAL (Fitur Atur Kolaborasi)
// ==========================================
if ($action == 'propose_deal') {
    $bundle_id      = mysqli_real_escape_string($koneksi, $_POST['bundle_id']);
    $nama_bundle    = mysqli_real_escape_string($koneksi, $_POST['nama_bundle']);
    $harga_bundle   = mysqli_real_escape_string($koneksi, $_POST['harga_bundle']);
    $pesan_tambahan = mysqli_real_escape_string($koneksi, $_POST['pesan_proposal']);
    
    $prod_saya      = mysqli_real_escape_string($koneksi, $_POST['produk_saya']);
    $prod_partner   = mysqli_real_escape_string($koneksi, $_POST['produk_partner']);

    // Buat JSON Data (Teknik tanpa ubah DB)
    $data_json = json_encode([
        'nama_bundle' => $nama_bundle,
        'harga'       => $harga_bundle,
        'prod_A'      => $prod_saya,
        'prod_B'      => $prod_partner,
        'catatan'     => $pesan_tambahan
    ]);

    // Bungkus dengan TANDA KHUSUS
    $final_message = "[DEAL_PROPOSAL]" . $data_json;

    $q = "INSERT INTO chats (bundle_id, sender_id, message, created_at) 
          VALUES ('$bundle_id', '$my_id', '$final_message', NOW())";

    if (mysqli_query($koneksi, $q)) {
        echo "<script>alert('Penawaran dikirim ke chat!'); window.location='chat_room.php?bundle_id=$bundle_id';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}

// ==========================================
// 6. TERIMA KESEPAKATAN (DEAL SAH)
// ==========================================
if ($action == 'accept_deal_proposal') {
    $chat_id = $_POST['chat_id'];
    
    // Ambil pesan dari DB
    $q_chat = mysqli_query($koneksi, "SELECT * FROM chats WHERE id='$chat_id'");
    $chat   = mysqli_fetch_assoc($q_chat);
    
    // Decode JSON dari pesan
    $clean_json = str_replace("[DEAL_PROPOSAL]", "", $chat['message']);
    $data       = json_decode($clean_json, true);
    
    $bundle_id = $chat['bundle_id'];

    // Cek peran pengirim untuk mapping produk
    $q_bundle = mysqli_query($koneksi, "SELECT * FROM bundles WHERE id='$bundle_id'");
    $bundle   = mysqli_fetch_assoc($q_bundle);

    if ($chat['sender_id'] == $bundle['pembuat_id']) {
        $prod_pembuat = $data['prod_A'];
        $prod_mitra   = $data['prod_B'];
    } else {
        $prod_mitra   = $data['prod_A']; 
        $prod_pembuat = $data['prod_B'];
    }

    // UPDATE BUNDLE (Finalisasi)
    $update = "UPDATE bundles SET 
               nama_bundle = '{$data['nama_bundle']}',
               harga_bundle = '{$data['harga']}',
               produk_pembuat_id = '$prod_pembuat',
               produk_mitra_id = '$prod_mitra'
               WHERE id = '$bundle_id'";
    
    if (mysqli_query($koneksi, $update)) {
        $sys = "[SISTEM] ✅ Kesepakatan DISETUJUI! Bundle telah diperbarui.";
        mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$bundle_id', '$my_id', '$sys')");
        
        echo "<script>alert('Kesepakatan Sah!'); window.location='chat_room.php?bundle_id=$bundle_id';</script>";
    }
}

// ==========================================
// 7. BATALKAN KOLABORASI (CANCEL)
// ==========================================
if ($action == 'cancel_bundle') {
    $bundle_id = mysqli_real_escape_string($koneksi, $_POST['bundle_id']);
    
    $update = mysqli_query($koneksi, "UPDATE bundles SET status='cancelled' WHERE id='$bundle_id' AND (pembuat_id='$my_id' OR mitra_id='$my_id')");
    
    if ($update) {
        $sys_msg = "[SISTEM] 🚫 Kolaborasi dibatalkan.";
        mysqli_query($koneksi, "INSERT INTO chats (bundle_id, sender_id, message) VALUES ('$bundle_id', '$my_id', '$sys_msg')");
        echo "<script>alert('Kolaborasi dibatalkan.'); window.location='history.php';</script>";
    } else {
        echo "<script>alert('Gagal membatalkan.'); window.location='my_bundles.php';</script>";
    }
}

// ==========================================
// 8. BUAT VOUCHER
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
    } else {
        echo "<script>alert('Gagal membuat voucher.'); window.history.back();</script>";
    }
}
?>