<?php
require_once __DIR__ . '/response.php';

function save_base64_image(?string $payload, string $subDirectory, array $allowedMime = ['image/jpeg', 'image/png', 'image/webp']): ?string
{
    if ($payload === null || $payload === '') {
        return null;
    }

    if (!preg_match('/^data:(.*?);base64,(.*)$/', $payload, $matches)) {
        response_error(422, 'Format upload tidak valid. Gunakan base64 dengan prefix data URI.', [
            'field' => 'photo_base64',
            'reason' => 'invalid_format',
        ]);
    }

    $mime = strtolower(trim($matches[1]));
    $base64Data = $matches[2];

    if (!in_array($mime, $allowedMime, true)) {
        response_error(422, 'Tipe file tidak diizinkan. Gunakan JPG/PNG/WEBP.', [
            'field' => 'photo_base64',
            'reason' => 'unsupported_mime',
        ]);
    }

    $binary = base64_decode($base64Data, true);
    if ($binary === false) {
        response_error(422, 'Data base64 tidak valid.', [
            'field' => 'photo_base64',
            'reason' => 'decode_failed',
        ]);
    }

    $extensionMap = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];
    $extension = $extensionMap[$mime] ?? 'bin';

    $baseDir = dirname(__DIR__) . '/uploads/' . trim($subDirectory, '/');
    if (!is_dir($baseDir) && !mkdir($baseDir, 0755, true) && !is_dir($baseDir)) {
        response_error(500, 'Gagal membuat folder upload.', [
            'reason' => 'mkdir_failed',
            'path' => $baseDir,
        ]);
    }

    if (!is_writable($baseDir)) {
        response_error(500, 'Folder upload tidak bisa ditulisi.', [
            'reason' => 'upload_dir_not_writable',
            'path' => $baseDir,
            'solution' => 'Pastikan user proses PHP memiliki akses tulis (contoh: chown/chmod pada folder uploads).',
        ]);
    }

    $filename = uniqid('upload_', true) . '.' . $extension;
    $filePath = $baseDir . '/' . $filename;

    if (file_put_contents($filePath, $binary) === false) {
        response_error(500, 'Gagal menyimpan file.', [
            'reason' => 'write_failed',
        ]);
    }

    @chmod($filePath, 0644);

    return 'uploads/' . trim($subDirectory, '/') . '/' . $filename;
}

function save_uploaded_file(array $file, string $subDirectory, array $allowedMime = ['image/jpeg', 'image/png', 'image/webp']): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        response_error(422, 'Gagal mengunggah file.', [
            'reason' => 'upload_error',
            'detail' => $file['error'] ?? null,
        ]);
    }

    $tmpName = $file['tmp_name'] ?? null;
    $isStreamUpload = !empty($file['is_stream_upload']);
    $isHttpUpload = $tmpName ? is_uploaded_file($tmpName) : false;

    if (empty($tmpName) || (!$isHttpUpload && !$isStreamUpload)) {
        response_error(400, 'File upload tidak ditemukan.', [
            'reason' => 'missing_tmp_file',
        ]);
    }

    $mime = mime_content_type($tmpName);
    if ($mime === false || !in_array($mime, $allowedMime, true)) {
        response_error(422, 'Tipe file tidak diizinkan. Gunakan JPG/PNG/WEBP.', [
            'reason' => 'unsupported_mime',
        ]);
    }

    $extension = determine_upload_extension($file, $mime);

    $baseDir = dirname(__DIR__) . '/uploads/' . trim($subDirectory, '/');
    if (!is_dir($baseDir) && !mkdir($baseDir, 0755, true) && !is_dir($baseDir)) {
        response_error(500, 'Gagal membuat folder upload.', [
            'reason' => 'mkdir_failed',
            'path' => $baseDir,
        ]);
    }

    if (!is_writable($baseDir)) {
        response_error(500, 'Folder upload tidak bisa ditulisi.', [
            'reason' => 'upload_dir_not_writable',
            'path' => $baseDir,
            'solution' => 'Pastikan user proses PHP memiliki akses tulis (contoh: chown/chmod pada folder uploads).',
        ]);
    }

    $filename = uniqid('upload_', true) . '.' . $extension;
    $destination = $baseDir . '/' . $filename;

    $moveSucceeded = false;
    if ($isHttpUpload) {
        $moveSucceeded = move_uploaded_file($tmpName, $destination);
    } else {
        $moveSucceeded = rename($tmpName, $destination);
        if (!$moveSucceeded && is_file($tmpName)) {
            $moveSucceeded = copy($tmpName, $destination) && unlink($tmpName);
        }
    }

    if (!$moveSucceeded) {
        response_error(500, 'Gagal memindahkan file upload.', [
            'reason' => 'move_failed',
        ]);
    }

    @chmod($destination, 0644);

    return 'uploads/' . trim($subDirectory, '/') . '/' . $filename;
}

function determine_upload_extension(array $file, string $mime): string
{
    $mimeExtensionMap = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'image/webp' => ['webp'],
    ];

    $originalExtension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    $allowedExtensions = $mimeExtensionMap[$mime] ?? [];

    if ($originalExtension !== '' && in_array($originalExtension, $allowedExtensions, true)) {
        return $originalExtension;
    }

    if (!empty($allowedExtensions)) {
        return $allowedExtensions[0];
    }

    return $originalExtension !== '' ? $originalExtension : 'bin';
}

function normalize_uploaded_file(?array $file): ?array
{
    if (!$file || !isset($file['name'])) {
        return null;
    }

    if (is_array($file['name'])) {
        $names = $file['name'];
        $types = $file['type'] ?? [];
        $tmpNames = $file['tmp_name'] ?? [];
        $errors = $file['error'] ?? [];
        $sizes = $file['size'] ?? [];

        foreach ($names as $idx => $name) {
            $error = $errors[$idx] ?? UPLOAD_ERR_NO_FILE;
            if ($error === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            return [
                'name' => $name,
                'type' => $types[$idx] ?? '',
                'tmp_name' => $tmpNames[$idx] ?? '',
                'error' => $error,
                'size' => $sizes[$idx] ?? 0,
            ];
        }

        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    return $file;
}

function find_uploaded_file(array $files, array $preferredKeys = []): ?array
{
    foreach ($preferredKeys as $key) {
        if (!array_key_exists($key, $files)) {
            continue;
        }

        $normalized = normalize_uploaded_file($files[$key]);
        if ($normalized) {
            return $normalized;
        }
    }

    foreach ($files as $file) {
        $normalized = normalize_uploaded_file($file);
        if ($normalized) {
            return $normalized;
        }
    }

    return null;
}
