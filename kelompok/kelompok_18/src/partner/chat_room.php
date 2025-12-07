<?php
include '../config/koneksi.php';
include '../layouts/header.php';

// Cek Sesi
$my_id = $_SESSION['id'] ?? $_SESSION['user_id'] ?? null;
if (!$my_id) { echo "<script>window.location='../auth/login.php';</script>"; exit; }

$back_url = 'my_bundles.php'; // Default back

// Logika Ambil Data Bundle & Partner
$bundle_id = $_GET['bundle_id'] ?? null;
$partner_id = $_GET['partner_id'] ?? null;
$bundle_data = null;

if ($bundle_id) {
    $q = mysqli_query($koneksi, "SELECT * FROM bundles WHERE id='$bundle_id'");
    $bundle_data = mysqli_fetch_assoc($q);
    $partner_id = ($bundle_data['pembuat_id'] == $my_id) ? $bundle_data['mitra_id'] : $bundle_data['pembuat_id'];
} elseif ($partner_id) {
    // Cek apakah sudah ada bundle aktif/pending
    $q = mysqli_query($koneksi, "SELECT * FROM bundles WHERE (pembuat_id='$my_id' AND mitra_id='$partner_id') OR (pembuat_id='$partner_id' AND mitra_id='$my_id') ORDER BY created_at DESC LIMIT 1");
    $bundle_data = mysqli_fetch_assoc($q);
    if ($bundle_data) $bundle_id = $bundle_data['id'];
}

if (!$partner_id) { echo "<script>window.location='index.php';</script>"; exit; }

$partner = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM users WHERE id='$partner_id'"));
$chats = ($bundle_id) ? mysqli_query($koneksi, "SELECT * FROM chats WHERE bundle_id='$bundle_id' ORDER BY created_at ASC") : [];

// --- QUERY UTK DROPDOWN PRODUK ---
// 1. Produk Saya
$q_prod_me = mysqli_query($koneksi, "SELECT id, nama_produk, harga FROM products WHERE user_id='$my_id'");
// 2. Produk Partner
$q_prod_partner = mysqli_query($koneksi, "SELECT id, nama_produk, harga FROM products WHERE user_id='$partner_id'");
?>

<link rel="stylesheet" href="../assets/css/style_partner.css">

<div class="container-fluid px-0"> 
    <div class="chat-container">
        
        <!-- HEADER -->
        <div class="chat-header">
            <div class="d-flex align-items-center gap-3">
                <a href="<?= $back_url ?>" class="text-secondary"><i class="fa fa-arrow-left fa-lg"></i></a>
                <img src="<?= !empty($partner['foto_profil']) ? '../assets/uploads/'.$partner['foto_profil'] : 'https://ui-avatars.com/api/?name='.urlencode($partner['nama_toko']).'&background=D7CCC8&color=6D4C41' ?>" class="rounded-circle border" width="45" height="45">
                <div>
                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($partner['nama_toko']) ?></h6>
                    <small class="text-success"><i class="fa fa-circle" style="font-size: 8px;"></i> Online</small>
                </div>
            </div>
            
            <div>
                <?php if (!$bundle_id): ?>
                    <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAjak">
                        <i class="fa fa-plus me-1"></i> Mulai Bundle
                    </button>
                <?php else: ?>
                    <!-- TOMBOL DEAL / SETTING PRODUK -->
                    <button class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalDeal">
                        <i class="fa-solid fa-handshake me-2"></i> Atur Produk Bundle
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- CHAT AREA -->
        <div class="chat-area" id="chatBox">
            <?php if ($bundle_id && mysqli_num_rows($chats) > 0): ?>
                <?php while($c = mysqli_fetch_assoc($chats)): 
                    $isMe = ($c['sender_id'] == $my_id);
                    $isSys = strpos($c['message'], '[SISTEM]') !== false;
                ?>
                    <?php if($isSys): ?>
                        <div class="system-message"><span class="system-badge"><?= str_replace('[SISTEM]', '', htmlspecialchars($c['message'])) ?></span></div>
                    <?php else: ?>
                        <div class="message-wrapper <?= $isMe ? 'me' : 'them' ?>">
                            <div class="message-bubble">
                                <?php if($c['attachment']): ?>
                                    <div class="mb-2">
                                        <?php if($c['attachment_type'] == 'image'): ?>
                                            <a href="../assets/uploads/chat/<?= $c['attachment'] ?>" target="_blank"><img src="../assets/uploads/chat/<?= $c['attachment'] ?>" class="img-fluid rounded" style="max-height: 200px;"></a>
                                        <?php else: ?>
                                            <a href="../assets/uploads/chat/<?= $c['attachment'] ?>" class="btn btn-sm btn-light border"><i class="fa fa-file"></i> Unduh File</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                <?= nl2br(htmlspecialchars($c['message'])) ?>
                                <div class="msg-time text-end mt-1" style="font-size: 0.6rem; opacity: 0.7;"><?= date('H:i', strtotime($c['created_at'])) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center mt-5 opacity-50"><i class="fa fa-comments fa-3x mb-3"></i><p>Mulai percakapan...</p></div>
            <?php endif; ?>
        </div>

        <!-- INPUT AREA -->
        <div class="chat-input-area">
            <form action="proses_partner.php" method="POST" enctype="multipart/form-data" class="d-flex w-100 align-items-center gap-2 mb-0">
                <input type="hidden" name="action" value="send_message">
                <input type="hidden" name="bundle_id" value="<?= $bundle_id ?>">

                <?php if($bundle_id): ?>
                    <label class="btn btn-light rounded-circle border" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                        <i class="fa fa-paperclip text-secondary"></i>
                        <input type="file" name="attachment" style="display: none;" onchange="alert('File dipilih!')">
                    </label>
                    <input type="text" name="message" class="input-msg" placeholder="Ketik pesan..." autocomplete="off">
                    <button type="submit" class="btn-send"><i class="fa fa-paper-plane"></i></button>
                <?php else: ?>
                    <input type="text" class="input-msg bg-light" placeholder="Buat bundle dulu..." disabled>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<!-- MODAL AJAK KOLAB (PERTAMA KALI) -->
