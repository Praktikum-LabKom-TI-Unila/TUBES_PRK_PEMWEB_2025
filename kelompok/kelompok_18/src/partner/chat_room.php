<?php
include '../config/koneksi.php';
include '../layouts/header.php';

// 1. Cek Sesi
$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) {
    echo "<script>alert('Sesi habis.'); window.location='../auth/login.php';</script>";
    exit;
}

// ==========================================
// LOGIKA TOMBOL KEMBALI
// ==========================================
if (!isset($_SESSION['chat_back_url'])) {
    $_SESSION['chat_back_url'] = 'my_bundles.php'; 
}
if (isset($_SERVER['HTTP_REFERER'])) {
    $ref = $_SERVER['HTTP_REFERER'];
    if (strpos($ref, 'chat_room.php') === false && strpos($ref, 'proses_partner.php') === false) {
        if (strpos($ref, 'index.php') !== false) {
            $_SESSION['chat_back_url'] = 'index.php';
        } elseif (strpos($ref, 'request.php') !== false) {
            $_SESSION['chat_back_url'] = 'request.php';
        } elseif (strpos($ref, 'history.php') !== false) {
            $_SESSION['chat_back_url'] = 'history.php';
        } elseif (strpos($ref, 'my_bundles.php') !== false) {
            $_SESSION['chat_back_url'] = 'my_bundles.php';
        }
    }
}
$back_url = $_SESSION['chat_back_url'];

// 2. Inisialisasi Data
$partner_id = null;
$bundle_id  = null;
$bundle_data = null;

if (isset($_GET['bundle_id'])) {
    $bid = mysqli_real_escape_string($koneksi, $_GET['bundle_id']);
    $q_cek = mysqli_query($koneksi, "SELECT * FROM bundles WHERE id='$bid'");
    $bundle_data = mysqli_fetch_assoc($q_cek);
    if ($bundle_data) {
        $bundle_id = $bid;
        $partner_id = ($bundle_data['pembuat_id'] == $my_id) ? $bundle_data['mitra_id'] : $bundle_data['pembuat_id'];
    }
} elseif (isset($_GET['partner_id'])) {
    $partner_id = mysqli_real_escape_string($koneksi, $_GET['partner_id']);
    $q_last = mysqli_query($koneksi, "SELECT * FROM bundles WHERE (pembuat_id='$my_id' AND mitra_id='$partner_id') OR (pembuat_id='$partner_id' AND mitra_id='$my_id') ORDER BY created_at DESC LIMIT 1");
    $bundle_data = mysqli_fetch_assoc($q_last);
    if ($bundle_data) $bundle_id = $bundle_data['id'];
}

if (!$partner_id) {
    echo "<script>window.location='index.php';</script>";
    exit;
}

$partner = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id='$partner_id'"));

$chats = [];
if ($bundle_id) {
    $q_chat = mysqli_query($koneksi, "SELECT * FROM chats WHERE bundle_id='$bundle_id' ORDER BY created_at ASC");
}
?>

<link rel="stylesheet" href="../assets/css/style_partner.css?v=<?= time(); ?>">

