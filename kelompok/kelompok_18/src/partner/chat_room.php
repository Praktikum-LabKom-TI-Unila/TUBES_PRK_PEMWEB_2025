<?php
include '../config/koneksi.php';
include '../layouts/header.php';

// 1. Cek Sesi
$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) { echo "<script>window.location='../auth/login.php';</script>"; exit; }

// ==========================================
// LOGIKA TOMBOL KEMBALI (SMART BACK BUTTON)
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

if (!$partner_id) { echo "<script>window.location='index.php';</script>"; exit; }

$partner = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id='$partner_id'"));
$chats = ($bundle_id) ? mysqli_query($koneksi, "SELECT * FROM chats WHERE bundle_id='$bundle_id' ORDER BY created_at ASC") : [];

// --- DATA UNTUK MODAL DEAL ---
$q_prod_me = mysqli_query($koneksi, "SELECT id, nama_produk, harga FROM products WHERE user_id='$my_id'");
$q_prod_partner = mysqli_query($koneksi, "SELECT id, nama_produk, harga FROM products WHERE user_id='$partner_id'");
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
                    <button class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalDeal">
                        <i class="fa-solid fa-handshake me-2"></i> Atur Kesepakatan
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="chat-area" id="chatBox">
            <?php if ($bundle_id && mysqli_num_rows($chats) > 0): ?>
                <?php while($c = mysqli_fetch_assoc($chats)): ?>
                    <?php 
                        $isMe = ($c['sender_id'] == $my_id);
                        $time = date('H:i', strtotime($c['created_at']));
                        $pesan_raw = $c['message'] ?? ''; 
                        $attachment = $c['attachment'] ?? null;
                        $type = $c['attachment_type'] ?? null;
                        
                        // Deteksi Tipe Pesan
                        $isSystem = strpos($pesan_raw, '[SISTEM]') !== false;
                        $isDealOffer = strpos($pesan_raw, '[DEAL_PROPOSAL]') === 0;
                    ?>

                    <?php if($isSystem): ?>
                        <div class="system-message">
                            <span class="system-badge"><?= htmlspecialchars(str_replace('[SISTEM]', '', $pesan_raw)) ?></span>
                        </div>

                    <?php elseif($isDealOffer): ?>
                        <?php 
                            $json_str = str_replace('[DEAL_PROPOSAL]', '', $pesan_raw);
                            $deal = json_decode($json_str, true);
                        ?>
                        <div class="message-wrapper justify-content-center my-3">
                            <div class="card shadow-sm border-warning" style="width: 85%; max-width: 400px; background: #fffbf0; border-radius: 15px;">
                                <div class="card-body text-center p-3">
                                    <div class="mb-2"><i class="fa fa-file-contract text-warning fa-2x"></i></div>
                                    <h6 class="fw-bold text-dark mb-1">PROPOSAL KESEPAKATAN</h6>
                                    <p class="text-muted small mb-2">Mitra mengajukan detail bundle baru:</p>
                                    
                                    <div class="bg-white p-2 rounded border mb-3">
                                        <h5 class="fw-bold text-primary mb-0"><?= htmlspecialchars($deal['nama_bundle']) ?></h5>
                                        <div class="text-success fw-bold">Rp <?= number_format($deal['harga']) ?></div>
                                    </div>

                                    <?php if(!empty($deal['catatan'])): ?>
                                        <div class="alert alert-light border small text-muted fst-italic py-2">"<?= htmlspecialchars($deal['catatan']) ?>"</div>
                                    <?php endif; ?>

                                    <?php if (!$isMe): ?>
                                        <form action="proses_partner.php" method="POST">
                                            <input type="hidden" name="action" value="accept_deal_proposal">
                                            <input type="hidden" name="chat_id" value="<?= $c['id'] ?>">
                                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold w-100 shadow-sm">
                                                <i class="fa fa-check-circle me-1"></i> Setujui Kesepakatan
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary rounded-pill px-4 w-100" disabled>
                                            <i class="fa fa-clock me-1"></i> Menunggu Persetujuan...
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div class="card-footer bg-transparent border-warning text-muted small text-center py-1">
                                    Diajukan: <?= date('d M, H:i', strtotime($c['created_at'])) ?>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="message-wrapper <?= $isMe ? 'me' : 'them' ?>">
                            <div class="message-bubble">
                                
                                <?php if($attachment): ?>
                                    <div class="mb-2">
                                        <?php if($type == 'image'): ?>
                                            <a href="../assets/uploads/chat/<?= $attachment ?>" target="_blank">
                                                <img src="../assets/uploads/chat/<?= $attachment ?>" class="img-fluid rounded" style="max-height: 200px;">
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

                                <?= nl2br(htmlspecialchars($pesan_raw)) ?>

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
                <i class="fa fa-times text-danger cursor-pointer ms-2" onclick="cancelFile()" style="cursor: pointer;"></i>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="modalAjak" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Ajak Kolaborasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_partner.php" method="POST">
                <input type="hidden" name="action" value="create_request">
                <input type="hidden" name="mitra_id" value="<?= $partner_id ?>">
                <div class="modal-body bg-light">
                    <label class="fw-bold small mb-1">Nama Proyek Bundle</label>
                    <input type="text" name="nama_bundle" class="form-control mb-3" placeholder="Contoh: Paket Sarapan Hemat" required>
                    <label class="fw-bold small mb-1">Pesan Sapaan</label>
                    <textarea name="pesan_awal" class="form-control" rows="3" required>Halo, mari kita buat bundling produk!</textarea>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary w-100 rounded-pill">Kirim Ajakan</button></div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDeal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header" style="background-color: var(--primary); color: white;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-file-signature me-2"></i>Atur Produk & Harga</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_partner.php" method="POST">
                <input type="hidden" name="action" value="propose_deal"> <input type="hidden" name="bundle_id" value="<?= $bundle_id ?>">
                
                <div class="modal-body bg-light">
                    <div class="mb-3">
                        <label class="fw-bold small">Nama Paket Bundle</label>
                        <input type="text" name="nama_bundle" class="form-control rounded-pill" value="<?= htmlspecialchars($bundle_data['nama_bundle'] ?? '') ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold small text-primary">Produk Anda</label>
                            <select name="produk_saya" class="form-select" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <?php while($p = mysqli_fetch_assoc($q_prod_me)): ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= $p['nama_produk'] ?> (Rp <?= number_format($p['harga']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold small text-secondary">Produk Partner</label>
                            <select name="produk_partner" class="form-select" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <?php while($p = mysqli_fetch_assoc($q_prod_partner)): ?>
                                    <option value="<?= $p['id'] ?>">
                                        <?= $p['nama_produk'] ?> (Rp <?= number_format($p['harga']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold small">Harga Total (Deal)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="harga_bundle" class="form-control fw-bold text-success" 
                                   value="<?= $bundle_data['harga_bundle'] > 0 ? $bundle_data['harga_bundle'] : '' ?>" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="fw-bold small text-muted">Catatan (Opsional)</label>
                        <textarea name="pesan_proposal" class="form-control" rows="2" placeholder="Contoh: Harga ini sudah termasuk diskon 10%..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">
                        <i class="fa fa-paper-plane me-1"></i> Kirim Penawaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto Scroll
    const chatBox = document.getElementById("chatBox");
    if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;

    // Preview File
    function previewFile() {
        const fileInput = document.getElementById('fileInput');
        const fileName = fileInput.files[0] ? fileInput.files[0].name : '';
        
        if (fileName) {
            document.getElementById('fileIndicator').style.display = 'block';
            document.getElementById('filePreviewArea').style.display = 'block';
            document.getElementById('fileNameDisplay').innerText = fileName;
        }
    }

    // Cancel File
    function cancelFile() {
        document.getElementById('fileInput').value = ""; 
        document.getElementById('fileIndicator').style.display = 'none';
        document.getElementById('filePreviewArea').style.display = 'none';
    }
</script>

<?php include '../layouts/footer.php'; ?>