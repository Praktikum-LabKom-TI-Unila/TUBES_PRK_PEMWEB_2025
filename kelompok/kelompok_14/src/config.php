<?php
/**
 * Konfigurasi Database
 * Mengatur koneksi ke database MySQL menggunakan MySQLi
 */
// Set Timezone ke WIB (Waktu Indonesia Barat)
date_default_timezone_set("Asia/Jakarta");

// Konfigurasi DB Host, User, Pass, Name
$servername = "localhost";
$user = 'root';
$pass = ''; // Default XAMPP password kosong
$db   = 'fixtrack';

// Buat koneksi
$conn = new mysqli($servername, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
