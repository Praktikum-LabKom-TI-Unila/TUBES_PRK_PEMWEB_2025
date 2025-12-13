<?php

class ProductController extends Controller
{
    private $productModel;

    public function __construct()
    {
        requireAdmin();
        $this->productModel = $this->model('Product');
    }

    public function index()
    {
        $data['title'] = 'Kelola Produk - ' . APP_NAME;
        $data['products'] = $this->productModel->getAll();

        $this->view('products/index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Produk - ' . APP_NAME;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<pre>";
            echo "POST Data:\n";
            print_r($_POST);
            echo "\nFILE Data:\n";
            print_r($_FILES);
            echo "</pre>";

            $name = trim($_POST['name']);
            $price = floatval($_POST['price']);
            $stock = intval($_POST['stock']);
            $image = 'default.jpg';

            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $image = $this->uploadImage($_FILES['image']);
            }

            $result = $this->productModel->create([
                'name' => $name,
                'price' => $price,
                'stock' => $stock,
                'image' => $image
            ]);

            if ($result) {
                $_SESSION['success'] = 'Produk berhasil ditambahkan!';
                header("Location: " . BASE_URL . "/product");
                exit;
            } else {
                $data['error'] = 'Gagal menambahkan produk!';
            }
        }

        $this->view('products/create', $data);
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Produk - ' . APP_NAME;
        $data['product'] = $this->productModel->find($id);

        if (!$data['product']) {
            $_SESSION['error'] = 'Produk tidak ditemukan!';
            header("Location: " . BASE_URL . "/product");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $price = floatval($_POST['price']);
            $stock = intval($_POST['stock']);
            $image = $data['product']['image'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                if ($image && $image !== 'default.jpg') {
                    $oldImagePath = BASE_PATH . "/src/public/uploads/products/" . $image;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $image = $this->uploadImage($_FILES['image']);
            }

            $result = $this->productModel->update($id, [
                'name' => $name,
                'price' => $price,
                'stock' => $stock,
                'image' => $image
            ]);

            if ($result) {
                $_SESSION['success'] = 'Produk berhasil diupdate!';
                header("Location: " . BASE_URL . "/product");
                exit;
            } else {
                $data['error'] = 'Gagal mengupdate produk!';
            }
        }

        $this->view('products/edit', $data);
    }

    public function delete($id)
    {
        $product = $this->productModel->find($id);

        if (!$product) {
            $_SESSION['error'] = 'Produk tidak ditemukan!';
            header("Location: " . BASE_URL . "/product");
            exit;
        }

        if ($product['image'] && $product['image'] !== 'default.jpg') {
            $imagePath = BASE_PATH . "/src/public/uploads/products/" . $product['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $result = $this->productModel->delete($id);

        if ($result) {
            $_SESSION['success'] = 'Produk dan gambar berhasil dihapus!';
        } else {
            $_SESSION['error'] = 'Gagal menghapus produk!';
        }

        header("Location: " . BASE_URL . "/product");
        exit;
    }

    private function uploadImage($file)
    {
        $targetDir = BASE_PATH . "/src/public/uploads/products/";

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors = [
                UPLOAD_ERR_INI_SIZE => 'File terlalu besar (php.ini limit)',
                UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (form limit)',
                UPLOAD_ERR_PARTIAL => 'File hanya ter-upload sebagian',
                UPLOAD_ERR_NO_FILE => 'Tidak ada file yang di-upload',
                UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ada',
                UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
                UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh extension',
            ];

            error_log("Upload Error: " . ($errors[$file['error']] ?? 'Unknown error'));
            return 'default.jpg';
        }

        $maxSize = 5 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            error_log("File terlalu besar: " . $file['size'] . " bytes");
            return 'default.jpg';
        }

        $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes)) {
            error_log("Tipe file tidak diizinkan: " . $mimeType);
            return 'default.jpg';
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('prod_', true) . '.' . strtolower($extension);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            chmod($targetFile, 0644);
            return $filename;
        } else {
            error_log("Gagal memindahkan file dari " . $file['tmp_name'] . " ke " . $targetFile);
            return 'default.jpg';
        }
    }

    private function getImageUrl($imageName)
    {
        if ($imageName && $imageName !== 'default.jpg') {
            $path = BASE_PATH . "/src/public/uploads/products/" . $imageName;
            if (file_exists($path)) {
                return BASE_URL . "/public/uploads/products/" . $imageName;
            }
        }
        return BASE_URL . "/public/images/default-product.jpg";
    }
}
