<?php
require_once '../../koneksi/database.php'; 
require_once '../manajemenUser/auth_middleware.php'; 

checkAuthorization(['STAFF', 'ADMIN', 'OWNER']);

$frontend_path = '../../frontend/catalog/'; 
$upload_dir = '../../../assets/uploads/products/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

function handle_image_upload($file_array, $old_image_name = null) {
    global $upload_dir;
    
    if ($file_array['error'] === UPLOAD_ERR_NO_FILE) {
        return $old_image_name; 
    }

    if ($file_array['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Error uploading file: " . $file_array['error']);
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file_array['type'], $allowed_types)) {
        throw new Exception("Format file tidak didukung (hanya JPG, PNG, GIF).");
    }

    $file_ext = pathinfo($file_array['name'], PATHINFO_EXTENSION);
    $new_file_name = uniqid('prod_') . '.' . $file_ext;
    $target_file = $upload_dir . $new_file_name;

    if (move_uploaded_file($file_array['tmp_name'], $target_file)) {
        if ($old_image_name && file_exists($upload_dir . $old_image_name) && $old_image_name !== 'default.png') {
            @unlink($upload_dir . $old_image_name); 
        }
        return $new_file_name;
    } else {
        throw new Exception("Gagal memindahkan file yang diupload.");
    }
}

if (isset($_POST['add_product'])) {
    $code          = bersihkan_input($_POST['code']);
    $name          = bersihkan_input($_POST['name']);
    $category      = bersihkan_input($_POST['category']);
    $unit          = bersihkan_input($_POST['unit']);
    $current_stock = (float)$_POST['current_stock'];
    $min_stock     = (int)$_POST['min_stock'];
    $selling_price = (float)$_POST['selling_price'];
    $is_active     = isset($_POST['is_active']) ? 1 : 0;
    $image_name    = 'default.png';

    try {
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $image_name = handle_image_upload($_FILES['image']);
        }
        
        $sql = "INSERT INTO products (code, name, category, unit, current_stock, min_stock, selling_price, is_active, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$code, $name, $category, $unit, $current_stock, $min_stock, $selling_price, $is_active, $image_name]);
        
        header("Location: {$frontend_path}products.php?status=success_add");
        exit();
    } catch (PDOException $e) {
        if ($image_name !== 'default.png' && file_exists($upload_dir . $image_name)) {
            @unlink($upload_dir . $image_name);
        }
        if ($e->getCode() === '23000') {
             $error_msg = urlencode("Kode atau Nama produk sudah terdaftar. Gunakan yang lain.");
             header("Location: {$frontend_path}create.php?status=error&msg=" . $error_msg);
             exit();
        } else {
             $error_msg = urlencode("Terjadi kesalahan database: " . $e->getMessage());
             header("Location: {$frontend_path}create.php?status=error&msg=" . $error_msg);
             exit();
        }
    } catch (Exception $e) {
        $error_msg = urlencode("Error Upload Gambar: " . $e->getMessage());
        header("Location: {$frontend_path}create.php?status=error&msg=" . $error_msg);
        exit();
    }
}

if (isset($_POST['edit_product'])) {
    $id            = $_POST['id'];
    $code          = bersihkan_input($_POST['code']);
    $name          = bersihkan_input($_POST['name']);
    $category      = bersihkan_input($_POST['category']);
    $unit          = bersihkan_input($_POST['unit']);
    $current_stock = (float)$_POST['current_stock'];
    $min_stock     = (int)$_POST['min_stock'];
    $selling_price = (float)$_POST['selling_price'];
    $is_active     = isset($_POST['is_active']) ? 1 : 0;
    $old_image     = $_POST['old_image']; 
    $image_name    = $old_image;
    
    try {
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $image_name = handle_image_upload($_FILES['image'], $old_image);
        }
        
        $sql = "UPDATE products SET 
                    code=?, 
                    name=?, 
                    category=?, 
                    unit=?, 
                    current_stock=?, 
                    min_stock=?, 
                    selling_price=?, 
                    is_active=?,
                    image=?
                WHERE id=?";
        
        $params = [$code, $name, $category, $unit, $current_stock, $min_stock, $selling_price, $is_active, $image_name, $id];

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        header("Location: {$frontend_path}products.php?status=success_edit");
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
             $error_msg = urlencode("Kode atau Nama produk sudah terdaftar. Gunakan yang lain.");
             header("Location: {$frontend_path}edit.php?id=$id&status=error&msg=" . $error_msg);
             exit();
        } else {
             $error_msg = urlencode("Terjadi kesalahan database: " . $e->getMessage());
             header("Location: {$frontend_path}edit.php?id=$id&status=error&msg=" . $error_msg);
             exit();
        }
    } catch (Exception $e) {
        $error_msg = urlencode("Error Upload Gambar: " . $e->getMessage());
        header("Location: {$frontend_path}edit.php?id=$id&status=error&msg=" . $error_msg);
        exit();
    }
}

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    $image_to_delete = $product['image'] ?? null;
    
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($image_to_delete && file_exists($upload_dir . $image_to_delete) && $image_to_delete !== 'default.png') {
            @unlink($upload_dir . $image_to_delete);
        }
        
        header("Location: {$frontend_path}products.php?status=success_delete");
        exit();
    } catch (PDOException $e) {
        $error_msg = urlencode("Gagal menghapus produk: " . $e->getMessage() . ". Mungkin produk ini masih memiliki data batch terkait.");
        header("Location: {$frontend_path}products.php?status=error&msg=" . $error_msg);
        exit();
    }
}
?>