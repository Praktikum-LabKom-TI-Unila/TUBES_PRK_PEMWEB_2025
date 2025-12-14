<?php
include '../config/koneksi.php';
include '../layouts/header.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    echo "<script>location.href='login.php';</script>";
    exit;
}

// Ambil data user terbaru
$id_user = $_SESSION['user_id'];
$query   = mysqli_query($koneksi, "SELECT * FROM users WHERE id='$id_user'");
$data    = mysqli_fetch_assoc($query);
?>

<div class="row justify-content-center mt-4 mb-5">
    <div class="col-md-8">
        <div class="card shadow border-0" style="border-radius: 15px;">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h4 class="mb-0 fw-bold" style="color: #ED7D31;">
                    <i class="fa-solid fa-store me-2"></i> Edit Profil Toko
                </h4>
            </div>
            <div class="card-body p-4">
                
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="alert alert-success border-0 shadow-sm" style="background-color: #d4edda; color: #155724;">
                        <i class="fa-solid fa-check-circle me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger border-0 shadow-sm">
                        <i class="fa-solid fa-exclamation-circle me-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- PENTING: enctype untuk upload file -->
                <form action="proses_auth.php?aksi=update_profil" method="POST" enctype="multipart/form-data">
                    
                    <!-- BAGIAN FOTO PROFIL / LOGO -->
                    <div class="text-center mb-4">
                        <label class="fw-bold d-block mb-2" style="color: #4F4A45;">Logo Toko</label>
                        
                        <!-- Container Preview -->
                        <div class="p-2 border rounded bg-light d-inline-block position-relative" style="min-width: 150px; max-width: 300px;">
                            <!-- Gambar Preview: 
                                 - max-width: 100% (agar responsif mengikuti lebar container)
                                 - height: auto (agar proporsi terjaga/tidak gepeng)
                                 - object-fit: contain (memastikan seluruh gambar terlihat)
                            -->
                            <img id="preview-foto" 
                                 src="<?php echo !empty($data['foto_profil']) && file_exists('../assets/uploads/'.$data['foto_profil']) ? '../assets/uploads/'.$data['foto_profil'] : 'https://ui-avatars.com/api/?name='.urlencode($data['nama_toko']).'&background=random&size=200'; ?>" 
                                 class="d-block mx-auto shadow-sm" 
                                 style="width: 100%; height: auto; max-height: 200px; object-fit: contain; border-radius: 8px;">
                            
                            <!-- Tombol Kamera Upload -->
                            <label for="upload-foto" class="position-absolute bottom-0 end-0 bg-white border shadow rounded-circle p-2 m-2" style="cursor: pointer; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fa-solid fa-camera text-primary"></i>
                                <input type="file" name="foto_profil" id="upload-foto" class="d-none" accept="image/*" onchange="previewImage(event)">
                            </label>
                        </div>
                        <div class="form-text mt-2">Format: JPG/PNG, Maks. 2MB. Disarankan rasio persegi atau logo transparan.</div>
                    </div>

                    <!-- Input Lainnya Tetap Sama -->
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #4F4A45;">Nama Toko</label>
                        <input type="text" name="nama_toko" class="form-control" value="<?php echo htmlspecialchars($data['nama_toko'] ?? ''); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #4F4A45;">Kategori Bisnis</label>
                        <select name="kategori_bisnis" class="form-select">
                            <?php 
                            $kategori = ['Kuliner (FnB)', 'Fashion', 'Agribisnis', 'Manufaktur/Kerajinan', 'Jasa', 'Retail/Grosir', 'Teknologi', 'Lainnya'];
                            $kategori_user = $data['kategori_bisnis'] ?? '';
                            foreach($kategori as $kat) {
                                $selected = ($kategori_user == $kat) ? 'selected' : '';
                                echo "<option value='$kat' $selected>$kat</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #4F4A45;">Nomor WhatsApp / HP</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fa-brands fa-whatsapp"></i></span>
                            <input type="text" name="no_hp" class="form-control" value="<?php echo htmlspecialchars($data['no_hp'] ?? ''); ?>" placeholder="Contoh: 08123456789">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: #4F4A45;">Alamat Lengkap</label>
                        <textarea name="alamat_toko" class="form-control" rows="2"><?php echo htmlspecialchars($data['alamat_toko'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold" style="color: #4F4A45;">Deskripsi Toko</label>
                        <textarea name="deskripsi_toko" class="form-control" rows="4" placeholder="Ceritakan keunggulan toko Anda..."><?php echo htmlspecialchars($data['deskripsi_toko'] ?? ''); ?></textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="../produk/index.php" class="btn btn-outline-secondary me-md-2 px-4">Batal</a>
                        <button type="submit" class="btn text-white px-4" style="background-color: #ED7D31;">Simpan Perubahan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('preview-foto');
        output.src = reader.result;
    };
    if(event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}
</script>

<?php include '../layouts/footer.php'; ?>