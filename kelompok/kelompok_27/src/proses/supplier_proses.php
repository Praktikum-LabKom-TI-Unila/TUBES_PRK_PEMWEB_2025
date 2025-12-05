<?php
include('../config/koneksi.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? ''; 
    $nama_supplier = $_POST['nama_supplier'] ?? '';
    $no_hp = $_POST['no_hp'] ?? '';
    $kategori = $_POST['kategori'] ?? ''; 
    $id_supplier = $_POST['id_supplier'] ?? 0;
    
    switch ($action) {
        default:
            header("Location: ../admin/master_supplier.php?status=error&pesan=Aksi tidak dikenal");
            exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'arsip' && isset($_GET['id'])) {
}


function tambahSupplier($koneksi, $nama, $hp, $kat) {  }
function ubahSupplier($koneksi, $id, $nama, $hp, $kat) { }
function arsipSupplier($koneksi, $id) { }

?>