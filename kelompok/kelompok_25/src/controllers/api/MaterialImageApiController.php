<?php

/**
 * MaterialImage API Controller
 * Menangani upload dan manajemen gambar material
 */

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Response.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/middleware/AuthMiddleware.php';
require_once ROOT_PATH . '/models/Material.php';
require_once ROOT_PATH . '/models/MaterialImage.php';

class MaterialImageApiController extends Controller
{
    private $imageModel;
    private $materialModel;
    private $uploadDir;
    private $maxFileSize = 2097152; // 2MB
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    public function __construct()
    {
        AuthMiddleware::check();
        
        $this->imageModel = new MaterialImage();
        $this->materialModel = new Material();
        $this->uploadDir = ROOT_PATH . '/public/uploads/materials';
        
        // Create upload directory if not exists
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * GET /api/materials/:id/images
     * Get all images for a material
     */
    public function index($materialId)
    {
        error_log("=== MaterialImageApiController index START ===");
        error_log("Material ID: " . $materialId);
        
        try {
            $materialId = intval($materialId);
            error_log("Material ID (int): " . $materialId);

            $material = $this->materialModel->findById($materialId);
            error_log("Material found: " . ($material ? 'YES' : 'NO'));

            if (!$material) {
                error_log("Material not found, sending 404");
                Response::error('Material tidak ditemukan', [], 404);
                return;
            }

            $images = $this->imageModel->getByMaterial($materialId);
            error_log("Images count: " . count($images));

            // image_url already exists in database, no need to modify

            error_log("Sending success response with " . count($images) . " images");
            Response::success('Data gambar berhasil diambil', [
                'data' => $images,
                'total' => count($images)
            ]);
            error_log("=== MaterialImageApiController index END ===");

        } catch (Exception $e) {
            error_log("MaterialImageApiController index ERROR: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            Response::error('Gagal mengambil data gambar: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * POST /api/materials/:id/images
     * Upload new image
     */
    public function upload($materialId)
    {
        try {
            $materialId = intval($materialId);

            $material = $this->materialModel->findById($materialId);

            if (!$material) {
                Response::error('Material tidak ditemukan', [], 404);
                return;
            }

            // Check if file uploaded
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                Response::error('File gambar tidak valid', ['image' => ['Mohon upload file gambar yang valid']], 422);
                return;
            }

            $file = $_FILES['image'];

            // Validate file size (max 2MB)
            $maxSize = 2 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                Response::error('Ukuran file terlalu besar', ['image' => ['Ukuran file maksimal 2MB']], 422);
                return;
            }

            // Validate file type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mimeType, $allowedTypes)) {
                Response::error('Format file tidak didukung', ['image' => ['File harus berformat JPG, PNG, GIF, atau WEBP']], 422);
                return;
            }

            // Create directory for material if not exists
            $materialDir = $this->uploadDir . '/' . $materialId;
            if (!file_exists($materialDir)) {
                mkdir($materialDir, 0755, true);
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = time() . '_' . uniqid() . '.' . $extension;
            $fullPath = $materialDir . '/' . $filename;
            $relativePath = '/uploads/materials/' . $materialId . '/' . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $fullPath)) {
                Response::error('Gagal meng-upload file', [], 500);
                return;
            }

            // Check if this is the first image
            $existingImages = $this->imageModel->getByMaterial($materialId);
            $isPrimary = empty($existingImages) ? 1 : 0;

            // Save to database
            $imageId = $this->imageModel->create([
                'material_id' => $materialId,
                'image_url' => $relativePath,
                'is_primary' => $isPrimary
            ]);

            if ($imageId) {
                $image = $this->imageModel->findById($imageId);

                Response::created('Gambar berhasil di-upload', $image);
            } else {
                // Clean up file if database insert fails
                unlink($fullPath);
                Response::error('Gagal menyimpan data gambar', [], 500);
            }

        } catch (Exception $e) {
            Response::error('Terjadi kesalahan: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * POST /api/materials/images/:id/set-primary
     * Set image as primary
     */
    public function setPrimary($id)
    {
        try {
            $id = intval($id);

            $image = $this->imageModel->findById($id);

            if (!$image) {
                Response::error('Gambar tidak ditemukan', [], 404);
                return;
            }

            $success = $this->imageModel->setPrimary($id, $image['material_id']);

            if ($success) {
                Response::success('Gambar utama berhasil diatur', []);
            } else {
                Response::error('Gagal mengatur gambar utama', [], 500);
            }

        } catch (Exception $e) {
            Response::error('Terjadi kesalahan: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * POST /api/materials/images/:id/delete
     * Delete image
     */
    public function destroy($id)
    {
        try {
            $id = intval($id);

            $image = $this->imageModel->findById($id);

            if (!$image) {
                Response::error('Gambar tidak ditemukan', [], 404);
                return;
            }

            // Delete file from filesystem
            $fullPath = ROOT_PATH . '/public' . $image['path'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            // If deleting primary image, set another as primary
            if ($image['is_primary']) {
                $images = $this->imageModel->getByMaterial($image['material_id']);
                if (count($images) > 1) {
                    // Set the next image as primary
                    foreach ($images as $img) {
                        if ($img['id'] != $id) {
                            $this->imageModel->setPrimary($img['id'], $image['material_id']);
                            break;
                        }
                    }
                }
            }

            // Delete from database
            $success = $this->imageModel->delete($id);

            if ($success) {
                Response::success('Gambar berhasil dihapus', []);
            } else {
                Response::error('Gagal menghapus gambar', [], 500);
            }

        } catch (Exception $e) {
            Response::error('Terjadi kesalahan: ' . $e->getMessage(), [], 500);
        }
    }

    /**
     * Resize image to max 1200x1200
     */
    private function resizeImage($path, $mimeType)
    {
        $maxWidth = 1200;
        $maxHeight = 1200;

        // Get current dimensions
        list($width, $height) = getimagesize($path);

        // Check if resize needed
        if ($width <= $maxWidth && $height <= $maxHeight) {
            return;
        }

        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = (int)($width * $ratio);
        $newHeight = (int)($height * $ratio);

        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Load original image
        switch ($mimeType) {
            case 'image/jpeg':
                $source = imagecreatefromjpeg($path);
                break;
            case 'image/png':
                $source = imagecreatefrompng($path);
                // Preserve transparency
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);
                break;
            case 'image/webp':
                $source = imagecreatefromwebp($path);
                break;
            default:
                return;
        }

        // Resize
        imagecopyresampled($newImage, $source, 0, 0, 0, 0, 
            $newWidth, $newHeight, $width, $height);

        // Save resized image
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($newImage, $path, 85);
                break;
            case 'image/png':
                imagepng($newImage, $path, 8);
                break;
            case 'image/webp':
                imagewebp($newImage, $path, 85);
                break;
        }

        // Clean up
        imagedestroy($source);
        imagedestroy($newImage);
    }

    /**
     * Get extension from MIME type
     */
    private function getExtensionFromMime($mimeType)
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        return $extensions[$mimeType] ?? 'jpg';
    }

    /**
     * Get full image URL
     */
    private function getImageUrl($path)
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        return "$protocol://$host/$path";
    }

    /**
     * Log activity
     */
    private function logActivity($action, $entity, $entityId, $description)
    {
        try {
            $userId = Auth::id();
            
            $sql = "INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare($sql);
            $stmt->execute([$userId, $action, $entity, $entityId, $description]);
        } catch (Exception $e) {
            error_log("Failed to log activity: " . $e->getMessage());
        }
    }
}
