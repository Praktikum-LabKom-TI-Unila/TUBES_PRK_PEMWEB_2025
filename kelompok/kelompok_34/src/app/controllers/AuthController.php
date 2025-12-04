<?php

class AuthController extends Controller
{
  private User $user;

  public function __construct()
  {
    $this->user = $this->model('User');
    session_start();

    if (isAuthenticated()) {
      header("Location: " . BASE_URL . "/pos");
      exit;
    }
  }

  public function register()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $name = trim($_POST['name']);
      $email = trim($_POST['email']);
      $password = trim($_POST['password']);
      $confirm = trim($_POST['confirm']);

      if ($password !== $confirm) {
        $error = "Password tidak sama!";
        return $this->view('auth/register', compact('error'));
      }

      $hash = password_hash($password, PASSWORD_BCRYPT);

      $result = $this->user->create([
        'name' => $name,
        'email' => $email,
        'password' => $hash,
      ]);

      if ($result) {
        $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
        header("Location: " . BASE_URL . "/auth/login");
        exit;
      } else {
        $error = "Gagal registrasi!";
        return $this->view('auth/register', compact('error'));
      }
    }

    return $this->view('auth/register');
  }

  public function login()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $email = trim($_POST['email']);
      $password = trim($_POST['password']);

      $user = $this->user->findByEmail($email);

      if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = [
          'id' => $user['id'],
          'name' => $user['name'],
          'email' => $user['email'],
          'role' => $user['role']
        ];

        return header("Location: " . BASE_URL . "/pos");
      }

      $error = "Email / Password salah!";
      return $this->view('auth/login', compact('error'));
    }

    return $this->view('auth/login');
  }

  public function logout()
  {
    session_start();
    session_destroy();

    session_start();
    $_SESSION['success'] = "Logout berhasil!";
    header("Location: " . BASE_URL . "/auth/login");
    exit;
  }
}
