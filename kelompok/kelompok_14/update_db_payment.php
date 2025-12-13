<?php
require_once 'src/config.php';

// Add metode_pembayaran column
$check1 = $conn->query("SHOW COLUMNS FROM servis LIKE 'metode_pembayaran'");
if ($check1->num_rows == 0) {
    $sql1 = "ALTER TABLE servis ADD COLUMN metode_pembayaran ENUM('Cash', 'Transfer', 'QRIS', 'Debit/Kredit') NULL AFTER status";
    if ($conn->query($sql1)) {
        echo "Column 'metode_pembayaran' added successfully.<br>";
    } else {
        echo "Error adding 'metode_pembayaran': " . $conn->error . "<br>";
    }
} else {
    echo "Column 'metode_pembayaran' already exists.<br>";
}

// Add bukti_pembayaran column
$check2 = $conn->query("SHOW COLUMNS FROM servis LIKE 'bukti_pembayaran'");
if ($check2->num_rows == 0) {
    $sql2 = "ALTER TABLE servis ADD COLUMN bukti_pembayaran VARCHAR(255) NULL AFTER metode_pembayaran";
    if ($conn->query($sql2)) {
        echo "Column 'bukti_pembayaran' added successfully.<br>";
    } else {
        echo "Error adding 'bukti_pembayaran': " . $conn->error . "<br>";
    }
} else {
    echo "Column 'bukti_pembayaran' already exists.<br>";
}

echo "Database update check complete.";
?>
