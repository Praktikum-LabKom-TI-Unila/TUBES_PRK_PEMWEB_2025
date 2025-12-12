<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit();
}


$examSessionKeys = [
    'ujian_id',
    'jumlah_soal',
    'ujian_judul',
    'ujian_mata_pelajaran_id',
    'ujian_waktu_pengerjaan',
    'ujian_kelas', 
    'ujian_gambar'
];
foreach ($examSessionKeys as $k) {
    if (isset($_SESSION[$k])) {
        unset($_SESSION[$k]);
    }
}

unset($_SESSION['ujian_id']);
unset($_SESSION['jumlah_soal']);

$_SESSION['success'] = "Ujian berhasil dibuat!";
header("Location: dashboard_admin.php");
exit();
?>