<div class="modal fade" id="modalAjak" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
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

<!-- MODAL DEAL / SETTING PRODUK (BARU) -->
<div class="modal fade" id="modalDeal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: var(--primary-orange); color: white;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-tags me-2"></i>Atur Produk Bundle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_partner.php" method="POST">
                <input type="hidden" name="action" value="deal_bundle">
                <input type="hidden" name="bundle_id" value="<?= $bundle_id ?>">
                
                <div class="modal-body bg-light">
                    <p class="text-muted small mb-3">Pilih produk dari toko Anda dan toko Partner untuk digabungkan.</p>

                    <!-- Edit Nama Bundle -->
                    <div class="mb-3">
                        <label class="fw-bold small">Nama Paket Bundle</label>
                        <input type="text" name="nama_bundle" class="form-control" value="<?= htmlspecialchars($bundle_data['nama_bundle'] ?? '') ?>" required>
                    </div>

                    <div class="row">
                        <!-- Produk Saya -->
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold small text-primary">Produk Anda</label>
                            <select name="produk_pembuat" class="form-select" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <?php while($p = mysqli_fetch_assoc($q_prod_me)): ?>
                                    <option value="<?= $p['id'] ?>" <?= ($bundle_data['produk_pembuat_id'] == $p['id']) ? 'selected' : '' ?>>
                                        <?= $p['nama_produk'] ?> (Rp <?= number_format($p['harga']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <!-- Produk Partner -->
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold small text-secondary">Produk Partner</label>
                            <select name="produk_mitra" class="form-select" required>
                                <option value="" disabled selected>-- Pilih --</option>
                                <?php while($p = mysqli_fetch_assoc($q_prod_partner)): ?>
                                    <option value="<?= $p['id'] ?>" <?= ($bundle_data['produk_mitra_id'] == $p['id']) ? 'selected' : '' ?>>
                                        <?= $p['nama_produk'] ?> (Rp <?= number_format($p['harga']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Harga Bundle -->
                    <div class="mb-3">
                        <label class="fw-bold small">Harga Total Paket (Setelah Gabung)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">Rp</span>
                            <input type="number" name="harga_bundle" class="form-control fw-bold text-success" 
                                   value="<?= $bundle_data['harga_bundle'] > 0 ? $bundle_data['harga_bundle'] : '' ?>" 
                                   placeholder="Contoh: 50000" required>
                        </div>
                        <div class="form-text">Harga jual paket gabungan ini di katalog.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Simpan Kesepakatan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var chatBox = document.getElementById("chatBox");
    if(chatBox) chatBox.scrollTop = chatBox.scrollHeight;
</script>

<?php include '../layouts/footer.php'; ?>