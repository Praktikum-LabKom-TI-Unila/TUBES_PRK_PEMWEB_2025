<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../../layouts/header.php';

// Query untuk Tutor Pending (Butuh Verifikasi)
$query_pending = "SELECT * FROM users WHERE role = 'tutor' AND status = 'pending' ORDER BY created_at DESC";
$result_pending = mysqli_query($conn, $query_pending);

// Query untuk Tutor Aktif
$query_active = "SELECT * FROM users WHERE role = 'tutor' AND status = 'active' ORDER BY name ASC";
$result_active = mysqli_query($conn, $query_active);
?>

<div class="container" style="margin-top: 40px; min-height: 80vh;">
    <h2 style="color: var(--color-primary); margin-bottom: 20px;">Admin Dashboard</h2>

    <?php if (isset($_GET['msg'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
    <?php endif; ?>

    <div class="card" style="border-left: 5px solid var(--color-secondary); margin-bottom: 30px;">
        <h3 style="margin-bottom: 15px; color: var(--color-secondary);">⏳ Menunggu Verifikasi</h3>
        
        <?php if (mysqli_num_rows($result_pending) > 0): ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: var(--color-bg-light); text-align: left;">
                        <th style="padding: 10px;">Nama</th>
                        <th style="padding: 10px;">Email</th>
                        <th style="padding: 10px;">Tanggal Daftar</th>
                        <th style="padding: 10px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result_pending)): ?>
                    <tr style="border-bottom: 1px solid var(--color-border);">
                        <td style="padding: 10px; font-weight: bold;"><?php echo $row['name']; ?></td>
                        <td style="padding: 10px;"><?php echo $row['email']; ?></td>
                        <td style="padding: 10px;"><?php echo $row['created_at']; ?></td>
                        <td style="padding: 10px;">
                            <div style="display: flex; gap: 5px;">
                                <form action="../../../backend/admin/verify_tutor.php" method="POST">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" style="padding: 5px 10px; background: var(--color-success); color: white; border: none; border-radius: 4px; cursor: pointer;">✔ Terima</button>
                                </form>
                                <form action="../../../backend/admin/verify_tutor.php" method="POST" onsubmit="return confirm('Tolak tutor ini?');">
                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" style="padding: 5px 10px; background: var(--color-danger); color: white; border: none; border-radius: 4px; cursor: pointer;">✖ Tolak</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: var(--color-text-light); font-style: italic;">Tidak ada permintaan verifikasi baru.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3 style="margin-bottom: 15px;">✅ Tutor Aktif</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: var(--color-bg-light); text-align: left;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Nama</th>
                    <th style="padding: 10px;">Email</th>
                    <th style="padding: 10px;">Status</th>
                    <th style="padding: 10px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_active)): ?>
                <tr style="border-bottom: 1px solid var(--color-border);">
                    <td style="padding: 10px;">#<?php echo $row['id']; ?></td>
                    <td style="padding: 10px;"><?php echo $row['name']; ?></td>
                    <td style="padding: 10px;"><?php echo $row['email']; ?></td>
                    <td style="padding: 10px;">
                        <span style="padding: 3px 8px; background: #d4edda; color: #155724; border-radius: 10px; font-size: 0.8em;">Active</span>
                    </td>
                    <td style="padding: 10px;">
                        <form action="../../../backend/admin/delete_user.php" method="POST" onsubmit="return confirm('Hapus user ini?');">
                            <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" style="padding: 5px 10px; background: var(--color-text-light); color: white; border: none; border-radius: 4px; cursor: pointer;">Hapus</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../../layouts/footer.php'; ?>