<?php
require "../config/database.php";
require "auth_admin.php";

$users = mysqli_query($conn, "SELECT * FROM users WHERE deleted_at IS NULL");
?>

<h2>List Pengguna</h2>

<table border="1" cellpadding="6">
<tr>
    <th>Nama</th>
    <th>Username</th>
    <th>Role</th>
    <th>No HP</th>
    <th>Aksi</th>
</tr>

<?php while ($u = mysqli_fetch_assoc($users)) { ?>
<tr>
    <td><?= $u['nama_lengkap'] ?></td>
    <td><?= $u['username'] ?></td>
    <td><?= $u['role'] ?></td>
    <td><?= $u['no_hp'] ?></td>
    <td>
        <a href="../actions/admin_user_delete.php?id=<?= $u['id'] ?>"
           onclick="return confirm('Yakin hapus user?')">Hapus</a>
    </td>
</tr>
<?php } ?>
</table>
