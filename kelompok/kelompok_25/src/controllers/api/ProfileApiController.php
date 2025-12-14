<?php

/**
 * Profile API Controller
 * Handles user profile management
 */

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Response.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/middleware/AuthMiddleware.php';
require_once ROOT_PATH . '/models/User.php';
require_once ROOT_PATH . '/models/ActivityLog.php';

class ProfileApiController extends Controller
{
    private $userModel;
    private $activityLogModel;

    public function __construct()
    {
        AuthMiddleware::check();
        $this->userModel = new User();
        $this->activityLogModel = new ActivityLog();
    }

    /**
     * Update user profile
     */
    public function update()
    {
        try {
            $userId = Auth::id();
            $data = json_decode(file_get_contents('php://input'), true);

            // Validation
            if (empty($data['name'])) {
                return Response::validationError(['name' => 'Nama tidak boleh kosong']);
            }

            if (empty($data['email'])) {
                return Response::validationError(['email' => 'Email tidak boleh kosong']);
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return Response::validationError(['email' => 'Format email tidak valid']);
            }

            // Check if email already exists for other users
            $existingUser = $this->userModel->findByEmail($data['email']);
            if ($existingUser && $existingUser['id'] != $userId) {
                return Response::validationError(['email' => 'Email sudah digunakan']);
            }

            // Update user
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null
            ];

            $result = $this->userModel->updateUser($userId, $updateData);

            if ($result) {
                // Update session data
                $_SESSION['user']['name'] = $data['name'];
                $_SESSION['user']['email'] = $data['email'];

                // Log activity
                ActivityLog::logActivity('profile_updated', 'users', $userId, 'Memperbarui profil');

                return Response::success('Profil berhasil diperbarui');
            } else {
                return Response::error('Gagal memperbarui profil');
            }

        } catch (Exception $e) {
            error_log("Profile Update Error: " . $e->getMessage());
            return Response::error('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        try {
            $userId = Auth::id();
            $data = json_decode(file_get_contents('php://input'), true);

            // Validation
            if (empty($data['old_password'])) {
                return Response::validationError(['old_password' => 'Password lama tidak boleh kosong']);
            }

            if (empty($data['new_password'])) {
                return Response::validationError(['new_password' => 'Password baru tidak boleh kosong']);
            }

            if (strlen($data['new_password']) < 6) {
                return Response::validationError(['new_password' => 'Password baru minimal 6 karakter']);
            }

            if ($data['new_password'] !== $data['confirm_password']) {
                return Response::validationError(['confirm_password' => 'Konfirmasi password tidak cocok']);
            }

            // Get user
            $user = $this->userModel->findWithRoles($userId);
            if (!$user) {
                return Response::error('User tidak ditemukan', 404);
            }

            // Verify old password
            if (!password_verify($data['old_password'], $user['password_hash'])) {
                return Response::validationError(['old_password' => 'Password lama salah']);
            }

            // Check if new password is same as old password
            if ($data['old_password'] === $data['new_password']) {
                return Response::validationError(['new_password' => 'Password baru harus berbeda dengan password lama']);
            }

            // Update password
            $newPasswordHash = password_hash($data['new_password'], PASSWORD_BCRYPT);
            $result = $this->userModel->updateUser($userId, ['password_hash' => $newPasswordHash]);

            if ($result) {
                // Log activity
                ActivityLog::logActivity('password_changed', 'users', $userId, 'Mengubah password');

                return Response::success('Password berhasil diubah');
            } else {
                return Response::error('Gagal mengubah password');
            }

        } catch (Exception $e) {
            error_log("Password Change Error: " . $e->getMessage());
            return Response::error('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar()
    {
        try {
            $userId = Auth::id();

            // Check if file was uploaded
            if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                return Response::validationError(['avatar' => 'File tidak ditemukan atau terjadi kesalahan upload']);
            }

            $file = $_FILES['avatar'];

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                return Response::validationError(['avatar' => 'File harus berupa gambar (JPG, PNG, GIF)']);
            }

            // Validate file size (max 2MB)
            if ($file['size'] > 2 * 1024 * 1024) {
                return Response::validationError(['avatar' => 'Ukuran file maksimal 2MB']);
            }

            // Create uploads directory if not exists
            $uploadDir = ROOT_PATH . '/public/uploads/avatars';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . '/' . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return Response::error('Gagal menyimpan file');
            }

            // Get old avatar to delete
            $user = $this->userModel->find($userId);
            $oldAvatar = $user['avatar_url'] ?? null;

            // Update user avatar_url
            $avatarUrl = '/uploads/avatars/' . $filename;
            $result = $this->userModel->updateUser($userId, ['avatar_url' => $avatarUrl]);

            if ($result) {
                // Delete old avatar file if exists
                if ($oldAvatar && file_exists(ROOT_PATH . '/public' . $oldAvatar)) {
                    @unlink(ROOT_PATH . '/public' . $oldAvatar);
                }

                // Update session
                $_SESSION['user']['avatar_url'] = $avatarUrl;

                // Log activity
                ActivityLog::logActivity('avatar_updated', 'users', $userId, 'Mengupload foto profil');

                return Response::success('Foto profil berhasil diupload', ['avatar_url' => $avatarUrl]);
            } else {
                // Delete uploaded file if database update fails
                @unlink($filepath);
                return Response::error('Gagal memperbarui database');
            }

        } catch (Exception $e) {
            error_log("Avatar Upload Error: " . $e->getMessage());
            return Response::error('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get current user profile
     */
    public function me()
    {
        try {
            $userId = Auth::id();
            $user = $this->userModel->findWithRoles($userId);

            if (!$user) {
                return Response::error('User tidak ditemukan', 404);
            }

            // Remove sensitive data
            unset($user['password_hash']);
            unset($user['remember_token']);

            return Response::success('Data profil', $user);

        } catch (Exception $e) {
            error_log("Get Profile Error: " . $e->getMessage());
            return Response::error('Terjadi kesalahan: ' . $e->getMessage(), 500);
        }
    }
}
