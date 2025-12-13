<?php

/**
 * Upload Helper
 */

class Upload
{
    private $file;
    private $uploadPath;
    private $allowedTypes;
    private $maxSize;
    private $errors = [];

    public function __construct($file, $uploadPath = 'materials')
    {
        $this->file = $file;
        $this->uploadPath = UPLOAD_PATH . '/' . $uploadPath;
        $this->allowedTypes = ALLOWED_IMAGE_TYPES;
        $this->maxSize = MAX_FILE_SIZE;

        // Create upload directory if not exists
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }
    }

    /**
     * Validate file
     */
    public function validate()
    {
        // Check if file exists
        if (!isset($this->file['tmp_name']) || empty($this->file['tmp_name'])) {
            $this->errors[] = 'Tidak ada file yang diupload.';
            return false;
        }

        // Check upload error
        if ($this->file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = 'Terjadi kesalahan saat upload file.';
            return false;
        }

        // Check file size
        if ($this->file['size'] > $this->maxSize) {
            $maxSizeMB = $this->maxSize / 1024 / 1024;
            $this->errors[] = "Ukuran file maksimal $maxSizeMB MB.";
            return false;
        }

        // Check file type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $this->file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $this->allowedTypes)) {
            $this->errors[] = 'Tipe file tidak diizinkan. Hanya gambar (JPEG, PNG, WebP).';
            return false;
        }

        // Check if file is image
        $imageInfo = getimagesize($this->file['tmp_name']);
        if ($imageInfo === false) {
            $this->errors[] = 'File bukan gambar yang valid.';
            return false;
        }

        return true;
    }

    /**
     * Upload file
     */
    public function upload()
    {
        if (!$this->validate()) {
            return false;
        }

        // Generate unique filename
        $extension = pathinfo($this->file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $destination = $this->uploadPath . '/' . $filename;

        // Resize image if too large
        if (!$this->resizeImage($this->file['tmp_name'], $destination)) {
            $this->errors[] = 'Gagal memproses gambar.';
            return false;
        }

        return $filename;
    }

    /**
     * Resize image
     */
    private function resizeImage($source, $destination)
    {
        list($width, $height, $type) = getimagesize($source);

        // Check if resize needed
        $maxWidth = UPLOAD_MAX_WIDTH;
        $maxHeight = UPLOAD_MAX_HEIGHT;

        if ($width <= $maxWidth && $height <= $maxHeight) {
            // No resize needed, just copy
            return move_uploaded_file($source, $destination);
        }

        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);

        // Create image resource
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($source);
                break;
            case IMAGETYPE_WEBP:
                $image = imagecreatefromwebp($source);
                break;
            default:
                return false;
        }

        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG
        if ($type === IMAGETYPE_PNG) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        // Resize
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save image
        switch ($type) {
            case IMAGETYPE_JPEG:
                $result = imagejpeg($newImage, $destination, 85);
                break;
            case IMAGETYPE_PNG:
                $result = imagepng($newImage, $destination, 8);
                break;
            case IMAGETYPE_WEBP:
                $result = imagewebp($newImage, $destination, 85);
                break;
            default:
                $result = false;
        }

        // Free memory
        imagedestroy($image);
        imagedestroy($newImage);

        return $result;
    }

    /**
     * Delete file
     */
    public static function delete($filename, $uploadPath = 'materials')
    {
        $filePath = UPLOAD_PATH . '/' . $uploadPath . '/' . $filename;
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return false;
    }

    /**
     * Get errors
     */
    public function errors()
    {
        return $this->errors;
    }
}
