<?php
session_start();
require_once "../config.php";

// Cek Login Superadmin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'superadmin') {
    header("Location: ../login.php");
    exit();
}

$q = $conn->query("SELECT * FROM servis ORDER BY tgl_masuk DESC");
$settings = $conn->query("SELECT * FROM app_settings WHERE id=1")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Servis - RepairinBro</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; padding: 40px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0; font-size: 14px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        td { vertical-align: top; }
        
        .status-badge { font-weight: bold; }
        
        .signature { margin-top: 50px; text-align: right; }
        .signature div { display: inline-block; text-align: center; width: 200px; }
        .signature p { margin-top: 60px; font-weight: bold; text-decoration: underline; }

        .no-print { margin-bottom: 20px; }
        .btn-print { background: #333; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-family: sans-serif; }
        .btn-back { background: #ddd; color: #333; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-family: sans-serif; margin-right: 10px;}

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <a href="superadmin_dashboard.php" class="btn-back">Kembali</a>
        <a href="#" onclick="window.print()" class="btn-print">Cetak Laporan</a>
    </div>

    <div class="header">
        <h1><?= htmlspecialchars($settings['company_name'] ?? 'FixTrack Service') ?></h1>
        <p><?= htmlspecialchars($settings['address'] ?? 'Alamat Belum Diatur') ?></p>
        <p>Telp: <?= htmlspecialchars($settings['phone'] ?? '-') ?> | Email: <?= htmlspecialchars($settings['email'] ?? '-') ?></p>
    </div>

    <h3 style="text-align: center;">LAPORAN DATA SERVIS</h3>
    <p>Dicetak Tanggal: <?= date("d F Y, H:i") ?></p>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">No. Resi</th>
                <th style="width: 15%;">Tgl Masuk</th>
                <th style="width: 20%;">Pelanggan</th>
                <th style="width: 20%;">Barang</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 15%;">Biaya</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            while($r = $q->fetch_assoc()) { 
            ?>
            <tr>
                <td style="text-align: center;"><?= $no++ ?></td>
                <td><?= $r['no_resi'] ?></td>
                <td><?= date('d/m/Y', strtotime($r['tgl_masuk'])) ?></td>
                <td>
                    <b><?= htmlspecialchars($r['nama_pelanggan']) ?></b><br>
                    <small><?= htmlspecialchars($r['no_hp']) ?></small>
                </td>
                <td><?= htmlspecialchars($r['nama_barang']) ?></td>
                <td style="text-align: center;"><?= $r['status'] ?></td>
                <td style="text-align: right;">
                    <?= $r['biaya'] ? "Rp ".number_format($r['biaya'],0,',','.') : "-" ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>

    <div class="signature">
        <div>
            Jakarta, <?= date("d F Y") ?><br>
            Mengetahui,<br>
            <br><br><br>
            <p>Superadmin</p>
        </div>
    </div>

</body>
</html>
