<!DOCTYPE html>
<html>
<head>
    <title>Register | ZeroWaste</title>
</head>
<body>

<h2>Registrasi Pengguna Baru</h2>

<form action="actions/auth_register.php" method="POST">

    <label>Nama Lengkap</label><br>
    <input type="text" name="nama_lengkap" required><br><br>

    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>No HP</label><br>
    <input type="text" name="no_hp" required><br><br>

    <label>Role</label><br>
    <select name="role" required>
        <option value="mahasiswa">Mahasiswa</option>
        <option value="donatur">Donatur</option>
    </select><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Daftar</button>
</form>

<p>Sudah punya akun? <a href="login.php">Login</a></p>

</body>
</html>
