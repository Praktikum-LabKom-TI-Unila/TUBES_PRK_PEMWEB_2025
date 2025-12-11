<?php
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "npc_printing_db";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Gagal terhubung ke database: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pdo = null;
}

function ambil_banyak_data($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function ambil_satu_data($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

function jalankan_query($query) {
    global $conn;
    mysqli_query($conn, $query);
    return mysqli_affected_rows($conn);
}

function bersihkan_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

function format_rupiah($angka) {
    return "Rp " . number_format($angka, 0, ',', '.');
}
?>
