<h2 class="mb-4">Pengaturan Akun</h2>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm text-center p-4">
            <div class="mb-3">
                <img src="https://ui-avatars.com/api/?name=Admin+Panel&size=128" class="rounded-circle img-thumbnail shadow-sm">
            </div>
            <h5><?= htmlspecialchars($_SESSION['user_name'] ?? 'Super Admin') ?></h5>
            <p class="text-muted">Administrator</p>
            <button class="btn btn-outline-primary btn-sm rounded-pill px-4">Upload Foto Baru</button>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Edit Informasi</h5>
            </div>
            <div class="card-body p-4">
                <form onsubmit="event.preventDefault(); showToast('Profil berhasil disimpan!');">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" value="admin@scholarbridge.com">
                    </div>
                    
                    <hr class="my-4">
                    <h6 class="fw-bold mb-3">Ganti Password</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control">
                        </div>
                    </div>

                    <div class="mt-3 text-end">
                        <button type="button" class="btn btn-light me-2">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>