<?php
declare(strict_types=1);

final class ProfileController
{
    private User $userModel;
    private Item $itemModel;
    private Claim $claimModel;

    public function __construct()
    {

        if (!isset($_SESSION['user'])) {
            flash('error', 'Silakan login terlebih dahulu', 'error');
            redirect('index.php?page=auth&action=login');
        }

        $this->userModel = new User();
        $this->itemModel = new Item();
        $this->claimModel = new Claim();
    }

    
    public function index(): void
    {
        $userId = (int) $_SESSION['user']['id'];

        $user = $this->userModel->findById($userId);

        if (!$user) {
            flash('error', 'User tidak ditemukan', 'error');
            redirect('index.php');
        }

        $stats = [
            'total_items' => $this->itemModel->countByUserId($userId),
            'total_claims' => $this->claimModel->countByUserId($userId)
        ];

        $pageTitle = 'Profil Saya - myUnila Lost & Found';

        $success = flash('success');
        $error = flash('error');

        require_once __DIR__ . '/../views/profile/index.php';
    }

    
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('index.php?page=profile');
        }

        $userId = (int) $_SESSION['user']['id'];
        $errors = [];
        $updateData = [];

        $name = trim($_POST['name'] ?? '');
        if (empty($name)) {
            $errors[] = 'Nama wajib diisi';
        } else {
            $updateData['name'] = $name;
        }

        $phone = trim($_POST['phone'] ?? '');
        if (!empty($phone)) {
            if (!preg_match('/^[0-9]{10,15}$/', $phone)) {
                $errors[] = 'Format nomor telepon tidak valid (10-15 digit)';
            } else {
                $updateData['phone'] = $phone;
            }
        } else {
            $updateData['phone'] = null;
        }

        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!empty($oldPassword) || !empty($newPassword) || !empty($confirmPassword)) {

            $currentUser = $this->userModel->findById($userId);

            if (empty($oldPassword)) {
                $errors[] = 'Password lama wajib diisi jika ingin mengubah password';
            } elseif (!password_verify($oldPassword, $currentUser['password'])) {
                $errors[] = 'Password lama tidak sesuai';
            }

            if (empty($newPassword)) {
                $errors[] = 'Password baru wajib diisi';
            } elseif (strlen($newPassword) < 8) {
                $errors[] = 'Password baru minimal 8 karakter';
            }

            if ($newPassword !== $confirmPassword) {
                $errors[] = 'Konfirmasi password tidak sesuai';
            }

            if (empty($errors) && !empty($newPassword)) {
                $updateData['password'] = $newPassword; // Will be hashed in model
            }
        }

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/uploads/profiles/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $avatarPath = uploadImage(
                $_FILES['avatar'],
                $uploadDir,
                'AVT',
                $userId
            );

            if ($avatarPath === false) {
                $errors[] = 'Gagal mengupload foto profil. Pastikan file adalah gambar (JPG, PNG, GIF) dan ukuran maksimal 5MB';
            } else {

                $currentUser = $this->userModel->findById($userId);
                if (!empty($currentUser['avatar']) && $currentUser['avatar'] !== 'default.jpg') {
                    $oldAvatarPath = __DIR__ . '/../assets/uploads/profiles/' . $currentUser['avatar'];
                    if (file_exists($oldAvatarPath)) {
                        unlink($oldAvatarPath);
                    }
                }

                $updateData['avatar'] = basename($avatarPath);
            }
        }

        if (!empty($errors)) {
            flash('error', implode('<br>', $errors), 'error');
            redirect('index.php?page=profile');
        }

        $success = $this->userModel->update($userId, $updateData);

        if ($success) {

            $_SESSION['user']['name'] = $updateData['name'];
            if (isset($updateData['avatar'])) {
                $_SESSION['user']['avatar'] = $updateData['avatar'];
            }

            flash('success', 'Profil berhasil diperbarui', 'success');
        } else {
            flash('error', 'Gagal memperbarui profil', 'error');
        }

        redirect('index.php?page=profile');
    }
}
