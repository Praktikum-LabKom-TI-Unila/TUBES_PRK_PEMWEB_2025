<?php
session_start();
require '../config/database.php';
require '../config/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

$uid = $_SESSION['user_id'];
$today = date("Y-m-d");

$limit = $conn->query("
    SELECT COUNT(*) AS total
    FROM claims
    WHERE mahasiswa_id='$uid' AND DATE(created_at)='$today'
")->fetch_assoc()['total'];

$pending = $conn->query("
    SELECT c.*, f.judul
    FROM claims c
    JOIN food_stocks f ON f.id = c.food_id
    WHERE c.mahasiswa_id='$uid' AND c.status='pending'
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
<div class="max-w-4xl mx-auto p-6">

    <h1 class="text-2xl font-bold mb-4">Dashboard Mahasiswa</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="p-4 bg-white shadow rounded">
            <h2 class="font-semibold">Klaim Hari Ini</h2>
            <p class="text-3xl font-bold text-green-600"><?php echo $limit; ?>/2</p>
        </div>

        <div class="p-4 bg-white shadow rounded">
            <h2 class="font-semibold">Tiket Pending</h2>
            <p class="text-3xl font-bold text-blue-600"><?php echo $pending->num_rows; ?></p>
        </div>
    </div>

    <h2 class="text-xl font-semibold mb-3">Tiket Aktif</h2>

    <?php if ($pending->num_rows == 0) { ?>
        <p class="text-gray-600">Tidak ada tiket pending.</p>
    <?php } ?>

    <?php while ($t = $pending->fetch_assoc()) { ?>
        <div class="p-4 bg-white rounded shadow border mb-3">
            <div class="font-bold text-lg"><?php echo $t['judul']; ?></div>
            <div>Kode Tiket: <span class="font-semibold text-blue-600"><?php echo $t['kode_tiket']; ?></span></div>
            <div>Dibuat: <?php echo formatTanggal($t['created_at']); ?></div>
        </div>
    <?php } ?>

</div>
</body>
</html>
