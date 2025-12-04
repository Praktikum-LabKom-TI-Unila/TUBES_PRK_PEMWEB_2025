<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/User.php';

final class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register(): void
    {
        $name     = clean($_POST['name'] ?? '');
        $npm      = clean($_POST['npm'] ?? '');
        $email    = clean($_POST['email'] ?? '');
        $phone    = clean($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirmation'] ?? '';

        $errors = $this->validateRegistration($name, $npm, $email, $password, $confirm);

        if (!empty($errors)) {
            flash('message', implode('<br>', $errors), 'error');
            redirect('index.php?page=auth&action=register');
            return;
        }

        if ($this->userModel->emailExists($email)) {
            flash('message', 'Email sudah terdaftar. Silakan gunakan email lain.', 'error');
            redirect('index.php?page=auth&action=register');
            return;
        }

        if ($this->userModel->npmExists($npm)) {
            flash('message', 'NPM sudah terdaftar. Silakan gunakan NPM lain.', 'error');
            redirect('index.php?page=auth&action=register');
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $userData = [
            'name'     => $name,
            'npm'      => $npm,
            'email'    => $email,
            'password' => $hashedPassword,
            'phone'    => $phone ?: null,
            'role'     => 'user',
            'is_active'=> 1
        ];

        $registered = $this->userModel->register($userData);

        if (!$registered) {
            flash('message', 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.', 'error');
            redirect('index.php?page=auth&action=register');
            return;
        }

        flash('message', 'Registrasi berhasil! Silakan login dengan akun Anda.', 'success');
        redirect('index.php?page=auth&action=login');
    }

    public function login(): void
    {
        $email    = clean($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            flash('message', 'Email dan password wajib diisi.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('message', 'Format email tidak valid.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $user = $this->userModel->findByEmail($email);

        if ($user === false) {
            flash('message', 'Email atau password salah.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        if (!password_verify($password, $user['password'])) {
            flash('message', 'Email atau password salah.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        if ((int)$user['is_active'] !== 1) {
            flash('message', 'Akun Anda tidak aktif. Silakan hubungi administrator.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        $this->createSession($user);
        $this->userModel->updateLastLogin((int)$user['id']);

        flash('message', 'Selamat datang, ' . $user['name'] . '!', 'success');

        if ($user['role'] === 'admin') {
            redirect('index.php?page=admin');
            return;
        }

        redirect('index.php?page=home');
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        flash('message', 'Anda telah berhasil logout.', 'success');
        redirect('index.php?page=home');
    }

    private function createSession(array $user): void
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user'] = [
            'id'     => (int)$user['id'],
            'name'   => $user['name'],
            'email'  => $user['email'],
            'npm'    => $user['npm'],
            'role'   => $user['role'],
            'avatar' => $user['avatar'] ?? null
        ];
    }

    private function validateRegistration(
        string $name,
        string $npm,
        string $email,
        string $password,
        string $confirm
    ): array {
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Nama lengkap wajib diisi.';
        } elseif (strlen($name) < 3) {
            $errors[] = 'Nama lengkap minimal 3 karakter.';
        }

        if (empty($npm)) {
            $errors[] = 'NPM wajib diisi.';
        } elseif (!ctype_digit($npm)) {
            $errors[] = 'NPM harus berupa angka.';
        } elseif (strlen($npm) < 10) {
            $errors[] = 'NPM minimal 10 digit.';
        }

        if (empty($email)) {
            $errors[] = 'Email wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }

        if (empty($password)) {
            $errors[] = 'Password wajib diisi.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter.';
        }

        if ($password !== $confirm) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }

        return $errors;
    }
}
