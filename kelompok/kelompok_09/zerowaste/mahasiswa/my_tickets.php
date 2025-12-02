<?php
session_start();
require '../config/database.php';
require '../config/functions.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: ../login.php");
    exit;
}

$uid = $_SESSION['user_id'];

$tiket = $conn->query("
    SELECT c.*, f.judul
    FROM claims c
    JOIN food_stocks f ON f.id = c.food_id
    WHERE c.mahasiswa_id='$uid'
    ORDER BY c.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tiket Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
<div class="max-w-4xl mx-auto p-6">

    <h1 class="text-2xl font-bold mb-4">Tiket Saya</h1>

    <?php while ($t = $tiket->fetch_assoc()) { ?>
        <div class="p-4 bg-white rounded shadow border mb-3">
            <div class="flex justify-between">

                <div>
                    <div class="font-bold text-lg"><?php echo $t['judul']; ?></div>
                    <div>Kode Tiket: <span class="text-blue-600 font-semibold"><?php echo $t['kode_tiket']; ?></span></div>
                    <div>Status: <span class="font-semibold"><?php echo $t['status']; ?></span></div>
                    <div>Waktu: <?php echo formatTanggal($t['created_at']); ?></div>

                    <?php if ($t['status'] == 'batal') { ?>
                        <div class="text-red-600">Alasan: <?php echo $t['alasan_batal']; ?></div>
                    <?php } ?>
                </div>

                <?php if ($t['status'] === 'pending') { ?>
                    <form action="../actions/claim_cancel.php" method="POST">
                        <input type="hidden" name="claim_id" value="<?php echo $t['id']; ?>">
                        <button class="px-3 py-2 bg-red-600 text-white rounded">Batalkan</button>
                    </form>
                <?php } ?>

            </div>
        </div>
    <?php } ?>

</div>
</body>
</html>
