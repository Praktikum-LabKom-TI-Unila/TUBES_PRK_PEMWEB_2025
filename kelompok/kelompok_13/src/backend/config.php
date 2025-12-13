<?php

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'evoting'; 

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("
        <div style='font-family: Arial; padding: 20px; background: #fef2f2; border-left: 4px solid #dc2626; margin: 20px;'>
            <h3 style='color: #991b1b;'>❌ Database Connection Failed!</h3>
            <p><strong>Error:</strong> " . mysqli_connect_error() . "</p>
            <hr>
            <h4>Troubleshooting:</h4>
            <ol>
                <li>Pastikan MySQL/MariaDB sudah berjalan (cek Laragon/XAMPP)</li>
                <li>Import file <code>evoting.sql</code> ke phpMyAdmin</li>
                <li>Periksa konfigurasi di <code>backend/config.php</code></li>
                <li>Username default: <code>root</code>, Password: <strong>kosong</strong></li>
            </ol>
        </div>
    ");
}

mysqli_set_charset($conn, "utf8mb4");

date_default_timezone_set('Asia/Jakarta');
?>
