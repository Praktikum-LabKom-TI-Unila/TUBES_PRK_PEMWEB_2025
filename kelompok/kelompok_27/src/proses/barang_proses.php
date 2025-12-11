<?php
session_start();
include('../config/koneksi.php'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../admin/master_barang.php?status=error&pesan=Anda tidak punya akses!");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? ''; 
    
    $nama_barang = $_POST['nama_barang'] ?? '';
    $harga_jual = $_POST['harga_jual'] ?? 0;
    $stok = $_POST['stok'] ?? 0;
    $id_supplier = $_POST['id_supplier'] ?? NULL; 
    $id_barang = $_POST['id_barang'] ?? 0; 
    
    if (empty($id_supplier) || $id_supplier == '0') {
        $id_supplier = NULL;
    }

    switch ($action) {
        case 'tambah':
            tambahBarang($conn, $nama_barang, $harga_jual, $stok, $id_supplier);
            break;

        case 'ubah':
            ubahBarang($conn, $id_barang, $nama_barang, $harga_jual, $stok, $id_supplier);
            break;

        default:
            header("Location: ../admin/master_barang.php?status=error&pesan=Aksi tidak dikenal");
            exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'arsip' && isset($_GET['id'])) {
    arsipBarang($conn, $_GET['id']);
}

function tambahBarang($conn, $nama_barang, $harga_jual, $stok, $id_supplier) {
    $stmt = $conn->prepare("INSERT INTO barang (nama_barang, harga_jual, stok, id_supplier) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdis", $nama_barang, $harga_jual, $stok, $id_supplier); 
    
    if ($stmt->execute()) {
        header("Location: ../admin/master_barang.php?status=sukses&pesan=Barang berhasil ditambahkan!");
    } else {
        header("Location: ../admin/master_barang.php?status=error&pesan=Gagal menambahkan Barang: " . $stmt->error);
    }
    $stmt->close();
    exit();
}

function ubahBarang($conn, $id_barang, $nama_barang, $harga_jual, $stok, $id_supplier) {
    $stmt = $conn->prepare("UPDATE barang SET nama_barang = ?, harga_jual = ?, stok = ?, id_supplier = ? WHERE id_barang = ?");
    $stmt->bind_param("sdsii", $nama_barang, $harga_jual, $stok, $id_supplier, $id_barang); 
    
    if ($stmt->execute()) {
        header("Location: ../admin/master_barang.php?status=sukses&pesan=Barang berhasil diubah!");
    } else {
        error_log("MySQL Error in ubahBarang: " . $stmt->error);
        header("Location: ../admin/master_barang.php?status=error&pesan=Gagal mengubah Barang: " . $stmt->error);
    }
    $stmt->close();
    exit();
}

function arsipBarang($conn, $id_barang) {
    $stmt = $conn->prepare("UPDATE barang SET is_active = '0' WHERE id_barang = ?");
    $stmt->bind_param("i", $id_barang);
    
    if ($stmt->execute()) {
        header("Location: ../admin/master_barang.php?status=sukses&pesan=Barang berhasil diarsipkan!");
    } else {
        header("Location: ../admin/master_barang.php?status=error&pesan=Gagal mengarsipkan Barang: " . $stmt->error);
    }
    $stmt->close();
    exit();
}
?>
        