<div class="container-fluid px-0"> 
    <div class="chat-container full-screen-chat">
        
        <div class="chat-header">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= $back_url ?>" class="text-secondary me-2"><i class="fa fa-arrow-left fa-lg"></i></a>
                
                <div class="position-relative">
                    <img src="<?= !empty($partner['foto_profil']) && file_exists('../assets/uploads/'.$partner['foto_profil']) ? '../assets/uploads/'.$partner['foto_profil'] : 'https://ui-avatars.com/api/?name='.urlencode($partner['nama_toko']).'&background=D7CCC8&color=6D4C41' ?>" 
                         class="rounded-circle border" width="45" height="45" style="object-fit: cover;">
                </div>
                <div>
                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($partner['nama_toko'] ?? '') ?></h6>
                    <div class="partner-status"><span>Online</span></div>
                </div>
            </div>
            
            <div>
                <?php if (!$bundle_id): ?>
                    <button class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalAjak">
                        <i class="fa fa-plus me-1"></i> Kolaborasi
                    </button>
                <?php else: ?>
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                        <i class="fa fa-handshake text-primary me-1"></i> <?= htmlspecialchars($bundle_data['nama_bundle'] ?? '') ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="chat-area" id="chatBox">
            <?php if ($bundle_id && mysqli_num_rows($q_chat) > 0): ?>
                <?php while($c = mysqli_fetch_assoc($q_chat)): ?>
                    <?php 
                        $isMe = ($c['sender_id'] == $my_id);
                        $time = date('H:i', strtotime($c['created_at']));
                        $pesan_raw = $c['message'] ?? ''; 
                        $attachment = $c['attachment'] ?? null;
                        $type = $c['attachment_type'] ?? null;
                        
                        $isSystem = strpos($pesan_raw, '[SISTEM]') !== false;
                    ?>

                    <?php if($isSystem): ?>
                        <div class="system-message">
                            <span class="system-badge"><?= htmlspecialchars($pesan_raw) ?></span>
                        </div>
                    <?php else: ?>
                        <div class="message-wrapper <?= $isMe ? 'me' : 'them' ?>">
                            <div class="message-bubble">
                                
                                <?php if($attachment): ?>
                                    <div class="mb-2">
                                        <?php if($type == 'image'): ?>
                                            <a href="../assets/uploads/chat/<?= $attachment ?>" target="_blank">
                                                <img src="../assets/uploads/chat/<?= $attachment ?>" class="img-fluid rounded" style="max-height: 200px; max-width: 100%;">
                                            </a>
                                        <?php else: ?>
                                            <a href="../assets/uploads/chat/<?= $attachment ?>" target="_blank" class="btn btn-sm btn-light border w-100 text-start d-flex align-items-center gap-2">
                                                <i class="fa fa-file-alt text-danger fa-lg"></i>
                                                <span class="text-truncate" style="max-width: 150px;"><?= $attachment ?></span>
                                                <i class="fa fa-download ms-auto text-secondary"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if(!empty($pesan_raw)): ?>
                                    <?= nl2br(htmlspecialchars($pesan_raw)) ?>
                                <?php endif; ?>

                                <span class="msg-time">
                                    <?= $time ?> <?= $isMe ? '<i class="fa fa-check-double ms-1"></i>' : '' ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php endwhile; ?>
            <?php else: ?>
                <div class="d-flex flex-column align-items-center justify-content-center h-100 opacity-50">
                    <i class="fa fa-comments fa-4x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada percakapan.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="chat-input-area">
            <form action="proses_partner.php" method="POST" enctype="multipart/form-data" class="d-flex w-100 align-items-center gap-2 mb-0" id="chatForm">
                <input type="hidden" name="action" value="send_message">
                <input type="hidden" name="bundle_id" value="<?= $bundle_id ?>">

                <?php if($bundle_id): ?>
                    <input type="file" name="attachment" id="fileInput" style="display: none;" onchange="previewFile()">
                    
                    <button type="button" class="btn btn-light rounded-circle text-muted border position-relative" style="width: 45px; height: 45px;" onclick="document.getElementById('fileInput').click()">
                        <i class="fa fa-paperclip"></i>
                        <span id="fileIndicator" class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="display: none;"></span>
                    </button>

                    <input type="text" name="message" class="input-msg" placeholder="Ketik pesan..." autocomplete="off">
                    
                    <button type="submit" class="btn-send">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                <?php else: ?>
                    <input type="text" class="input-msg bg-light" placeholder="Buat bundle dulu..." disabled>
                    <button type="button" class="btn-send bg-secondary" disabled><i class="fa fa-lock"></i></button>
                <?php endif; ?>
            </form>
        </div>
        
        <div id="filePreviewArea" class="px-4 pb-2 small text-primary fw-bold bg-white border-top" style="display: none;">
            <div class="d-flex align-items-center py-1">
                <i class="fa fa-file-arrow-up me-2"></i> 
                <span id="fileNameDisplay" class="me-auto text-truncate"></span> 
                <i class="fa fa-times text-danger cursor-pointer" onclick="cancelFile()" style="cursor: pointer;"></i>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modalAjak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header text-white">
                <h5 class="modal-title fw-bold"><i class="fa fa-rocket me-2"></i>Ajak Kolaborasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_partner.php" method="POST">
                <input type="hidden" name="action" value="create_request">
                <input type="hidden" name="mitra_id" value="<?= $partner_id ?>">
                <div class="modal-body p-4 bg-light">
                    <div class="mb-3">
                        <label class="fw-bold small text-secondary mb-1">Judul Bundle</label>
                        <input type="text" name="nama_bundle" class="form-control rounded-pill px-3" placeholder="Contoh: Paket Bundling Hemat" required>
                    </div>
                    <div class="mb-0">
                        <label class="fw-bold small text-secondary mb-1">Pesan Pembuka</label>
                        <textarea name="pesan_awal" class="form-control" rows="3" required>Halo, ayo kita buat bundling produk bareng!</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white">
                    <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Kirim <i class="fa fa-paper-plane ms-1"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto Scroll ke bawah
    const chatBox = document.getElementById("chatBox");
    if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;

    // Logic Preview File
    function previewFile() {
        const fileInput = document.getElementById('fileInput');
        const fileName = fileInput.files[0] ? fileInput.files[0].name : '';
        
        if (fileName) {
            document.getElementById('fileIndicator').style.display = 'block';
            document.getElementById('filePreviewArea').style.display = 'block';
            document.getElementById('fileNameDisplay').innerText = fileName;
        }
    }

    // Logic Batal Kirim File
    function cancelFile() {
        document.getElementById('fileInput').value = ""; // Reset input file
        document.getElementById('fileIndicator').style.display = 'none';
        document.getElementById('filePreviewArea').style.display = 'none';
    }
</script>

<?php include '../layouts/footer.php'; ?>