<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/Emailer.php';

final class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register(): void
    {
        $name           = clean($_POST['name'] ?? '');
        $identityNumber = clean($_POST['identity_number'] ?? '');
        $email          = clean($_POST['email'] ?? '');
        $phone          = clean($_POST['phone'] ?? '');
        $password       = $_POST['password'] ?? '';
        $confirm        = $_POST['password_confirmation'] ?? '';

        $errors = $this->validateRegistration($name, $identityNumber, $email, $password, $confirm);

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

        if ($this->userModel->identityNumberExists($identityNumber)) {
            flash('message', 'Nomor Identitas sudah terdaftar. Silakan gunakan nomor identitas lain.', 'error');
            redirect('index.php?page=auth&action=register');
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $userData = [
            'name'            => $name,
            'identity_number' => $identityNumber,
            'email'           => $email,
            'password'        => $hashedPassword,
            'phone'           => $phone ?: null,
            'role'            => 'user',
            'is_active'       => 1
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
            flash('message', 'Akun Anda tidak aktif/banned. Silakan hubungi administrator.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        session_regenerate_id(true);

        $this->createSession($user);
        $this->userModel->updateLastLogin((int)$user['id']);

        flash('message', 'Selamat datang, ' . $user['name'] . '!', 'success');

        if ($user['role'] === 'admin') {
            // Arahkan ke Dashboard Admin via Controller
            redirect('index.php?page=admin&action=dashboard');
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
    
    public function forgotPasswordForm(): void
    {
        view('auth/forgot_password'); 
    }

    public function sendResetLink(): void
    {
        $email = clean($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('message', 'Format email tidak valid.', 'error');
            redirect('index.php?page=auth&action=forgotPasswordForm');
            return;
        }

        $user = $this->userModel->findByEmail($email);
        
        // Keamanan: Hanya proses reset jika user ditemukan dan aktif.
        // Frontend tetap diberi pesan sukses agar tidak membocorkan informasi email terdaftar.
        if ($user && (int)$user['is_active'] === 1) {
            
            $token = bin2hex(random_bytes(32)); 
            
            // 1. Simpan Token
            if ($this->userModel->saveResetToken($email, $token)) {
                
                // 2. Bangun Link Verifikasi
                $resetLink = base_url("index.php?page=auth&action=resetPasswordForm&email=" . urlencode($email) . "&token={$token}");
                
                // 3. Kirim Email
                $emailer = new Emailer();
                $emailSent = $emailer->sendResetPasswordEmail($email, $user['name'], $resetLink);
                
                if (!$emailSent) {
                    error_log("Failed to send reset email for: " . $email);
                    // Kita biarkan sukses di frontend
                }
            } else {
                 error_log("Database failed to save token for: " . $email);
            }
        }
        
        // Tampilkan pesan sukses umum
        flash('message', 'Link reset password telah dikirim ke email Anda. Periksa kotak masuk.', 'success');
        redirect('index.php?page=auth&action=forgotPasswordForm');
    }

    public function resetPasswordForm(): void
    {
        $token = $_GET['token'] ?? '';
        $email = clean($_GET['email'] ?? '');

        if (empty($token) || empty($email)) {
            flash('message', 'Link reset tidak valid atau hilang.', 'error');
            redirect('index.php?page=auth&action=login');
            return;
        }

        // Menggunakan findValidResetToken (memeriksa kadaluarsa 1 jam)
        $tokenData = $this->userModel->findValidResetToken($email, $token); 

        if ($tokenData === false) {
            flash('message', 'Token reset tidak valid atau sudah kadaluarsa (lebih dari 1 jam). Silakan minta link reset baru.', 'error');
            redirect('index.php?page=auth&action=forgotPasswordForm');
            return;
        }
        
        view('auth/reset_password', ['email' => $email, 'token' => $token]);
    }

    public function resetPassword(): void
    {
        $token    = $_POST['token'] ?? '';
        $email    = clean($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirmation'] ?? '';

    
        if (empty($token) || empty($email) || empty($password) || empty($confirm)) {
            flash('message', 'Semua field wajib diisi.', 'error');
            redirect("index.php?page=auth&action=resetPasswordForm&token={$token}&email={$email}");
            return;
        }

        if ($password !== $confirm || strlen($password) < 6) {
            flash('message', 'Password minimal 6 karakter dan harus cocok dengan konfirmasi.', 'error');
            redirect("index.php?page=auth&action=resetPasswordForm&token={$token}&email={$email}");
            return;
        }
        

        $tokenData = $this->userModel->findValidResetToken($email, $token);

        if ($tokenData === false) {
            flash('message', 'Token tidak valid atau kadaluarsa. Silakan minta link reset baru.', 'error');
            redirect('index.php?page=auth&action=forgotPasswordForm');
            return;
        }
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        if ($this->userModel->updatePassword($email, $hashedPassword)) {

            $this->userModel->deleteResetToken($token);
            
            flash('message', 'Password berhasil direset! Silakan login.', 'success');
            redirect('index.php?page=auth&action=login');
            return;
        } else {
            flash('message', 'Gagal memperbarui password. Silakan coba lagi.', 'error');
            redirect('index.php?page=auth&action=forgotPasswordForm');
            return;
        }
    }
    
    private function createSession(array $user): void
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user'] = [
            'id'              => (int)$user['id'],
            'name'            => $user['name'],
            'email'           => $user['email'],
            'identity_number' => $user['identity_number'],
            'role'            => $user['role'],
            'avatar'          => $user['avatar'] ?? null
        ];
    }

    private function validateRegistration(
        string $name,
        string $identityNumber,
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

        if (empty($identityNumber)) {
            $errors[] = 'Nomor Identitas (NPM/NIP/NIK) wajib diisi.';
        } elseif (!ctype_digit($identityNumber)) {
            $errors[] = 'Nomor Identitas harus berupa angka.';
        } elseif (strlen($identityNumber) < 8 || strlen($identityNumber) > 20) {
            $errors[] = 'Nomor Identitas harus antara 8-20 digit.';
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