<?php require_once '../../layouts/header.php'; ?>

<div class="container" style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div class="card" style="width: 100%; max-width: 450px;">
        <h2 style="text-align: center; color: var(--color-primary); margin-bottom: 20px;">Daftar Akun</h2>

        <?php if (isset($_GET['error'])): ?>
            <div style="color: var(--color-danger); text-align: center; margin-bottom: 15px;">
                <?php
                    if ($_GET['error'] == 'email_taken') echo "Email sudah terdaftar.";
                    else if ($_GET['error'] == 'db_error') echo "Terjadi kesalahan sistem.";
                    else echo "Harap isi semua data.";
                ?>
            </div>
        <?php endif; ?>

        <form action="../../../backend/auth/register_process.php" method="POST">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Nama Lengkap</label>
                <input type="text" name="name" required style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: var(--border-radius);">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: var(--border-radius);">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: var(--border-radius);">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px;">Daftar Sebagai</label>
                <select name="role" required style="width: 100%; padding: 10px; border: 1px solid var(--color-border); border-radius: var(--border-radius); background: white;">
                    <option value="" disabled selected>Pilih Peran...</option>
                    <option value="learner">Siswa (Pencari Tutor)</option>
                    <option value="tutor">Tutor (Pengajar)</option>
                </select>
            </div>

            <button type="submit" style="width: 100%; padding: 12px; background-color: var(--color-primary); color: white; border: none; border-radius: var(--border-radius); cursor: pointer; font-size: 16px;">
                Daftar Sekarang
            </button>
        </form>

        <p style="text-align: center; margin-top: 15px;">
            Sudah punya akun? <a href="login.php">Login</a>
        </p>
    </div>
</div>

<?php require_once '../../layouts/footer.php'; ?>