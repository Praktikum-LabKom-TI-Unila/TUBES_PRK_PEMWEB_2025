<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

require "../config/config.php";
require "../layouts/header.php";
require "../layouts/sidebar.php";

$stmt = mysqli_prepare($conn, "SELECT * FROM umkm WHERE user_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="p-4">
    <h3 class="fw-bold text-brand-primary mb-4">Status Izin UMKM</h3>

    <?php if (mysqli_num_rows($result) == 0): ?>
        <div class='alert alert-info'>
            Anda belum pernah mendaftar UMKM.
        </div>
        <a href='daftar_umkm.php' class='btn btn-primary'>Daftar UMKM Sekarang</a>

    <?php else: ?>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Usaha</th>
                        <th>Pemilik</th>
                        <th>Bidang</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)):

                        $status = $row['status'];
                        $badge = "secondary";

                        if ($status == "pending") $badge = "warning";
                        if ($status == "approved") $badge = "success";
                        if ($status == "rejected") $badge = "danger";
                    ?>
                    
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= htmlspecialchars($row['nama_usaha'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['nama_pemilik'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['bidang_usaha'], ENT_QUOTES, 'UTF-8'); ?></td>

                        <td>
                            <span class="badge bg-<?= htmlspecialchars($badge, ENT_QUOTES, 'UTF-8'); ?>"><?= ucfirst(htmlspecialchars($status, ENT_QUOTES, 'UTF-8')); ?></span>
                        </td>

                        <td><?= htmlspecialchars($row['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>

                        <td>
                            <?php if ($status == "rejected"): ?>
                                <a href="daftar_umkm.php" class="btn btn-sm btn-primary">
                                    Ajukan Ulang
                                </a>
                            <?php elseif ($status == "approved"): ?>
                                <button class="btn btn-sm btn-success" disabled>Download (Soon)</button>
                            <?php else: ?>
                                <span class="text-muted">Menunggu...</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>

</div>

<?php require "../layouts/footer.php"; ?>
