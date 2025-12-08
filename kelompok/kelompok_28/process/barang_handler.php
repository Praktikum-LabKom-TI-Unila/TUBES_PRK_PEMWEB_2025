<?php
session_start();
// PERBAIKAN 1: Sesuaikan nama file koneksi jadi database.php
require_once '../config/database.php'; 

// Cek apakah user sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../auth/login.php");
    exit;
}

$action = $_GET['act'] ?? '';

// --- LOGIKA TAMBAH BARANG (CREATE) ---
if ($action == 'add' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil store_id dari session (asumsi saat login karyawan, store_id disimpan di session)
    // Jika belum ada di session login, kita query dulu berdasarkan user_id
    $user_id = $_SESSION['user_id'];
    $sql_emp = "SELECT store_id FROM employees WHERE id = '$user_id'";
    $res_emp = mysqli_query($conn, $sql_emp);
    $emp_data = mysqli_fetch_assoc($res_emp);
    $store_id = $emp_data['store_id'];

    $name           = mysqli_real_escape_string($conn, $_POST['name']);
    $category_id    = $_POST['category_id'];
    $price          = $_POST['price'];
    $stock          = $_POST['stock'];
    $description    = "-"; 

    $query = "INSERT INTO products (store_id, category_id, name, description, price, stock, is_active) 
              VALUES ('$store_id', '$category_id', '$name', '$description', '$price', '$stock', 1)";

    if (mysqli_query($conn, $query)) {
        header("Location: ../pages/admin_gudang/inventory.php?status=success");
    } else {
        header("Location: ../pages/admin_gudang/inventory.php?status=error&msg=".urlencode(mysqli_error($conn)));
    }
}

// --- LOGIKA HAPUS BARANG (SOFT DELETE) ---
elseif ($action == 'delete') {
    $id = $_GET['id'];
    
    // Update is_active jadi 0
    $query = "UPDATE products SET is_active = 0 WHERE id = '$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: ../pages/admin_gudang/inventory.php?msg=deleted");
    } else {
        echo "Gagal menghapus data";
    }
}

// --- LOGIKA TAMBAH KATEGORI BARU ---
elseif ($action == 'add_category') {
    $store_id = $_SESSION['store_id']; // Ambil ID toko dari session
    $name     = mysqli_real_escape_string($conn, $_POST['category_name']);

    // Cek biar ga duplikat
    $check = mysqli_query($conn, "SELECT id FROM categories WHERE store_id='$store_id' AND name='$name'");
    if(mysqli_num_rows($check) > 0) {
        header("Location: ../pages/admin_gudang/inventory.php?status=error&msg=Kategori sudah ada");
        exit;
    }

    $query = "INSERT INTO categories (store_id, name) VALUES ('$store_id', '$name')";

    if (mysqli_query($conn, $query)) {
        header("Location: ../pages/admin_gudang/inventory.php?status=success_cat");
    } else {
        header("Location: ../pages/admin_gudang/inventory.php?status=error");
    }
}
?>